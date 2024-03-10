<?php

namespace com\cminds\seokeywords\plugin\seo\contents\helpers;

class TagHelper {

    public static function getContentForTags($s, $tags) {
        $res = [];
        $dom = CachedHtmlDomParser::str_get_html($s);
        if ($dom === FALSE) {
            return [];
        }
        foreach ($tags as $tag) {
            foreach ($dom->find($tag) as $elem) {
                $res[] = $elem->plaintext;
            }
        }
        return $res;
    }

}
