<?php

use com\cminds\seokeywords\App;
use com\cminds\seokeywords\plugin\models;
use com\cminds\seokeywords\plugin\misc;

wp_enqueue_script( misc\Assets::JS_TOOLTIPS );
wp_enqueue_script( misc\Assets::JS_KEYWORDSMETABOX );
wp_enqueue_style( misc\Assets::CSS_SWEETALERT2 );
wp_enqueue_style( misc\Assets::CSS_KEYWORDSMETABOX );

global $post;

function _c0dda3175dfd9c04271cf0e20c89c75c2b1588e5() {
    $lut   = models\Options::getAllStatTableColumnsAssoc();
    $items = [ ];
    foreach ( models\Options::getStatTableColumns() as $item ) {
        $items[] = $lut[ $item ];
    }
    return join( ' / ', $items );
}

function _052066cdb01f0c81aedfc51f6cb7cfd3d21106dc( $key ) {
    $arr = models\Options::getAllStatTableColumnsAssoc();
    return $arr[ $key ];
}

function _73d6087b2dfd6da8b85868ed0a904a942dd3732c() {
    $items = [ ];
    foreach ( models\Options::getStatTableColumns() as $item ) {
        if ( $item == 'density' ) {
            $items[] = "{{contentDensity(item) + '%'}}";
        } else {
            $items[] = sprintf( '{{item.stats.%s}}', $item );
        }
    }
    return join( ' / ', $items );
}

function _c553b81a8e2366d495038ee9161b2694695d8e00() {
    $lut   = models\Options::getAllContentHeadersAssoc();
    $items = [ ];
    foreach ( models\Options::getContentHeaders() as $item ) {
        $items[] = $lut[ $item ];
    }
    return join( ', ', $items );
}
?>

