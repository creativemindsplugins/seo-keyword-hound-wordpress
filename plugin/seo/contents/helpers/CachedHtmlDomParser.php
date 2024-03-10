<?php

namespace com\cminds\seokeywords\plugin\seo\contents\helpers;

use Sunra\PhpSimple\HtmlDomParser;

// for autoloading only
class BogusHtmlDomParser extends HtmlDomParser {

}

class CachedHtmlDomParser extends \simplehtmldom_1_5\simple_html_dom {

    static protected $instances;
    static protected $cache;
    protected $dom;

    public function __construct( $dom ) {
        $this->dom = $dom;
    }

    public static function str_get_html() {
        $args = func_get_args();
        $hash = '_' . sha1( serialize( $args ) );
        if ( !isset( static::$instances[ $hash ] ) ) {
            $dom = call_user_func_array( '\simplehtmldom_1_5\str_get_html', $args );
            if ( $dom ) {
                static::$instances[ $hash ] = new static( $dom );
            } else {
                static::$instances[ $hash ] = $dom;
            }
        }
        return static::$instances[ $hash ];
    }

    function find( $selector, $idx = null, $lowercase = false ) {
        $hash = '_' . sha1( $selector . serialize( $idx ) . serialize( $lowercase ) );
        if ( !isset( static::$cache[ $hash ] ) ) {
            static::$cache[ $hash ] = $this->dom->find( $selector, $idx, $lowercase );
        }
        return static::$cache[ $hash ];
    }

}
