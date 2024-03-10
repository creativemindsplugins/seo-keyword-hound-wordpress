<?php

namespace com\cminds\seokeywords\plugin\seo\contents\posts;

use com\cminds\seokeywords\plugin\models;
use com\cminds\seokeywords\plugin\seo\contents;

class FirstXWords extends Content implements contents\ContentInterface {

    protected $wordsCount;

    public function __construct(models\GenericPost $post, $wordsCount) {
        $this->wordsCount = $wordsCount;
        parent::__construct($post);
    }

    public function getContent() {
        return wp_trim_words(parent::getContent(), $this->wordsCount, '');
    }

}
