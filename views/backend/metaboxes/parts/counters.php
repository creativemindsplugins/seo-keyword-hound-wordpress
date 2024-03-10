<?php
$id = uniqid( 'cmsk_' );
?>
<template id="<?php echo $id; ?>">
    <span id="<?php echo $id; ?>-content" class="cmsk-tooltips-area">
        <template v-if="keywords">
            <span class="cmsk-post-word-count hide-on-postbox-container-1">
                Keywords: <strong>{{keywords}}</strong>
                <template v-if="alternate">
                    <small>(Alternate: +<strong>{{alternate}}</strong>)</small>
                </template>
            </span>
        </template>
<!--        <template v-if="words">
            <span class="cmsk-post-word-count hide-on-postbox-container-1" data-title="Requires page refresh to update.">
                Words: <strong>{{words}}</strong>
            </span>
        </template>-->
    </span>
</template>
<script type="text/javascript">
    ( function ( $ ) {
        $( function () {

            if ( typeof window.cmsk_counters === 'undefined' ) {
                window.cmsk_counters = [ ];
            }

            var metabox = '<?php echo $metabox; ?>';
            var templateId = '<?php echo $id; ?>';
            var data = $.extend( { }, {
                keywords: 0,
                alternate: 0,
                words: 0,
                competitors: 0
            }, <?php echo json_encode( $data ); ?> );

            $( '#{0}'.replace( '{0}', metabox ) )
                .find( 'h2' )
                .first()
                .prepend( $( '#{0}'.replace( '{0}', templateId ) ).html() );

        } );
    } )( jQuery );
</script>