(function ($) {
    $(function () {
        $('body').on('mouseenter', '.cmsk-tooltips-area *[data-title]', function () {
            var _this = this;
            $('.cmsk-tooltips-area *[data-title]').each(function () {
                if (this !== _this && typeof $(this).tooltip('instance') !== 'undefined') {
                    $(this).tooltip('destroy');
                }
            });
            $(_this).tooltip({
                items: '*',
                show: false,
                hide: false,
                tooltipClass: 'cmsk-tooltip',
                content: function () {
                    var title = $('<div />').text($(_this).attr('data-title')).html().replace(/(?:\r\n|\r|\n)/g, '<br />');
                    if (title.length == 0) {
                        return;
                    }
                    var subtitle = $('<div />').text($(_this).attr('data-subtitle')).html().replace(/(?:\r\n|\r|\n)/g, '<br />');
                    if (subtitle.length > 0) {
                        title += '<hr /><small>{0}</small>'.replace('{0}', subtitle);
                    }
                    var content = $('<div />').text($(_this).attr('data-content')).html().replace(/(?:\r\n|\r|\n)/g, '<br />');
                    if (content.length > 0) {
                        title += '<hr /><div>{0}</div>'.replace('{0}', content);
                    }
                    return title;
                },
                create: function (event, ui) {
                    $(_this).trigger('mouseenter');
                }
            });
        });
        $('body').on('mouseleave, mousedown', '.cmsk-tooltips-area *[data-title]', function () {
            if (typeof $(this).tooltip('instance') !== 'undefined') {
                $(this).tooltip('destroy');
            }
        });
        $('body').on('mouseenter', '.cmsk-tooltip', function () {
            $('.cmsk-tooltips-area *[data-title]').each(function () {
                if (typeof $(this).tooltip('instance') !== 'undefined') {
                    $(this).tooltip('destroy');
                }
            });
        });
    });
})(jQuery);