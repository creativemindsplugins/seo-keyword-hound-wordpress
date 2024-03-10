<?php

namespace com\cminds\seokeywords\plugin\seo\contents\posts;

use com\cminds\seokeywords\plugin\models;
use com\cminds\seokeywords\plugin\seo\contents;

class First100Words extends FirstXWords implements contents\ContentInterface {

    public function __construct(models\GenericPost $post) {
        parent::__construct($post, 100);
    }

}
