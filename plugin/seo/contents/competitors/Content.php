<?php

namespace com\cminds\seokeywords\plugin\seo\contents\competitors;

use com\cminds\seokeywords\plugin\seo\contents;

class Content extends ContentAbstract implements contents\ContentInterface {

    public function getContent() {
        $content = mb_strtolower($this->html);
        if (preg_match_all('/(<body.+)/s', $content, $matches)) {
            $content = $matches[0][0];
        }
        $content = wp_strip_all_tags($content);
        $content = preg_replace('!\s+!', ' ', $content);
        return $content;
    }

}
