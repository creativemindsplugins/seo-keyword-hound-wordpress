<?php

namespace com\cminds\seokeywords\plugin\services;

use com\cminds\seokeywords\plugin\models\Options;
use com\cminds\seokeywords\plugin\misc\Misc;
use com\cminds\seokeywords\plugin\models;

class NotificationService {

    const PLACEHOLDER_PAGE_TITLE = '{page-title}';
    const PLACEHOLDER_EDIT_URL   = '{edit-url}';
    const PLACEHOLDER_RESET_DATE = '{reset-date}';
    const PLACEHOLDER_NOTIFICATION_TEXT = '{custom-notification-message}';

    public static function onNotification( $post_id, $data = array() ) {
        $generic        = models\GenericPost::getInstance( $post_id );
        $data[ 'meta' ] = $generic->getSeoKeywordsDashboardUrlData();

        /*
         * Don't send if already was sent
         */
        if ( $data[ 'meta' ][ 'notification_email_sent' ] ) {
            return;
        }
        !isset( $data[ 'to' ] ) && $data[ 'to' ]      = !empty( $data[ 'meta' ][ 'notification_email' ] ) ? $data[ 'meta' ][ 'notification_email' ] : Options::getOption( Options::NOTIFICATION_EMAILS );
        !isset( $data[ 'subject' ] ) && $data[ 'subject' ] = apply_filters( 'cmsk_email_subject', Options::getOption( Options::NOTIFICATION_EMAIL_SUBJECT ), $post_id, 'notification' );
        !isset( $data[ 'message' ] ) && $data[ 'message' ] = apply_filters( 'cmsk_email_message', Options::getOption( Options::NOTIFICATION_EMAIL_CONTENT ), $post_id, 'notification' );
        $result            = static::mail( $post_id, $data );
        if ( $result ) {
            $data[ 'meta' ][ 'notification_email_sent' ] = true;
            $generic->setSeoKeywordsDashboardUrlData( $data[ 'meta' ] );
        }
    }

    private static function mail( $post_id, $data ) {
        if ( empty( $data[ 'to' ] ) ) {
            $date[ 'to' ] = Options::getOption( Options::NOTIFICATION_EMAILS );
        }
        if ( !is_email( $data[ 'to' ] ) ) {
            return FALSE;
        }
        if ( !isset( $data[ 'source' ] ) ) {
            $data[ 'source' ] = 'mail';
        }
        $vars              = static::getVars( $post_id, $data );
        $data[ 'subject' ] = strtr( $data[ 'subject' ], $vars );
        $data[ 'message' ] = strtr( $data[ 'message' ], $vars );
        return wp_mail( sanitize_email( $data[ 'to' ] ), $data[ 'subject' ], $data[ 'message' ] );
    }

    public static function getVars( $post_id, $data ) {
        $arr                                   = array();
        $arr[ static::PLACEHOLDER_PAGE_TITLE ] = get_the_title( $post_id );
        $arr[ static::PLACEHOLDER_EDIT_URL ]   = static::get_edit_post_link( $post_id, 'url' );

        $resetDate                             = isset( $data[ 'meta' ][ 'notification_last_reset' ] ) ? $data[ 'meta' ][ 'notification_last_reset' ] : time() * 1000;
        $arr[ static::PLACEHOLDER_RESET_DATE ] = Misc::jsDateToDate( $resetDate );
        $arr[ static::PLACEHOLDER_NOTIFICATION_TEXT ] = isset( $data[ 'meta' ][ 'notification' ]['notification_text'] ) ? $data[ 'meta' ][ 'notification' ]['notification_text'] : '';
        return $arr;
    }

    private static function get_edit_post_link( $id = 0, $context = 'display' ) {
        if ( !$post = get_post( $id ) )
            return;

        if ( 'revision' === $post->post_type )
            $action = '';
        elseif ( 'display' == $context )
            $action = '&amp;action=edit';
        else
            $action = '&action=edit';

        $post_type_object = get_post_type_object( $post->post_type );
        if ( !$post_type_object )
            return;

        if ( $post_type_object->_edit_link ) {
            $link = admin_url( sprintf( $post_type_object->_edit_link . $action, $post->ID ) );
        } else {
            $link = '';
        }

        return apply_filters( 'get_edit_post_link', $link, $post->ID, $context );
    }

}
