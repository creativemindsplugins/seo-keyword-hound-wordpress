<?php

namespace com\cminds\seokeywords\plugin\seo\dto;

class KeywordStatsDTO {

    public $title;
    public $headers;
    public $content;
    public $url;
    public $first100;
    public $marked;
    public $images;

    public function __construct() {
        $this->title = 'ⁿ/ₐ';
        $this->headers = 'ⁿ/ₐ';
        $this->content = 'ⁿ/ₐ';
        $this->url = 'ⁿ/ₐ';
        $this->first100 = 'ⁿ/ₐ';
        $this->marked = 'ⁿ/ₐ';
        $this->images = 'ⁿ/ₐ';
    }

}
