<?php

namespace com\cminds\seokeywords\plugin\misc;

use com\cminds\seokeywords\App;

class Assets {

    const JS_PAGEMETRICS         = 'cmsk-js-page-metrics';
    const JS_EVENTS              = 'cmsk-js-events';
    const JS_KEYWORDSMETABOX     = 'cmsk-keywords-metabox';
    const JS_COMPETITORSMETABOX  = 'cmsk-competitors-metabox';
    const JS_PAGEMETRICSMETABOX  = 'cmsk-pagemerics-metabox';
    const JS_CHARTS              = 'cmsk-charts';
    const JS_TOOLTIPS            = 'cmsk-tooltips';
    const JS_VUEJS               = 'vue-js';
    const JS_VALIDURL            = 'valid-url';
    const JS_SWEETALERT2         = 'sweetalert2';
    const JS_JQUERYMARKJS        = 'jquery-mark-js';
    const JS_CRYPTOJS            = 'crypto-js';
    const JS_CHARTJS             = 'chart-js';
    const JS_MOMENTJS            = 'moment-js';
    const JS_DATATABLES          = 'cmsk-datatables-js';
    const CSS_SWEETALERT2        = 'sweetalert2';
    const CSS_JQUERYUI           = 'jquery-ui';
    const CSS_DATATABLES         = 'cmsk-datatables';
    const CSS_METABOX            = 'cmsk-metabox';
    const CSS_KEYWORDSMETABOX    = 'cmsk-keywords-metabox';
    const CSS_COMPETITORSMETABOX = 'cmsk-competitors-metabox';
    const CSS_PAGEMETRICSMETABOX = 'cmsk-pagemetrics-metabox';
    const CSS_EVENTSMETABOX      = 'cmsk-events-metabox';

    public function __construct() {
        static::init();
    }

    public static function init() {
        add_action( 'init', function() {
            /*
             * CSS section
             */
            $wp_scripts = wp_scripts();
            wp_register_style( static::CSS_SWEETALERT2, plugin_dir_url( App::PLUGIN_FILE ) . 'assets/lib/sweetalert2/dist/sweetalert2.min.css', [ ], App::VERSION );
            wp_register_style( static::CSS_JQUERYUI, 'https://ajax.googleapis.com/ajax/libs/jqueryui/' . $wp_scripts->registered[ 'jquery-ui-core' ]->ver . '/themes/smoothness/jquery-ui.css' );
            wp_register_style( static::CSS_METABOX, plugin_dir_url( App::PLUGIN_FILE ) . 'assets/css/metabox.css', [static::CSS_JQUERYUI ], App::VERSION );
            wp_register_style( static::CSS_DATATABLES, plugin_dir_url( App::PLUGIN_FILE ) . 'assets/lib/datatables/datatables.min.css', [ ], App::VERSION );
            wp_register_style( static::CSS_KEYWORDSMETABOX, plugin_dir_url( App::PLUGIN_FILE ) . 'assets/css/keywords-metabox.css', [static::CSS_JQUERYUI, static::CSS_METABOX ], App::VERSION );

            /*
             * JS section
             */
            wp_register_script( static::JS_VUEJS, plugin_dir_url( App::PLUGIN_FILE ) . 'assets/lib/vue/dist/vue.min.js', [ ], App::VERSION );
            wp_register_script( static::JS_VALIDURL, plugin_dir_url( App::PLUGIN_FILE ) . 'assets/lib/valid-url/index.js', [ ], App::VERSION );
            wp_register_script( static::JS_SWEETALERT2, plugin_dir_url( App::PLUGIN_FILE ) . 'assets/lib/sweetalert2/dist/sweetalert2.min.js', [ ], App::VERSION );
            wp_register_script( static::JS_JQUERYMARKJS, plugin_dir_url( App::PLUGIN_FILE ) . 'assets/lib/mark.js/dist/jquery.mark.min.js', ['jquery' ], App::VERSION );
            wp_register_script( static::JS_CRYPTOJS, plugin_dir_url( App::PLUGIN_FILE ) . 'assets/lib/crypto-js/crypto-js.js', [ ], App::VERSION );
            wp_register_script( static::JS_TOOLTIPS, plugin_dir_url( App::PLUGIN_FILE ) . 'assets/js/tooltips.js', [ ], App::VERSION );
            wp_register_script( static::JS_MOMENTJS, plugin_dir_url( App::PLUGIN_FILE ) . 'assets/lib/moment/moment.js', [ ], App::VERSION );
            wp_register_script( static::JS_CHARTJS, plugin_dir_url( App::PLUGIN_FILE ) . 'assets/lib/chart.js/dist/Chart.js', [static::JS_MOMENTJS ], App::VERSION );
            wp_register_script( static::JS_CHARTJS . '_empty_overlay', plugin_dir_url( App::PLUGIN_FILE ) . 'assets/lib/chart.js/dist/chartjs-plugin-empty-overlay.min.js', [static::JS_CHARTJS ], App::VERSION );
            wp_register_script( static::JS_CHARTS, plugin_dir_url( App::PLUGIN_FILE ) . 'assets/js/charts.js', ['jquery', static::JS_CHARTJS, static::JS_CHARTJS . '_empty_overlay', static::JS_SWEETALERT2 ], App::VERSION );
            wp_register_script( static::JS_DATATABLES, plugin_dir_url( App::PLUGIN_FILE ) . 'assets/lib/datatables/datatables.js', ['jquery' ], App::VERSION );
            wp_register_script( static::JS_KEYWORDSMETABOX, plugin_dir_url( App::PLUGIN_FILE ) . 'assets/js/keywords-metabox.js', [
                'jquery',
                static::JS_CHARTS,
                static::JS_VUEJS,
                static::JS_SWEETALERT2,
                static::JS_CRYPTOJS,
                static::JS_JQUERYMARKJS,
                static::JS_MOMENTJS,
                'jquery-ui-tooltip',
                'jquery-ui-dialog',
                'jquery-ui-datepicker',
            ], App::VERSION );
            wp_register_script( static::JS_PAGEMETRICS, plugin_dir_url( App::PLUGIN_FILE ) . 'assets/js/pagemetrics.js', [
                'jquery',
                static::JS_CHARTS,
                static::JS_VUEJS,
                static::JS_SWEETALERT2,
                static::JS_CRYPTOJS,
                static::JS_JQUERYMARKJS,
                'jquery-ui-tooltip',
                'jquery-ui-datepicker',
            ], App::VERSION );
            wp_register_script( static::JS_EVENTS, plugin_dir_url( App::PLUGIN_FILE ) . 'assets/js/events.js', [
                'jquery',
                static::JS_CHARTS,
                static::JS_VUEJS,
                static::JS_SWEETALERT2,
                static::JS_CRYPTOJS,
                static::JS_JQUERYMARKJS,
                'jquery-ui-tooltip',
                'jquery-ui-datepicker',
            ], App::VERSION );
        } );
    }

}
