<?php

namespace com\cminds\seokeywords\plugin\seo\contents\posts;

use com\cminds\seokeywords\plugin\models;
use com\cminds\seokeywords\plugin\seo\contents;

class Images extends ContentAbstract implements contents\ContentInterface {

    public function getContent() {
        $content = $this->getContextHtmlContent();
        $res = [];
        $dom = contents\helpers\CachedHtmlDomParser::str_get_html($content);
        if ($dom === FALSE) {
            return [];
        }
        foreach ($dom->find('img') as $elem) {
            $res[] = $elem->alt . '__SEPARATOR__' . $elem->title;
        }
        return $res;
    }

}
