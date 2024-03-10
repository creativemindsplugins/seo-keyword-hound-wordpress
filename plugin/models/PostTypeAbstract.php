<?php

namespace com\cminds\seokeywords\plugin\models;

abstract class PostTypeAbstract {

    const POST_TYPE = NULL;

    static protected $options = [];
    static protected $instances;
    protected $post;

    function __construct($post) {
        if (is_object($post)) {
            $this->post = $post;
        } else {
            $post = static::getInstance($post);
            if (!empty($post)) {
                $this->post = $post->post;
            }
        }
    }

    static function registerPostType() {
        static::$options['labels'] = static::getPostTypeLabels();
        $options = static::$options;
        register_post_type(static::POST_TYPE, $options);
    }

    static protected function getPostTypeLabels() {
        return array();
    }

    static function getInstance($post) {
        if (is_scalar($post)) {
            if (!empty(static::$instances[$post]))
                return static::$instances[$post];
            else if (is_numeric($post))
                $post = get_post($post);
            else
                $post = get_post(array('post_name' => $post));
        }
        if (!empty($post) AND is_object($post) AND $post instanceof \WP_Post) {
            if (empty(static::$instances[$post->ID])) {
                static::$instances[$post->ID] = new static($post);
            }
            return static::$instances[$post->ID];
        }
    }

    function getId() {
        return $this->post->ID;
    }

    function getPostStatus() {
        if (isset($this->post->post_status)) {
            return $this->post->post_status;
        }
    }

    function setPostStatus($status) {
        $this->post->post_status = $status;
        return $this;
    }

    function getPostMeta($name, $single = true) {
        return get_post_meta($this->getId(), $name, $single);
    }

    function setPostMeta($name, $value) {
        update_post_meta($this->getId(), $name, $value);
        return $this;
    }

    function getTitle() {
        return $this->post->post_title;
    }

    function setTitle($title) {
        $this->post->post_title = $title;
        return $this;
    }

    function getSlug() {
        return $this->post->post_name;
    }

    function setSlug($slug) {
        $this->post->post_name = $slug;
        return $this;
    }

    function getContent() {
        return $this->post->post_content;
    }

    function setExcerpt($excerpt) {
        $this->post->post_excerpt = $excerpt;
        return $this;
    }

    function setContent($desc) {
        $this->post->post_content = $desc;
        return $this;
    }

    function save() {
        return wp_update_post((array) $this->post);
    }

    function getPermalink() {
        return get_permalink($this->getId());
    }

    function getPost() {
        return $this->post;
    }

}
