<script type="text/javascript">
    (function ($) {
        $(function () {
            var metabox = '<?php echo $metabox; ?>';

            $('#{0}'.replace('{0}', metabox))
                    .find('h2')
                    .first()
                    .prepend('<span style="float: right; display: inline-block;" id="cmsk-page-trends-chart"></span>');
            $('#cmsk-page-trends-chart').on('click', 'a', function (e) {
                e.stopPropagation();
            });
        });
    })(jQuery);
</script>