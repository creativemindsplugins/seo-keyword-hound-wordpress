<?php

namespace com\cminds\seokeywords\plugin\seo\contents\competitors;

use com\cminds\seokeywords\plugin\models;
use com\cminds\seokeywords\plugin\seo\contents;

class Headers extends ContentAbstract implements contents\ContentInterface {

    public function getContent() {
        return contents\helpers\TagHelper::getContentForTags($this->html, models\Options::getContentHeaders());
    }

}
