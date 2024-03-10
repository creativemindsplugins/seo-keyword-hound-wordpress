<?php

namespace com\cminds\seokeywords\plugin\seo\contents\competitors;

use com\cminds\seokeywords\plugin\seo\contents;
use com\cminds\seokeywords\plugin\models;

abstract class ContentAbstract extends contents\ContentAbstract implements contents\ContentInterface {

    protected $html;
    protected $url;

    public function __construct($response) {
        $this->html = $response['body'];
        $this->url = $response['url'];
    }

    public function getContent() {
        
    }

}
