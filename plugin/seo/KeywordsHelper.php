<?php

namespace com\cminds\seokeywords\plugin\seo;

use com\cminds\seokeywords\plugin\seo;

class KeywordsHelper {

    public static function fixItems($res) {
        if (!isset($res['items']) || !is_array($res['items'])) {
            $res['items'] = [];
        }
        $order = 0;
        foreach ($res['items'] as $k => $v) {
            if (!is_object($v) || !$v instanceof dto\KeywordDTO) {
                unset($res['items'][$k]);
                continue;
            }
            // 20170825 alternative keywords
            if (!is_array($res['items'][$k]->altkeywords)) {
                $res['items'][$k]->altkeywords = [];
            }
            // 20171006
            $res['items'][$k]->order = $order++;
        }
        return $res;
    }

    public static function getKeywords($dtos) {
        $arr = [];
        foreach ($dtos as $item) {
            $arr[] = $item->keyword;
        }
        return $arr;
    }

    public static function getAlternateKeywords($dtos) {
        $arr = [];
        foreach ($dtos as $item) {
            $arr = array_merge($arr, (array) $item->altkeywords);
        }
        return $arr;
    }

    public static function getAllKeywords($dtos) {
        return array_merge(static::getKeywords($dtos), static::getAlternateKeywords($dtos));
    }

}
