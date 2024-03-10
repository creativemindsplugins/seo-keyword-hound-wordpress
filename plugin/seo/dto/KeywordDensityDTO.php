<?php

namespace com\cminds\seokeywords\plugin\seo\dto;

class KeywordDensityDTO {

    public $content;
    public $first100;

    public function __construct() {
        $this->content = 0;
        $this->first100 = 0;
    }

}
