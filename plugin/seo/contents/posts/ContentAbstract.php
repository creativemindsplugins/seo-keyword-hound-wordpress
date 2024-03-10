<?php

namespace com\cminds\seokeywords\plugin\seo\contents\posts;

use com\cminds\seokeywords\plugin\seo\contents;
use com\cminds\seokeywords\plugin\models;
use com\cminds\seokeywords\plugin\misc;

abstract class ContentAbstract extends contents\ContentAbstract implements contents\ContentInterface {

    protected $post;
    protected static $cache = [];

    public function __construct(models\GenericPost $post) {
        $this->post = $post;
        if (!isset(static::$cache[$post->getId()])) {
            static::$cache[$post->getId()] = [];
        }
    }

    public function getContent() {
        
    }

    protected function getContextPermalink() {
        if (!isset(static::$cache[$this->post->getId()]['permalink'])) {
            static::$cache[$this->post->getId()]['permalink'] = get_permalink($this->post->getId());
        }
        return static::$cache[$this->post->getId()]['permalink'];
    }

    protected function getContextHtmlContent() {
        if (!isset(static::$cache[$this->post->getId()]['html_content'])) {
            $content = apply_filters('the_content', $this->post->getContent());
            static::$cache[$this->post->getId()]['html_content'] = $content;
        }
        return static::$cache[$this->post->getId()]['html_content'];
    }

    protected function getContextTextContent() {
        if (!isset(static::$cache[$this->post->getId()]['text_content'])) {
            $content = $this->getContextHtmlContent();
            $content = wp_strip_all_tags($content);
            static::$cache[$this->post->getId()]['text_content'] = $content;
        }
        return static::$cache[$this->post->getId()]['text_content'];
    }

    protected function getContextTitle() {
        if (!isset(static::$cache[$this->post->getId()]['title'])) {
            static::$cache[$this->post->getId()]['title'] = misc\Misc::wpRemoteGetTitle($this->getContextPermalink());
        }
        return static::$cache[$this->post->getId()]['title'];
    }

}
