<?php

namespace com\cminds\seokeywords\plugin\seo\contents\posts;

use com\cminds\seokeywords\plugin\seo\contents;

class MarkedContent extends ContentAbstract implements contents\ContentInterface {

    public function getContent() {
        return contents\helpers\TagHelper::getContentForTags($this->getContextHtmlContent(), ['strong', 'i', 'b', 'em']);
    }

}
