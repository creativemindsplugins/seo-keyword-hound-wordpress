<?php

namespace com\cminds\seokeywords\plugin\controllers;

use com\cminds\seokeywords\App;
use com\cminds\seokeywords\plugin\helpers;
use com\cminds\seokeywords\plugin\models;
use com\cminds\seokeywords\plugin\misc;

class DashboardPageController extends PageControllerAbstract {

    public function __construct() {
        add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
//        add_action( 'save_post', array( &$this, 'save_post' ) );
        add_action( 'wp_ajax_cmsk_get_metrics', array( &$this, 'get_metrics' ) );
        add_action( 'wp_ajax_cmsk_get_search_results', array( &$this, 'get_search_results' ) );
        add_action( 'wp_ajax_cmsk_get_words_count', array( &$this, 'get_words_count' ) );
        add_action( 'wp_ajax_cmsk_update_metric_custom_fields', array( &$this, 'update_metric_custom_fields' ) );
        add_action( 'wp_ajax_cmsk_update_notification_date', array( &$this, 'update_notification_date' ) );
    }

    public function update_metric_custom_fields() {

        $data   = $this->get_metrics_data();
        $postId = filter_input( INPUT_POST, 'post_id' );
        $json   = json_decode( filter_input( INPUT_POST, 'custom_data' ), TRUE );
//        if ( $json === NULL || !is_array( $json[ 'fields' ] ) ) {
        if ( $json === NULL ) {
            return;
        }
        $generic = models\GenericPost::getInstance( $postId );
        if ( $generic ) {
            $generic->setMetricTableAdditionalData( $json );
        }

        $this->get_metrics();
    }

    public function update_notification_date() {

        $data  = $this->get_metrics_data();
        $index = filter_input( INPUT_POST, 'index' );
        $json  = filter_input( INPUT_POST, 'notification_last_reset' );
//        if ( $json === NULL || !is_array( $json[ 'fields' ] ) ) {
        if ( $json === NULL ) {
            return;
        }
        $postId  = $data[ $index ][ 'post_id' ];
        $generic = models\GenericPost::getInstance( $postId );
        if ( $generic ) {
            $arr                              = $generic->getSeoKeywordsDashboardUrlData();
            $arr[ 'notification_last_reset' ] = $json;
            $arr[ 'notification_email_sent' ] = false;

            $generic->setSeoKeywordsDashboardUrlData( $arr );
            $generic->setSendNotificationData( $arr );
        }

        $this->get_metrics();
    }

    public function get_search_results() {
        $post_id = filter_input( INPUT_POST, 'post_id' );
        $query   = filter_input( INPUT_POST, 'query' );
        if ( !empty( $query ) ) {
            $params      = ['query' => $query ];
            $result      = helpers\GoogleSearchHelper::getArray( $params );
            $searchQuery = helpers\GoogleSearchHelper::getQuery( $params );

            $post = get_post( $post_id );
            if ( empty( $post ) ) {
                wp_send_json( ['result' => FALSE, 'message' => 'Invalid post' ] );
            }
            $permalink               = get_permalink( $post );
            $generic                 = models\GenericPost::getInstance( $post );
            $urls                    = $generic->getSeoCompetitorsDataItemsUrls();
            $topCompetitors          = $generic->getSeoCompetitorsDataItemsTopCompetitors();
            $competitorSearchPhrases = $generic->getSeoCompetitorsDataItemsSearchPhrases();
            $dashboardUrlData        = $generic->getSeoCompetitorsDashboardUrl();
            $searchPhrases           = isset( $dashboardUrlData[ 'searchphrases' ] ) ? $dashboardUrlData[ 'searchphrases' ] : [ ];

            /*
             * Remove current post url from the list as a duplicate
             */
            $urls[] = get_permalink( $post_id );

            foreach ( $result as $key => $item ) {
                /*
                 * Mark duplicates
                 */
                if ( in_array( $item[ 'url' ], $urls ) ) {
                    $printableSearchPhrases        = [ ];
                    $result[ $key ][ 'duplicate' ] = true;
                    if ( !empty( $competitorSearchPhrases[ $item[ 'url' ] ] ) ) {
                        foreach ( $competitorSearchPhrases[ $item[ 'url' ] ] as $searchPhraseId ) {
                            $printableSearchPhrases[] = $searchPhrases[ $searchPhraseId ];
                        }
                    }
                    $result[ $key ][ 'is_main_competitor' ] = !empty( $topCompetitors[ $item[ 'url' ] ] );
                    $result[ $key ][ 'searchphrases' ]      = implode( "\r\n", $printableSearchPhrases );
                } else {
                    $result[ $key ][ 'duplicate' ]          = false;
                    $result[ $key ][ 'is_main_competitor' ] = false;
                    $result[ $key ][ 'searchphrases' ]      = [ ];
                }

                $result[ $key ][ 'current' ] = $item[ 'url' ] == $permalink;
            }
        } else {
            $result = [ ];
        }
        wp_send_json_success( [
            'result'       => (array) $result,
            'search_query' => $searchQuery
        ] );
    }

