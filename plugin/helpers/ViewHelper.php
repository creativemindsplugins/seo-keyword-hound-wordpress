<?php

namespace com\cminds\seokeywords\plugin\helpers;

use com\cminds\seokeywords\App;

class ViewHelper {

    public static function load($filename, $data = array()) {
        $_a12ec803b2ce49e4a541068d495ab570 = $data;
        unset($data);
        ob_start();
        extract($_a12ec803b2ce49e4a541068d495ab570, EXTR_SKIP);
        include plugin_dir_path(App::PLUGIN_FILE) . $filename;
        return ob_get_clean();
    }

}
