<?php

namespace com\cminds\seokeywords\plugin\controllers;

use com\cminds\seokeywords\plugin\ga;

class RedirectController extends ControllerAbstract {

    public function __construct() {

        add_action('admin_init', function() {
            if (filter_input(INPUT_GET, 'cmsk-action') === 'redirect-LandingPagesSearchDatesAndPostId') {
                $date00 = filter_input(INPUT_GET, 'date00', FILTER_VALIDATE_INT);
                $date01 = filter_input(INPUT_GET, 'date01', FILTER_VALIDATE_INT);
                $post_id = filter_input(INPUT_GET, 'post_id', FILTER_VALIDATE_INT);
                $url = ga\GAUrlHelper::getLandingPagesSearchDatesAndPostId($date00, $date01, $post_id);
                wp_redirect($url);
                exit();
            }
            if (filter_input(INPUT_GET, 'cmsk-action') === 'redirect-SelectedLandingPageForPostIdSearchKeyword') {
                $post_id = filter_input(INPUT_GET, 'post_id', FILTER_VALIDATE_INT);
                $keyword = filter_input(INPUT_GET, 'keyword');
                $url = ga\GAUrlHelper::getSelectedLandingPageForPostIdSearchKeyword($post_id, $keyword);
                wp_redirect($url);
                exit();
            }
            if (filter_input(INPUT_GET, 'cmsk-action') === 'redirect-SelectedLandingPageForPostIdSearchDatesAndKeyword') {
                $post_id = filter_input(INPUT_GET, 'post_id', FILTER_VALIDATE_INT);
                $keyword = filter_input(INPUT_GET, 'keyword');
                $date00 = filter_input(INPUT_GET, 'date00', FILTER_VALIDATE_INT);
                $date01 = filter_input(INPUT_GET, 'date01', FILTER_VALIDATE_INT);
                $url = ga\GAUrlHelper::getSelectedLandingPageForPostIdSearchDatesAndKeyword($post_id, $date00, $date01, $keyword);
                wp_redirect($url);
                exit();
            }
        }, 9);
    }

}