    public function get_words_count() {
        $post_id = filter_input( INPUT_POST, 'post_id' );
        if ( !empty( $post_id ) ) {
            $post = get_post( $post_id );
            if ( empty( $post ) ) {
                wp_send_json( ['result' => FALSE, 'message' => 'Invalid post' ] );
            }
            $result = misc\Misc::wpRemoteGetWordCount( get_permalink( $post_id ) );
        } else {
            $result = [ ];
        }
        wp_send_json_success( [
            'result' => (array) $result
        ] );
    }

    public function filter_metrics_data( $item ) {
        $result      = TRUE;
        $filter      = filter_input( INPUT_POST, 'filter' );
        $trend       = maybe_unserialize( $item[ 'trend' ] );
        $trendMargin = models\Options::getTrendsMargin() / 100;
        switch ( $filter ) {
            case 'title-update':
                /*
                 * If last title change more than 30days ago
                 */
                $treshold = strtotime( '-30days' );
                if ( empty( $item[ 'last_title_change_date' ] ) || strtotime( $item[ 'last_title_change_date' ] ) < $treshold ) {
                    $result = FALSE;
                }
                break;
            case 'keyword-update':
                /*
                 * If last title change more than 30days ago
                 */
                $treshold = strtotime( '-30days' );
                if ( empty( $item[ 'date' ] ) || strtotime( $item[ 'date' ] ) < $treshold ) {
                    $result = FALSE;
                }
                break;
            case 'impressions-asc':
                $result = $trend[ 'impressions' ] && $trend[ 'impressions' ] > $trendMargin;
                break;
            case 'impressions-neutral':
                $result = !$trend[ 'impressions' ] || abs( $trend[ 'impressions' ] ) < $trendMargin;
                break;
            case 'impressions-desc':
                $result = $trend[ 'impressions' ] && $trend[ 'impressions' ] < -$trendMargin;
                break;
            case 'click-asc':
                $result = $trend[ 'clicks' ] && $trend[ 'clicks' ] > $trendMargin;
                break;
            case 'click-neutral':
                $result = !$trend[ 'clicks' ] || abs( $trend[ 'clicks' ] ) < $trendMargin;
                break;
            case 'click-desc':
                $result = $trend[ 'clicks' ] && $trend[ 'clicks' ] < -$trendMargin;
                break;
            case 'ctr-asc':
                $result = $trend[ 'ctr' ] && $trend[ 'ctr' ] > $trendMargin;
                break;
            case 'ctr-neutral':
                $result = !$trend[ 'ctr' ] || abs( $trend[ 'ctr' ] ) < $trendMargin;
                break;
            case 'ctr-desc':
                $result = $trend[ 'ctr' ] && $trend[ 'ctr' ] < -$trendMargin;
                break;
            case 'bounce-rate-asc':
                $result = $trend[ 'bounce_rate' ] && $trend[ 'bounce_rate' ] > $trendMargin;
                break;
            case 'bounce-rate-neutral':
                $result = !$trend[ 'bounce_rate' ] || abs( $trend[ 'bounce_rate' ] ) < $trendMargin;
                break;
            case 'bounce-rate-desc':
                $result = $trend[ 'bounce_rate' ] && $trend[ 'bounce_rate' ] < -$trendMargin;
                break;
            case 'conversion-asc':
                $result = $trend[ 'conversion' ] && $trend[ 'conversion' ] > $trendMargin;
                break;
            case 'conversion-neutral':
                $result = !$trend[ 'conversion' ] || abs( $trend[ 'conversion' ] ) < $trendMargin;
                break;
            case 'conversion-desc':
                $result = $trend[ 'conversion' ] && $trend[ 'conversion' ] < -$trendMargin;
                break;
            case 'conversion-value-asc':
                $result = $trend[ 'conversion_value' ] && $trend[ 'conversion_value' ] > $trendMargin;
                break;
            case 'conversion-value-neutral':
                $result = !$trend[ 'conversion-value' ] || abs( $trend[ 'conversion-value' ] ) < $trendMargin;
                break;
            case 'conversion-value-desc':
                $result = $trend[ 'conversion_value' ] && $trend[ 'conversion_value' ] < -$trendMargin;
                break;
            case 'all':
            default:
                $result = TRUE;
                break;
        }
        return $result;
    }

