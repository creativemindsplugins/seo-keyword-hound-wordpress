<?php

namespace com\cminds\seokeywords\plugin\controllers;

use com\cminds\seokeywords\App;
use com\cminds\seokeywords\plugin\helpers;
use com\cminds\seokeywords\plugin\models;

class OptionsPageController extends PageControllerAbstract {

    const ACTION_SAVE_OPTIONS = 'cmks_save_options';

    public function __construct() {
        add_action('admin_menu', function() {
            add_submenu_page(App::SLUG, 'Settings', 'Settings', 'manage_options', App::SLUG, array($this, 'render'));
        }, 10);

        add_action('admin_init', array($this, 'admin_init'));
    }

    public function getRender($arr = array()) {
        return parent::getRender(array(
                    'title' => sprintf('%s - %s', App::PLUGIN_NAME_EXTENDED, 'Options'),
                    'content' => helpers\ViewHelper::load('views/backend/pages/options/options.php')
        ));
    }

    public function admin_init() {
        if (wp_verify_nonce(filter_input(INPUT_POST, '_wpnonce'), static::ACTION_SAVE_OPTIONS)) {
            foreach ($_POST as $k => $v) {
                $v = is_array($v) ? array_map('stripslashes', $v) : stripslashes($v);
                models\Options::updateOption($k, $v);
            }
        }
    }

}
