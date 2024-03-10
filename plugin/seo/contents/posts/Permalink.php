<?php

namespace com\cminds\seokeywords\plugin\seo\contents\posts;

use com\cminds\seokeywords\plugin\seo\contents;

class Permalink extends ContentAbstract implements contents\ContentInterface {

    public function getContent() {
        return $this->getContextPermalink();
    }

}
