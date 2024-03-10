<?php

namespace com\cminds\seokeywords\plugin\seo\contentpolicies;

use com\cminds\seokeywords\plugin\seo;

abstract class PolicyAbstract implements PolicyInterface {

    protected $keyword;
    protected $content;

    public function __construct($keyword, seo\contents\ContentAbstract $content) {
        $this->keyword = trim($keyword);
        $this->content = $content->getContent();
    }

    public function getOccurrence() {
        return 0;
    }

}
