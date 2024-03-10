<?php

namespace com\cminds\seokeywords\plugin\misc;

use com\cminds\seokeywords\App;

class Misc {

    static $urls = null;

    public static function wpRemoteGetTitle( $url ) {
        if ( empty( self::$urls[ $url ] ) ) {
            $res                  = wp_remote_get( $url );
            static::$urls[ $url ] = $res; //cache
        } else {
            $res = static::$urls[ $url ];
        }
        if ( !is_wp_error( $res ) && $res[ 'response' ][ 'code' ] == 200 ) {
            if ( preg_match_all( '/\<title.*?>(.*?)\<\/title/si', $res[ 'body' ], $matches ) ) {
                return html_entity_decode( $matches[ 1 ][ 0 ] );
            }
        }
    }

    public static function wpRemoteGetWordCount( $url ) {
        $count = 0;
        if ( empty( static::$urls[ $url ] ) ) {
            $res                  = wp_remote_get( $url );
            static::$urls[ $url ] = $res; //cache
        } else {
            $res = static::$urls[ $url ];
        }

        if ( !empty( $res ) && !is_wp_error( $res ) ) {
            $body  = wp_remote_retrieve_body( $res );
            $count = (int) str_word_count( strip_tags( strtolower( $body ) ) );
        }

        return $count;
    }

    public static function wpRemoteGetMeta( $url, $tag = '' ) {
        $tags = get_meta_tags( $url );
        if ( !empty( $tag ) && !empty( $tags[ $tag ] ) ) {
            return $tags[ $tag ];
        }
        return $tags;
    }

    public static function jsDateToTimestamp( $jsDate ) {
        return $jsDate / 1000;
    }

    public static function jsDateToHumanDiff( $jsDate ) {
        $result = human_time_diff( static::jsDateToTimestamp( $jsDate ), current_time( 'timestamp' ) ) . ' ago';
        return $result;
    }

    public static function jsDateToDate( $jsDate, $withDiff = true ) {
        $result = date( 'Y-m-d H:i:s', static::jsDateToTimestamp( $jsDate ) );
        if ( $withDiff ) {
            $result .= ' (' . static::jsDateToHumanDiff( $jsDate ) . ')';
        }
        return $result;
    }

}