<?php wp_nonce_field( 'metabox', $nonce ); ?>
<input type="hidden" name="cmsk1_data" id="cmsk1-data" value="" />
<div class="cmsk-tooltips-area <?php echo sprintf( 'cmsk-grid-%s', models\Options::getMetaboxGridDisplayDensity() ); ?>">
    <div id="cmsk1">
        <?php
        /*
         * Sub-view - metabox/parts/pinned.php
         */
        echo $dialog_content;
        ?>
        <div class="loading" v-cloak><span class="cmsk-spinner"></span></div>
        <p v-cloak v-if="items.length == 0">
            No monitored keywords. <a href="javascript:void(0)" class="button" id="cmsk1-add-sample" onclick="return false;">Get Sample Keywords</a>
        </p>
        <div id="cmsk1-list" v-cloak v-if="items.length > 0">
            <header>
                <div>
                    Keyword
                    <span v-if="orderby === 'keyword' && orderdir === 'asc'">&uarr;</span>
                    <span v-if="orderby === 'keyword' && orderdir === 'desc'">&darr;</span>
                    <input type="search" class="cmsk-input-short" id="cmsk-keyword-filter" placeholder="Search" />
                </div>
                <div class="space-around hide-on-postbox-container-1" v-show="showColumn('density')">
                    <?php echo _052066cdb01f0c81aedfc51f6cb7cfd3d21106dc( 'density' ); ?>
                    <span v-if="orderby === 'density' && orderdir === 'asc'">&uarr;</span>
                    <span v-if="orderby === 'density' && orderdir === 'desc'">&darr;</span>
                </div>
                <div class="space-around hide-on-postbox-container-1" v-show="showColumn('title')">
                    <?php echo _052066cdb01f0c81aedfc51f6cb7cfd3d21106dc( 'title' ); ?>
                    <span v-if="orderby === 'title' && orderdir === 'asc'">&uarr;</span>
                    <span v-if="orderby === 'title' && orderdir === 'desc'">&darr;</span>
                </div>
                <div class="space-around hide-on-postbox-container-1" v-show="showColumn('headers')">
                    <span data-title="Keywords in <?php echo _c553b81a8e2366d495038ee9161b2694695d8e00(); ?> tags"><?php echo _052066cdb01f0c81aedfc51f6cb7cfd3d21106dc( 'headers' ); ?></span>
                    <span v-if="orderby === 'headers' && orderdir === 'asc'">&uarr;</span>
                    <span v-if="orderby === 'headers' && orderdir === 'desc'">&darr;</span>
                </div>
                <div class="space-around hide-on-postbox-container-1" v-show="showColumn('content')">
                    <?php echo _052066cdb01f0c81aedfc51f6cb7cfd3d21106dc( 'content' ); ?>
                    <span v-if="orderby === 'content' && orderdir === 'asc'">&uarr;</span>
                    <span v-if="orderby === 'content' && orderdir === 'desc'">&darr;</span>
                </div>
                <div class="space-around hide-on-postbox-container-1" v-show="showColumn('url')">
                    <?php echo _052066cdb01f0c81aedfc51f6cb7cfd3d21106dc( 'url' ); ?>
                    <span v-if="orderby === 'url' && orderdir === 'asc'">&uarr;</span>
                    <span v-if="orderby === 'url' && orderdir === 'desc'">&darr;</span>
                </div>
                <div class="space-around hide-on-postbox-container-1" v-show="showColumn('first100')">
                    <?php echo _052066cdb01f0c81aedfc51f6cb7cfd3d21106dc( 'first100' ); ?>
                    <span v-if="orderby === 'first100' && orderdir === 'asc'">&uarr;</span>
                    <span v-if="orderby === 'first100' && orderdir === 'desc'">&darr;</span>
                </div>
                <div class="space-around hide-on-postbox-container-1" v-show="showColumn('marked')">
                    <span data-title="Keywords in STRONG, EM, B and I tags"><?php echo _052066cdb01f0c81aedfc51f6cb7cfd3d21106dc( 'marked' ); ?></span>
                    <span v-if="orderby === 'marked' && orderdir === 'asc'">&uarr;</span>
                    <span v-if="orderby === 'marked' && orderdir === 'desc'">&darr;</span>
                </div>
                <div class="space-around hide-on-postbox-container-1" v-show="showColumn('images')">
                    <span data-title="Images with keywords in ALT and TITLE attributes"><?php echo _052066cdb01f0c81aedfc51f6cb7cfd3d21106dc( 'images' ); ?></span>
                    <span v-if="orderby === 'images' && orderdir === 'asc'">&uarr;</span>
                    <span v-if="orderby === 'images' && orderdir === 'desc'">&darr;</span>
                </div>
                <div>

                </div>
            </header>
            <div style="position: relative;">
                <div class="cmsk1-section-area">
                    <section v-for="(item, index) in items" :key="item.uuid" v-bind:data-index="index">
                        <div class="cmsk-row cmsk-row-main">
                            <div data-drag-handle="true">
                                <a href="javascript:void(0)" class="cmsk1-action-edit cmsk-primary-text" v-bind:data-index="index" onclick="return false;" style="margin-right: 5px;">{{item.keyword}}</a>
                                <?php if ( models\Options::getKeywordMetaboxWarningNotFoundEnabled() ): ?>
                                    <span v-if="redflag(item)"><a href="javascript:void(0)" class="cmsk-alert-icon" data-title="This keyword is not found anywhere" onclick="return false;"><span class="dashicons dashicons-warning"></span></a></span>
                                <?php endif; ?>
                                <?php if ( models\Options::getKeywordMetaboxWarningDensityEnabled() ): ?>
                                    <span v-if="densityflag(item)"><a href="javascript:void(0)" class="cmsk-warning-icon" v-bind:data-title="'This keyword has too high density (about {0}%)'.replace('{0}', density(item))" onclick="return false;"><span class="dashicons dashicons-warning"></span></a></span>
                                <?php endif; ?>
                                <template v-if="item.altkeywords.length > 0">
                                    <a href="javascript:void(0)" class="cmsk1-action-show-altkeywords cmsk-icon" v-bind:data-index="index" data-drag-disabled="true" v-bind:data-title="item.altkeywords.join(', ')" data-subtitle="Alternate keywords" onclick="return false;">
                                        <span v-bind:class="{'dashicons': true, 'dashicons-networking': true, 'is-opportunity': isopportunityalt(item)}"></span>
                                    </a>
                                </template>
                            </div>
                            <!--                            <div class="space-around hide-on-postbox-container-2" data-drag-handle="true">
                            <?php echo _73d6087b2dfd6da8b85868ed0a904a942dd3732c(); ?>
                                                        </div>-->
                            <div class="space-around hide-on-postbox-container-1 stat-cell" data-drag-handle="true" v-show="showColumn('density')">
                                <span>
                                    <span v-bind:class="{'value-empty': contentDensity(item) == '0.0'}">
                                        {{contentDensity(item)}}%
                                    </span>
                                    <template v-if="isCompareStatusOk()">
                                        <span v-bind:class="{'compare-value': true, 'compare-value-empty': competitoritem(item.keyword, 'density') == '0.0'}">{{competitoritem(item.keyword, 'density')}}%</span>
                                    </template>
                                    <template v-if="isCompareAllStatusOk()">
                                        <span v-bind:class="{'compare-value': true, 'compare-value-empty': compareallitem(item.keyword, 'density') == 0}">{{compareallitem(item.keyword, 'density')}}/{{compareall.compared}}</span>
                                    </template>
                                </span>
                            </div>
                            <div class="space-around hide-on-postbox-container-1" data-drag-handle="true" v-show="showColumn('title')">
                                <span>
                                    <span v-bind:class="{'value-empty': item.stats.title == 0}">
                                        {{item.stats.title}}
                                    </span>
                                    <template v-if="isCompareStatusOk()">
                                        <span v-bind:class="{'compare-value': true, 'is-opportunity-all': isopportunity(item, 'title'), 'compare-value-empty': competitoritem(item.keyword, 'title') == 0}">{{competitoritem(item.keyword, 'title')}}</span>
                                    </template>
                                    <template v-if="isCompareAllStatusOk()">
                                        <span v-bind:data-title="getcomparetooltip(item.keyword, 'title')" v-bind:class="{'compare-value': true, 'is-opportunity-all': isopportunityall(item, 'title'), 'compare-value-empty': compareallitem(item.keyword, 'title') == 0}">{{compareallitem(item.keyword, 'title')}}/{{compareall.compared}}</span>
                                    </template>
                                </span>
                            </div>
                            <div class="space-around hide-on-postbox-container-1" data-drag-handle="true" v-show="showColumn('headers')">
                                <span>
                                    <span v-bind:class="{'value-empty': item.stats.headers == 0}">
                                        {{item.stats.headers}}
                                    </span>
                                    <template v-if="isCompareStatusOk()">
                                        <span v-bind:class="{'compare-value': true, 'is-opportunity-all': isopportunity(item, 'headers'), 'compare-value-empty': competitoritem(item.keyword, 'headers') == 0}">{{competitoritem(item.keyword, 'headers')}}</span>
                                    </template>
                                    <template v-if="isCompareAllStatusOk()">
                                        <span v-bind:data-title="getcomparetooltip(item.keyword, 'headers')" v-bind:class="{'compare-value': true, 'is-opportunity-all': isopportunityall(item, 'headers'), 'compare-value-empty': compareallitem(item.keyword, 'headers') == 0}">{{compareallitem(item.keyword, 'headers')}}/{{compareall.compared}}</span>
                                    </template>
                                </span>
                            </div>
                            <div class="space-around hide-on-postbox-container-1" data-drag-handle="true" v-show="showColumn('content')">
                                <span>
                                    <span v-bind:class="{'value-empty': item.stats.content == 0}">
                                        {{item.stats.content}}
                                    </span>
                                    <template v-if="isCompareStatusOk()">
                                        <span v-bind:class="{'compare-value': true, 'is-opportunity-all': isopportunity(item, 'content'), 'compare-value-empty': competitoritem(item.keyword, 'content') == 0}">{{competitoritem(item.keyword, 'content')}}</span>
                                    </template>
                                    <template v-if="isCompareAllStatusOk()">
                                        <span v-bind:data-title="getcomparetooltip(item.keyword, 'content')" v-bind:class="{'compare-value': true, 'is-opportunity-all': isopportunityall(item, 'content'), 'compare-value-empty': compareallitem(item.keyword, 'content') == 0}">{{compareallitem(item.keyword, 'content')}}/{{compareall.compared}}</span>
                                    </template>
                                </span>
                            </div>
                            <div class="space-around hide-on-postbox-container-1" data-drag-handle="true" v-show="showColumn('url')">
                                <span>
                                    <span v-bind:class="{'value-empty': item.stats.url == 0}">
                                        {{item.stats.url}}
                                    </span>
                                    <template v-if="isCompareStatusOk()">
                                        <span v-bind:class="{'compare-value': true, 'is-opportunity-all': isopportunity(item, 'url'), 'compare-value-empty': competitoritem(item.keyword, 'url') == 0}">{{competitoritem(item.keyword, 'url')}}</span>
                                    </template>
                                    <template v-if="isCompareAllStatusOk()">
                                        <span v-bind:data-title="getcomparetooltip(item.keyword, 'url')" v-bind:class="{'compare-value': true, 'is-opportunity-all': isopportunityall(item, 'url'), 'compare-value-empty': compareallitem(item.keyword, 'url') == 0}">{{compareallitem(item.keyword, 'url')}}/{{compareall.compared}}</span>
                                    </template>
                                </span>
                            </div>
                            <div class="space-around hide-on-postbox-container-1" data-drag-handle="true" v-show="showColumn('first100')">
                                <span>
                                    <span v-bind:class="{'value-empty': item.stats.first100 == 0}">
                                        {{item.stats.first100}}
                                    </span>
                                    <template v-if="isCompareStatusOk()">
                                        <span v-bind:class="{'compare-value': true, 'is-opportunity-all': isopportunity(item, 'first100'), 'compare-value-empty': competitoritem(item.keyword, 'first100') == 0}">{{competitoritem(item.keyword, 'first100')}}</span>
                                    </template>
                                    <template v-if="isCompareAllStatusOk()">
                                        <span v-bind:data-title="getcomparetooltip(item.keyword, 'first100')" v-bind:class="{'compare-value': true, 'is-opportunity-all': isopportunityall(item, 'first100'), 'compare-value-empty': compareallitem(item.keyword, 'first100') == 0}">{{compareallitem(item.keyword, 'first100')}}/{{compareall.compared}}</span>
                                    </template>
                                </span>
                            </div>
                            <div class="space-around hide-on-postbox-container-1" data-drag-handle="true" v-show="showColumn('marked')">
                                <span>
                                    <span v-bind:class="{'value-empty': item.stats.marked == 0}">
                                        {{item.stats.marked}}
                                    </span>
                                    <template v-if="isCompareStatusOk()">
                                        <span v-bind:class="{'compare-value': true, 'is-opportunity-all': isopportunity(item, 'marked'), 'compare-value-empty': competitoritem(item.keyword, 'marked') == 0}">{{competitoritem(item.keyword, 'marked')}}</span>
                                    </template>
                                    <template v-if="isCompareAllStatusOk()">
                                        <span v-bind:data-title="getcomparetooltip(item.keyword, 'marked')" v-bind:class="{'compare-value': true, 'is-opportunity-all': isopportunityall(item, 'marked'), 'compare-value-empty': compareallitem(item.keyword, 'marked') == 0}">{{compareallitem(item.keyword, 'marked')}}/{{compareall.compared}}</span>
                                    </template>
                                </span>
                            </div>
                            <div class="space-around hide-on-postbox-container-1" data-drag-handle="true" v-show="showColumn('images')">
                                <span>
                                    <span v-bind:class="{'value-empty': item.stats.images == 0}">
                                        {{item.stats.images}}
                                    </span>
                                    <template v-if="isCompareStatusOk()">
                                        <span v-bind:class="{'compare-value': true, 'is-opportunity-all': isopportunity(item, 'images'), 'compare-value-empty': competitoritem(item.keyword, 'images') == 0}">{{competitoritem(item.keyword, 'images')}}</span>
                                    </template>
                                    <template v-if="isCompareAllStatusOk()">
                                        <span v-bind:data-title="getcomparetooltip(item.keyword, 'images')" v-bind:class="{'compare-value': true, 'is-opportunity-all': isopportunityall(item, 'images'), 'compare-value-empty': compareallitem(item.keyword, 'images') == 0}">{{compareallitem(item.keyword, 'images')}}/{{compareall.compared}}</span>
                                    </template>
                                </span>
                            </div>
                            <div>
                                <template v-if="trend(item) === 0">
                                    <a href="javascript:void(0)" class="cmsk-action-trends-modal-chart cmsk-icon cmsk-trend-neutral-icon" v-bind:data-chart-config="trendsChartConfig(item)" v-bind:data-chart-title="trendsChartTitle(item)" data-title="Neutral trend" onclick="return false;"><span class="dashicons dashicons-menu"></span></a>
                                </template>
                                <template v-else-if="trend(item) === 1">
                                    <a href="javascript:void(0)" class="cmsk-action-trends-modal-chart cmsk-icon cmsk-trend-up-icon" v-bind:data-chart-config="trendsChartConfig(item)" v-bind:data-chart-title="trendsChartTitle(item)" data-title="Up trend" onclick="return false;"><span class="dashicons dashicons-chart-bar"></span></a>
                                </template>
                                <template v-else-if="trend(item) === -1">
                                    <a href="javascript:void(0)" class="cmsk-action-trends-modal-chart cmsk-icon cmsk-trend-down-icon" v-bind:data-chart-config="trendsChartConfig(item)" v-bind:data-chart-title="trendsChartTitle(item)" data-title="Down trend" onclick="return false;"><span class="dashicons dashicons-chart-bar"></span></a>
                                </template>
                                <template v-else>
                                    <!-- spacer only -->
                                    <span class="cmsk-visibility-hidden">
                                        <a href="javascript:void(0)" class="cmsk-icon cmsk-trend-neutral-icon" onclick="return false;"><span class="dashicons dashicons-chart-bar"></span></a>
                                    </span>
                                </template>
                                <!--<span v-bind:class="{'cmsk-visibility-hidden': item.note == null || item.note.content == null || item.note.content.length == 0}">-->
                                <span>
                                    <a href="javascript:void(0)" class="cmsk1-action-note cmsk-icon" v-bind:data-index="index" data-drag-disabled="true" v-bind:data-title="item.note.content" v-bind:data-subtitle="new Date(item.note.timestamp).toLocaleString()+'\nLabel: '+getColorLabel(item.note.color)" onclick="return false;"><span :style="'color:'+item.note.color" class="dashicons dashicons-admin-comments"></span></a>
                                </span>
                                <div class="cmsk-dropdown">
                                    <a href="javascript:void(0)" class="cmsk-dropbtn" onclick="return false;"><span class="dashicons dashicons-admin-generic"></span></a>
                                    <div class="cmsk-dropdown-content">
                                        <a href="javascript:void(0)" class="cmsk1-action-edit" v-bind:data-index="index" onclick="return false;">Edit Keyword</a>
                                        <a href="javascript:void(0)" class="cmsk1-action-altkeywords" v-bind:data-index="index" onclick="return false;">Alternate Keywords</a>
                                        <!--<a href="javascript:void(0)" class="cmsk1-action-note" v-bind:data-index="index" onclick="return false;">Add/Edit Note</a>-->
                                        <hr />
                                        <a href="javascript:void(0)" class="cmsk1-action-websearch" v-bind:data-index="index" onclick="return false;">Web Search <span class="dashicons dashicons-share-alt2"></span></a>
                                        <a href="javascript:void(0)" class="cmsk1-action-mark" v-bind:data-index="index" onclick="return false;">Mark in Content</a>
                                        <hr />
                                        <a href="javascript:void(0)" id="cmskl-action-pin" v-bind:data-index="index">
                                            <label>
                                                <input type="checkbox" v-model="item.pinned" />
                                                Pin Keyword
                                            </label>
                                        </a>
                                        <hr />
                                        <a href="javascript:void(0)" class="cmsk1-action-merge" v-bind:data-index="index" onclick="return false;">Merge Keyword</a>
                                        <a href="javascript:void(0)" v-bind:data-index="index" class="cmsk1-action-remove" onclick="return false;">Remove Keyword</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--<template v-if="inArray(item.keyword, expanded) !== -1">-->
                        <div v-bind:class="{'cmsk-row': true, 'cmsk-row-alt': true, 'cmsk-row-alt-main': keyword == item.keyword, 'hidden': true, 'expanded': inArray(item.keyword, expanded) !== -1}" v-for="(keyword, index2) in [item.keyword].concat(item.altkeywords)" v-bind:data-index="index2">
                            <div data-drag-handle="false">
                                <a href="javascript:void(0)" style="margin-left: 5px;" class="cmsk1-action-altkeywords-parent cmsk-primary-text" onclick="return false;">{{ keyword }}</a>
                            </div>
                            <div class="space-around hide-on-postbox-container-1" data-drag-handle="false" v-show="showColumn('density')">
                                <span>
                                    <span v-bind:class="{'value-empty': altitem(keyword, 'density') == '0.0'}">
                                        {{altitem(keyword, 'density')}}%
                                    </span>
                                    <template v-if="isCompareStatusOk()">
                                        <span v-bind:class="{'compare-value': true, 'compare-value-empty': competitoraltitem(keyword, 'density') == 0}">{{competitoraltitem(keyword, 'density')}}%</span>
                                    </template>
                                    <template v-if="isCompareAllStatusOk()">
                                        <span v-bind:class="{'compare-value': true, 'compare-value-empty': compareallaltitem(keyword, 'density') == 0}">{{compareallaltitem(keyword, 'density')}}/{{compareall.compared}}</span>
                                    </template>
                                </span>
                            </div>
                            <div class="space-around hide-on-postbox-container-1" data-drag-handle="false" v-show="showColumn('title')">
                                <span>
                                    <span v-bind:class="{'value-empty': altitem(keyword, 'title') == 0}">
                                        {{altitem(keyword, 'title')}}
                                    </span>
                                    <template v-if="isCompareStatusOk()">
                                        <span v-bind:class="{'compare-value': true, 'compare-value-empty': competitoraltitem(keyword, 'title') == 0}">{{competitoraltitem(keyword, 'title')}}</span>
                                    </template>
                                    <template v-if="isCompareAllStatusOk()">
                                        <span v-bind:class="{'compare-value': true, 'compare-value-empty': compareallaltitem(keyword, 'title') == 0}">{{compareallaltitem(keyword, 'title')}}/{{compareall.compared}}</span>
                                    </template>
                                </span>
                            </div>
                            <div class="space-around hide-on-postbox-container-1" data-drag-handle="false" v-show="showColumn('headers')">
                                <span>
                                    <span v-bind:class="{'value-empty': altitem(keyword, 'headers') == 0}">
                                        {{altitem(keyword, 'headers')}}
                                    </span>
                                    <template v-if="isCompareStatusOk()">
                                        <span v-bind:class="{'compare-value': true, 'compare-value-empty': competitoraltitem(keyword, 'headers') == 0}">{{competitoraltitem(keyword, 'headers')}}</span>
                                    </template>
                                    <template v-if="isCompareAllStatusOk()">
                                        <span v-bind:class="{'compare-value': true, 'compare-value-empty': compareallaltitem(keyword, 'headers') == 0}">{{compareallaltitem(keyword, 'headers')}}/{{compareall.compared}}</span>
                                    </template>
                                </span>
                            </div>
                            <div class="space-around hide-on-postbox-container-1" data-drag-handle="false" v-show="showColumn('content')">
                                <span>
                                    <span v-bind:class="{'value-empty': altitem(keyword, 'content') == 0}">
                                        {{altitem(keyword, 'content')}}
                                    </span>
                                    <template v-if="isCompareStatusOk()">
                                        <span v-bind:class="{'compare-value': true, 'compare-value-empty': competitoraltitem(keyword, 'content') == 0}">{{competitoraltitem(keyword, 'content')}}</span>
                                    </template>
                                    <template v-if="isCompareAllStatusOk()">
                                        <span v-bind:class="{'compare-value': true, 'compare-value-empty': compareallaltitem(keyword, 'content') == 0}">{{compareallaltitem(keyword, 'content')}}/{{compareall.compared}}</span>
                                    </template>
                                </span>
                            </div>
                            <div class="space-around hide-on-postbox-container-1" data-drag-handle="false" v-show="showColumn('url')">
                                <span>
                                    <span v-bind:class="{'value-empty': altitem(keyword, 'url') == 0}">
                                        {{altitem(keyword, 'url')}}
                                    </span>
                                    <template v-if="isCompareStatusOk()">
                                        <span v-bind:class="{'compare-value': true, 'compare-value-empty': competitoraltitem(keyword, 'url') == 0}">{{competitoraltitem(keyword, 'url')}}</span>
                                    </template>
                                    <template v-if="isCompareAllStatusOk()">
                                        <span v-bind:class="{'compare-value': true, 'compare-value-empty': compareallaltitem(keyword, 'url') == 0}">{{compareallaltitem(keyword, 'url')}}/{{compareall.compared}}</span>
                                    </template>
                                </span>
                            </div>
                            <div class="space-around hide-on-postbox-container-1" data-drag-handle="false" v-show="showColumn('first100')">
                                <span>
                                    <span v-bind:class="{'value-empty': altitem(keyword, 'first100') == 0}">
                                        {{altitem(keyword, 'first100')}}
                                    </span>
                                    <template v-if="isCompareStatusOk()">
                                        <span v-bind:class="{'compare-value': true, 'compare-value-empty': competitoraltitem(keyword, 'first100') == 0}">{{competitoraltitem(keyword, 'first100')}}</span>
                                    </template>
                                    <template v-if="isCompareAllStatusOk()">
                                        <span v-bind:class="{'compare-value': true, 'compare-value-empty': compareallaltitem(keyword, 'first100') == 0}">{{compareallaltitem(keyword, 'first100')}}/{{compareall.compared}}</span>
                                    </template>
                                </span>
                            </div>
                            <div class="space-around hide-on-postbox-container-1" data-drag-handle="false" v-show="showColumn('marked')">
                                <span>
                                    <span v-bind:class="{'value-empty': altitem(keyword, 'marked') == 0}">
                                        {{altitem(keyword, 'marked')}}
                                    </span>
                                    <template v-if="isCompareStatusOk()">
                                        <span v-bind:class="{'compare-value': true, 'compare-value-empty': competitoraltitem(keyword, 'marked') == 0}">{{competitoraltitem(keyword, 'marked')}}</span>
                                    </template>
                                    <template v-if="isCompareAllStatusOk()">
                                        <span v-bind:class="{'compare-value': true, 'compare-value-empty': compareallaltitem(keyword, 'marked') == 0}">{{compareallaltitem(keyword, 'marked')}}/{{compareall.compared}}</span>
                                    </template>
                                </span>
                            </div>
                            <div class="space-around hide-on-postbox-container-1" data-drag-handle="false" v-show="showColumn('images')">
                                <span>
                                    <span v-bind:class="{'value-empty': altitem(keyword, 'images') == 0}">
                                        {{altitem(keyword, 'images')}}
                                    </span>
                                    <template v-if="isCompareStatusOk()">
                                        <span v-bind:class="{'compare-value': true, 'compare-value-empty': competitoraltitem(keyword, 'images') == 0}">{{competitoraltitem(keyword, 'images')}}</span>
                                    </template>
                                    <template v-if="isCompareAllStatusOk()">
                                        <span v-bind:class="{'compare-value': true, 'compare-value-empty': compareallaltitem(keyword, 'images') == 0}">{{compareallaltitem(keyword, 'images')}}/{{compareall.compared}}</span>
                                    </template>
                                </span>
                            </div>
                            <div>
                                <template v-if="keyword != item.keyword">
                                    <!-- spacer only -->
                                    <span class="cmsk-visibility-hidden">
                                        <a href="javascript:void(0)" class="cmsk-icon cmsk-trend-neutral-icon" onclick="return false;"><span class="dashicons dashicons-chart-bar"></span></a>
                                    </span>
                                    <!-- spacer only -->
                                    <span class="cmsk-visibility-hidden">
                                        <a href="javascript:void(0)" class="cmsk-icon cmsk-trend-neutral-icon" onclick="return false;"><span class="dashicons dashicons-chart-bar"></span></a>
                                    </span>
                                    <div class="cmsk-dropdown">
                                        <a href="javascript:void(0)" class="cmsk-dropbtn" onclick="return false;"><span class="dashicons dashicons-admin-generic"></span></a>
                                        <div class="cmsk-dropdown-content">
                                            <a href="javascript:void(0)" class="cmsk1-action-websearch" v-bind:data-index="index" v-bind:data-altkeyword="keyword" onclick="return false;">Web Search <span class="dashicons dashicons-share-alt2"></span></a>
                                            <a href="javascript:void(0)" v-bind:data-index="index" v-bind:data-altkeyword="keyword" class="cmsk1-action-alttomain" onclick="return false;">Make Primary Keyword</a>
                                            <a href="javascript:void(0)" v-bind:data-index="index" v-bind:data-altkeyword="keyword" class="cmsk1-action-altreassign" onclick="return false;">Move to Other Keyword</a>
                                            <a href="javascript:void(0)" v-bind:data-index="index" v-bind:data-altkeyword="keyword" class="cmsk1-action-altremove" onclick="return false;">Remove Alternate Keyword</a>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <!--</template>-->
                    </section>
                </div>
            </div>
        </div>
        <div v-cloak v-if="isCompareAll()" style="margin-top: 1em;">
            <template v-if="isCompareAllReady()">
                Compared {{compareall.compared}} out of {{Object.keys(compareall.competitors).length}}
                <nobr>
                    <a href="javascript:void(0)" v-on:click="refreshCompareAll" class="button" style="vertical-align: initial;">Refresh All</a>
                    <a href="javascript:void(0)" v-on:click="endCompareAll" class="button" style="vertical-align: initial;">Close</a>
                </nobr>
            </template>
            <template v-else>
                Comparing {{compareall.compared + 1}} out of {{Object.keys(compareall.competitors).length}}
                <span class="cmsk-spinner" style="vertical-align: top;"></span>
                <nobr>
                    <a href="javascript:void(0)" v-on:click="refreshCompareAll" class="button" style="vertical-align: initial;">Refresh All</a>
                    <a href="javascript:void(0)" v-on:click="endCompareAll" class="button" style="vertical-align: initial;">Cancel</a>
                </nobr>
            </template>
        </div>
        <div v-cloak v-if="isCompare()" style="margin-top: 1em;">
            <template v-if="getCompareType() == 'competitor'">
                <template v-if="isCompareReady()">
                    <template v-if="isCompareStatusOk()">
                        Compared with
                        <template v-if="compare.competitor.uatype === 'mobile'">
                            mobile version of
                        </template>
                        <template v-else>
                            desktop version of
                        </template>
                        <a v-bind:href="compare.competitor.url" target="_blank">{{compare.competitor.url}}</a>
                    </template>
                    <template v-else>
                        Error on comparing with <a v-bind:href="compare.competitor.url" target="_blank">{{compare.competitor.url}}</a>:
                        {{compare.data.status}}
                    </template>
                    (results from {{compare.data.ts|ts2datetime}})
                    <nobr>
                        <a href="javascript:void(0)" v-on:click="refreshCompare" class="button" style="vertical-align: initial;">Refresh</a>
                        <a href="javascript:void(0)" v-on:click="endCompare" class="button" style="vertical-align: initial;">Close</a>
                    </nobr>
                </template>
                <template v-else>
                    Comparing with
                    <template v-if="compare.competitor.uatype === 'mobile'">
                        mobile version of
                    </template>
                    <template v-else>
                        desktop version of
                    </template>
                    <a v-bind:href="compare.competitor.url" target="_blank">{{compare.competitor.url}}</a>
                    <span class="cmsk-spinner" style="vertical-align: top;"></span>
                    <a href="javascript:void(0)" v-on:click="endCompare" class="button" style="vertical-align: initial;">Cancel</a>
                </template>
            </template>
            <template v-else>
                <template v-if="isCompareReady()">
                    <template v-if="isCompareStatusOk()">
                        Compared with snapshot of the same page from: <strong> {{compare.data.timestamp|ts2datetime}} </strong>
                        <a href="javascript:void(0)" v-on:click="removeSnapshot(compare.data.timestamp)" class="button" style="vertical-align: initial;">Delete Snapshot</a>
                        <a href="javascript:void(0)" v-on:click="endCompare" class="button" style="vertical-align: initial;">Close</a>
                    </template>
                    <template v-else>
                        Comparing with snapshot of the same page.
                        <span class="cmsk-spinner" style="vertical-align: top;"></span>
                        <a href="javascript:void(0)" v-on:click="endCompare" class="button" style="vertical-align: initial;">Close</a>
                    </template>
                </template>
            </template>
        </div>
        <div v-cloak v-if="items.length > 1" style="float:right;">
            <p>
                <label>
                    Order by
                    <select v-model="orderby" v-bind:change="orderChange" style="vertical-align: baseline;">
                        <option value="custom">Custom</option>
                        <option value="keyword">Keyword</option>
                        <option value="density"><?php echo _052066cdb01f0c81aedfc51f6cb7cfd3d21106dc( 'density' ); ?></option>
                        <option value="title"><?php echo _052066cdb01f0c81aedfc51f6cb7cfd3d21106dc( 'title' ); ?></option>
                        <option value="headers"><?php echo _052066cdb01f0c81aedfc51f6cb7cfd3d21106dc( 'headers' ); ?></option>
                        <option value="content"><?php echo _052066cdb01f0c81aedfc51f6cb7cfd3d21106dc( 'content' ); ?></option>
                        <option value="url"><?php echo _052066cdb01f0c81aedfc51f6cb7cfd3d21106dc( 'url' ); ?></option>
                        <option value="first100"><?php echo _052066cdb01f0c81aedfc51f6cb7cfd3d21106dc( 'first100' ); ?></option>
                        <option value="marked"><?php echo _052066cdb01f0c81aedfc51f6cb7cfd3d21106dc( 'marked' ); ?></option>
                        <option value="images"><?php echo _052066cdb01f0c81aedfc51f6cb7cfd3d21106dc( 'images' ); ?></option>
                    </select>
                </label>
                <template v-if="orderby !== 'custom'">
                    <label>
                        <select v-model="orderdir" v-bind:change="orderChange()" style="vertical-align: baseline;">
                            <option value="asc">Ascending</option>
                            <option value="desc">Descending</option>
                        </select>
                    </label>
                </template>
                <template v-if="orderby === 'custom'">
                    <button class="button" v-on:click="saveOrder" onclick="return false;">Save Order</button>
                </template>
            </p>
        </div>
        <p v-cloak></p>
        <a href="javascript:void(0)" class="button" id="cmsk1-add" onclick="return false;" v-cloak>+ Keyword</a>
        <button class="button hidden" id="cmsk1-unmark" onclick="return false;" v-cloak>Remove Marks</button>
    </div>
