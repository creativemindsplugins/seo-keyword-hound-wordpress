<?php

namespace com\cminds\seokeywords\plugin\seo\contents\competitors;

use com\cminds\seokeywords\plugin\seo\contents;

class FirstXWords extends Content implements contents\ContentInterface {

    protected $wordsCount;

    public function __construct($response, $wordsCount) {
        $this->wordsCount = $wordsCount;
        parent::__construct($response);
    }

    public function getContent() {
        return wp_trim_words(parent::getContent(), $this->wordsCount, '');
    }

}
