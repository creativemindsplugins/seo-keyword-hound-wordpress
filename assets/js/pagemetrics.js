var cmsk_pagemetrics = { };

( function ( $ ) {
    $( function () {

        var dataTable;
        var cmsk_ajax_xhr = null;
        var cmsk_ajax_update_xhr = null;
        var data = { items: [ ], offset: 0, limit: 999, total: 0, filter: 'all', data_type: '0' };
        var config = cmskPagemetricsConfig;

        var xhrDataUpdate = function ( loadmore ) {
            if ( cmsk_ajax_xhr !== null ) {
                try {
                    cmsk_ajax_xhr.abort();
                } catch ( e ) {
                }
            }
            $( '.cmsk_pagemetrics_ajax1_loading' ).removeClass( 'hidden' );
            cmsk_ajax_xhr = $.ajax( {
                method: 'POST',
                url: ajaxurl,
                cache: false,
                beforeSend: function () {
                    $( '#cmsk1-swal-app1' ).addClass( 'ajax' );
                },
                data: {
                    action: 'cmsk_get_metrics',
                    offset: data.offset,
                    limit: data.limit,
                    filter: data.filter
                }
            } ).done( function ( d ) {
                try {
                    if ( loadmore ) {
                        for ( k in d.data.items ) {
                            data.items.push( d.data.items[k] );
                        }
                        data.total = d.data.total;
                        data.offset += d.data.items.length;
                    } else {
                        data.items = [ ];
                        for ( k in d.data.items ) {
                            d.data.items[k]['index'] = k;
                            data.items.push( d.data.items[k] );
                        }
                        data.total = d.data.items.length;
                        data.offset = 0;
                    }
                    $( '.cmsk_pagemetrics_ajax1_error' ).addClass( 'hidden' );
                    if ( dataTable ) {
                        dataTable.destroy( false );
                    }
                    vm.$forceUpdate();
                } catch ( e ) {
                    $( '.cmsk_pagemetrics_ajax1_error' ).removeClass( 'hidden' );
                }
            } ).fail( function () {
                $( '.cmsk_pagemetrics_ajax1_error' ).removeClass( 'hidden' );
            } ).always( function () {
                $( '#cmsk3' ).removeClass( 'cmsk-init' );
                $( '.cmsk_pagemetrics_ajax1_loading' ).addClass( 'hidden' );
            } );
        };

        var xhrUpdateCustom = function ( post_id, custom_data ) {
            if ( cmsk_ajax_update_xhr !== null ) {
                try {
                    cmsk_ajax_update_xhr.abort();
                } catch ( e ) {
                }
            }
            $( '.cmsk_pagemetrics_ajax1_loading' ).removeClass( 'hidden' );
            cmsk_ajax_update_xhr = $.ajax( {
                method: 'POST',
                url: ajaxurl,
                cache: false,
                beforeSend: function () {
                    $( '#cmsk1-swal-app1' ).addClass( 'ajax' );
                },
                data: {
                    action: 'cmsk_update_metric_custom_fields',
                    post_id: post_id,
                    custom_data: custom_data
                }
            } ).done( function ( d ) {
                vm.$forceUpdate();
            } ).fail( function () {
            } ).always( function () {
            } );
        };

        var xhrUpdateNotificationDate = function ( index, date ) {
            if ( cmsk_ajax_update_xhr !== null ) {
                try {
                    cmsk_ajax_update_xhr.abort();
                } catch ( e ) {
                }
            }

            var newDate = typeof date === 'undefined' ? +new Date() : +new Date( date );
            $( '.cmsk_pagemetrics_ajax1_loading' ).removeClass( 'hidden' );
            cmsk_ajax_update_xhr = $.ajax( {
                method: 'POST',
                url: ajaxurl,
                cache: false,
                beforeSend: function () {
                    $( '#cmsk1-swal-app1' ).addClass( 'ajax' );
                },
                data: {
                    action: 'cmsk_update_notification_date',
                    index: index,
                    notification_last_reset: newDate
                }
            } ).done( function ( d ) {
                vm.$forceUpdate();
                vm.reload();
            } ).fail( function () {
            } ).always( function () {
            } );
        };

        cmsk_pagemetrics['updateLastReset'] = function ( field ) {
            var index = field.parents( 'tr' ).data( 'index' );
            var date = field.val(); //new selected date
            xhrUpdateNotificationDate( index, date );
        }

        $( document ).on( 'click', '.cmsk3-toggle-input', function () {
            var custom_field = true;
            var triggers = 'cmsk_save';
            var _this = this;
            var index = $( this ).data( 'index' );
            var post_id = $( this ).data( 'post_id' );
            var o = vm.items[index];
            var input = $( this ).siblings( 'input' );
            if ( !input.length ) {
                input = $( this ).siblings( 'textarea' );
            }
            var callback = input.data( 'callback' ) || function () {};
            if ( input.hasClass( 'cmsk-has-datepicker' ) ) {
                custom_field = false;
                var defaultDate = typeof input.data( 'time' ) !== 'undefined' ? moment( parseInt( input.data( 'time' ) ) ).format( 'YYYY-MM-DD' ) : '';
                $( input ).datepicker( {
                    dateFormat: 'yy-mm-dd',
                    defaultDate: defaultDate,
                    onSelect: function ( dateText, inst ) {
                        input.trigger( 'cmsk_save' );
                    }
                } );
            } else {
                triggers += ' focusout blur';
            }
            var field = '' + input.data( 'field' );
            var state = input.is( ':visible' );
            var old_value = ( o.custom_data && o.custom_data.fields ) ? o.custom_data.fields[field] : '';

            if ( !state ) {
                if ( custom_field ) {
                    input.val( old_value ); //set the value
                }
                input.show().focus();
                $( this ).hide();
            }

            input.keypress( function ( event ) {
                if ( event.which === 13 ) { //enter
                    event.preventDefault();
                    input.trigger( 'cmsk_save' );
                }
            } );

            input.one( triggers, function () {
                $( _this ).show();
                input.hide();
                if ( old_value !== input.val( ) && custom_field ) {
                    if ( typeof o.custom_data === 'undefined' || o.custom_data === null || typeof o.custom_data.fields === 'undefined' ) {
                        var custom_fields = {
                            'custom_data': {
                                'fields': { }
                            }
                        };
                        o = $.extend( o, custom_fields );
                    }
                    o.custom_data.fields[field] = input.val();
                    xhrUpdateCustom( post_id, JSON.stringify( o.custom_data ) );
                    vm.$forceUpdate();
                } else {
                    cmsk_pagemetrics[callback]( input );
                }
            } );
        } );

        $( document ).on( 'change', '#cmsk-metrics-filters', function () {
            var hideArr = [ 'all', 'title-update', 'keyword-update' ];
            var selectedFilter = $( this ).val();
            if ( hideArr.indexOf( selectedFilter ) !== -1 ) {
                $( '#cmsk-metrics-filters-order' ).hide();
            } else {
                $( '#cmsk-metrics-filters-order' ).show();
            }
        } );

        $( document ).on( 'click', '.cmsk-metrics-filter-apply', function () {
            var filter = '';
            var filter = selectedFilter = $( '#cmsk-metrics-filters' ).val();
            var hideArr = [ 'all', 'title-update', 'keyword-update' ];

            if ( hideArr.indexOf( selectedFilter ) === -1 ) {
                filter = selectedFilter + $( '#cmsk-metrics-filters-order' ).val();
            }

            data.filter = filter;
            vm.reload();
        } );

        $( document ).on( 'click', '.cmsk-data-type-filter-apply', function () {
            var data_type = $( '#cmsk-data-type-filter' ).val();

            data.data_type = data_type;
            vm.reload();
        } );

        $( document ).on( 'click', '.toggle-vis span', function ( e ) {
            var visible;
            if ( dataTable ) {
                // Get the column API object
                var column = dataTable.column( $( this ).attr( 'data-column' ) );
                visible = !column.visible();
                // Toggle the visibility
                column.visible( visible );
                $( this ).removeClass( 'hidden' );
                if ( !visible ) {
                    $( this ).addClass( 'hidden' );
                }
            }
        } );

        function toggleColumnVisibility() {
            var visible;
            var columns = $( '.toggle-vis span' );
            $.each( columns, function ( index, item ) {
                // Get the column API object
                var column = dataTable.column( $( this ).attr( 'data-column' ) );
                visible = !$( item ).hasClass( 'hidden' );
                // Toggle the visibility
                column.visible( visible );
            } );
        }

        var vm = new Vue( {
            el: '#cmsk3',
            data: data,
            mounted: function () {
                xhrDataUpdate();
            },
            updated: function () {
                var i = 0;
                var columnDefs = [
                    { "width": "10%", "targets": 0 },
                    { "width": "5%", "targets": 2 },
                    { "width": "10%", "targets": 15 },
                    { "width": "10%", "targets": 16 },
                    { "visible": false, "type": "num", "targets": 4 },
                    { "orderData": 4, "type": "num", "targets": 5 },
                    { "visible": false, "type": "num", "targets": 6 },
                    { "orderData": 6, "type": "num", "targets": 7 },
                    { "visible": false, "type": "num", "targets": 8 },
                    { "orderData": 8, "type": "num", "targets": 9 },
                    { "visible": false, "type": "num", "targets": 10 },
                    { "orderData": 10, "type": "num", "targets": 11 }
                ];
                if ( $.inArray( 'conversion', config.additional ) !== -1 ) {
                    columnDefs = columnDefs.concat( [
                        { "visible": false, "type": "num", "targets": 12 },
                        { "orderData": 12, "type": "num", "targets": 13 }
                    ] );
                    i = 2;
                }
                if ( $.inArray( 'conversion_value', config.additional ) !== -1 ) {
                    columnDefs = columnDefs.concat( [
                        { "visible": false, "type": "num", "targets": i + 12 },
                        { "orderData": i + 12, "type": "num", "targets": i + 13 }
                    ] );
                }
                var pageInfo = {
                    length: 10,
                    page: 1
                };
                if ( typeof dataTable !== 'undefined' ) {
                    pageInfo = dataTable.page.info();
                }
                dataTable = $( '#cmsk3-list' ).DataTable( {
                    destroy: true,
                    stateSave: true,
                    "columnDefs": columnDefs,
                    "pageLength": pageInfo.length,
                    "displayStart": ( pageInfo.page - 1 ) * pageInfo.length
                } );

                toggleColumnVisibility();
            },
            methods: {
                reload: function () {
                    xhrDataUpdate( 0 );
                },
                more: function () {
                    xhrDataUpdate( 4 );
                },
                all: function () {
                    xhrDataUpdate( 999 );
                },
                trend: function ( item, param ) {
                    if ( item.trend === null ) {
                        return;
                    }
                    if ( item.trend[param] > config.trendsmargin ) {
                        return 1;
                    }
                    if ( item.trend[param] < -config.trendsmargin ) {
                        return -1;
                    }
                    return 0;
                },
                trendsChartTitle: function ( item ) {
                    return 'Trends for <span class="cmsk-markout">{0}</span>'.replace( '{0}', item.post_title );
                },
                trendsChartConfig: function ( item ) {
                    return JSON.stringify( {
                        action: 'CE5EB3B31DE890081B77AC82BE145F0CC5CCE14C',
                        post_id: item.post_id
                    } );
                },
                getDate: function ( timestamp, format ) {
                    if ( !timestamp ) {
                        return '';
                    }
                    if ( typeof format === 'undefined' ) {
                        format = 'L';
                    }
                    var date = moment( parseInt( timestamp ) ).format( format );
                    if ( date == 'Invalid date' ) {
                        console.log( timestamp );
                        date = '';
                    }
                    return date;
                },
                getDaysAgo: function ( timestamp ) {
                    if ( !timestamp ) {
                        return '';
                    }
                    var date = moment( parseInt( timestamp ) ).format( 'L' );
                    if ( date == 'Invalid date' ) {
                        console.log( timestamp );
                    }
                    return moment( parseInt( timestamp ) ).fromNow();
                }
            }
        } );

        $( document ).on( 'click', '.cmsk-reset-notification', function () {
            var index = $( this ).parents( 'tr' ).data( 'index' );
            xhrUpdateNotificationDate( index );
        } );

        $( document ).on( 'click', '.cmsk-row-color-selector-div > span', function () {
            var parentrow = $( this ).parents( 'tr' );
            var index = $( this ).parents( '.cmsk-row-color-selector-div' ).data( 'index' );
            var post_id = $( this ).parents( '.cmsk-row-color-selector-div' ).data( 'post_id' );
            var value = $( this ).data( 'val' );
            parentrow.css( 'background', value );

            $( this ).siblings().removeClass( 'active' );
            $( this ).addClass( 'active' );

            var o = vm.items[index];
            o.custom_data.row_color = value;
            setTimeout( function () {
                xhrUpdateCustom( post_id, JSON.stringify( o.custom_data ) );
            }, 500 );
        } );

    } );
} )( jQuery );