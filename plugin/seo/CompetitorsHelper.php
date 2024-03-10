<?php

namespace com\cminds\seokeywords\plugin\seo;

use com\cminds\seokeywords\plugin\models;

class CompetitorsHelper {

    public static function fixItems( $res ) {
        if ( !isset( $res[ 'items' ] ) || !is_array( $res[ 'items' ] ) ) {
            $res[ 'items' ] = [ ];
        }
        foreach ( $res[ 'items' ] as $k => $v ) {
            if ( !is_object( $v ) || !$v instanceof dto\CompetitorDTO ) {
                unset( $res[ 'items' ][ $k ] );
            }
            if ( empty( $v->phrases ) ) {
                $res[ 'items' ][ $k ]->phrases = array_filter( [$v->phrase1, $v->phrase2, $v->phrase3 ], 'strlen' );
            }
        }
        return $res;
    }

    public static function getIsTop( $dtos ) {
        $arr = [ ];
        foreach ( $dtos as $item ) {
            $arr[ $item->url ] = $item->is_main_competitor;
        }
        return $arr;
    }

    public static function getUrls( $dtos ) {
        $arr = [ ];
        foreach ( $dtos as $item ) {
            $arr[] = $item->url;
        }
        return $arr;
    }

    public static function getSearchPhrases( $dtos ) {
        $arr = [ ];
        foreach ( $dtos as $item ) {
            $phrases   = [ ];
            $phrases[] = $item->phrase1;
            $phrases[] = $item->phrase2;
            $phrases[] = $item->phrase3;
            if ( is_array( $item->phrases ) ) {
                $phrases = array_merge( $phrases, $item->phrases );
            }
            $arr[ $item->url ] = array_unique(array_filter( $phrases, 'strlen' ));
        }
        return $arr;
    }

}
