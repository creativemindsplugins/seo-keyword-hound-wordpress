<?php
/*
Plugin Name: CM SEO Keyword Hound Free
Plugin URI: https://www.cminds.com/
Description: SEO Keyword Hound for WordPress Streamline keyword management and boost the SEO of your website with this one-of-a-kind WordPress SEO plugin!
Author: CreativeMindsSolutions
Author URI: https://profiles.wordpress.org/creativemindssolutions
Version: 1.0.6
*/

namespace com\cminds\seokeywords;

if (version_compare('5.4', PHP_VERSION, '>')) {
    die(sprintf('We are sorry, but you need to have at least PHP 5.4 (currently installed version: %s) to run this plugin (%s)'
                    . ' - please upgrade or contact your system administrator', PHP_VERSION, __NAMESPACE__));
}

if (class_exists('com\cminds\seokeywords\App')) {
    die(sprintf('Plugin (%s) is already activated', __NAMESPACE__));
}

require_once plugin_dir_path(__FILE__) . 'plugin/Psr4AutoloaderClass.php';
require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

$loader = new plugin\Psr4AutoloaderClass();
$loader->register();
$loader->addNamespace(__NAMESPACE__, untrailingslashit(plugin_dir_path(__FILE__)));

class App extends plugin\PluginAbstract {

    const VERSION = '1.0.6';
    const PREFIX = 'cmsk';
    const SLUG = 'cm-seo-keywords';
    const PLUGIN_NAME = 'CM SEO Keywords Aid';
    const PLUGIN_NAME_EXTENDED = 'CM SEO Keyword Hound';
    const PLUGIN_FILE = __FILE__;

}

require_once plugin_dir_path(__FILE__) . 'bundle/licensing/cminds-free.php';

new App();

register_activation_hook(__FILE__, array('com\cminds\seokeywords\App', 'activation'));

