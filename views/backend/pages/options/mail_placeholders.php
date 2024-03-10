<?php

use com\cminds\seokeywords\plugin\options\Options;
use com\cminds\seokeywords\plugin\services\NotificationService;
?>

<h2><?php _e( 'Placeholders', 'cmsk' ); ?></h2>
<p><?php _e( 'You can use predefined placeholders in mail templates.', 'cmsk' ); ?></p>
<h3><?php _e( 'Available Placeholders', 'cmsk' ); ?></h3>
<ul>
    <li><code><?php echo NotificationService::PLACEHOLDER_PAGE_TITLE; ?></code> &mdash; <?php _e( 'page title', 'cmsk' ); ?>,</li>
    <li><code><?php echo NotificationService::PLACEHOLDER_EDIT_URL; ?></code> &mdash; <?php _e( 'edit page url', 'cmsk' ); ?>,</li>
    <li><code><?php echo NotificationService::PLACEHOLDER_RESET_DATE; ?></code> &mdash; <?php _e( 'date of the last reset', 'cmsk' ); ?>,</li>
    <li><code><?php echo NotificationService::PLACEHOLDER_NOTIFICATION_TEXT; ?></code> &mdash; <?php _e( 'the notification text', 'cmsk' ); ?>,</li>
</ul>