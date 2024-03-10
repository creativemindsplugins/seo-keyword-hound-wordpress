<?php

namespace com\cminds\seokeywords\plugin\controllers;

use com\cminds\seokeywords\plugin\models;

class CMRDApiController extends ControllerAbstract {

    public function __construct() {
        add_action('init', array( &$this, 'init' ));
        add_action( 'cmsk_check_cmrd_api', array( &$this, 'check_cmrd_api' ) );
    }

    public function init() {
		if ( ! wp_next_scheduled( 'cmsk_check_cmrd_api' ) ) {
			wp_schedule_event( time(), 'daily', 'cmsk_check_cmrd_api' );
		}
    }

    public function check_cmrd_api() {
    	$license = models\Options::getRemoteDensityApiKey();
    	if( !empty( $license ) ) {
		    models\RemoteDensityTool::check_license( $license );
	    }
    }
}