</div>
<script type="text/html" id="cmsk1-tpl-import-help">
    <div id="cmsk1-tpl-import-help-tab1" style="display: none;">
        <h2 class="swal2-title">Keywords CSV File Format</h2>
        <p class="swal2-content">CSV files structure:</p>
        <br />
        <table class="swal2-custom-csv-table">
            <tr>
                <th>&nbsp;</th>
                <th>A</th>
                <th>B</th>
                <th>C</th>
                <th>&nbsp;</th>
            </tr>
            <tr>
                <th>1</th>
                <td>KEYWORD</td>
                <td>ALTERNATE KEYWORD</td>
                <td>NOTE</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <th>2</th>
                <td>wordpress</td>
                <td>&nbsp;</td>
                <td>note for my first keyword</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <th>3</th>
                <td>wordpress</td>
                <td>blog</td>
                <td>note related to alternate keyword will be skipped</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <th>4</th>
                <td>wordpress</td>
                <td>CMS</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <th>5</th>
                <td>keyword-1</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <th>6</th>
                <td>keyword-2</td>
                <td>&nbsp;</td>
                <td>my second note</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <th>7</th>
                <td>...</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
        <div>
            <input type="button" class="swal2-styled swal2-custom-btn-blue" onclick="jQuery( '#cmsk1-tpl-import-help-tab1' ).hide();jQuery( '#cmsk1-tpl-import-help-tab2' ).show();" value="Close" />
        </div>
    </div>
    <div id="cmsk1-tpl-import-help-tab2">
        <h2 class="swal2-title">Keywords CSV Import and Export</h2>
        <p class="swal2-content">Import and export files are CSV files and have same format. See <a href="javascript:void(0)" onclick="jQuery( '#cmsk1-tpl-import-help-tab2' ).hide();jQuery( '#cmsk1-tpl-import-help-tab1' ).show();">file format details</a>.</p>
        <div>
            <p>Import type:</p>
            <label><input type="radio" name="cmsk1-import-type" value="overwrite"/>Overwrite existing</label>
            <label><input type="radio" name="cmsk1-import-type" value="add" checked="checked"/>Add new</label>
        </div>
        <div style="width:50%;display:inline-block;text-align:right;">
            <p>
                <span>
                    <span class="cmsk-spinner hidden" style="width:120px;"></span>
                    <button class="swal2-styled swal2-custom-btn-green" id="cmsk1-import" onclick="return false;" data-nonce="<?php echo wp_create_nonce( 'FB75D63B02DFFC9BD6C7013D6698DCA9CB666FBA' ); ?>" data-action="FB75D63B02DFFC9BD6C7013D6698DCA9CB666FBA" data-id="<?php echo $post->ID; ?>" data-form-action="<?php echo admin_url( 'admin-ajax.php' ); ?>">Import</button>
                </span>
            </p>
        </div><div style="width:50%;display:inline-block;text-align:left;">
            <p>
                <button class="swal2-styled swal2-custom-btn-green" id="cmsk1-export" onclick="return false;" data-nonce="<?php echo wp_create_nonce( 'A46F92E668FF6005D8B5B08D4F58A664C28C4B27' ); ?>" data-action="A46F92E668FF6005D8B5B08D4F58A664C28C4B27" data-id="<?php echo $post->ID; ?>" data-form-action="<?php echo admin_url( 'admin-ajax.php' ); ?>">Export</button>
            </p>
        </div>
        <p id="cmsk1-import-msg" class="hidden swal2-content"></p>
        <div id="cmsk1-restore-backup-container" style="display:none">
            If you wish you can revert the last import action.
            <button class="swal2-styled swal2-custom-btn-blue" id="cmsk1-restore-backup" onclick="return false;">Revert</button>
        </div>
        <div id="cmsk1-app-flatexport">
            <div v-cloak>
                <div class="swal2-content">
                    All keywords and alternate keywords comma separated:
                    <textarea class="swal2-textarea" placeholder="" style="display: block;" v-model="all" onclick="this.select();" readonly></textarea>
                </div>
            </div>
        </div>
        <div>
            <input type="button" class="swal2-styled swal2-custom-btn-grey" onclick="swal.closeModal();return false;" value="Close" />
        </div>
    </div>
