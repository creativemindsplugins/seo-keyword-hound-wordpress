<?php

namespace com\cminds\seokeywords\plugin\controllers;

use com\cminds\seokeywords\App;
use com\cminds\seokeywords\plugin;

abstract class PageControllerAbstract extends ControllerAbstract {

    public function render($arr) {
        echo $this->getRender($arr);
    }

    public function getRender($arr) {
        return plugin\helpers\ViewHelper::load('views/backend/pages/template.php', array_merge(array('nav' => $this->nav()), $arr));
    }

    private static function nav() {
        global $self, $parent_file, $submenu_file, $plugin_page, $typenow, $submenu;
        $submenus = array();

        $menuItem = App::SLUG;

        if (isset($submenu[$menuItem])) {
            $thisMenu = $submenu[$menuItem];

            foreach ($thisMenu as $sub_item) {
                $slug = $sub_item[2];

                // Handle current for post_type=post|page|foo pages, which won't match $self.
                $self_type = !empty($typenow) ? $self . '?post_type=' . $typenow : 'nothing';

                $isCurrent = FALSE;
                $subpageUrl = get_admin_url('', 'admin.php?page=' . $slug);

                if ((!isset($plugin_page) && $self == $slug) || (isset($plugin_page) && $plugin_page == $slug && ($menuItem == $self_type || $menuItem == $self || file_exists($menuItem) === false))) {
                    $isCurrent = TRUE;
                }

                $url = (strpos($slug, '.php') !== false || strpos($slug, 'http') !== false) ? $slug : $subpageUrl;
                $isExternalPage = strpos($slug, 'http') !== FALSE;
                $submenus[] = array(
                    'link' => $url,
                    'title' => $sub_item[0],
                    'current' => $isCurrent,
                    'target' => $isExternalPage ? '_blank' : ''
                );
            }
        }
        return plugin\helpers\ViewHelper::load('views/backend/pages/nav.php', array('submenus' => $submenus));
    }

}
