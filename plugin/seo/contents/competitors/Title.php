<?php

namespace com\cminds\seokeywords\plugin\seo\contents\competitors;

use com\cminds\seokeywords\plugin\seo\contents;

class Title extends ContentAbstract implements contents\ContentInterface {

    public function getContent() {
        return contents\helpers\TagHelper::getContentForTags($this->html, ['title']);
    }

}