</script>

<script type="text/html" id="cmsk1-tpl-websearch">
    <div id="cmsk1-app-websearch">
        <div v-cloak>
            <h2 class="swal2-title">Web Search</h2>
            <p class="swal2-content">
                <template v-if="options2.length">
                    Select keyword to search web.
                    <select class="swal2-select" style="display: block;" v-model="keyword">
                        <optgroup label="Keyword">
                            <option v-for="option in options1" v-bind:value="option">
                                {{ option }}
                            </option>
                        </optgroup>
                        <optgroup label="Alternate Keywords">
                            <option v-for="option in options2" v-bind:value="option">
                                {{ option }}
                            </option>
                        </optgroup>
                    </select>
                </template>
                <template v-else>
                    Search web for keyword <span class="cmsk-keyword">{{keyword}}</span>.
                </template>
            </p>
            <p class="swal2-content">For better results open links in incognito / inprivate window<span data-for="cmsk1-tpl-help1">&nbsp;(<a href="#" onclick="jQuery( '*[data-for=\'cmsk1-tpl-help1\']' ).toggle( 0 );return false;">more</a>)</span>.</p>
            <div data-for="cmsk1-tpl-help1" style="display: none;">
                <hr />
                <p class="swal2-content">How to open incognito / inprivate window:</p>
                <ul class="swal2-content">
                    <li><a href="https://support.google.com/chromebook/answer/95464" target="_blank">Google Chrome</a></li>
                    <li><a href="https://support.mozilla.org/en-US/kb/private-browsing-use-firefox-without-history" target="_blank">Mozilla Firefox</a></li>
                    <li><a href="https://support.apple.com/kb/ph21413?locale=en_US" target="_blank">Apple Safari</a></li>
                    <li><a href="https://support.microsoft.com/en-us/instantanswers/34b9a3a6-68bc-510b-2a9e-833107495ee5/browse-inprivate-in-microsoft-edge" target="_blank">Microsoft Edge</a></li>
                </ul>
                <hr />
            </div>
            <p></p>
            <a :href="url('https://www.google.com/#q={0}', keyword)" target="_blank"><img style="height:50px;" src="https://www.google.com/images/branding/googlelogo/2x/googlelogo_color_120x44dp.png" alt="google" /></a>
            <br />
            <a :href="url('https://www.bing.com/search?q={0}', keyword)" target="_blank"><img style="height:50px;" src="https://upload.wikimedia.org/wikipedia/commons/c/c7/Bing_logo_%282016%29.svg" alt="bing" /></a>
            <br />
            <a :href="url('http://soovle.com/?q={0}&e=0&s=google,amazon,yahoo,live,youtube,answers,wikipedia', keyword)" target="_blank"><img style="height:50px;" src="<?php echo plugin_dir_url( App::PLUGIN_FILE ) . 'assets/images/soovle.png'; ?>" alt="soovle" /></a>
        </div>
    </div>
