<?php

use com\cminds\seokeywords\App;
use com\cminds\seokeywords\plugin\models;
use com\cminds\seokeywords\plugin\misc;

wp_enqueue_script( misc\Assets::JS_TOOLTIPS );
wp_enqueue_script( misc\Assets::JS_KEYWORDSMETABOX );
wp_enqueue_style( misc\Assets::CSS_SWEETALERT2 );
wp_enqueue_style( misc\Assets::CSS_KEYWORDSMETABOX );

global $post;

function _65b1d766682c0a047bb3e2b8a430c6f2( $key ) {
    $arr = models\Options::getAllStatTableColumnsAssoc();
    return $arr[ $key ];
}

function _df75c0423266ac6dc496287179b96b75() {
    $lut   = models\Options::getAllContentHeadersAssoc();
    $items = [ ];
    foreach ( models\Options::getContentHeaders() as $item ) {
        $items[] = $lut[ $item ];
    }
    return join( ', ', $items );
}
?>

<div class="cmsk-dialog" v-cloak style="width: 400px; height: 200px; border: 1px solid black;">
    <p v-cloak v-if="items.length == 0">
        No pinned keywords.
    </p>
    <div id="cmsk1-list" v-cloak v-if="items.length > 0">
        <header>
            <div>
                Keyword
            </div>
            <div class="space-around hide-on-postbox-container-1" v-show="showColumn('headers')">
                <span title="Keywords in <?php echo _df75c0423266ac6dc496287179b96b75(); ?> tags"><?php echo _65b1d766682c0a047bb3e2b8a430c6f2( 'headers' ); ?></span>
            </div>
            <div class="space-around hide-on-postbox-container-1" v-show="showColumn('content')">
                <?php echo 'Content'; ?>
            </div>
            <div class="space-around hide-on-postbox-container-1" v-show="showColumn('first100')">
                <?php echo _65b1d766682c0a047bb3e2b8a430c6f2( 'first100' ); ?>
            </div>
        </header>
        <div style="position: relative;">
            <div class="cmsk1-section-area">
                <section v-for="(item, index) in items" :key="item.uuid" v-bind:data-index="index">
                    <template v-if="item.pinned">
                        <div class="cmsk-row cmsk-row-main">
                            <div>
                                <a href="javascript:void(0)" id="cmskl-action-pin" title="Unpin" v-bind:data-index="index">
                                    <input type="checkbox" v-model="item.pinned" />
                                </a>
                                {{item.keyword}}
                            </div>
                            <div class="space-around hide-on-postbox-container-1"  v-show="showColumn('headers')">
                                <span>
                                    <span v-bind:class="{'value-empty': item.stats.headers == 0}">
                                        {{item.stats.headers}}
                                    </span>
                                    <template v-if="isCompareStatusOk()">
                                        <span v-bind:class="{'compare-value': true, 'is-opportunity-all': isopportunity(item, 'title'), 'compare-value-empty': competitoritem(item.keyword, 'headers') == 0}">{{competitoritem(item.keyword, 'headers')}}</span>
                                    </template>
                                    <!--<template v-if="isCompareAllStatusOk() && item.stats.headers == 0 && compareallitem(item.keyword, 'headers') != 0">-->
                                    <template v-if="isCompareAllStatusOk()">
                                        <span v-bind:class="{'compare-value-all': true, 'is-opportunity-all': isopportunityall(item, 'headers'), 'compare-value-empty': compareallitem(item.keyword, 'headers') == 0}">{{compareallitem(item.keyword, 'headers')}}/{{compareall.compared}}</span>
                                    </template>
                                </span>
                            </div>
                            <div class="space-around hide-on-postbox-container-1"  v-show="showColumn('content')">
                                <span>
                                    <span v-bind:class="{'value-empty': item.stats.content == 0}">
                                        {{item.stats.content}}
                                    </span>
                                    <template v-if="isCompareStatusOk()">
                                        <span v-bind:class="{'compare-value': true, 'is-opportunity-all': isopportunity(item, 'content'), 'compare-value-empty': competitoritem(item.keyword, 'content') == 0}">{{competitoritem(item.keyword, 'content')}}</span>
                                    </template>
                                    <!--<template v-if="isCompareAllStatusOk() && item.stats.content == 0 && compareallitem(item.keyword, 'content') != 0">-->
                                    <template v-if="isCompareAllStatusOk()">
                                        <span v-bind:class="{'compare-value-all': true, 'is-opportunity-all': isopportunityall(item, 'content'), 'compare-value-empty': compareallitem(item.keyword, 'content') == 0}">{{compareallitem(item.keyword, 'content')}}/{{compareall.compared}}</span>
                                    </template>
                                </span>
                            </div>
                            <div class="space-around hide-on-postbox-container-1"  v-show="showColumn('first100')">
                                <span>
                                    <span v-bind:class="{'value-empty': item.stats.first100 == 0}">
                                        {{item.stats.first100}}
                                    </span>
                                    <template v-if="isCompareStatusOk()">
                                        <span v-bind:class="{'compare-value': true, 'is-opportunity-all': isopportunity(item, 'first100'), 'compare-value-empty': competitoritem(item.keyword, 'first100') == 0}">{{competitoritem(item.keyword, 'first100')}}</span>
                                    </template>
                                    <!--<template v-if="isCompareAllStatusOk() && item.stats.first100 == 0 && compareallitem(item.keyword, 'first100') != 0">-->
                                    <template v-if="isCompareAllStatusOk()">
                                        <span v-bind:class="{'compare-value-all': true, 'is-opportunity-all': isopportunityall(item, 'first100'), 'compare-value-empty': compareallitem(item.keyword, 'first100') == 0}">{{compareallitem(item.keyword, 'first100')}}/{{compareall.compared}}</span>
                                    </template>
                                </span>
                            </div>
                        </div>
                        <div v-bind:class="{'cmsk-row': true, 'cmsk-row-alt': true, 'cmsk-row-alt-main': keyword == item.keyword, 'hidden': true, 'expanded': inArray(item.keyword, expanded) !== -1}" v-for="(keyword, index2) in [item.keyword].concat(item.altkeywords)" v-bind:data-index="index2">
                            <div data-drag-handle="false">
                                <span style="margin-left: 5px;" class="cmsk-primary-text">{{ keyword }}</span>
                            </div>
                            <div class="space-around hide-on-postbox-container-1" data-drag-handle="false" v-show="showColumn('headers')">
                                <span>
                                    <span v-bind:class="{'value-empty': altitem(keyword, 'headers') == 0}">
                                        {{altitem(keyword, 'headers')}}
                                    </span>
                                    <template v-if="isCompareStatusOk()">
                                        <span v-bind:class="{'compare-value-all': true, 'compare-value-empty': competitoraltitem(keyword, 'headers') == 0}">{{competitoraltitem(keyword, 'headers')}}</span>
                                    </template>
                                    <template v-if="isCompareAllStatusOk()">
                                        <span v-bind:class="{'compare-value-all': true, 'compare-value-empty': compareallaltitem(keyword, 'headers') == 0}">{{compareallaltitem(keyword, 'headers')}}/{{compareall.compared}}</span>
                                    </template>
                                </span>
                            </div>
                            <div class="space-around hide-on-postbox-container-1" data-drag-handle="false" v-show="showColumn('content')">
                                <span>
                                    <span v-bind:class="{'value-empty': altitem(keyword, 'content') == 0}">
                                        {{altitem(keyword, 'content')}}
                                    </span>
                                    <template v-if="isCompareStatusOk()">
                                        <span v-bind:class="{'compare-value-all': true, 'compare-value-empty': competitoraltitem(keyword, 'content') == 0}">{{competitoraltitem(keyword, 'content')}}</span>
                                    </template>
                                    <template v-if="isCompareAllStatusOk()">
                                        <span v-bind:class="{'compare-value-all': true, 'compare-value-empty': compareallaltitem(keyword, 'content') == 0}">{{compareallaltitem(keyword, 'content')}}/{{compareall.compared}}</span>
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
                        </div>
                    </template>
                </section>
            </div>
        </div>
    </div>
</div>