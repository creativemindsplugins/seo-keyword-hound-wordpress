<?php

namespace com\cminds\seokeywords\plugin\metaboxes;

class MetaboxAbstract {

    const METABOX = '';
    const NONCE = '';

    public function __construct() {

        if (is_admin()) {
            add_action('load-post.php', array($this, 'init'));
            add_action('load-post-new.php', array($this, 'init'));

//            add_action('save_post', array($this, 'save'), 10, 2);
        }
    }

    public function init() {

    }

    public function save($post_id, $post) {

    }

}
