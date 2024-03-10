<?php

use com\cminds\seokeywords\plugin\models;

global $post;
$ids = models\Options::getDashboardMetricsIds();
$ids = is_array($ids) ? $ids : [];
?>
<span style="display: none">
    <label class="cmsk_show_on_dashboard">
        <input type="checkbox" name="cmsk_show_on_dashboard" value="1" <?php checked(in_array($post->ID, $ids)); ?> />
        Show in Dashboard
    </label>
</span>
<script type="text/javascript">
    (function ($) {
        $(function () {
            var metabox = '<?php echo $metabox; ?>';
            $('.cmsk_show_on_dashboard').clone().prependTo($('#' + metabox).find('h2:first')).on('click', function (e) {
                e.stopPropagation();
            });
            $('.cmsk_show_on_dashboard:last').remove();
        });
    })(jQuery);
</script>