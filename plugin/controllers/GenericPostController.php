<?php

namespace com\cminds\seokeywords\plugin\controllers;

use com\cminds\seokeywords\App;
use com\cminds\seokeywords\plugin\models;
use com\cminds\seokeywords\plugin\metaboxes;

class GenericPostController extends ControllerAbstract {

    private $title;
    private $yoast_wpseo_title;
    private $yoast_wpseo_metadesc;
    private $allinone_title;
    private $allinone_description;
    private $isNewTitle        = false;
    private $oldSeoTitle       = '';
    private $newSeoTitle       = '';
    private $oldSeoDescription = '';
    private $newSeoDescription = '';
    private $id;

    public function __construct() {

        if ( App::isLicenseOk() ) {
            new metaboxes\KeywordsMetabox();
        }

//        add_filter( 'content_save_pre', function($s) {
//            return preg_replace( '/\<mark.*?data-markjs.*?\>(.*?)\<\/mark\>/', '${1}', $s );
//        } );

        add_action( 'pre_post_update', function($id) {
            $this->title                = get_the_title( $id );
            $this->yoast_wpseo_title    = get_post_meta( $id, '_yoast_wpseo_title', TRUE );
            $this->yoast_wpseo_metadesc = get_post_meta( $id, '_yoast_wpseo_metadesc', TRUE );
            $this->allinone_title       = get_post_meta( $id, '_aioseop_title', TRUE );
            $this->allinone_description = get_post_meta( $id, '_aioseop_description', TRUE );
        } );

        add_action( 'post_updated', function($id) {
            if ( !App::isLicenseOk() ) {
                return;
            }
            $this->isNewTitle  = $this->title != get_the_title( $id );
            $this->newSeoTitle = $this->yoast_wpseo_title != filter_input( INPUT_POST, 'yoast_wpseo_title' ) ? filter_input( INPUT_POST, 'yoast_wpseo_title' ) : '';
            if ( empty( $this->newSeoTitle ) ) {
                $this->oldSeoTitle = $this->allinone_title;
                $this->newSeoTitle = $this->allinone_title != filter_input( INPUT_POST, '_aioseop_title' ) ? filter_input( INPUT_POST, '_aioseop_title' ) : '';
            } else {
                $this->oldSeoTitle = $this->yoast_wpseo_title;
            }

            $this->newSeoDescription = $this->yoast_wpseo_metadesc != filter_input( INPUT_POST, 'yoast_wpseo_metadesc' ) ? filter_input( INPUT_POST, 'yoast_wpseo_metadesc' ) : '';
            if ( empty( $this->newSeoDescription ) ) {
                $this->oldSeoDescription = $this->allinone_description;
                $this->newSeoDescription = $this->allinone_description != filter_input( INPUT_POST, '_aioseop_description' ) ? filter_input( INPUT_POST, '_aioseop_description' ) : '';
            } else {
                $this->oldSeoDescription = $this->yoast_wpseo_metadesc;
            }

            $this->id = $id;
        } );

        add_action( 'shutdown', function() {
            if ( defined( 'DOING_AJAX' ) ) {
                return;
            }
            if ( $this->isNewTitle ) {
                $PageMetrics = new models\PageMetrics();
                $PageMetrics->addEntry( $this->id, [
                    'date'             => date( 'Y-m-d' ),
                    'period'           => models\Options::getDataCollectPeriod(),
                    'is_title_changed' => 1,
                    'custom_text'      => 'Title Change from "' . $this->title . '" to "' . filter_input( INPUT_POST, 'post_title' ) . '"',
                    'type'             => models\PageMetrics::TITLE_CHANGE
                ] );
            }
            if ( !empty( $this->newSeoTitle ) ) {
                $PageMetrics = new models\PageMetrics();
                $PageMetrics->addEntry( $this->id, [
                    'date'             => date( 'Y-m-d' ),
                    'period'           => models\Options::getDataCollectPeriod(),
                    'is_title_changed' => 1,
                    'custom_text'      => 'SEO Title Change from "' . $this->oldSeoTitle . '" to "' . $this->newSeoTitle . '"',
                    'type'             => models\PageMetrics::SEO_TITLE_CHANGE
                ] );
            }
            if ( !empty( $this->newSeoDescription ) ) {
                $PageMetrics = new models\PageMetrics();
                $PageMetrics->addCustomEntry( $this->id, [
                    'date'             => date( 'Y-m-d' ),
                    'period'           => models\Options::getDataCollectPeriod(),
                    'is_title_changed' => 0,
                    'custom_text'      => 'SEO Description Change from "' . $this->oldSeoDescription . '" to "' . $this->newSeoDescription . '"',
                    'type'             => models\PageMetrics::SEO_DESCRIPTION_CHANGE
                ] );
            }
        } );
    }

}
