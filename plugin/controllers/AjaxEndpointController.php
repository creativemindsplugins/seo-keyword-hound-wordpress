<?php

namespace com\cminds\seokeywords\plugin\controllers;

use com\cminds\seokeywords\App;
use com\cminds\seokeywords\plugin\models;
use com\cminds\seokeywords\plugin\seo;

class AjaxEndpointController extends ControllerAbstract {

    public function __construct() {

        // SEO Keywords trends data
        add_action( sprintf( 'wp_ajax_%s', 'B691D87F05F4FF973018D6B11084593BC03675C6' ), function() {
            $post_id = filter_input( INPUT_POST, 'post_id' );
            $model   = models\GenericPost::getInstance( $post_id );
            wp_send_json( ['result' => TRUE, 'data' => $model->getSeoKeywordsTrendsData() ] );
        } );

        // SEO Keywords trends chart data
        add_action( sprintf( 'wp_ajax_%s', 'A1384A9352131DB095B281250B060272166D1AD0' ), function() {
            $post_id = filter_input( INPUT_POST, 'post_id' );
            $keyword = filter_input( INPUT_POST, 'keyword' );
            $model   = new models\KeywordStatistics( $keyword );
            $limit   = max( 3, filter_input( INPUT_POST, 'limit' ) );
            $date1   = filter_input( INPUT_POST, 'date1' );
            $date2   = filter_input( INPUT_POST, 'date2' );
            if ( !empty( $date1 ) && !empty( $date2 ) ) {
                $data = $model->getEntriesByPostIdAndDate( $post_id, $date1, $date2 );
            } else {
                $data = $model->getEntriesByPostId( $post_id, $limit );
            }
            wp_send_json( ['result' => TRUE, 'data' => $data ] );
        } );

        // Page Metrics trends chart data
        add_action( sprintf( 'wp_ajax_%s', 'CE5EB3B31DE890081B77AC82BE145F0CC5CCE14C' ), function() {
            $post_id = filter_input( INPUT_POST, 'post_id' );
            $model   = new models\PageMetrics();
            $limit   = filter_input( INPUT_POST, 'limit' );
            if ( !empty( $limit ) ) {
                $limit = max( 3, $limit );
            }
            $date1 = filter_input( INPUT_POST, 'date1' );
            $date2 = filter_input( INPUT_POST, 'date2' );
            if ( !empty( $date1 ) && !empty( $date2 ) ) {
                $data = $model->getEntriesByPostIdAndDate( $post_id, $date1, $date2 );
            } else {
                $data = $model->getEntriesByPostId( $post_id, PHP_INT_MAX );
            }
            foreach ( $data as $key => $value ) {
                if ( !$value[ 'is_manual' ] ) {
                    unset( $data[ $key ] );
                }
            }
            if ( !empty( $limit ) ) {
                $data = array_slice( $data, 0, $limit, true );
            }
            wp_send_json( ['result' => TRUE, 'data' => $data ] );
        } );

        // Competitor statistics
        add_action( sprintf( 'wp_ajax_%s', 'AF4CC6BE73C35630829267D851D2EADE41319BBB' ), function() {
            // TODO: nonce
            $post_id = filter_input( INPUT_POST, 'post_id' );
            $url     = filter_input( INPUT_POST, 'url' );
            $generic = models\GenericPost::getInstance( $post_id );
            $service = new seo\services\CompetitorStatisticsService();
            $data    = $service->setContext( $generic )
            ->setUrl( $url )
            ->setUAType( filter_input( INPUT_POST, 'uatype' ) )
            ->clearCache( filter_input( INPUT_POST, 'refresh' ) )
            ->getStatistics();
            wp_send_json( array_merge( $data, ['token' => filter_input( INPUT_POST, 'token' ) ] ) );
        } );

        // Snapshot statistics
        add_action( sprintf( 'wp_ajax_%s', '0B2F395F9B69A5930B7FC041A20D63AA51B38C9F' ), function() {
            $post_id   = filter_input( INPUT_POST, 'post_id' );
            $generic   = models\GenericPost::getInstance( $post_id );
            $timestamp = filter_input( INPUT_POST, 'timestamp' );
            $snapshot  = $generic->getSnapshotData( $timestamp );

            $data[ 'items1' ] = $snapshot[ 'items1' ][ 'items' ];
            $data[ 'items2' ] = $snapshot[ 'items2' ][ 'items' ];

            $data[ 'status' ]    = 'OK';
            $data[ 'ts' ]        = time();
            $data[ 'ua' ]        = 'desktop';
            $data[ 'timestamp' ] = $timestamp;

            $result = array_merge( $data, ['token' => filter_input( INPUT_POST, 'token' ) ] );
            wp_send_json( $result );
        } );
    }

}
