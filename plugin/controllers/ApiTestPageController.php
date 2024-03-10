<?php

namespace com\cminds\seokeywords\plugin\controllers;

use com\cminds\seokeywords\App;
use com\cminds\seokeywords\plugin\helpers;

class ApiTestPageController extends PageControllerAbstract {

    public function __construct() {
        add_action('admin_menu', function() {
            add_submenu_page(App::SLUG, 'API Test Page', 'API Test Page', 'manage_options', sprintf('%s-%s', App::SLUG, 'api'), array($this, 'render'));
        }, 20);
    }

    public function getRender($arr = array()) {
        return parent::getRender(array(
                    'title' => '',
                    'content' => helpers\ViewHelper::load('views/backend/apitest.php')
        ));
    }

}
