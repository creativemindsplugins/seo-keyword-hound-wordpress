;
( function ( $ ) {
    $( function () {

        var lastDatasetType = null;

        swal.setDefaults( {
            animation: false
        } );

        var chartColors = {
            red: 'rgb(255, 99, 132)',
            orange: 'rgb(255, 159, 64)',
            yellow: 'rgb(255, 205, 86)',
            blue: 'rgb(54, 162, 235)',
            green: 'rgb(255, 33, 1)',
            purple: 'rgb(153, 102, 255)',
            grey: 'rgb(201, 203, 207)',
            black: 'rgb(0, 0, 0)',
            teal: 'rgb(75, 192, 192)'
        };
        var trendsChartDefaultConfig = {
            type: 'line',
            data: {
                datasets: [ ]
            },
            options: {
                responsive: true,
                legend: {
                    display: true,
                    labels: {
                        lineWidth: 20,
                        boxWidth: 20
                    }
                },
                scales: {
                    xAxes: [ {
//                            type: "time",
//                            time: {
//                                format: 'll',
////                                unit: 'day',
////                                unitStepSize: 1,
//                                tooltipFormat: 'll',
//                                distribution: 'linear',
//                                bounds: 'data',
//                            },
                            ticks: {
                                autoSkip: false,
                                maxRotation: 90,
                                minRotation: 0,
                                source: 'labels',
                                callback: function ( value, index, values ) {
                                    return new moment( value ).format( 'YYYY-MM-DD' );
                                }
                            }
                        } ],
                    yAxes: [ { ticks: { suggestedMin: 0, suggestedMax: 100 }, id: "y-axis-1" },
                        {
                            type: "linear",
                            position: "right",
                            id: "y-axis-2",
                            unitStepSize: 5,
                            ticks: {suggestedMin: 0, suggestedMax: 1},
                            gridLines: {
                                drawOnChartArea: false
                            }
                        }
                    ]
                }
            }
        };
        var trendsChart = null;
        var trendsChartConfig = null;
        var elementChartConfig = null;
        var initTrendsChart = function ( data ) {
            var ctx = document.getElementById( "cmsk-trends-chart" ).getContext( "2d" );
            trendsChartConfig = $.extend( { }, trendsChartDefaultConfig, data );
            trendsChart = new Chart( ctx, trendsChartConfig );
        };
        var updateTrendsChart = function ( data ) {
            trendsChartConfig.data = data.data;
            trendsChart.update();
        };
        var prepareDataset = function ( data, key ) {
            var res = [ ];
            $.each( data, function ( index, item ) {
                if ( typeof ( item[key] ) !== 'undefined' ) {
                    res.push( {
                        x: moment( item.date ),
                        y: item[key]
                    } );
                }
            } );

            /*
             * Fix for date issues
             */
            res = res.reverse();
            return res;
        };
        var prepareLabels = function ( d ) {
            var labels = [ ];

            $.each( d.data, function ( index, item ) {
                labels.push( moment( item.date ) );
            } );

            /*
             * Fix for date issues
             */
            labels = labels.reverse();
            return labels;
        }
        var prepareDatasets = function ( d, datasetType ) {

            if ( datasetType ) {
                lastDatasetType = datasetType;
            }
            if ( !datasetType && lastDatasetType ) {
                datasetType = lastDatasetType;
            }
            var datasets = [ ];
            datasets.push( {
                label: 'Impressions',
                backgroundColor: chartColors.red,
                borderColor: chartColors.red,
                data: prepareDataset( d.data, 'impressions' ),
                fill: false,
                hidden: 'impressions' !== datasetType,
                yAxisID: 'y-axis-1'
            } );
            datasets.push( {
                label: 'Clicks',
                backgroundColor: chartColors.orange,
                borderColor: chartColors.orange,
                data: prepareDataset( d.data, 'clicks' ),
                fill: false,
                hidden: 'clicks' !== datasetType,
                yAxisID: 'y-axis-1'
            } );
            datasets.push( {
                label: 'CTR',
                backgroundColor: chartColors.green,
                borderColor: chartColors.green,
                data: prepareDataset( d.data, 'ctr' ),
                fill: false,
                hidden: 'ctr' !== datasetType,
                yAxisID: 'y-axis-2'
            } );
            datasets.push( {
                label: 'Bounce Rate',
                backgroundColor: chartColors.blue,
                borderColor: chartColors.blue,
                data: prepareDataset( d.data, 'bounce_rate' ),
                fill: false,
                hidden: 'bounce_rate' !== datasetType,
                yAxisID: 'y-axis-2'
            } );
            datasets.push( {
                label: 'Average Position',
                backgroundColor: chartColors.grey,
                borderColor: chartColors.grey,
                data: prepareDataset( d.data, 'position' ),
                fill: false,
                hidden: 'position' !== datasetType,
                yAxisID: 'y-axis-1'
            } );
            datasets.push( {
                label: 'Conversions',
                backgroundColor: chartColors.black,
                borderColor: chartColors.black,
                data: prepareDataset( d.data, 'conversion' ),
                fill: false,
                hidden: 'conversion' !== datasetType,
                yAxisID: 'y-axis-1'
            } );
            datasets.push( {
                label: 'Conversion Value',
                backgroundColor: chartColors.teal,
                borderColor: chartColors.teal,
                data: prepareDataset( d.data, 'conversion_value' ),
                fill: false,
                hidden: 'conversion_value' !== datasetType,
                yAxisID: 'y-axis-1'
            } );
            return datasets.filter( function ( x ) {
                return x.data.length > 0;
            } );
        };

        $( 'body' ).on( 'click', '.cmsk-action-trends-modal-chart', function () {
            cmsk_trendsmodalchart( this );
        } );
        $( 'body' ).on( 'click', '.cmsk-trends-chart-action-3entries', function () {
            elementChartConfig.limit = 3;
            elementChartConfig.date1 = null;
            elementChartConfig.date2 = null;
            cmsk_updatetrendsmodalchart();
        } );
        $( 'body' ).on( 'click', '.cmsk-trends-chart-action-1month', function () {
            elementChartConfig.limit = null;
            elementChartConfig.date1 = moment().add( -1, 'M' ).format( 'YYYY-MM-DD' );
            elementChartConfig.date2 = moment().format( 'YYYY-MM-DD' );
            cmsk_updatetrendsmodalchart();
        } );
        $( 'body' ).on( 'click', '.cmsk-trends-chart-action-3months', function () {
            elementChartConfig.limit = null;
            elementChartConfig.date1 = moment().add( -3, 'M' ).format( 'YYYY-MM-DD' );
            elementChartConfig.date2 = moment().format( 'YYYY-MM-DD' );
            cmsk_updatetrendsmodalchart();
        } );
        $( 'body' ).on( 'click', '.cmsk-trends-chart-action-1year', function () {
            elementChartConfig.limit = null;
            elementChartConfig.date1 = moment().add( -12, 'M' ).format( 'YYYY-MM-DD' );
            elementChartConfig.date2 = moment().format( 'YYYY-MM-DD' );
            cmsk_updatetrendsmodalchart();
        } );
        $( 'body' ).on( 'click', '.cmsk-trends-chart-action-10years', function () {
            elementChartConfig.limit = null;
            elementChartConfig.date1 = moment().add( -120, 'M' ).format( 'YYYY-MM-DD' );
            elementChartConfig.date2 = moment().format( 'YYYY-MM-DD' );
            cmsk_updatetrendsmodalchart();
        } );

        window.cmsk_updatetrendsmodalchart = function () {
            $( '#cmsk-trends-chart-spinner' ).show();
            $.ajax( {
                method: 'POST',
                url: ajaxurl,
                cache: false,
                data: elementChartConfig
            } ).done( function ( d ) {
                $( '#cmsk-trends-chart-spinner' ).hide();
                updateTrendsChart( { data: { datasets: prepareDatasets( d ), labels: prepareLabels( d ) } } );
            } );
        };


        window.cmsk_trendsmodalchart = function ( elem ) {
            var datasetType = $( elem ).data( 'chart-type' );
            elementChartConfig = $( elem ).data( 'chart-config' );
            elementChartConfig.limit = 3;
            elementChartConfig.date1 = null;
            elementChartConfig.date2 = null;
            var kickstart = function () {
                $.ajax( {
                    method: 'POST',
                    url: ajaxurl,
                    cache: false,
                    data: elementChartConfig
                } ).done( function ( d ) {
                    $( '#cmsk-trends-chart-spinner' ).hide();
                    initTrendsChart( { data: { datasets: prepareDatasets( d, datasetType ), labels: prepareLabels( d ) } } );
                } );
            };

            swal( {
                confirmButtonText: 'Close',
                width: 800,
                onOpen: kickstart,
                title: $( elem ).data( 'chart-title' ),
                html: '<p class="swal2-content">\n\
<a href="javascript:void(0)" class="cmsk-trends-chart-action-3entries">Last 3 entries</a>\n\
&bull; <a href="javascript:void(0)" class="cmsk-trends-chart-action-1month">Last month</a>\n\
&bull; <a href="javascript:void(0)" class="cmsk-trends-chart-action-3months">Last 3 months</a>\n\
&bull; <a href="javascript:void(0)" class="cmsk-trends-chart-action-1year">Last year</a>\n\
&bull; <a href="javascript:void(0)" class="cmsk-trends-chart-action-10years">From beginning</a>\n\
</p>\n\
<div style="width: 100%; min-height: 250px;"><span id="cmsk-trends-chart-spinner" class="cmsk-spinner" style="position:absolute;margin:0;padding:0;left:0;top:100px;width:100%;background-position:center;"></span><canvas id="cmsk-trends-chart" style="width:650px; height:250px;"></canvas></div>'
            } ).catch( swal.noop );
        };
    } );
} )( jQuery );