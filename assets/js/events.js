var cmsk_events = {};

(function ($) {

    $(function () {


        (function (factory) {
            if (typeof define === "function" && define.amd) {
                define(["jquery", "moment", "datatables.net"], factory);
            } else {
                factory(jQuery, moment);
            }
        }(function ($, moment) {

            $.fn.dataTable.moment = function (format, locale) {
                var types = $.fn.dataTable.ext.type;

                // Add type detection
                types.detect.unshift(function (d) {
                    if (d) {
                        // Strip HTML tags and newline characters if possible
                        if (d.replace) {
                            d = d.replace(/(<.*?>)|(\r?\n|\r)/g, '');
                        }

                        // Strip out surrounding white space
                        d = $.trim(d);
                    }

                    // Null and empty values are acceptable
                    if (d === '' || d === null) {
                        return 'moment-' + format;
                    }

                    return moment(d, format, locale, true).isValid() ?
                            'moment-' + format :
                            null;
                });

                // Add sorting method - use an integer for the sorting
                types.order[ 'moment-' + format + '-pre' ] = function (d) {
                    if (d) {
                        // Strip HTML tags and newline characters if possible
                        if (d.replace) {
                            d = d.replace(/(<.*?>)|(\r?\n|\r)/g, '');
                        }

                        // Strip out surrounding white space
                        d = $.trim(d);
                    }

                    return !moment(d, format, locale, true).isValid() ?
                            Infinity :
                            parseInt(moment(d, format, locale, true).format('x'), 10);
                };
            };

        }));

        var dataTable;
        var cmsk_ajax_xhr = null;
        var cmsk_ajax_update_xhr = null;
        var data = {items: [], offset: 0, limit: 999, total: 0, filter: 'all', data_type: '0'};
        var config = cmskEventsConfig;

        var xhrDataUpdate = function (loadmore) {
            if (cmsk_ajax_xhr !== null) {
                try {
                    cmsk_ajax_xhr.abort();
                } catch (e) {
                }
            }
            cmsk_ajax_xhr = $.ajax({
                method: 'POST',
                url: ajaxurl,
                cache: false,
                beforeSend: function () {
                    $('#cmsk1-swal-app1').addClass('ajax');
                },
                data: {
                    action: 'cmsk_get_events',
                    offset: data.offset,
                    limit: data.limit,
                    filter: data.filter
                }
            }).done(function (d) {
                try {
                    if (loadmore) {
                        for (k in d.data.items) {
                            data.items.push(d.data.items[k]);
                        }
                        data.total = d.data.total;
                        data.offset += d.data.items.length;
                    } else {
                        data.items = [];
                        for (k in d.data.items) {
                            d.data.items[k]['index'] = k;
                            data.items.push(d.data.items[k]);
                        }
                        data.total = d.data.items.length;
                        data.offset = 0;
                    }
                    $('.cmsk_events_ajax1_error').addClass('hidden');
                    if (dataTable) {
                        dataTable.destroy(false);
                    }
                    vm.$forceUpdate();
                } catch (e) {
                    $('.cmsk_events_ajax1_error').removeClass('hidden');
                }
                $('.cmsk_events_ajax1_loading').addClass('hidden');
            }).fail(function () {
                $('.cmsk_events_ajax1_error').removeClass('hidden');
            }).always(function () {
                $('#cmsk4').removeClass('cmsk-init');
//                $('.cmsk_events_ajax1_loading').addClass('hidden');
            });
        };

        var xhrUpdateCustom = function (index, custom_data) {
            if (cmsk_ajax_update_xhr !== null) {
                try {
                    cmsk_ajax_update_xhr.abort();
                } catch (e) {
                }
            }
            cmsk_ajax_update_xhr = $.ajax({
                method: 'POST',
                url: ajaxurl,
                cache: false,
                beforeSend: function () {
                    $('#cmsk1-swal-app1').addClass('ajax');
                },
                data: {
                    action: 'cmsk_update_metric_custom_fields',
                    index: index,
                    custom_data: custom_data
                }
            }).done(function (d) {
//                xhrDataUpdate();
                vm.$forceUpdate();
            }).fail(function () {
            }).always(function () {
            });
        };

        $(document).on('click', '.cmsk4-toggle-input', function () {
            var custom_field = true;
            var triggers = 'cmsk_save';
            var _this = this;
            var index = $(this).data('index');
            var o = vm.items[index];
            var input = $(this).siblings('input');
            if (!input.length) {
                input = $(this).siblings('textarea');
            }
            var callback = input.data('callback') || function () {};
            if (input.hasClass('cmsk-has-datepicker')) {
                custom_field = false;
                var defaultDate = typeof input.data('time') !== 'undefined' ? moment(parseInt(input.data('time'))).format('YYYY-MM-DD') : '';
                $(input).datepicker({
                    dateFormat: 'yy-mm-dd',
                    defaultDate: defaultDate,
                    onSelect: function (dateText, inst) {
                        input.trigger('cmsk_save');
                    }
                });
            } else {
                triggers += ' focusout blur';
            }
            var field = '' + input.data('field');
            var state = input.is(':visible');
            var old_value = (o.custom_data && o.custom_data.fields) ? o.custom_data.fields[field] : '';

            if (!state) {
                if (custom_field) {
                    input.val(old_value); //set the value
                }
                input.show().focus();
                $(this).hide();
            }

            input.keypress(function (event) {
                if (event.which === 13) { //enter
                    event.preventDefault();
                    input.trigger('cmsk_save');
                }
            });

            input.one(triggers, function () {
                $(_this).show();
                input.hide();
                if (old_value !== input.val( ) && custom_field) {
                    if (typeof o.custom_data === 'undefined' || o.custom_data === null || typeof o.custom_data.fields === 'undefined') {
                        var custom_fields = {
                            'custom_data': {
                                'fields': {}
                            }
                        };
                        o = $.extend(o, custom_fields);
                    }
                    o.custom_data.fields[field] = input.val();
                    xhrUpdateCustom(index, JSON.stringify(o.custom_data));
                    vm.$forceUpdate();
                } else {
                    cmsk_events[callback](input);
                }
            });
        });

        $(document).on('click', '.cmsk-metrics-filter-apply', function () {
            var filter = '';
            var filter = selectedFilter = $('#cmsk-metrics-filters').val();
            data.filter = filter;
            vm.reload();
        });

        $(document).on('click', '.cmsk-data-type-filter-apply', function () {
            var data_type = $('#cmsk-data-type-filter').val();

            data.data_type = data_type;
            vm.reload();
        });

        $(document).on('click', '.toggle-vis span', function (e) {
            var visible;
            if (dataTable) {
                // Get the column API object
                var column = dataTable.column($(this).attr('data-column'));
                visible = !column.visible();
                // Toggle the visibility
                column.visible(visible);
                $(this).removeClass('hidden');
                if (!visible) {
                    $(this).addClass('hidden');
                }
            }
        });

        function toggleColumnVisibility() {
            var visible;
            var columns = $('.toggle-vis span');
            $.each(columns, function (index, item) {
                // Get the column API object
                var column = dataTable.column($(this).attr('data-column'));
                visible = !$(item).hasClass('hidden');
                // Toggle the visibility
                column.visible(visible);
            });
        }

        var vm = new Vue({
            el: '#cmsk4',
            data: data,
            mounted: function () {
                xhrDataUpdate();
            },
            updated: function () {
                var i = 0;
                var columnDefs = [
                    {"width": "10%", "targets": 0},
                    {"visible": false, "targets": 1},
                    {"type": "num", "orderData": 1, "width": "5%", "targets": 2},
                    {"width": "5%", "targets": 3},
                    {"width": "50%", "targets": 4}
                ];
                var pageInfo = {
                    length: 10,
                    page: 1
                };
                if (typeof dataTable !== 'undefined') {
                    pageInfo = dataTable.page.info();
                    if (typeof pageInfo === 'undefined') {
                        pageInfo = {
                            length: 10,
                            page: 1
                        };
                    }
                }
                dataTable = $('#cmsk4-list').DataTable({
                    destroy: true,
                    stateSave: true,
                    "order": [[2, "desc"]],
                    "columnDefs": columnDefs,
                    "pageLength": pageInfo.length,
                    "displayStart": (pageInfo.page - 1) * pageInfo.length
                });

                toggleColumnVisibility();
            },
            methods: {
                reload: function () {
                    xhrDataUpdate(0);
                },
                more: function () {
                    xhrDataUpdate(4);
                },
                all: function () {
                    xhrDataUpdate(999);
                },
                showAlert: function (item) {
                    var result = this.inPast(this.addDays(item.date, item.notifications.days));
                    return (result && item.notifications.disabled !== '1');
                },
                inPast: function (timestamp) {
                    var result = moment().diff(timestamp);
                    return result > 0;
                },
                addDays: function (timestamp, days) {
                    if (!days) {
                        days = 0;
                    }
                    var newTimestamp = timestamp + ((days - 0) * 1000 * 3600 * 24);
                    return newTimestamp;
                },
                getDate: function (timestamp, format) {
                    if (typeof format === 'undefined') {
                        format = 'L';
                    }
                    var date = moment(parseInt(timestamp)).format(format);
                    if (date == 'Invalid date') {
                        date = '';
                    }
                    return date;
                },
                getDaysAgo: function (timestamp) {
                    var date = moment(parseInt(timestamp)).format('L');
                    if (date == 'Invalid date') {
                    }
                    return moment(parseInt(timestamp)).fromNow();
                }
            }
        });

        var xhrUpdateNotificationDate = function (id, postid) {
            if (cmsk_ajax_update_xhr !== null) {
                try {
                    cmsk_ajax_update_xhr.abort();
                } catch (e) {
                }
            }

            cmsk_ajax_update_xhr = $.ajax({
                method: 'POST',
                url: ajaxurl,
                cache: false,
                beforeSend: function () {
                    $('#cmsk1-swal-app1').addClass('ajax');
                },
                data: {
                    action: 'cmsk_disable_notification',
                    id: id,
                    postid: postid
                }
            }).done(function (d) {
                vm.$forceUpdate();
                vm.reload();
            }).fail(function () {
            }).always(function () {
            });
        };

        $(document).on('click', '.cmsk-disable-notification', function () {
            var id = $(this).parents('tr').data('id');
            var postid = $(this).parents('tr').data('postid');
            $(this).siblings('.cmsk_events_ajax1_loading').removeClass('hidden');
            xhrUpdateNotificationDate(id, postid);
        });

        $(document).on('click', '.cmsk-row-color-selector-div > span', function () {
            var parentrow = $(this).parents('tr');
            var index = parentrow.data('index');
            var value = $(this).data('val');
            parentrow.css('background', value);

            $(this).siblings().removeClass('active');
            $(this).addClass('active');
        });

    });
})(jQuery);