</script>
<script type="text/html" id="cmsk1-tpl-swal-app1">
    <div id="cmsk1-swal-app1" class="ajax">
        <div class="loading" v-cloak><span class="cmsk-spinner"></span></div>
        <div class="ajax-cloak">
            <template v-if="items.length == 0">
                <p class="swal2-spacer"></p>
                <p v-cloak class="swal2-content">
                    No statistics collected yet for <span class="cmsk-keyword">{{keyword}}</span> keyword.
                </p>
                <p class="swal2-spacer"></p>
            </template>
            <template v-else>
                <p class="swal2-spacer"></p>
                <p style="text-align: left;" class="swal2-content">Statistics collected for <span class="cmsk-keyword">{{keyword}}</span>.</p>
                <p class="swal2-spacer"></p>
                <table class="swal2-custom-table1">
                    <thead>
                        <tr>
                            <th style="text-align: left;">Date</th>
                            <th width="15%">Source</th>
                            <th width="15%">Impressions</th>
                            <th width="15%">Clicks</th>
                            <th width="15%">CTR</th>
                            <th width="15%">Average Position</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                    <template v-for="(item, index) in items">
                        <tr v-bind:data-index="index">
                            <td style="text-align: left">{{item.date}} <small><nobr>(for {{item.period}}d)</nobr></small></td>
                            <td>
                                <template v-if="item.is_manual > 0">
                                    Manual
                                </template>
                                <template v-else>
                                    <template v-if="item.custom_text.length">
                                        {{item.custom_text}}
                                    </template>
                                    <template v-else>
                                        Title Change to "{{item.post_title}}"
                                    </template>
                                </template>
                            </td>
                            <td>{{item.impressions}}</td>
                            <td>{{item.clicks}}</td>
                            <td>
                                <template v-if="item.ctr  !== null">
                                    {{item.ctr}}%
                                </template>
                            </td>
                            <td>
                                <template v-if="item.position !== null">
                                    {{item.position}}
                                </template>
                            </td>
                            <td style="text-align: right; vertical-align: middle;" rowspan="2">
                                <a href="javascript:void(0)" v-on:click="remove(item.id)"><span class="dashicons dashicons-dismiss"></span></a>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" class="swal2-custom-table1-td-summary1 cmsk-tooltips-area">
                                <div>
                                    <span v-show="showColumn('density')" v-if="item.cmsk_density !== null">
                                        <i><?php echo _052066cdb01f0c81aedfc51f6cb7cfd3d21106dc( 'density' ); ?></i>: {{parseFloat(item.cmsk_density).toFixed(1)}}% <span class="cmsk-bull">&bull;</span>
                                    </span>
                                    <span v-show="showColumn('title')" v-if="item.cmsk_title !== null">
                                        <i><?php echo _052066cdb01f0c81aedfc51f6cb7cfd3d21106dc( 'title' ); ?></i>: {{item.cmsk_title}} <span class="cmsk-bull">&bull;</span>
                                    </span>
                                    <span v-show="showColumn('headers')" v-if="item.cmsk_headers !== null">
                                        <i><span data-title="Keywords in <?php echo _c553b81a8e2366d495038ee9161b2694695d8e00(); ?> tags"><?php echo _052066cdb01f0c81aedfc51f6cb7cfd3d21106dc( 'headers' ); ?></span></i>: {{item.cmsk_headers}} <span class="cmsk-bull">&bull;</span>
                                    </span>
                                    <span v-show="showColumn('content')" v-if="item.cmsk_content !== null">
                                        <i><?php echo _052066cdb01f0c81aedfc51f6cb7cfd3d21106dc( 'content' ); ?></i>: {{item.cmsk_content}} <span class="cmsk-bull">&bull;</span>
                                    </span>
                                    <span v-show="showColumn('url')" v-if="item.cmsk_url !== null">
                                        <i><?php echo _052066cdb01f0c81aedfc51f6cb7cfd3d21106dc( 'url' ); ?></i>: {{item.cmsk_url}} <span class="cmsk-bull">&bull;</span>
                                    </span>
                                    <span v-show="showColumn('first100')" v-if="item.cmsk_first100 !== null">
                                        <i><?php echo _052066cdb01f0c81aedfc51f6cb7cfd3d21106dc( 'first100' ); ?></i>: {{item.cmsk_first100}} <span class="cmsk-bull">&bull;</span>
                                    </span>
                                    <span v-show="showColumn('marked')" v-if="item.cmsk_marked !== null">
                                        <i><span data-title="Keywords in STRONG, EM, B and I tags"><?php echo _052066cdb01f0c81aedfc51f6cb7cfd3d21106dc( 'marked' ); ?></span></i>: {{item.cmsk_marked}} <span class="cmsk-bull">&bull;</span>
                                    </span>
                                    <span v-show="showColumn('images')" v-if="item.cmsk_images !== null">
                                        <i><span data-title="Images with keywords in ALT and TITLE attributes"><?php echo _052066cdb01f0c81aedfc51f6cb7cfd3d21106dc( 'images' ); ?></span></i>: {{item.cmsk_images}} <span class="cmsk-bull">&bull;</span>
                                    </span>
                                </div>
                                <div v-if="item.post_html_title !== null">
                                    <i>Title:</i> {{item.post_html_title}}
                                </div>
                            </td>
                        </tr>
                    </template>
                    </tbody>
                </table>
                <div class="swal2-content" style="margin: 20px 0; text-align: left;">
                    <template v-if="count == 1">
                        <small>First entry of {{count}} total.</small>
                    </template>
                    <template v-else>
                        <small>First {{Math.min(limit, count)}} entries of {{count}} total.</small>
                    </template>
                    <template v-if="limit < count">
                        <small>Load <a href="javascript:void(0)" v-on:click="more">more</a>.</small>
                    </template>
                </div>
            </template>
        </div>
        <div class="ajax-cloak" style="text-align: left;">
            <hr />
            <div style="width: 25%; display: inline-block">
                <label class="swal2-custom-label1" for="swal-input1">Date:</label>
                <input type="text" id="swal-input1" class="swal2-input" placeholder="date" v-model="form.date"/>
            </div>
            <div style="width: 15%; display: inline-block">
                <label class="swal2-custom-label1" for="swal-input2">Period (days):</label>
                <input type="number" id="swal-input2" class="swal2-input" placeholder="perdiod" v-model="form.period" />
            </div>
            <div></div>
            <div style="width: 25%; display: inline-block">
                <label class="swal2-custom-label1" for="swal-input2">Impressions:</label>
                <input type="number" id="swal-input2" class="swal2-input" placeholder="impressions" v-model="form.impressions" />
            </div>
            <div style="width: 25%; display: inline-block">
                <label class="swal2-custom-label1" for="swal-input3">Clicks:</label>
                <input type="number" id="swal-input3" class="swal2-input" placeholder="clicks" v-model="form.clicks"/>
            </div>
            <div style="width: 25%; display: inline-block">
                <label class="swal2-custom-label1" for="swal-input4">Average Position:</label>
                <input type="text" id="swal-input4" class="swal2-input" placeholder="average position" v-model="form.position"/>
            </div>
            <div style="display: inline-block; text-align: left">
                <input type="button" class="swal2-styled swal2-custom-btn-green" value="Save" v-on:click="add" />
            </div>
        </div>
    </div>