    public function get_metrics_data() {
        $data_type        = isset( $_POST[ 'data_type' ] ) ? $_POST[ 'data_type' ] : 0;
        $offset           = isset( $_POST[ 'offset' ] ) ? $_POST[ 'offset' ] : 0;
        $ids              = models\Options::getDashboardMetricsIds();
        $limit            = isset( $_POST[ 'limit' ] ) ? $_POST[ 'limit' ] : PHP_INT_MAX;
        $pageMetricsModel = new models\PageMetrics();
        $data             = $pageMetricsModel->getLastEntries( $ids, 0, $offset, $limit );
        $data             = array_map( function($item) {
            $item[ 'edit_link' ] = get_admin_url() . 'post.php?post=' . $item[ 'post_id' ] . '&action=edit';
            return $item;
        }, $data );


        foreach ( $data as $key => $item ) {
            if ( !$item[ 'is_manual' ] ) {
                unset( $data[ $key ] );
                continue;
            }
            $data[ $key ][ 'trend' ] = maybe_unserialize( $data[ $key ][ 'trend' ] );
            $model                   = models\GenericPost::getInstance( $item[ 'post_id' ] );
            if ( $model ) {
                $additionalData     = $model->getPageMetricsDashboardUrlData();
                $lastTitleChange    = $pageMetricsModel->getTitleChangeEntriesByPostId( $item[ 'post_id' ], 1 );
                $dashboardUrlData   = $model->getSeoKeywordsDashboardUrlData();
                $notificationEmails = explode( ',', $dashboardUrlData[ 'notification_email' ] );

                $data[ $key ][ 'pa' ]                     = !empty( $additionalData[ 'pa' ] ) ? strip_tags( $additionalData[ 'pa' ] ) : '';
                $data[ $key ][ 'post_title' ]             = !empty( $data[ $key ][ 'post_title' ] ) ? strip_tags( $data[ $key ][ 'post_title' ] ) : '';
                $data[ $key ][ 'conversion' ]             = !empty( $data[ $key ][ 'conversion' ] ) ? intval( $data[ $key ][ 'conversion' ] ) : '';
                $data[ $key ][ 'date' ]                   = !empty( $data[ $key ][ 'date' ] ) ? $data[ $key ][ 'date' ] : '';
                $data[ $key ][ 'last_title_change_date' ] = !empty( $lastTitleChange ) ? $lastTitleChange[ 0 ][ 'date' ] : '';
                $data[ $key ][ 'custom_data' ]            = $model->getMetricTableAdditionalData();
                if ( !$data[ $key ][ 'custom_data' ][ 'row_color' ] ) {
                    $data[ $key ][ 'custom_data' ][ 'row_color' ] = '';
                }
                $data[ $key ][ 'notification_email' ]      = !empty( $dashboardUrlData[ 'notification_email' ] ) ? reset( $notificationEmails ) : '';
                $data[ $key ][ 'notification_last_reset' ] = !empty( $dashboardUrlData[ 'notification_last_reset' ] ) ? $dashboardUrlData[ 'notification_last_reset' ] : null;

                /*
                 * Remove the unneeded data
                 */
                if ( !$this->filter_metrics_data( $data[ $key ] ) ) {
                    unset( $data[ $key ] );
                }
                if ( !empty( $data[ $key ][ 'last_title_change_date' ] ) ) {
                    $data[ $key ][ 'last_title_change_date' ] = 1000 * (int) strtotime( $data[ $key ][ 'last_title_change_date' ] );
                }
                if ( !empty( $data[ $key ][ 'date' ] ) ) {
                    $data[ $key ][ 'date' ] = 1000 * (int) strtotime( $data[ $key ][ 'date' ] );
                }
            } else {
                unset( $data[ $key ] );
                continue;
            }
        }
        return $data;
    }

    public function get_metrics() {
        $data  = $this->get_metrics_data();
        $total = count( $data );

        wp_send_json_success( [
            'items' => $data,
            'total' => (int) $total
        ] );
    }

    public function admin_menu() {
//        add_submenu_page(
//        App::SLUG, 'Dashboard', 'Dashboard', 'manage_options', App::SLUG, array( &$this, 'render' )
//        );
        //var_Dump(App::SLUG); exit;
    }

    public function save_post( $post_id ) {

    }

    public function getRender( $arr = array() ) {
        return parent::getRender( array(
            'title'   => sprintf( '%s - %s', App::PLUGIN_NAME_EXTENDED, 'Metrics' ),
            'content' => helpers\ViewHelper::load( 'views/backend/pages/metrics.php' )
        ) );
    }

}
