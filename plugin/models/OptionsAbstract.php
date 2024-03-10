<?php

namespace com\cminds\seokeywords\plugin\models;

use com\cminds\seokeywords\App;

abstract class OptionsAbstract {

    protected static $_defaultOptions = array();

    public static function isValidOption( $option ) {
        return key_exists( $option, static::$_defaultOptions );
    }

    public static function resetOption( $option ) {
        if ( static::isValidOption( $option ) ) {
            return delete_option( sprintf( '_%s_%s', App::PREFIX, $option ) );
        } else {
            return FALSE;
        }
    }

    public static function updateOption( $option, $value ) {
        if ( static::isValidOption( $option ) ) {
            return update_option( sprintf( '_%s_%s', App::PREFIX, $option ), $value );
        } else {
            return FALSE;
        }
    }

    public static function getOption( $option ) {
        if ( static::isValidOption( $option ) ) {
            return get_option( sprintf( '_%s_%s', App::PREFIX, $option ), static::$_defaultOptions[ $option ] );
        } else {
            return NULL;
        }
    }

    public static function getDefaultOption( $option ) {
        if ( static::isValidOption( $option ) ) {
            return static::$_defaultOptions[ $option ];
        } else {
            return NULL;
        }
    }

}