</script>

<script type="text/html" id="cmsk1-tpl-thesarus">
    <div type="text/html" id="cmsk1-tpl-thesarus-content" class="ajax">
        <div class="loading" v-cloak><span class="cmsk-spinner" style="margin: 50px auto;"></span></div>
        <div class="ajax-cloak">
            <p class="swal2-spacer"></p>
            <p style="text-align: left" class="swal2-content">
                Thesaurus results for <span class="cmsk-keyword">{{keyword}}</span> keyword.
            </p>
            <p class="swal2-spacer"></p>
            <p style="text-align: left" class="swal2-content">
                <label><b>Select All</b> <input type="checkbox" name="thesaurus[]" class="cmsk_select_all" style="visibility: hidden" /></label>
            </p>
            <ul class="cmsk_keywords_list">
                <template v-for="(item, index) in items">
                    <li>
                        <label><input type="checkbox" name="thesaurus[]" :value="item" /> {{item}}</label>
                    </li>
                </template>
            </ul>
            <p class="swal2-content">
                <label><input type="radio" name="thesaurus-app-mode" value="1" checked/> <b>Add as new keywords</b></label>
                <small>&nbsp;&mdash;&nbsp;OR&nbsp;&mdash;&nbsp;</small>
                <label><input type="radio" name="thesaurus-app-mode" value="0" /> <b>Merge with alternate keywords</b></label>
            </p>
        </div>
    </div>
</script>

<script type="text/html" id="cmsk1-tpl-keyword-note">
    <style>
        .color-sample{
            display: inline-block;
            width: 21px;
            height: 21px;
            vertical-align: text-bottom;
            padding: 0;
            margin: 0;
        }
    </style>
    <div id="cmsk1-tpl-keyword-note-content">
        <span>Note for <strong>{{keyword}}</strong> keyword.</span>
        <span>{{timestamp}}</span>
        <textarea class="swal2-textarea" id="swa-note" v-model="note"></textarea>

        <?php
        $colorsWithLabels = models\Options::getKeywordsColorsWithLabels();
        ?>
        <select id="swa-color" v-model="color">
            <?php foreach ( $colorsWithLabels as $color => $label ) : ?>
                <option value="<?php echo esc_attr( $color ); ?>"><?php echo esc_attr( $label ); ?></option>
            <?php endforeach; ?>
        </select>

        <span style="text-align: right">
            <span class="color-sample" :style="'background-color:'+color"></span>&nbsp;<span>{{getColorLabel(color)}}</span>
        </span>
    </div>
</script>