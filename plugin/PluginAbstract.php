<?php

namespace com\cminds\seokeywords\plugin;

use com\cminds\seokeywords\plugin\models;

abstract class PluginAbstract {

    const VERSION              = NULL;
    const PREFIX               = NULL;
    const SLUG                 = NULL;
    const PLUGIN_NAME          = NULL;
    const PLUGIN_FILE          = NULL;
    const PLUGIN_NAME_EXTENDED = NULL;

    protected static $_addons = [ ];

    public function __construct() {

        register_deactivation_hook( static::PLUGIN_FILE, array( $this, 'deactivation' ) );

        // post types
        add_action( 'init', function () {

        } );

        add_action( 'init', function() {
            if ( get_option( models\KeywordStatistics::DB_VERSION_KEY ) < models\KeywordStatistics::DB_VERSION ) {
                models\KeywordStatistics::dbInit();
                update_option( models\KeywordStatistics::DB_VERSION_KEY, models\KeywordStatistics::DB_VERSION );
            }
            if ( get_option( models\PageMetrics::DB_VERSION_KEY ) < models\PageMetrics::DB_VERSION ) {
                models\PageMetrics::dbInit();
                update_option( models\PageMetrics::DB_VERSION_KEY, models\PageMetrics::DB_VERSION );
            }
        } );

        misc\Assets::init();

        new controllers\AjaxEndpointController();
//        $dashboardPageController = new controllers\DashboardPageController();
        $dashboardPageController = new controllers\OptionsPageController();
        new controllers\GenericPostController();
        new controllers\CMRDApiController();
        new controllers\RedirectController();

        add_action( 'admin_menu', function () use ($dashboardPageController) {
            add_menu_page( static::SLUG, static::PLUGIN_NAME_EXTENDED, 'manage_options', static::SLUG, array( $dashboardPageController, 'render' ), plugin_dir_url( static::PLUGIN_FILE ) . 'assets/images/backend_icon.png' );
        } );
    }

    public static function activation() {

    }

    public function deactivation() {

    }

    public static function isPro() {
        return file_exists( plugin_dir_path( static::PLUGIN_FILE ) . 'bundle/licensing/cminds-pro.php' );
    }

    public static function isLicenseOk() {
        return $GLOBALS[ sprintf( '%s_isLicenseOk', static::SLUG ) ] || filter_input( INPUT_COOKIE, 'FOR_DEVELOPMENT_USE_ONLY_CMSK_PRO' );
    }

    public static function isAddonSupported( $prefix, $version = 0 ) {
        return FALSE;
    }

    public static function hasAddon( $prefix, $version = 0 ) {
        if ( isset( static::$_addons[ $prefix ] ) ) {
            // TODO: support check version
            return TRUE;
        }
        return FALSE;
    }

    public static function registerAddon( $prefix, $version = 0 ) {
        if ( static::isAddonSupported( $prefix ) ) {
            static::$_addons[ $prefix ] = $version;
            return TRUE;
        }
        return FALSE;
    }

}
