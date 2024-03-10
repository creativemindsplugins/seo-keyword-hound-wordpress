;
( function ( $ ) {
    $( function () {

        var labels = {
            changelog: {
                keywordchanged: 'keyword "{0}" changed to "{1}"',
                keywordadded: 'created "{0}" keyword',
                altkeywordadded: 'added "{0}" alternate keyword',
                altkeywordeleted: 'deleted "{0}" alternate keyword'
            }
        };

        var scrollDiv = document.createElement( "div" );
        scrollDiv.className = "cmsk-scrollbar-measure";
        document.body.appendChild( scrollDiv );
        var scrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth;
        $( '#cmsk1-list header' ).css( { 'padding-right': '{0}px'.replace( '{0}', scrollbarWidth ) } );

        swal.setDefaults( {
            animation: false
        } );

        var config = cmsk1Config;

        var ajax1xhr = null;
        var lastSavedData = null;

        var isEditorActive = function () {
            return ( typeof tinyMCE != "undefined" ) && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
        };
        var getEditor = function () {
            return $( '#content_ifr' ).contents().find( 'body' );
        };

        var preparePostData = function ( data ) {
            var d = JSON.parse( JSON.stringify( data ) );
            d.items.sort( function ( a, b ) {
                return a.order - b.order;
            } );
            d.items.map( function ( x ) {
                x.uuid = null;
                x.stats = { };
                x.density = { };
                return x;
            } );
            return d;
        };

        var data;
        var data2;
        var backup;
        var trends;

        try {
            data = config.data;
            trends = config.trends;
            data2 = config.data2;
            var postData = preparePostData( data );
            lastSavedData = JSON.stringify( postData.items );
        } catch ( e ) {
            window.console && console.log( e );
        }
        if ( typeof data === 'undefined' || data === null || typeof data.items === 'undefined' ) {
            data = { items: [ ] };
        }
        data = $.extend( { }, data, {
            allowmultipleexpanded: config.allowmultipleexpanded,
            expanded: [ ],
            orderby: 'custom',
            lastorderby: 'custom',
            orderdir: 'asc',
            lastorderdir: 'asc',
            undeletedkeywords: [ ],
            compare: {
                competitor: null,
                data: null,
                type: null
            },
            compareall: {
                competitors: null,
                data: null,
                compared: 0,
                type: null
            }
        } );

        if ( typeof config.deletedkeywords.indexOf === 'undefined' ) {
            config.deletedkeywords = $.map( config.deletedkeywords, function ( value, index ) {
                return [ value ];
            } );
        }

        if ( typeof data2 === 'undefined' || data2 === null || typeof data2.items === 'undefined' ) {
            data2 = { items: [ ] };
        }

        var keywordDataTemplate = {
            uuid: '',
            keyword: '',
            altkeywords: [ ],
            stats: {
                title: 'ⁿ/ₐ',
                title_sum: 'ⁿ/ₐ',
                title_min: 'ⁿ/ₐ',
                title_max: 'ⁿ/ₐ',
                headers: 'ⁿ/ₐ',
                content: 'ⁿ/ₐ',
                content_sum: 'ⁿ/ₐ',
                content_min: 'ⁿ/ₐ',
                content_max: 'ⁿ/ₐ',
                url: 'ⁿ/ₐ',
                first100: 'ⁿ/ₐ',
                marked: 'ⁿ/ₐ',
                images: 'ⁿ/ₐ'
            },
            note: {
                content: '',
                timestamp: 0
            },
            density: {
                content: 0,
                first100: 0
            },
            order: 9999
        };

        var doBackup = function ( ) {
            backup = vm.items.slice();
            $( '#cmsk1-restore-backup-container' ).show( 100 );
        };

        var restoreBackup = function ( ) {
            if ( backup ) {
                vm.items = backup.slice();
                $( '#cmsk1-restore-backup-container' ).hide( 100 );
            }
        };

        var liveFilter = function ( input, list, options ) {
            // Options: input, list, timeout, callback
            options = options || { };
            list = jQuery( list );
            var lastFilter = '';
            input = jQuery( input );
            var timeout = options.timeout || 0;
            var callback = options.callback || function () {};

            var keyTimeout;

            // NOTE: because we cache lis & len here, users would need to re-init the plugin
            // if they modify the list in the DOM later.  This doesn't give us that much speed
            // boost, so perhaps it's not worth putting it here.
            var lis = list.children();
            var len = lis.length;
            var oldDisplay = len > 0 ? lis[0].style.display : "block";
            callback( len ); // do a one-time callback on initialization to make sure everything's in sync

            input.change( function () {
                // var startTime = new Date().getTime();
                var filter = input.val().toLowerCase();
                var li, innerText, $li, showMain = false;
                var numShown = 0;
                for ( var i = 0; i < len; i++ ) {
                    li = lis[i];
                    $li = jQuery( li );
                    if ( filter && $li.hasClass( 'cmsk-row-alt' ) ) {
                        $li.removeClass( 'hidden' );
                    }

                    if ( '' === filter && $li.hasClass( 'cmsk-row-alt' ) && !$li.hasClass( 'hidden' ) ) {
                        $li.addClass( 'hidden' );
                    }

                    innerText = !options.selector ?
                        ( li.textContent || li.innerText || "" ) :
                        jQuery( li ).find( options.selector ).text();

                    if ( innerText.toLowerCase().indexOf( filter ) >= 0 ) {
                        if ( li.style.display === "none" ) {
                            li.style.display = oldDisplay;
                        }
                        showMain = true;
                        numShown++;
                    } else {
                        showMain = false;
                        if ( li.style.display !== "none" ) {
                            li.style.display = "none";
                        }
                    }
                    if ( showMain ) {
                        $li.parents( 'section' ).find( '.cmsk-row-main' ).show();
                    }

                }
                callback( numShown );
                return false;
            } ).keydown( function () {
                clearTimeout( keyTimeout );
                keyTimeout = setTimeout( function () {
                    if ( input.val() === lastFilter )
                        return;
                    lastFilter = input.val();
                    input.change();
                }, timeout );
            } );
        };

        var isKeywordExists = function ( keyword, o ) {
            var res = false;
            keyword = $.trim( keyword ).toLowerCase();
            if ( typeof ( o ) !== 'undefined' && o.keyword === keyword ) {
                return false;
            }
            $.each( vm.items, function ( i, item ) {
                if ( item.keyword === keyword ) {
                    res = true;
                    return false;
                }
            } );
            return res;
        };

        var isAlternateKeywordExists = function ( keyword, o ) {
            var res = false;
            keyword = $.trim( keyword ).toLowerCase();
            if ( typeof ( o ) !== 'undefined' && o.altkeywords.indexOf( keyword ) !== -1 ) {
                return false;
            }
            $.each( vm.items, function ( i, item ) {
                if ( item.altkeywords.indexOf( keyword ) !== -1 ) {
                    res = true;
                    return false;
                }
            } );
            return res;
        };

        var vmKeywordIndex = function ( keyword ) {
            var res = -1;
            keyword = $.trim( keyword ).toLowerCase();
            $.each( vm.items, function ( index, item ) {
                if ( item.keyword === keyword ) {
                    res = index;
                    return false;
                }
            } );
            return res;
        };

        var wasKeywordDeleted = function ( keyword ) {
            return config.deletedkeywords.indexOf( $.trim( keyword ).toLowerCase() ) !== -1;
        };

        var removeKeywordFromDeleted = function ( keyword ) {
            var index = config.deletedkeywords.indexOf( $.trim( keyword ).toLowerCase() );
            if ( index !== -1 ) {
                config.deletedkeywords.splice( index, 1 );
            }
        };

        var addKeywords = function ( items ) {
            $.each( items, function ( index, item ) {
                item.keyword = $.trim( item.keyword ).toLowerCase();
                if ( !item.keyword.length ) {
                    return false;
                }
                if ( !isKeywordExists( item.keyword ) ) {
                    if ( typeof ( item.altkeywords ) !== 'undefined' ) {
                        item.altkeywords = item.altkeywords.filter( function ( x ) {
                            return !isKeywordExists( x ) && !isAlternateKeywordExists( x );
                        } );
                        item.altkeywords = item.altkeywords.map( function ( x ) {
                            return $.trim( x ).toLowerCase();
                        } );
                        item.altkeywords.sort( function ( a, b ) {
                            return a.toLowerCase().localeCompare( b.toLowerCase() );
                        } );
                    }
                    item.uuid = '_' + CryptoJS.MD5( item.keyword );
                    vm.items.push( $.extend( { }, JSON.parse( JSON.stringify( keywordDataTemplate ) ), item ) );
                    changelog( vmKeywordIndex( item.keyword ), labels.changelog.keywordadded.replace( '{0}', item.keyword ) );
                    $.each( item.altkeywords, function ( i, keyword ) {
                        changelog( vmKeywordIndex( item.keyword ), labels.changelog.altkeywordadded.replace( '{0}', keyword ) );
                    } );
                } else {
                    var index = vmKeywordIndex( item.keyword );
                    if ( index !== -1 ) {
                        var o = vm.items[index];
                        if ( typeof ( item.altkeywords ) !== 'undefined' ) {
                            item.altkeywords = item.altkeywords.map( function ( x ) {
                                return $.trim( x ).toLowerCase();
                            } );
                            item.altkeywords = item.altkeywords.concat( o.altkeywords );
                            item.altkeywords = $.grep( item.altkeywords, function ( v, k ) {
                                return $.inArray( v, item.altkeywords ) === k;
                            } );
                            item.altkeywords = item.altkeywords.filter( function ( x ) {
                                return !isKeywordExists( x ) && !isAlternateKeywordExists( x, o );
                            } );
                            item.altkeywords = item.altkeywords.filter( function ( x ) {
                                return x !== item.keyword;
                            } );
                            item.altkeywords.sort( function ( a, b ) {
                                return a.toLowerCase().localeCompare( b.toLowerCase() );
                            } );
                            if ( JSON.stringify( item.altkeywords ) != JSON.stringify( o.altkeywords ) ) {
                                $.each( item.altkeywords.filter( function ( x ) {
                                    return o.altkeywords.indexOf( x ) < 0;
                                } ), function ( i, keyword ) {
                                    changelog( index, labels.changelog.altkeywordadded.replace( '{0}', keyword ) );
                                } );
                                o.stats = keywordDataTemplate.stats;
                                o.density = keywordDataTemplate.density;
                            }
                        }
                        o = $.extend( o, item );
                        Vue.set( vm.items, index, o );
                    }
                }
            } );
        };

        var getKeywords = function ( exclude ) {

            var options_arr = [ ];
            vm.items.reduce( function ( a, b, i ) {
                if ( i === exclude ) {
                    return a;
                }
                options_arr.push( { key: i, val: b.keyword } );
                return a;
            }, { } );

            options_arr = options_arr.sort( function ( a, b ) {
                return a.val.localeCompare( b.val );
            } );

            return options_arr;
        };

        var getKeywordData2 = function ( keyword ) {
            var res = false;
            keyword = $.trim( keyword ).toLowerCase();
            $.each( data2.items, function ( index, item ) {
                if ( item.keyword === keyword ) {
                    res = item;
                    return false;
                }
            } );
            return res;
        };

        var getCompetitorKeywordData = function ( keyword ) {
            var res = false;
            keyword = $.trim( keyword ).toLowerCase();
            if ( data.compare.data === null || typeof ( data.compare.data.items1 ) === 'undefined' ) {
                return false;
            }
            $.each( data.compare.data.items1, function ( index, item ) {
                if ( item.keyword === keyword ) {
                    res = item;
                    return false;
                }
            } );
            return res;
        };

        var getCompetitorKeywordData2 = function ( keyword ) {
            var res = false;
            keyword = $.trim( keyword ).toLowerCase();
            if ( data.compare.data === null || typeof ( data.compare.data.items2 ) === 'undefined' ) {
                return false;
            }
            $.each( data.compare.data.items2, function ( index, item ) {
                if ( item.keyword === keyword ) {
                    res = item;
                    return false;
                }
            } );
            return res;
        };

        var getCompareAllKeywordData = function ( keyword ) {
            var res = false;
            keyword = $.trim( keyword ).toLowerCase();
            if ( data.compareall.data === null || typeof ( data.compareall.data.items1 ) === 'undefined' ) {
                return false;
            }
            $.each( data.compareall.data.items1, function ( index, item ) {
                if ( item.keyword === keyword ) {
                    res = item;
                    return false;
                }
            } );
            return res;
        };

        var getCompareAllKeywordData2 = function ( keyword ) {
            var res = false;
            keyword = $.trim( keyword ).toLowerCase();
            if ( data.compareall.data === null || typeof ( data.compareall.data.items2 ) === 'undefined' ) {
                return false;
            }
            $.each( data.compareall.data.items2, function ( index, item ) {
                if ( item.keyword === keyword ) {
                    res = item;
                    return false;
                }
            } );
            return res;
        };

        var changelog = function ( index, s ) {
            if ( !config.ischangelog ) {
                return;
            }
            var o = vm.items[index];
            if ( o.note.content.length && o.note.content.indexOf( "\n", o.note.content.length - 1 ) === -1 ) {
                o.note.content += "\n";
            }
            o.note.content += '{0} — {1}'
                .replace( '{0}', new Date().toLocaleString() )
                .replace( '{1}', s );
            o.note.timestamp = +new Date();
            Vue.set( vm.items, index, o );
        };

        $( '#cmsk1-data' ).val( JSON.stringify( preparePostData( data ) ) );

        var vm = new Vue( {
            el: '#cmsk1',
            data: data,
            methods: {
                inArray: function ( item, array ) {
                    return $.inArray( item, array );
                },
                getColorLabel: function ( color ) {
                    if ( typeof config.colorlabels[color] === 'undefined' ) {
                        return '-Label not set-';
                    }
                    return config.colorlabels[color];
                },
                showColumn: function ( columnName ) {
                    return config.columns.indexOf( columnName ) > -1;
                },
                redflag: function ( item ) {
                    return config.columns.reduce( function ( a, b ) {
                        if ( b === 'density' ) {
                            return a;
                        }
                        return a && ( typeof item.stats[b] === 'undefined' || item.stats[b] === 0 );
                    }, true );
                },
                densityflag: function ( item ) {
                    if ( typeof item.density === 'undefined' || typeof item.density['content'] === 'undefined' ) {
                        return false;
                    }
                    return parseInt( item.density['content'] ) >= parseInt( config.densitythreshold );
                },
                contentDensity: function ( item ) {
                    if ( typeof item.density === 'undefined' || typeof item.density.content === 'undefined' ) {
                        return '0.0';
                    }
                    return parseFloat( item.density.content ).toFixed( 1 );
                },
                density: function ( item ) {
                    return config.columns.reduce( function ( a, b ) {
                        if ( typeof item.density === 'undefined' || typeof item.density[b] === 'undefined' ) {
                            return a;
                        }
                        a = Math.max( a, parseFloat( item.density[b] ).toFixed( 1 ) );
                        if ( a == a.toFixed( 0 ) ) {
                            a = a.toFixed( 0 );
                        }
                        return a;
                    }, 0 );
                },
                trend: function ( item ) {
                    var slope = typeof ( trends[item.keyword] ) !== 'undefined' ? trends[item.keyword] : false;
                    if ( slope === false || slope === null ) {
                        return null;
                    }
                    if ( slope > config.trendsmargin ) {
                        return 1;
                    }
                    if ( slope < -config.trendsmargin ) {
                        return -1;
                    }
                    return 0;
                },
                trendsChartTitle: function ( item ) {
                    return 'Trends for <span class="cmsk-keyword">{0}</span> Keyword'.replace( '{0}', $( '<div/>' ).text( item.keyword ).html() );
                },
                trendsChartConfig: function ( item ) {
                    return JSON.stringify( {
                        action: 'A1384A9352131DB095B281250B060272166D1AD0',
                        post_id: config.post_id,
                        keyword: item.keyword
                    } );
                },
                gaLandingPagesURL: function ( item ) {
                    return '?cmsk-action=redirect-SelectedLandingPageForPostIdSearchKeyword&post_id={0}&keyword={1}'
                        .replace( '{0}', config.post_id )
                        .replace( '{1}', item.keyword );
                },
                item: function ( o, prop ) {
                    if ( o === false || typeof o.stats === 'undefined' ) {
                        return prop === 'density' ? '0.0' : 'ⁿ/ₐ';
                    }
                    if ( prop === 'density' ) {
                        return this.contentDensity( o );
                    }
                    if ( typeof o.stats[prop] !== 'undefined' ) {
                        return o.stats[prop];
                    }
                    return 'ⁿ/ₐ';
                },
                altitem: function ( keyword, prop ) {
                    return this.item( getKeywordData2( keyword ), prop );
                },
                saveOrder: function () {
                    data.items.map( function ( o, index ) {
                        o.order = index;
                    } );
                    vm.$forceUpdate();
                },
                orderChange: function () {
                    if ( data.orderby === data.lastorderby && data.orderdir === data.lastorderdir ) {
                        return;
                    }
                    if ( data.orderby === 'custom' ) {
                        data.items.sort( function ( a, b ) {
                            return a.order - b.order;
                        } );
                    }
                    if ( data.orderby === 'keyword' ) {
                        data.items.sort( function ( a, b ) {
                            return a.keyword.localeCompare( b.keyword );
                        } );
                    }
                    if ( [ 'title', 'headers', 'content', 'url', 'marked', 'images' ].indexOf( data.orderby ) !== -1 ) {
                        data.items.sort( function ( a, b ) {
                            return ( isNaN( a.stats[data.orderby] ) ? -1 : a.stats[data.orderby] )
                                - ( isNaN( b.stats[data.orderby] ) ? -1 : b.stats[data.orderby] );
                        } );
                    }
                    if ( data.orderby === 'density' ) {
                        data.items.sort( function ( a, b ) {
                            return ( isNaN( a.density.content ) ? -1 : a.density.content )
                                - ( isNaN( b.density.content ) ? -1 : b.density.content );
                        } );
                    }
                    if ( data.orderby !== 'custom' && data.orderdir === 'desc' ) {
                        data.items.reverse();
                    }
                    var ts = +new Date();
                    data.items = data.items.map( function ( x ) {
                        x.uuid = '_' + CryptoJS.MD5( x.keyword ) + ts;
                        return x;
                    } );
                    data.lastorderby = data.orderby;
                    data.lastorderdir = data.orderdir;
                },
                isCompare: function () {
                    return data.compare.competitor !== null;
                },
                isCompareReady: function () {
                    return data.compare.competitor !== null && data.compare.data !== null;
                },
                isCompareStatusOk: function () {
                    return this.isCompareReady() && data.compare.data.status === 'OK';
                },
                getCompareType: function () {
                    return data.compare.type !== null ? data.compare.type : 'competitor';
                },
                endCompare: function () {
                    data.compare.competitor = null;
                    data.compare.data = null;
                    data.compare.type = null;
                },
                removeSnapshot: function ( timestamp ) {
                    $( document ).trigger( 'cmsk-snapshot-remove', $.extend( { }, { timestamp: timestamp } ) );
                    this.endCompare();
                },
                refreshCompare: function () {
                    $( document ).trigger( 'cmsk-competitor-compare', [ data.compare.competitor, true ] );
                },
                competitoritem: function ( keyword, prop ) {
                    return this.item( getCompetitorKeywordData( keyword ), prop );
                },
                competitoraltitem: function ( keyword, prop ) {
                    return this.item( getCompetitorKeywordData2( keyword ), prop );
                },
                isCompareAll: function () {
                    return data.compareall.competitors !== null;
                },
                isCompareAllReady: function () {
                    return data.compareall.competitors !== null && data.compareall.data !== null && data.compareall.compared === Object.keys( data.compareall.competitors ).length;
                },
                isCompareAllStatusOk: function () {
                    return this.isCompareAll() && data.compareall.data !== null;
                },
                endCompareAll: function () {
                    data.compareall.competitors = null;
                    data.compareall.data = null;
                    data.compareall.compared = 0;
                },
                refreshCompareAll: function () {
                    $( document ).trigger( 'cmsk-competitor-compare-all', [ data.compareall.competitors, true ] );
                },
                refreshCompareTop: function () {
                    $( document ).trigger( 'cmsk-competitor-compare-top', [ data.compareall.competitors, true ] );
                },
                refreshCompareSearch: function () {
                    $( document ).trigger( 'cmsk-competitor-compare-search', [ data.compareall.competitors, true ] );
                },
                compareallitem: function ( keyword, prop ) {
                    var res = this.item( getCompareAllKeywordData( keyword ), prop );
                    if ( isNaN( parseInt( res ) ) ) {
                        return 0;
                    }
                    return parseInt( res );
                },
                compareallaltitem: function ( keyword, prop ) {
                    var res = this.item( getCompareAllKeywordData2( keyword ), prop );
                    if ( isNaN( parseInt( res ) ) ) {
                        return 0;
                    }
                    return parseInt( res );
                },
                isopportunityalt: function ( item ) {
                    var competitorData = getCompetitorKeywordData( item.keyword );
                    if ( !competitorData ) {
                        return false;
                    } else {
                        var owndensity = this.item( item, 'density' );
                        var competitordensity = this.item( competitorData, 'density' );

                        if ( owndensity === '0.0' && competitordensity !== '0.0' ) {
                            return true;
                        }
                    }
                    return false;
                },
                isopportunity: function ( item, field ) {
                    var factor = 2;
                    var competitoramount = this.competitoritem( item.keyword, field );
                    var thisamount = item.stats[field];
                    var result = competitoramount >= thisamount + factor && competitoramount >= factor * thisamount;
                    return result;
                },
                isopportunityall: function ( item, field ) {
                    var result = item.stats[field] === 0 && this.compareallitem( item.keyword, field ) !== 0;
                    return result;
                },
                getcomparetooltip: function ( keyword, prop ) {
                    var min = 0, max = 0, average = 0, sum = 0, average2 = 0, count = 0, result = '';
                    min = this.item( getCompareAllKeywordData( keyword ), prop + '_min' );
                    if ( isNaN( parseInt( min ) ) ) {
                        min = 0;
                    }
                    max = this.item( getCompareAllKeywordData( keyword ), prop + '_max' );
                    if ( isNaN( parseInt( max ) ) ) {
                        max = 0;
                    }
                    sum = this.item( getCompareAllKeywordData( keyword ), prop + '_sum' );
                    if ( isNaN( parseInt( sum ) ) ) {
                        sum = 0;
                    }
                    average = data.compareall.compared != 0 ? sum / data.compareall.compared : 0;

                    count = this.item( getCompareAllKeywordData( keyword ), prop );
                    average2 = count != 0 ? sum / count : 0;
                    result = 'USED BY COMPETITORS\r\n\r\n Min: ' + min + "\r\n Max: " + max + "\r\n Average: " + Number.parseFloat( average ).toPrecision( 2 ) + "\r\n Average(excluding empty): " + Number.parseFloat( average2 ).toPrecision( 2 );
                    return result;
                },
                getaltkeywordscount: function ( ) {
                    var altkeywordscount = 0;
                    $.each( vm.items, function ( i, item ) {
                        altkeywordscount += item.altkeywords.length;
                    } );
                    return altkeywordscount;
                }
            },
            updated: function () {
                var d = preparePostData( data );
                if ( lastSavedData === JSON.stringify( d.items ) ) {
                    return;
                }
                if ( ajax1xhr !== null ) {
                    try {
                        ajax1xhr.abort();
                    } catch ( e ) {
                    }
                }
                lastSavedData = JSON.stringify( d.items );
                $( '#cmsk1-data' ).val( JSON.stringify( d ) );
                $( '.cmsk_keywords_ajax1_loading' ).removeClass( 'hidden' );
                ajax1xhr = $.ajax( {
                    method: 'POST',
                    url: ajaxurl,
                    cache: false,
                    data: {
                        action: 'fe872a92eacbbda07d755d29195e4e5a3bfaa5a2',
                        post_id: config.post_id,
                        cmsk_keywords_nonce: $( '#cmsk_keywords_nonce' ).val(),
                        cmsk1_data: JSON.stringify( d )
                    }
                } ).done( function ( data ) {
                    try {
                        if ( data.result ) {
                            data2 = data.data2;
                            vm.items = data.data.items;
                            window.cmsk_counters[cmsk1Config.unique_id].keywords = vm.items.length;
                            window.cmsk_counters[cmsk1Config.unique_id].alternate = vm.getaltkeywordscount();
                            $( '.cmsk_keywords_ajax1_error' ).addClass( 'hidden' );
                        } else {
                            if(data.error && data.error.length){
                                alert(data.error);
                            }
                            $( '.cmsk_keywords_ajax1_error' ).removeClass( 'hidden' );
                        }
                    } catch ( e ) {
                        $( '.cmsk_keywords_ajax1_error' ).removeClass( 'hidden' );
                    }
                } ).fail( function () {
                    $( '.cmsk_keywords_ajax1_error' ).removeClass( 'hidden' );
                } ).always( function () {
                    $( '.cmsk_keywords_ajax1_loading' ).addClass( 'hidden' );
                    ajax1xhr = null;
                } );
            },
            mounted: function () {
                liveFilter( '#cmsk-keyword-filter', '#cmsk1-list .cmsk1-section-area section', { selector: 'div:eq(0)' } );
                $( '#cmsk-keyword-filter' ).on( 'search', function () {
                    $( this ).trigger( 'input' );
                    $( this ).trigger( 'change' );
                } );
                $( '#cmsk1' ).sortable( {
                    items: 'section',
                    handle: '*[data-drag-handle]',
                    cancel: '*[data-drag-disabled]',
                    stop: function ( event, ui ) {
                        var newItems = [ ];
                        var oldItems = JSON.parse( JSON.stringify( vm.items ) );
                        $( '#cmsk1 section' ).each( function () {
                            var tmp = oldItems[$( this ).data( 'index' )];
                            tmp.uuid = '_' + Math.random(); // MUST be something new because vuejs freak out
                            newItems.push( tmp );
                        } );
                        vm.items = newItems;
                    }
                } );
            },
            filters: {
                ts2datetime: function ( s ) {
                    return moment( s * 1000 ).format( 'llll' );
                }
            }
        } );
        $( '#post' ).on( 'submit', function () {
            // moved to ajax update
            //$('#cmsk1-data').val(JSON.stringify(data));
        } );

        /*
         * dropdown menu
         */
        $( '#cmsk1' ).on( 'mouseenter', '.cmsk-dropbtn', function () {
            $( this ).siblings( '.cmsk-dropdown-content' ).css( {
                display: 'block',
                top: $( this ).position().top + $( this ).height(),
                right: $( '#cmsk1' ).width() - $( this ).width() - $( this ).position().left
            } );
            $( '#cmsk1' ).find( '.cmsk-dropdown-content-hidden' ).removeClass( 'cmsk-dropdown-content-hidden' );
        } ).on( 'mouseleave', '.cmsk-dropdown', function () {
            $( '#cmsk1' ).find( '.cmsk-dropdown-content' ).css( {
                display: 'none'
            } );
        } );

        /*
         * Mark keyword
         */
        $( '#cmsk1' ).on( 'click', '.cmsk1-action-mark', function () {
            if ( !isEditorActive() ) {
                swal( 'Oops...', 'Switch to visual editor first', 'error' );
                return;
            }
            $( '#cmsk1' ).find( '.cmsk-dropdown-content' ).addClass( 'cmsk-dropdown-content-hidden' );

            var index = $( this ).data( 'index' );
            var o = vm.items[index];
            var hash = CryptoJS.MD5( o.keyword );
            var editor = getEditor();
            var count = 0;

            var editorMark = function ( keyword, hash ) {
                editor.mark( keyword, {
                    separateWordSearch: false,
                    className: 'cmsk-mark{0} cmsk-mark{1}'.replace( '{0}', parseInt( index ) % 10 ).replace( '{1}', hash ),
                    accuracy: {
                        value: 'exactly',
                        limiters: [ ',', '.', ':' ]
                    },
                    done: function ( c ) {
                        count += c;
                    }
                } );
            };

            editor.unmark( {
                className: 'cmsk-mark{1}'.replace( '{1}', hash )
            } );

            editorMark( o.keyword, hash );

            $.each( o.altkeywords, function ( i, keyword ) {
                editorMark( keyword, hash );
            } );

            if ( count > 0 ) {
                $( '#cmsk1-unmark' ).removeClass( 'hidden' );
            } else {
                swal( {
                    title: 'Keyword not found',
                    html: 'Your post content has no <span class="cmsk-keyword">{0}</span> keyword.'.replace( '{0}', $( '<div/>' ).text( o.keyword ).html() ),
                    type: 'error'
                } ).catch( swal.noop );
            }
        } );

        /*
         * Unmark on editor switch
         */
        $( '#content-html' ).on( 'click', function () {
            getEditor().unmark();
            $( '#cmsk1-unmark' ).addClass( 'hidden' );
        } );

        /*
         * Unmark
         */
        $( '#cmsk1-unmark' ).on( 'click', function () {
            if ( !isEditorActive() ) {
                swal( 'Oops...', 'Switch to visual editor first', 'error' );
                return;
            }
            getEditor().unmark();
            $( '#cmsk1-unmark' ).addClass( 'hidden' );
        } );

        /*
         * Note edit
         */
        $( '#cmsk1' ).on( 'click', '.cmsk1-action-note', function () {
            var _this = this;
            var index = $( _this ).data( 'index' );
            var o = vm.items[index];
            var app;

            swal( {
                title: 'Keyword Note',
                html: $( '#cmsk1-tpl-keyword-note' ).html(),
                confirmButtonText: 'Update',
                showCancelButton: true,
                width: 600,
                onOpen: function ( modal ) {
                    var timestamp = ( o.note.timestamp ? ' Last updated: {0}.'.replace( '{0}', new Date( o.note.timestamp ).toLocaleString() ) : '' );
                    app = new Vue( {
                        el: '#cmsk1-tpl-keyword-note-content',
                        data: {
                            note: o.note.content,
                            timestamp: timestamp,
                            keyword: o.keyword,
                            color: o.note.color
                        },
                        methods: {
                            getColorLabel: function ( color ) {
                                if ( null === color || typeof config.colorlabels[color] === 'undefined' ) {
                                    return '-Label not set-';
                                }
                                return config.colorlabels[color];
                            }
                        }
                    } )
                },
                preConfirm: function () {
                    return new Promise( function ( resolve, reject ) {
                        resolve( {
                            content: app.note,
                            color: app.color
                        } );
                    } );
                }
            } ).then( function ( value ) {

                o.note.content = value.content;
                o.note.color = value.color;
                o.note.timestamp = +new Date();
                if ( o.note.content.length == 0 ) {
                    o.note.timestamp = 0;
                }
                Vue.set( vm.items, index, o );
                vm.$forceUpdate();

                swal.close();
            } ).catch( swal.noop );
        } );

        /*
         * Web search
         */
        $( '#cmsk1' ).on( 'click', '.cmsk1-action-websearch', function () {
            var index = $( this ).data( 'index' );
            var o = vm.items[index];
            var selected = $( this ).data( 'altkeyword' );
            if ( typeof selected !== 'undefined' ) {
                selected = selected.toString();
            } else {
                selected = o.keyword;
            }

            swal( {
                confirmButtonText: 'Close',
                html: $( '#cmsk1-tpl-websearch' ).html(),
                onOpen: function () {
                    new Vue( {
                        el: '#cmsk1-app-websearch',
                        data: {
                            keyword: selected,
                            options1: [ o.keyword ],
                            options2: o.altkeywords
                        },
                        mounted: function () {

                        },
                        methods: {
                            url: function ( url, keyword ) {
                                return url.replace( '{0}', encodeURIComponent( keyword ) )
                            }
                        }
                    } );
                }
            } ).catch( swal.noop );
        } );

        /*
         * Remove keyword
         */
        $( '#cmsk1' ).on( 'click', '.cmsk1-action-remove', function () {
            var index = $( this ).data( 'index' );
            var o = vm.items[index];
            swal( {
                title: 'Are you sure?',
                html: 'Are you sure you want to remove <span class="cmsk-keyword">{0}</span> keyword. You won\'t be able to revert this!'.replace( '{0}', $( '<div/>' ).text( o.keyword ).html() ),
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Remove'
            } ).then( function () {
                config.deletedkeywords.push( o.keyword );
                Vue.delete( vm.items, index );
                swal.close();
            } ).catch( swal.noop );
        } );

        $( '#cmsk1' ).on( 'click', '.cmsk1-action-thesarus', function () {
            var index = $( this ).data( 'index' );
            var o = vm.items[index];
            var keyword = o.keyword;

            var load_synonyms = function () {
                $.ajax( {
                    method: 'POST',
                    url: ajaxurl,
                    cache: false,
                    beforeSend: function () {
                        $( '#cmsk1-tpl-thesarus-content' ).addClass( 'ajax' );
                    },
                    data: {
                        action: 'cmsk_get_synonym',
                        keyword: keyword
                    }
                } ).done( function ( response ) {
                    if ( response.success ) {
                        var array = [ ];
                        if ( typeof response.data != 'undefined' &&
                            typeof response.data.noun != 'undefined' &&
                            response.data.noun.syn != 'undefined' ) {

                            array = response.data.noun.syn;
                            array = array.filter( function ( x ) {
                                return !isKeywordExists( x ) && !isAlternateKeywordExists( x );
                            } );
                            if ( array.length ) {
                                new Vue( {
                                    el: '#cmsk1-tpl-thesarus-content',
                                    data: {
                                        items: array,
                                        keyword: keyword
                                    }
                                } );
                            } else {
                                swal.closeModal();
                                swal( 'Keyword Thesarus', 'No results were found for <span class="cmsk-keyword">{0}</span>.'.replace( '{0}', $( '<div/>' ).text( keyword ).html() ), 'error' ).catch( swal.noop );
                            }
                        } else {
                            swal.closeModal();
                            swal( 'Keyword Thesarus', 'No results were found for <span class="cmsk-keyword">{0}</span>.'.replace( '{0}', $( '<div/>' ).text( keyword ).html() ), 'error' ).catch( swal.noop );
                        }
                    } else {
                        swal.closeModal();
                        swal( 'Error!', response.data, 'error' );
                    }
                    $( '#cmsk1-tpl-thesarus-content' ).removeClass( 'ajax' );
                } );
            };

            swal( {
                //title: 'Thesaurus results for <span class="cmsk-keyword">{0}</span>'.replace('{0}', $('<div/>').text(keyword).html()),
                title: 'Keyword Thesaurus',
                html: $( '#cmsk1-tpl-thesarus' ).html(),
                confirmButtonText: 'Save',
                cancelButtonText: 'Close',
                showCancelButton: true,
                width: 800,
                onOpen: load_synonyms,
                preConfirm: function () {
                    return new Promise( function ( resolve ) {
                        resolve( {
                            appMode: $( 'input[name="thesaurus-app-mode"]:checked' ).val() === '1' ? 'add' : 'merge'
                        } );
                    } )
                }
            } ).then( function ( resolve ) {
                var keywords = $( 'body' ).data( 'cmsk_thesaurus_checkboxes' );
                $( 'body' ).data( 'cmsk_thesaurus_checkboxes', [ ] );
                if ( typeof ( keywords ) != 'undefined' && keywords.length > 0 ) {
                    keywords = keywords.map( function ( x ) {
                        return $.trim( x ).toLowerCase();
                    } );
                    var unique_keywords = keywords.filter( function ( x ) {
                        return !isKeywordExists( x ) && !isAlternateKeywordExists( x );
                    } );
                    if ( unique_keywords.length ) {
                        if ( resolve.appMode === 'add' ) {
                            addKeywords( unique_keywords.map( function ( x ) {
                                return { keyword: x };
                            } ) );
                        } else {
                            o.altkeywords = o.altkeywords.concat( unique_keywords );
                            o.altkeywords.sort( function ( a, b ) {
                                return a.toLowerCase().localeCompare( b.toLowerCase() );
                            } );
                            $.each( unique_keywords, function ( i, keyword ) {
                                changelog( index, labels.changelog.altkeywordadded.replace( '{0}', keyword ) );
                            } );
                            o.stats = keywordDataTemplate.stats;
                            o.density = keywordDataTemplate.density;
                            Vue.set( vm.items, index, o );
                        }
                    } else {
                        swal( 'No new keywords', 'No new keywords were found.' ).catch( swal.noop );
                    }
                }
            } ).catch( swal.noop );
        } );

        $( 'body' ).on( 'click cmsk_change', '#cmsk1-tpl-thesarus-content input[type="checkbox"]:not(.cmsk_select_all)', function () {
            var array = [ ];
            $( '#cmsk1-tpl-thesarus-content input[type="checkbox"]:checked:not(.cmsk_select_all)' ).each( function () {
                array.push( $( this ).val() );
            } );
            $( 'body' ).data( 'cmsk_thesaurus_checkboxes', array );
        } );

        $( 'body' ).on( 'click', '#cmsk1-tpl-thesarus-content .cmsk_select_all', function () {
            $( '#cmsk1-tpl-thesarus-content input[type="checkbox"]:not(.cmsk_select_all)' ).prop( 'checked', true )
                .filter( ':first' ).trigger( 'cmsk_change' );
        } );

        /*
         * Add new keyword
         */
        $( '#cmsk1-add' ).on( 'click', function () {
            var whitelist = [ ];
            var invalidKeyword = null;
            swal( {
                title: 'New Keyword',
                html: 'Add new keyword to monitor. Use comma to separate multiple keywords. Statistics for new keywords will be updated after post save. All keywords will be standardized to lower case.',
                inputPlaceholder: 'keyword',
                input: 'text',
                confirmButtonText: 'Save',
                showCancelButton: true,
                inputValidator: function ( value ) {
                    if ( typeof ( $( '#swal-ignore-checkbox' ).attr( 'checked' ) ) !== 'undefined' ) {
                        whitelist.push( invalidKeyword );
                        removeKeywordFromDeleted( invalidKeyword );
                        vm.undeletedkeywords.push( invalidKeyword );
                    }
                    return new Promise( function ( resolve, reject ) {
                        var arr = value.split( ',' );
                        $.each( arr, function ( i, keyword ) {
                            keyword = $.trim( keyword ).toLowerCase();
                            if ( isKeywordExists( keyword ) || isAlternateKeywordExists( keyword ) ) {
                                reject( '"{0}" keyword already exists!'.replace( '{0}', $( '<div/>' ).text( keyword ).html() ) );
                                return false;
                            }
                            if ( wasKeywordDeleted( keyword ) && whitelist.indexOf( keyword ) === -1 ) {
                                invalidKeyword = keyword;
                                reject( '"{0}" keyword was already added and deleted!<br /><label><small><input type="checkbox" id="swal-ignore-checkbox"/> ignore this alert</small></label>'.replace( '{0}', $( '<div/>' ).text( keyword ).html() ) );
                                return false;
                            }
                        } );
                        if ( value ) {
                            resolve();
                        } else {
                            reject( 'You need to write something!' );
                        }
                    } );
                }
            } ).then( function ( result ) {
                var arr = result.split( ',' );
                addKeywords( arr.map( function ( x ) {
                    return { keyword: x };
                } ) );
                swal.close();
            } ).catch( swal.noop );
        } );

        /*
         * Add new keyword
         */
        $( document ).on( 'click', '#cmsk1-add-sample', function () {
            var whitelist = [ ];
            var invalidKeyword = null;
            swal( {
                title: 'Sample Keywords',
                html: 'Sample keywords are generated from your pages’ tags and categories. If no sample keywords are displayed, no categories or tags have been assigned to this page or post. All keywords will be standardized to lower case.',
                inputPlaceholder: 'keyword',
                input: 'text',
                inputValue: config.examplekeywords.join( ',' ),
                confirmButtonText: 'Save',
                showCancelButton: true,
                inputValidator: function ( value ) {
                    if ( typeof ( $( '#swal-ignore-checkbox' ).attr( 'checked' ) ) !== 'undefined' ) {
                        whitelist.push( invalidKeyword );
                        removeKeywordFromDeleted( invalidKeyword );
                        vm.undeletedkeywords.push( invalidKeyword );
                    }
                    return new Promise( function ( resolve, reject ) {
                        var arr = value.split( ',' );
                        $.each( arr, function ( i, keyword ) {
                            keyword = $.trim( keyword ).toLowerCase();
                            if ( isKeywordExists( keyword ) || isAlternateKeywordExists( keyword ) ) {
                                reject( '"{0}" keyword already exists!'.replace( '{0}', $( '<div/>' ).text( keyword ).html() ) );
                                return false;
                            }
                            if ( wasKeywordDeleted( keyword ) && whitelist.indexOf( keyword ) === -1 ) {
                                invalidKeyword = keyword;
                                reject( '"{0}" keyword was already added and deleted!<br /><label><small><input type="checkbox" id="swal-ignore-checkbox"/> ignore this alert</small></label>'.replace( '{0}', $( '<div/>' ).text( keyword ).html() ) );
                                return false;
                            }
                        } );
                        if ( value ) {
                            resolve();
                        } else {
                            reject( 'You need to write something!' );
                        }
                    } );
                }
            } ).then( function ( result ) {
                var arr = result.split( ',' );
                addKeywords( arr.map( function ( x ) {
                    return { keyword: x };
                } ) );
                swal.close();
            } ).catch( swal.noop );
        } );

        /*
         * Keyword edit
         */
        $( '#cmsk1' ).on( 'click', '.cmsk1-action-edit', function () {
            var index = $( this ).data( 'index' );
            var o = vm.items[index];
            var whitelist = [ ];
            var invalidKeyword = null;
            swal( {
                title: 'Edit Keyword',
                html: 'Statistics for changed keyword will be updated after post save. Keyword will be standardize to lower case.',
                inputPlaceholder: 'keyword',
                input: 'text',
                inputValue: o.keyword,
                confirmButtonText: 'Update',
                showCancelButton: true,
                inputValidator: function ( value ) {
                    if ( typeof ( $( '#swal-ignore-checkbox' ).attr( 'checked' ) ) !== 'undefined' ) {
                        whitelist.push( invalidKeyword );
                        removeKeywordFromDeleted( invalidKeyword );
                        vm.undeletedkeywords.push( invalidKeyword );
                    }
                    return new Promise( function ( resolve, reject ) {
                        var keyword = value;
                        keyword = $.trim( keyword );
                        if ( keyword === o.keyword ) {
                            resolve();
                        }
                        if ( isKeywordExists( keyword ) || isAlternateKeywordExists( keyword ) ) {
                            reject( '"{0}" keyword already exists!'.replace( '{0}', $( '<div/>' ).text( keyword ).html() ) );
                        }
                        if ( wasKeywordDeleted( keyword ) && whitelist.indexOf( keyword ) === -1 ) {
                            invalidKeyword = keyword;
                            reject( '"{0}" keyword was already added and deleted!<br /><label><small><input type="checkbox" id="swal-ignore-checkbox"/> ignore this alert</small></label>'.replace( '{0}', $( '<div/>' ).text( keyword ).html() ) );
                        }
                        if ( value ) {
                            resolve();
                        } else {
                            reject( 'You need to write something!' );
                        }
                    } );
                }
            } ).then( function ( result ) {
                var keyword = $.trim( result );
                if ( keyword !== o.keyword ) {
                    changelog( index, labels.changelog.keywordchanged.replace( '{0}', o.keyword ).replace( '{1}', keyword ) );
                    o.keyword = keyword;
                    o.stats = keywordDataTemplate.stats;
                    o.density = keywordDataTemplate.density;
                    Vue.set( vm.items, index, o );
                }
                swal.close();
            } ).catch( swal.noop );
        } );

        $( '#cmsk1' ).on( 'click', '.cmsk1-action-altkeywords-parent', function () {
            $( this ).parents( 'section' ).find( '.cmsk1-action-altkeywords' ).trigger( 'click' );
        } );

        /*
         * Alternate keywords edit
         */
        $( '#cmsk1' ).on( 'click', '.cmsk1-action-altkeywords', function () {
            var whitelist = [ ];
            var invalidKeyword = null;
            var _this = this;
            var index = $( _this ).data( 'index' );
            var o = vm.items[index];
            swal( {
                title: 'Alternate Keywords',
                html: 'Alternate keywords for <span class="cmsk-keyword">{0}</span> keyword. Use comma to separate multiple keywords. To change keywords list in future just open this form again.'.replace( '{0}', $( '<div/>' ).text( o.keyword ).html() ),
                input: 'textarea',
                inputValue: o.altkeywords.join( ",\r\n" ),
                confirmButtonText: 'Update',
                showCancelButton: true,
                inputValidator: function ( value ) {
                    if ( typeof ( $( '#swal-ignore-checkbox' ).attr( 'checked' ) ) !== 'undefined' ) {
                        whitelist.push( invalidKeyword );
                        removeKeywordFromDeleted( invalidKeyword );
                        vm.undeletedkeywords.push( invalidKeyword );
                    }
                    return new Promise( function ( resolve, reject ) {
                        var arr = value.toLowerCase().replace( /(\r\n)+/g, '' ).split( ',' );
                        $.each( arr, function ( i, keyword ) {
                            keyword = $.trim( keyword );
                            if ( isKeywordExists( keyword ) || isAlternateKeywordExists( keyword, o ) ) {
                                reject( '"{0}" keyword already exists!'.replace( '{0}', $( '<div/>' ).text( keyword ).html() ) );
                                return false;
                            }
                            if ( wasKeywordDeleted( keyword ) && whitelist.indexOf( keyword ) === -1 ) {
                                invalidKeyword = keyword;
                                reject( '"{0}" keyword was already added and deleted!<br /><label><small><input type="checkbox" id="swal-ignore-checkbox"/> ignore this alert</small></label>'.replace( '{0}', $( '<div/>' ).text( keyword ).html() ) );
                                return false;
                            }
                        } );
                        resolve();
                    } );
                }
            } ).then( function ( value ) {
                var arr = value.toLowerCase().split( ',' );
                var res = [ ];
                $.each( arr, function ( i, keyword ) {
                    keyword = $.trim( keyword );
                    if ( keyword.length && res.indexOf( keyword ) === -1 ) {
                        res.push( keyword );
                    }
                } );
                res.sort( function ( a, b ) {
                    return a.toLowerCase().localeCompare( b.toLowerCase() );
                } );
                if ( JSON.stringify( res ) != JSON.stringify( o.altkeywords ) ) {
                    $.each( o.altkeywords.filter( function ( x ) {
                        return res.indexOf( x ) < 0;
                    } ), function ( i, keyword ) {
                        changelog( index, labels.changelog.altkeywordeleted.replace( '{0}', keyword ) );
                    } );
                    $.each( res.filter( function ( x ) {
                        return o.altkeywords.indexOf( x ) < 0;
                    } ), function ( i, keyword ) {
                        changelog( index, labels.changelog.altkeywordadded.replace( '{0}', keyword ) );
                    } );
                    o.altkeywords = res;
                    o.stats = keywordDataTemplate.stats;
                    o.density = keywordDataTemplate.density;
                    Vue.set( vm.items, index, o );
                }
                swal.close();
            } ).catch( swal.noop );
        } );

        /*
         * Show and hide alternate keywords
         */
        $( '#cmsk1' ).on( 'click', '.cmsk1-action-show-altkeywords', function () {
            var _this = this;
            var index = $( _this ).data( 'index' );
            var o = vm.items[index];

            var foundPos = vm.inArray( o.keyword, data.expanded );
            if ( !data.allowmultipleexpanded ) {
                data.expanded = [ ];
            }
            if ( foundPos !== -1 ) {
                data.expanded.splice( foundPos, 1 );
            } else {
                data.expanded.push( o.keyword );
            }
        } );

        /*
         * Merge keyword
         */
        $( '#cmsk1' ).on( 'click', '.cmsk1-action-merge', function () {
            var _this = this;
            var index = $( _this ).data( 'index' );
            var o = vm.items[index];
            var options_arr = getKeywords();

//            options_arr = options_arr.sort( function ( a, b ) {
//                return a.val.localeCompare( b.val );
//            } );

            swal( {
                title: 'Merge Keywords',
                html: '<span class="cmsk-keyword">{0}</span> keyword and all related alternate keywords will be merged with alternate keywords of target.'.replace( '{0}', $( '<div/>' ).text( o.keyword ).html() ),
                input: 'select',
                showCancelButton: true,
                confirmButtonText: 'Merge',
                inputPlaceholder: 'Select target keyword',
                inputOptions: options_arr
            } ).then( function ( idx ) {
                if ( !$.isNumeric( idx ) ) {
                    return;
                }
                var target = vm.items[idx];
                target.altkeywords.push( o.keyword );
                changelog( idx, labels.changelog.altkeywordadded.replace( '{0}', o.keyword ) );
                $.each( o.altkeywords, function ( i, keyword ) {
                    target.altkeywords.push( keyword );
                    changelog( idx, labels.changelog.altkeywordadded.replace( '{0}', keyword ) );
                } );
                target.altkeywords.sort( function ( a, b ) {
                    return a.toLowerCase().localeCompare( b.toLowerCase() );
                } );
                target.stats = keywordDataTemplate.stats;
                target.density = keywordDataTemplate.density;
                Vue.set( vm.items, idx, target );
                Vue.delete( vm.items, index );
                swal.close();
            } ).catch( swal.noop );
        } );

        /*
         * Keyword statistics
         */
        $( '#cmsk1' ).on( 'click', '.cmsk1-action-stats', function () {

            var _this = this;
            var index = $( _this ).data( 'index' );
            var o = vm.items[index];

            var xhrTrendsDataUpdate = function () {
                $( '.cmsk_keywords_ajax1_loading' ).removeClass( 'hidden' );
                $.ajax( {
                    method: 'POST',
                    url: ajaxurl,
                    cache: false,
                    data: {
                        action: 'B691D87F05F4FF973018D6B11084593BC03675C6',
                        post_id: config.post_id
                    }
                } ).done( function ( d ) {
                    trends = d.data;
                    vm.$forceUpdate();
                } ).always( function () {
                    if ( ajax1xhr === null ) {
                        $( '.cmsk_keywords_ajax1_loading' ).addClass( 'hidden' );
                    }
                } );
            };
            var kickstart = function () {
                var defaultFormData = {
                    date: moment().format( 'YYYY-MM-DD' ), impressions: '', clicks: '', position: '', period: config.datacollectperiod
                };
                var data = { keyword: o.keyword, count: 0, limit: 4, form: { }, items: [ ] };
                data.form = $.extend( data.form, defaultFormData );
                var xhrDataUpdate = function () {
                    $.ajax( {
                        method: 'POST',
                        url: ajaxurl,
                        cache: false,
                        beforeSend: function () {
                            $( '#cmsk1-swal-app1' ).addClass( 'ajax' );
                        },
                        data: {
                            action: 'CE41340FB7A5B1ADCA871A563907E5C0B8D4F082',
                            post_id: config.post_id,
                            keyword: o.keyword,
                            limit: data.limit
                        }
                    } ).done( function ( d ) {
                        data.items = d.data;
                        data.count = d.count;
                    } ).always( function () {
                        $( '#cmsk1-swal-app1' ).removeClass( 'ajax' );
                    } );
                };
                new Vue( {
                    el: '#cmsk1-swal-app1',
                    data: data,
                    mounted: function () {
                        xhrDataUpdate();
                        $( '#swal-input1' ).datepicker( {
                            dateFormat: 'yy-mm-dd',
                            onSelect: function ( date ) {
                                data.form.date = date;
                            }
                        } );
                    },
                    methods: {
                        showColumn: function ( columnName ) {
                            return config.columns.indexOf( columnName ) > -1;
                        },
                        add: function () {
                            var d = {
                                action: 'A9B5B82C1ED20F2E1DB13FEB70A0BC655ED3BDCD',
                                post_id: config.post_id,
                                keyword: o.keyword
                            };
                            d = $.extend( d, data.form );
                            $.ajax( {
                                method: 'POST',
                                url: ajaxurl,
                                cache: false,
                                beforeSend: function () {
                                    $( '#cmsk1-swal-app1' ).addClass( 'ajax' );
                                },
                                data: d
                            } ).done( function ( d ) {
                                try {
                                    if ( d.result ) {
                                        data.form = $.extend( data.form, defaultFormData );
                                    }
                                } catch ( e ) {

                                }
                                xhrDataUpdate();
                                xhrTrendsDataUpdate();
                            } );
                        },
                        remove: function ( id ) {
                            $.ajax( {
                                method: 'POST',
                                url: ajaxurl,
                                cache: false,
                                beforeSend: function () {
                                    $( '#cmsk1-swal-app1' ).addClass( 'ajax' );
                                },
                                data: {
                                    action: 'A3A5EA13EF31D73DE7562F1003652B8F361571E0',
                                    post_id: config.post_id,
                                    keyword: o.keyword,
                                    id: id
                                }
                            } ).done( function ( d ) {
                                xhrDataUpdate();
                                xhrTrendsDataUpdate();
                            } );

                        },
                        more: function () {
                            data.limit += 4;
                            xhrDataUpdate();
                        },
                        gaLandingPagesURL: function () {
                            return '?cmsk-action=redirect-SelectedLandingPageForPostIdSearchDatesAndKeyword&post_id={0}&date00={1}&date01={2}&keyword={3}'
                                .replace( '{0}', config.post_id )
                                .replace( '{2}', moment( data.form.date ).format( 'YYYYMMDD' ) )
                                .replace( '{1}', moment( data.form.date ).subtract( data.form.period, 'd' ).format( 'YYYYMMDD' ) )
                                .replace( '{3}', o.keyword );
                        }
                    }
                } );
            };

            swal( {
                //title: '<span class="cmsk-keyword">{0}</span> Keyword Statistics'.replace('{0}', $('<div/>').text(o.keyword).html()),
                title: 'Keyword Statistics',
                html: $( '#cmsk1-tpl-swal-app1' ).html(),
                confirmButtonText: 'Close',
                width: 800,
                onOpen: kickstart
            } ).catch( swal.noop );
        } );
        /*
         * CSV Export
         */
        $( document ).on( 'click', '#cmsk1-export', function () {

            var form = document.createElement( "form" );
            form.setAttribute( "method", "POST" );
            form.setAttribute( "action", $( this ).data( 'form-action' ) );
            form.setAttribute( "target", '_blank' );

            var params = {
                action: $( this ).data( 'action' ),
                nonce: $( this ).data( 'nonce' ),
                post_id: $( this ).data( 'id' )
            };

            for ( var key in params ) {
                if ( params.hasOwnProperty( key ) ) {
                    var hiddenField = document.createElement( "input" );
                    hiddenField.setAttribute( "type", "hidden" );
                    hiddenField.setAttribute( "name", key );
                    hiddenField.setAttribute( "value", params[key] );
                    form.appendChild( hiddenField );
                }
            }

            document.body.appendChild( form );
            form.submit();
        } );

        /*
         * CSV Import
         */
        $( document ).on( 'click', '#cmsk1-import', function () {
            var _this = this;
            var type = $( _this ).parents( '#cmsk1-tpl-import-help-tab2' ).find( 'input[name="cmsk1-import-type"]:checked' );
            var msg = $( _this ).parents( '#cmsk1-tpl-import-help-tab2' ).find( '#cmsk1-import-msg' );
            var input = document.createElement( "input" );
            input.setAttribute( "name", "file" );
            input.setAttribute( "type", "file" );
            input.setAttribute( "accept", ".csv" );
            input.onchange = function () {
                $( '#cmsk1-import-msg' ).addClass( 'hidden' );
                var file = $( this )[0].files[0];
                var data = new FormData();
                data.append( "file", file, file.name );
                data.append( 'action', $( _this ).data( 'action' ) );
                data.append( 'nonce', $( _this ).data( 'nonce' ) );
                data.append( 'post_id', $( _this ).data( 'id' ) );
                data.append( 'type', type.val() );
                $.ajax( {
                    type: "POST",
                    url: $( _this ).data( 'form-action' ),
                    async: true,
                    data: data,
                    cache: false,
                    contentType: false,
                    processData: false,
                    timeout: 60000,
                    beforeSend: function () {
                        $( _this ).addClass( 'hidden' );
                        $( _this ).siblings( '.cmsk-spinner' ).removeClass( 'hidden' );
                    }
                } ).done( function ( d ) {
                    doBackup(); //we do a backup
                    if ( 'overwrite' === type.val() ) {
                        while ( vm.items.length > 0 ) {
                            vm.items.pop();
                        }
                    }
                    addKeywords( d.data );
                    var msg = 'Successfully imported ' + d.data.length + ' keywords.';
                    $( '#cmsk1-import-msg' ).html( msg );
                    $( '#cmsk1-import-msg' ).removeClass( 'hidden' );
                } ).always( function () {
                    $( _this ).siblings( '.cmsk-spinner' ).addClass( 'hidden' );
                    $( _this ).removeClass( 'hidden' );
                } );
            };
            input.click();
        } );

        /*
         * CSV restore Backup
         */
        $( document ).on( 'click', '#cmsk1-restore-backup', function () {
            $( '#cmsk1-import-msg' ).addClass( 'hidden' );
            restoreBackup();
        } );

        /*
         * CSV import help
         */
        $( '#cmsk1-import-help' ).on( 'click', function () {
            var arr = vm.items.reduce( function ( a, b ) {
                a.push( b.keyword );
                a = a.concat( b.altkeywords );
                return a;
            }, [ ] );
            arr.sort( function ( a, b ) {
                return a.toLowerCase().localeCompare( b.toLowerCase() );
            } );
            swal( {
                width: 600,
                showConfirmButton: false,
                html: $( '#cmsk1-tpl-import-help' ).html(),
                onOpen: function () {
                    new Vue( {
                        el: '#cmsk1-app-flatexport',
                        data: {
                            all: arr.join( ', ' )
                        }
                    } );
                }
            } ).catch( swal.noop );
        } );

//        /*
//         * CSV import help
//         */
//        $( '#cmsk1-flatexport' ).on( 'click', function () {
//            var arr = vm.items.reduce( function ( a, b ) {
//                a.push( b.keyword );
//                a = a.concat( b.altkeywords );
//                return a;
//            }, [ ] );
//            arr.sort( function ( a, b ) {
//                return a.toLowerCase().localeCompare( b.toLowerCase() );
//            } );
//            swal( {
//                confirmButtonText: 'Close',
//                html: $( '#cmsk1-tpl-flatexport' ).html(),
//                onOpen: function () {
//                    new Vue( {
//                        el: '#cmsk1-app-flatexport',
//                        data: {
//                            all: arr.join( ', ' )
//                        }
//                    } );
//                }
//            } ).catch( swal.noop );
//        } );

        /*
         * Turn alt keyword into main
         */
        $( '#cmsk1' ).on( 'click', '.cmsk1-action-alttomain', function () {
            var index = $( this ).data( 'index' );
            var kw = $( this ).data( 'altkeyword' );
            swal( {
                title: 'Are you sure?',
                html: 'Are you sure you want to turn <span class="cmsk-keyword">{0}</span> alternate keyword into main keyword? You won\'t be able to revert this!'.replace( '{0}', $( '<div/>' ).text( kw ).html() ),
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Make Primary'
            } ).then( function () {
                var o = vm.items[index];
                var removedKeyword = kw.toString();
                var i = o.altkeywords.indexOf( removedKeyword );
                if ( i > -1 ) {
                    o.altkeywords.splice( i, 1 );
                    changelog( index, labels.changelog.altkeywordeleted.replace( '{0}', removedKeyword ) );
                    o.stats = keywordDataTemplate.stats;
                    o.density = keywordDataTemplate.density;
                }
                Vue.set( vm.items, index, o );

                var arr = removedKeyword.split( ',' );
                addKeywords( arr.map( function ( x ) {
                    return { keyword: x };
                } ) );
                swal.close();
            } ).catch( swal.noop );
        } );

        /*
         * Move alternate keyword from one main keyword to another
         */
        $( '#cmsk1' ).on( 'click', '.cmsk1-action-altreassign', function () {
            var _this = this;
            var kw = $( this ).data( 'altkeyword' );
            var parent = $( _this ).parents( 'section' );
            var parent_index = $( parent ).data( 'index' );
            var o = vm.items[parent_index];
            var options_arr = getKeywords( parent_index );

            swal( {
                title: 'Reassign Alternate Keyword',
                html: '<span class="cmsk-keyword">{0}</span> keyword will be reassigned as an alternate keyword of target.'.replace( '{0}', kw.toString() ),
                input: 'select',
                showCancelButton: true,
                confirmButtonText: 'Merge',
                inputPlaceholder: 'Select target keyword',
                inputOptions: options_arr
            } ).then( function ( idx ) {
                if ( !$.isNumeric( idx ) ) {
                    return;
                }

                /*
                 * Remove the current alternate keyword from the keyword list
                 * @type Vue.items|vm.items
                 */
                var o = vm.items[parent_index];
                var i = o.altkeywords.indexOf( kw.toString() );
                if ( i > -1 ) {
                    o.altkeywords.splice( i, 1 );
                    changelog( parent_index, labels.changelog.altkeywordeleted.replace( '{0}', kw.toString() ) );
                    o.stats = keywordDataTemplate.stats;
                    o.density = keywordDataTemplate.density;
                }
                Vue.set( vm.items, parent_index, o );

                /*
                 * Add the alternate keyword as alternate keyword of selected main keyword
                 * @type Vue.items|vm.items
                 */
                var target = vm.items[idx];
                target.altkeywords.push( kw );
                changelog( idx, labels.changelog.altkeywordadded.replace( '{0}', kw ) );

                target.altkeywords.sort( function ( a, b ) {
                    return a.toLowerCase().localeCompare( b.toLowerCase() );
                } );
                target.stats = keywordDataTemplate.stats;
                target.density = keywordDataTemplate.density;
                Vue.set( vm.items, idx, target );
                swal.close();
            } ).catch( swal.noop );
        } );

        /*
         * Remove alt keyword
         */
        $( '#cmsk1' ).on( 'click', '.cmsk1-action-altremove', function () {
            var index = $( this ).data( 'index' );
            var kw = $( this ).data( 'altkeyword' )
            swal( {
                title: 'Are you sure?',
                html: 'Are you sure you want to remove <span class="cmsk-keyword">{0}</span> alternate keyword. You won\'t be able to revert this!'.replace( '{0}', $( '<div/>' ).text( kw ).html() ),
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Remove'
            } ).then( function () {
                var o = vm.items[index];
                var i = o.altkeywords.indexOf( kw.toString() );
                if ( i > -1 ) {
                    o.altkeywords.splice( i, 1 );
                    changelog( index, labels.changelog.altkeywordeleted.replace( '{0}', kw.toString() ) );
                    o.stats = keywordDataTemplate.stats;
                    o.density = keywordDataTemplate.density;
                }
                Vue.set( vm.items, index, o );
                swal.close();
            } ).catch( swal.noop );
        } );

        /*
         * Competitor compare
         */
        var compareToken;
        $( document ).on( 'cmsk-competitor-compare', function ( e, d, refresh ) {
            vm.endCompare();
            vm.endCompareAll();
            data.compare.type = 'competitor';
            data.compare.competitor = d;
            compareToken = +new Date();
            $.ajax( {
                method: 'POST',
                url: ajaxurl,
                cache: false,
                data: {
                    action: 'AF4CC6BE73C35630829267D851D2EADE41319BBB',
                    token: compareToken,
                    post_id: config.post_id,
                    url: data.compare.competitor.url,
                    uatype: data.compare.competitor.uatype,
                    refresh: typeof ( refresh ) !== 'undefined' ? true : null
                }
            } ).done( function ( d ) {
                if ( d.token == compareToken ) {
                    data.compare.data = d;
                    data.compare.type = 'competitor';
                }
            } );
        } );

        var multipleCompetitorsCompare = function ( competitors, passedToken, refresh ) {

            var NaN2N = function ( s ) {
                return isNaN( s ) ? 0 : parseFloat( s );
            };
            var N201 = function ( i ) {
                return  i > 0 ? 1 : 0;
            };
            var getMin = function ( current, incoming ) {
                if ( typeof current === 'undefined' ) {
                    return incoming;
                } else {
                    if ( incoming === 0 ) {
                        return 0;
                    } else {
                        return Math.min( NaN2N( current ), NaN2N( incoming ) );
                    }
                }
            };
            var getMax = function ( current, incoming ) {
                return Math.max( NaN2N( current ), NaN2N( incoming ) );
            };
            var worker = function ( urls, tmp1, tmp2, token ) {
                if ( token != passedToken ) {
                    return;
                }
                if ( data.compareall.competitors === null ) {
                    return;
                }
                var url = urls.shift();
                $.ajax( {
                    method: 'POST',
                    url: ajaxurl,
                    cache: false,
                    data: {
                        action: 'AF4CC6BE73C35630829267D851D2EADE41319BBB',
                        token: token,
                        post_id: config.post_id,
                        url: url,
                        uatype: 'desktop',
                        refresh: typeof ( refresh ) !== 'undefined' ? true : null
                    }
                } ).done( function ( d ) {
                    data.compare.type = 'competitor';
                    if ( d.token == passedToken ) {
                        if ( data.compareall.data === null ) {
                            data.compareall.data = { items1: null, items2: null };
                        }
                        $.each( d.items1, function ( i, item ) {
                            if ( typeof ( tmp1[item.keyword] ) === 'undefined' ) {
                                tmp1[item.keyword] = { keyword: item.keyword, stats: { }, density: { } };
                            }
                            tmp1[item.keyword].stats.title = NaN2N( tmp1[item.keyword].stats.title ) + N201( NaN2N( item.stats.title ) );
                            tmp1[item.keyword].stats.title_sum = NaN2N( tmp1[item.keyword].stats.title_sum ) + NaN2N( item.stats.title );
                            tmp1[item.keyword].stats.title_min = getMin( tmp1[item.keyword].stats.title_min, NaN2N( item.stats.title ) );
                            tmp1[item.keyword].stats.title_max = getMax( tmp1[item.keyword].stats.title_max, item.stats.title );
                            tmp1[item.keyword].stats.headers = NaN2N( tmp1[item.keyword].stats.headers ) + N201( NaN2N( item.stats.headers ) );
                            tmp1[item.keyword].stats.headers_sum = NaN2N( tmp1[item.keyword].stats.headers_sum ) + NaN2N( item.stats.headers );
                            tmp1[item.keyword].stats.headers_min = getMin( tmp1[item.keyword].stats.headers_min, NaN2N( item.stats.headers ) );
                            tmp1[item.keyword].stats.headers_max = getMax( tmp1[item.keyword].stats.headers_max, item.stats.headers );
                            tmp1[item.keyword].stats.content = NaN2N( tmp1[item.keyword].stats.content ) + N201( NaN2N( item.stats.content ) );
                            tmp1[item.keyword].stats.content_sum = NaN2N( tmp1[item.keyword].stats.content_sum ) + NaN2N( item.stats.content );
                            tmp1[item.keyword].stats.content_min = getMin( tmp1[item.keyword].stats.content_min, NaN2N( item.stats.content ) );
                            tmp1[item.keyword].stats.content_max = getMax( tmp1[item.keyword].stats.content_max, item.stats.content );
                            tmp1[item.keyword].stats.url = NaN2N( tmp1[item.keyword].stats.url ) + N201( NaN2N( item.stats.url ) );
                            tmp1[item.keyword].stats.url_sum = NaN2N( tmp1[item.keyword].stats.url_sum ) + NaN2N( item.stats.url );
                            tmp1[item.keyword].stats.url_min = getMin( tmp1[item.keyword].stats.url_min, NaN2N( item.stats.url ) );
                            tmp1[item.keyword].stats.url_max = getMax( tmp1[item.keyword].stats.url_max, item.stats.url );
                            tmp1[item.keyword].stats.first100 = NaN2N( tmp1[item.keyword].stats.first100 ) + N201( NaN2N( item.stats.first100 ) );
                            tmp1[item.keyword].stats.first100_sum = NaN2N( tmp1[item.keyword].stats.first100_sum ) + NaN2N( item.stats.first100 );
                            tmp1[item.keyword].stats.first100_min = getMin( tmp1[item.keyword].stats.first100_min, NaN2N( item.stats.first100 ) );
                            tmp1[item.keyword].stats.first100_max = getMax( tmp1[item.keyword].stats.first100_max, item.stats.first100 );
                            tmp1[item.keyword].stats.marked = NaN2N( tmp1[item.keyword].stats.marked ) + N201( NaN2N( item.stats.marked ) );
                            tmp1[item.keyword].stats.marked_sum = NaN2N( tmp1[item.keyword].stats.marked_sum ) + NaN2N( item.stats.marked );
                            tmp1[item.keyword].stats.marked_min = getMin( tmp1[item.keyword].stats.marked_min, NaN2N( item.stats.marked ) );
                            tmp1[item.keyword].stats.marked_max = getMax( tmp1[item.keyword].stats.marked_max, item.stats.marked );
                            tmp1[item.keyword].stats.images = NaN2N( tmp1[item.keyword].stats.images ) + N201( NaN2N( item.stats.images ) );
                            tmp1[item.keyword].stats.images_sum = NaN2N( tmp1[item.keyword].stats.images_sum ) + NaN2N( item.stats.images );
                            tmp1[item.keyword].stats.images_min = getMin( tmp1[item.keyword].stats.images_min, NaN2N( item.stats.images ) );
                            tmp1[item.keyword].stats.images_max = getMax( tmp1[item.keyword].stats.images_max, item.stats.images );
                            tmp1[item.keyword].density.content = NaN2N( tmp1[item.keyword].density.content ) + N201( NaN2N( item.density.content ) );
                            tmp1[item.keyword].density.first100 = NaN2N( tmp1[item.keyword].density.first100 ) + N201( NaN2N( item.density.first100 ) );
                        } );
                        data.compareall.data.items1 = tmp1;
                        $.each( d.items2, function ( i, item ) {
                            if ( typeof ( tmp2[item.keyword] ) === 'undefined' ) {
                                tmp2[item.keyword] = { keyword: item.keyword, stats: { }, density: { } };
                            }
                            tmp2[item.keyword].stats.title = NaN2N( tmp2[item.keyword].stats.title ) + N201( NaN2N( item.stats.title ) );
                            tmp2[item.keyword].stats.title_sum = NaN2N( tmp2[item.keyword].stats.title_sum ) + NaN2N( item.stats.title );
                            tmp2[item.keyword].stats.title_min = getMin( tmp2[item.keyword].stats.title_min, NaN2N( item.stats.title ) );
                            tmp2[item.keyword].stats.title_max = getMax( tmp2[item.keyword].stats.title_min, item.stats.title );
                            tmp2[item.keyword].stats.headers = NaN2N( tmp2[item.keyword].stats.headers ) + N201( NaN2N( item.stats.headers ) );
                            tmp2[item.keyword].stats.headers_sum = NaN2N( tmp2[item.keyword].stats.headers_sum ) + NaN2N( item.stats.headers );
                            tmp2[item.keyword].stats.headers_min = getMin( tmp2[item.keyword].stats.headers_min, NaN2N( item.stats.headers ) );
                            tmp2[item.keyword].stats.headers_max = getMax( tmp2[item.keyword].stats.headers_min, item.stats.headers );
                            tmp2[item.keyword].stats.content = NaN2N( tmp2[item.keyword].stats.content ) + N201( NaN2N( item.stats.content ) );
                            tmp2[item.keyword].stats.content_sum = NaN2N( tmp2[item.keyword].stats.content_sum ) + NaN2N( item.stats.content );
                            tmp2[item.keyword].stats.content_min = getMin( tmp2[item.keyword].stats.content_min, NaN2N( item.stats.content ) );
                            tmp2[item.keyword].stats.content_max = getMax( tmp2[item.keyword].stats.content_min, item.stats.content );
                            tmp2[item.keyword].stats.url = NaN2N( tmp2[item.keyword].stats.url ) + N201( NaN2N( item.stats.url ) );
                            tmp2[item.keyword].stats.url_sum = NaN2N( tmp2[item.keyword].stats.url_sum ) + NaN2N( item.stats.url );
                            tmp2[item.keyword].stats.url_min = getMin( tmp2[item.keyword].stats.url_min, NaN2N( item.stats.url ) );
                            tmp2[item.keyword].stats.url_max = getMax( tmp2[item.keyword].stats.url_min, item.stats.url );
                            tmp2[item.keyword].stats.first100 = NaN2N( tmp2[item.keyword].stats.first100 ) + N201( NaN2N( item.stats.first100 ) );
                            tmp2[item.keyword].stats.first100_sum = NaN2N( tmp2[item.keyword].stats.first100_sum ) + NaN2N( item.stats.first100 );
                            tmp2[item.keyword].stats.first100_min = getMin( tmp2[item.keyword].stats.first100_min, NaN2N( item.stats.first100 ) );
                            tmp2[item.keyword].stats.first100_max = getMax( tmp2[item.keyword].stats.first100_min, item.stats.first100 );
                            tmp2[item.keyword].stats.marked = NaN2N( tmp2[item.keyword].stats.marked ) + N201( NaN2N( item.stats.marked ) );
                            tmp2[item.keyword].stats.marked_sum = NaN2N( tmp2[item.keyword].stats.marked_sum ) + NaN2N( item.stats.marked );
                            tmp2[item.keyword].stats.marked_min = getMin( tmp2[item.keyword].stats.marked_min, NaN2N( item.stats.marked ) );
                            tmp2[item.keyword].stats.marked_max = getMax( tmp2[item.keyword].stats.marked_min, item.stats.marked );
                            tmp2[item.keyword].stats.images = NaN2N( tmp2[item.keyword].stats.images ) + N201( NaN2N( item.stats.images ) );
                            tmp2[item.keyword].stats.images_sum = NaN2N( tmp2[item.keyword].stats.images_sum ) + NaN2N( item.stats.images );
                            tmp2[item.keyword].stats.images_min = getMin( tmp2[item.keyword].stats.images_min, NaN2N( item.stats.images ) );
                            tmp2[item.keyword].stats.images_max = getMax( tmp2[item.keyword].stats.images_min, item.stats.images );
                            tmp2[item.keyword].density.content = NaN2N( tmp2[item.keyword].density.content ) + N201( NaN2N( item.density.content ) );
                            tmp2[item.keyword].density.first100 = NaN2N( tmp2[item.keyword].density.first100 ) + N201( NaN2N( item.density.first100 ) );
                        } );
                        data.compareall.data.items2 = tmp2;
                    }
                } ).always( function () {
                    if ( token != passedToken ) {
                        return;
                    }
                    data.compareall.compared = Object.keys( data.compareall.competitors ).length - urls.length;
                    if ( urls.length ) {
                        worker( urls, tmp1, tmp2, token );
                    }
                } );
            };
            worker( competitors, { }, { }, passedToken );
            return;
        };

        /*
         * Competitor all compare
         */
        var compareAllToken;
        $( document ).on( 'cmsk-competitor-compare-all', function ( e, d, refresh ) {
            vm.endCompare();
            vm.endCompareAll();
            data.compareall.competitors = d;
            data.compare.type = 'competitor';
            compareAllToken = +new Date();
            var arr = [ ];
            $.each( data.compareall.competitors, function ( i, item ) {
                arr.push( item.url );
            } );

            multipleCompetitorsCompare( arr, compareAllToken, refresh );
        } );

        /*
         * Competitor top compare
         */
        var compareTopToken;
        $( document ).on( 'cmsk-competitor-compare-top', function ( e, d, refresh ) {
            vm.endCompare();
            vm.endCompareAll();
            data.compareall.competitors = d;
            data.compare.type = 'competitor';
            compareTopToken = +new Date();
            var arr = [ ];
            var newcompetitors = { };
            $.each( data.compareall.competitors, function ( i, item ) {
                if ( item.is_main_competitor ) {
                    arr.push( item.url );
                    newcompetitors[i] = item;
                }
            } );
            data.compareall.competitors = newcompetitors;

            multipleCompetitorsCompare( arr, compareTopToken, refresh );
        } );

        /*
         * Competitor search compare
         */
        var compareSearchToken;
        $( document ).on( 'cmsk-competitor-compare-search', function ( e, d, refresh, phrase ) {
            vm.endCompare();
            vm.endCompareAll();
            data.compareall.competitors = d;
            data.compare.type = 'competitor';
            compareSearchToken = +new Date();
            var arr = [ ];
            var newcompetitors = { };
            $.each( data.compareall.competitors, function ( i, item ) {
                if ( $.inArray( phrase, item.phrases ) !== -1 ) {
                    arr.push( item.url );
                    newcompetitors[i] = item;
                }
            } );
            data.compareall.competitors = newcompetitors;

            multipleCompetitorsCompare( arr, compareSearchToken, refresh );
        } );

        $( document ).on( 'cmsk-snapshot-compare', function ( e, d, refresh ) {
            vm.endCompare();
            vm.endCompareAll();
            data.compare.type = 'snapshot';
            data.compare.competitor = d;
            compareToken = +new Date();
            $.ajax( {
                method: 'POST',
                url: ajaxurl,
                cache: false,
                data: {
                    action: '0B2F395F9B69A5930B7FC041A20D63AA51B38C9F',
                    token: compareToken,
                    timestamp: d.timestamp,
                    post_id: config.post_id,
                    url: 'n/a',
                    uatype: 'desktop',
                    refresh: typeof ( refresh ) !== 'undefined' ? true : null
                }
            } ).done( function ( d ) {
                if ( d.token == compareToken ) {
                    data.compare.type = 'snapshot';
                    data.compare.data = d;
                }
            } );
        } );

        $( document ).on( 'cmsk-recalculate-statistics', function ( e, d, refresh ) {
            lastSavedData = null; //required for a change without difference
            vm.$forceUpdate();
        } );

        $.ui.dialog.prototype._oldinit = $.ui.dialog.prototype._init;
        $.ui.dialog.prototype._init = function () {
            $( this.element ).parent().css( 'position', 'fixed' );
            $( this.element ).dialog( "option", {
                resizeStop: function ( event, ui ) {
                    var position = [ ( Math.floor( ui.position.left ) - $( window ).scrollLeft() ),
                        ( Math.floor( ui.position.top ) - $( window ).scrollTop() ) ];
                    $( event.target ).parent().css( 'position', 'fixed' );
                    // $(event.target).parent().dialog('option','position',position);
                    // removed parent() according to hai's comment (I didn't test it)
                    $( event.target ).dialog( 'option', 'position', position );
                    return true;
                }
            } );
            this._oldinit();
        };

        $( '.cmsk-dialog' ).dialog( {
            autoOpen: false,
            height: "auto",
            width: 450,
            title: 'Pinned Keywords',
            position: {
                my: "right middle",
                at: "right middle",
                of: window,
                collision: "none"
            },
            show: {
//                effect: "blind",
//                duration: 1000
            },
            hide: {
//                effect: "explode",
//                duration: 1000
            }
        } );

        $( ".cmsk-dialog-opener" ).on( "click", function () {
            $( ".cmsk-dialog" ).dialog( "open" );
        } );


    } );
} )( jQuery );
