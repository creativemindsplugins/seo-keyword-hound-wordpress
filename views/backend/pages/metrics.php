<?php

use com\cminds\seokeywords\plugin\models;
use com\cminds\seokeywords\plugin\misc;

$additionalColumns = [ ];
if ( models\Options::getIsShowConversion() ) {
    $additionalColumns[] = 'conversion';
}
if ( models\Options::getIsShowConversionValue() ) {
    $additionalColumns[] = 'conversion_value';
}

wp_localize_script( misc\Assets::JS_PAGEMETRICS, 'cmskPagemetricsConfig', [
    'trendsmargin' => models\Options::getTrendsMargin() / 100,
    'additional'   => $additionalColumns
] );

$colors = ['transparent', '#ff000033', '#00ff0033', '#0000ff33', '#ff00a533', '#a900ff33', '#efff0033', '#ffa50033' ];

wp_enqueue_script( misc\Assets::JS_TOOLTIPS );
wp_enqueue_script( misc\Assets::JS_PAGEMETRICS );
wp_enqueue_script( misc\Assets::JS_DATATABLES );
wp_enqueue_style( misc\Assets::CSS_SWEETALERT2 );
wp_enqueue_style( misc\Assets::CSS_PAGEMETRICSMETABOX );
wp_enqueue_style( misc\Assets::CSS_DATATABLES );
?>
<div class="cmsk-tooltips-area">
    <div id="cmsk3" class="cmsk-init">

        <div v-cloak>
<!--        <select id="cmsk-metrics-filters">
            <option value="all">Show all pages</option>
            <option value="title-update">Show all pages with title update in the last 30 days</option>
            <option value="keyword-update">Show all pages with keywords updates in the last 30 days</option>
            <option value="impressions-asc">Show all pages with increasing Impressions</option>
            <option value="impressions-neutral">Show all pages with stable Impressions</option>
            <option value="impressions-desc">Show all pages with decreasing Impressions</option>
            <option value="click-asc">Show all pages with increasing Clicks</option>
            <option value="click-neutral">Show all pages with stable Clicks</option>
            <option value="click-desc">Show all pages with decreasing Clicks</option>
            <option value="ctr-asc">Show all pages with increasing CTR</option>
            <option value="ctr-neutral">Show all pages with stable CTR</option>
            <option value="ctr-desc">Show all pages with decreasing CTR</option>
            <option value="bounce-rate-asc">Show all pages with increasing Bounce Rate</option>
            <option value="bounce-rate-neutral">Show all pages with stable Bounce Rate</option>
            <option value="bounce-rate-desc">Show all pages with decreasing Bounce Rate</option>
            <option value="conversion-asc">Show all pages with increasing Conversions</option>
            <option value="conversion-neutral">Show all pages with stable Conversions</option>
            <option value="conversion-desc">Show all pages with decreasing Conversions</option>
            <option value="conversion-value-asc">Show all pages with increasing Conversion Value</option>
            <option value="conversion-value-neutral">Show all pages with neutral Conversion Value</option>
            <option value="conversion-value-desc">Show all pages with decreasing Conversion Value</option>
        </select>-->
            <select id="cmsk-metrics-filters">
                <option value="all">Show all pages</option>
                <option value="title-update">Show all pages with title update in the last 30 days</option>
                <option value="keyword-update">Show all pages with keywords updates in the last 30 days</option>
                <option value="impressions">Show all pages with Impressions</option>
                <option value="click">Show all pages with Clicks</option>
                <option value="ctr">Show all pages with CTR</option>
                <option value="bounce-rate">Show all pages with Bounce Rate</option>
                <option value="conversion">Show all pages with Conversions</option>
                <option value="conversion-value">Show all pages with Conversion Value</option>
            </select>
            <select id="cmsk-metrics-filters-order">
                <option value="">Any</option>
                <option value="-asc">Up trend</option>
                <option value="-neutral">Neutral trend</option>
                <option value="-desc">Down trend</option>
            </select>
            <a href="javascript:void(0);" class="cmsk-metrics-filter-apply button">Filter</a>

        </div>

        <div class="toggle-vis" v-cloak>
            <label>Toggle column visibility:</label>
            <?php
            $columnIndex = 0;
            ?>
            <span data-column="<?php echo $columnIndex++; ?>">
                Page
            </span>
            <span data-column="<?php echo $columnIndex++; ?>">
                Date of Last Edit
            </span>
            <span data-column="<?php echo $columnIndex++; ?>">
                Date of Last Metrics
            </span>
            <span data-column="<?php echo $columnIndex; ?>">
                PA
            </span>
            <span data-column="<?php echo $columnIndex+=2; ?>">
                Impressions
            </span>
            <span data-column="<?php echo $columnIndex+=2; ?>">
                Clicks
            </span>
            <span data-column="<?php echo $columnIndex+=2; ?>">
                CTR
            </span>
            <span data-column="<?php echo $columnIndex+=2; ?>">
                Bounce Rate
            </span>
            <?php if ( models\Options::getIsShowConversion() ) : ?>
                <span data-column="<?php echo $columnIndex+=2; ?>">
                    Conversions
                </span>
            <?php endif; ?>
            <?php if ( models\Options::getIsShowConversionValue() ) : ?>
                <span data-column="<?php echo $columnIndex+=2; ?>">
                    Conversion Value
                </span>
            <?php endif; ?>
            <?php
            $additionalColumns = models\Options::getMetricTableLabels();
            foreach ( $additionalColumns as $value ) :
                if ( empty( $value ) ) {
                    continue;
                }
                ?>
                <span data-column="<?php echo ++$columnIndex; ?>">
                    <?php echo esc_html( $value ); ?>
                </span>
                <?php
            endforeach;
            ?>
        </div>

        <div class="loading" v-cloak><span class="cmsk-spinner"></span></div>

        <p v-cloak v-if="items.length == 0">
            No page metrics.
        </p>

        <table id="cmsk3-list" class="display" v-cloak v-if="items.length > 0" style="width: 100%">
            <thead>
                <tr>
                    <th>
                        Page
                    </th>
                    <th class="space-around">
                        Date of Last Edit
                    </th>
                    <th>
                        Date of Last Metrics
                    </th>
                    <th>
                        PA
                    </th>
                    <th class="">
                        Impressions(hidden)
                    </th>
                    <th class="space-around">
                        Impressions
                    </th>
                    <th class="">
                        Clicks(hidden)
                    </th>
                    <th class="space-around">
                        Clicks
                    </th>
                    <th class="">
                        CTR(hidden)
                    </th>
                    <th class="space-around">
                        CTR
                    </th>
                    <th class="">
                        Bounce Rate(hidden)
                    </th>
                    <th class="space-around">
                        Bounce Rate
                    </th>
                    <?php if ( models\Options::getIsShowConversion() ) : ?>
                        <th class="">
                            Conversion(hidden)
                        </th>
                        <th class="space-around">
                            Conversions
                        </th>
                    <?php endif; ?>
                    <?php if ( models\Options::getIsShowConversionValue() ) : ?>
                        <th class="">
                            Conversion Value(hidden)
                        </th>
                        <th class="space-around">
                            Conversion Value
                        </th>
                    <?php endif; ?>
                    <?php
                    $additionalColumns = models\Options::getMetricTableLabels();
                    foreach ( $additionalColumns as $value ) :
                        if ( empty( $value ) ) {
                            continue;
                        }
                        ?>
                        <th class="space-around">
                            <?php echo esc_html( $value ); ?>
                        </th>
                        <?php
                    endforeach;
                    ?>
                </tr>
            </thead>
            <tbody style="position: relative;" class="cmsk3-section-area">
                <tr v-for="(item, index) in items" v-bind:data-index="item.index" :style="'background:'+item.custom_data.row_color" >
                    <td class="cmsk_metrics_title">
                        <a v-bind:href="item.edit_link" target="_blank" style="margin-right: 5px;">
                            {{item.post_title}}
                        </a>
                        <a v-bind:href="item.permalink" target="_blank" class="cmsk-icon"><span class="dashicons dashicons-visibility"></span></a>
                        <div class="cmsk-row-color-selector-div" v-bind:data-index="index" v-bind:data-post_id="item.post_id">
                            <?php foreach ( $colors as $k => $c ) : ?>
                                <span class="cmsk-color-sample" :class="item.custom_data.row_color == '<?php echo esc_attr( $c ); ?>' ? 'active' : ''" data-val="<?php echo esc_attr( $c ); ?>" style="background-color:<?php echo esc_attr( $c ); ?>"></span>
                            <?php endforeach; ?>
                        </div>
                    </td>
                    <td class="space-around">
                        <input type="text" class="hidden cmsk2-short-inline-input cmsk-has-datepicker" data-callback='updateLastReset' :value="getDate(item.notification_last_reset,'YYYY-MM-DD')" v-bind:data-time="item.notification_last_reset" />
                        <template v-if="item.notification_last_reset !== null">
                            {{getDate(item.notification_last_reset,'MMMM Do')}} <br/>({{getDaysAgo(item.notification_last_reset)}})
                        </template>
                        <br/>
                        <span class="dashicons dashicons-update cmsk-reset-notification" data-title="Reset notification"></span>
                        <span data-drag-disabled="true" class="cmsk3-toggle-input cmsk3-togglable" v-bind:data-index="index">
                            <span class="cmsk-small-text"><span class="dashicons dashicons-edit"></span></span>
                        </span>
                    </td>
                    <td>
                        <span style="margin-right: 5px;" v-bind:data-title="'Last title change: ' + getDate(item.last_title_change_date) + ' (' + getDaysAgo(item.last_title_change_date)+')'">
                            <!--{{getDate(item.date)}}&nbsp;<small><nobr>(for {{item.period}}d)</nobr></small>-->
                            {{getDate(item.date)}}
                        </span>
                    </td>
                    <td class="">
                        {{item.pa}}
                    </td>
                    <td class="">
                        {{item.impressions}}
                    </td>
                    <td class="space-around">
                        {{item.impressions}}
                        <template v-if="trend(item,'impressions') === 0">
                            <a href="#" class="cmsk-action-trends-modal-chart cmsk-icon cmsk-trend-neutral-icon" data-chart-type="impressions" v-bind:data-chart-config="trendsChartConfig(item)" v-bind:data-chart-title="trendsChartTitle(item)" data-title="Neutral trend" onclick="return false;"><span class="dashicons dashicons-minus"></span></a>
                        </template>
                        <template v-if="trend(item,'impressions') === 1">
                            <a href="#" class="cmsk-action-trends-modal-chart cmsk-icon cmsk-trend-up-icon" data-chart-type="impressions" v-bind:data-chart-config="trendsChartConfig(item)" v-bind:data-chart-title="trendsChartTitle(item)" data-title="Up trend" onclick="return false;"><span class="dashicons dashicons-arrow-up-alt"></span></a>
                        </template>
                        <template v-if="trend(item,'impressions') === -1">
                            <a href="#" class="cmsk-action-trends-modal-chart cmsk-icon cmsk-trend-down-icon" data-chart-type="impressions" v-bind:data-chart-config="trendsChartConfig(item)" v-bind:data-chart-title="trendsChartTitle(item)" data-title="Down trend" onclick="return false;"><span class="dashicons dashicons-arrow-up-alt reverse"></span></a>
                        </template>
                    </td>
                    <td class="">
                        {{item.clicks}}
                    </td>
                    <td class="space-around">
                        {{item.clicks}}
                        <template v-if="trend(item,'clicks') === 0">
                            <a href="#" class="cmsk-action-trends-modal-chart cmsk-icon cmsk-trend-neutral-icon" data-chart-type="clicks" v-bind:data-chart-config="trendsChartConfig(item)" v-bind:data-chart-title="trendsChartTitle(item)" data-title="Neutral trend" onclick="return false;"><span class="dashicons dashicons-minus"></span></a>
                        </template>
                        <template v-if="trend(item,'clicks') === 1">
                            <a href="#" class="cmsk-action-trends-modal-chart cmsk-icon cmsk-trend-up-icon" data-chart-type="clicks" v-bind:data-chart-config="trendsChartConfig(item)" v-bind:data-chart-title="trendsChartTitle(item)" data-title="Up trend" onclick="return false;"><span class="dashicons dashicons-arrow-up-alt"></span></a>
                        </template>
                        <template v-if="trend(item,'clicks') === -1">
                            <a href="#" class="cmsk-action-trends-modal-chart cmsk-icon cmsk-trend-down-icon" data-chart-type="clicks" v-bind:data-chart-config="trendsChartConfig(item)" v-bind:data-chart-title="trendsChartTitle(item)" data-title="Down trend" onclick="return false;"><span class="dashicons dashicons-arrow-up-alt reverse"></span></a>
                        </template>
                    </td>
                    <td class="">
                        {{item.ctr}}
                    </td>
                    <td class="space-around">
                        <template v-if="item.ctr  !== null">
                            {{item.ctr}}%
                            <template v-if="trend(item,'ctr') === 0">
                                <a href="#" class="cmsk-action-trends-modal-chart cmsk-icon cmsk-trend-neutral-icon" data-chart-type="ctr" v-bind:data-chart-config="trendsChartConfig(item)" v-bind:data-chart-title="trendsChartTitle(item)" data-title="Neutral trend" onclick="return false;"><span class="dashicons dashicons-minus"></span></a>
                            </template>
                            <template v-if="trend(item,'ctr') === 1">
                                <a href="#" class="cmsk-action-trends-modal-chart cmsk-icon cmsk-trend-up-icon" data-chart-type="ctr" v-bind:data-chart-config="trendsChartConfig(item)" v-bind:data-chart-title="trendsChartTitle(item)" data-title="Up trend" onclick="return false;"><span class="dashicons dashicons-arrow-up-alt"></span></a>
                            </template>
                            <template v-if="trend(item,'ctr') === -1">
                                <a href="#" class="cmsk-action-trends-modal-chart cmsk-icon cmsk-trend-down-icon" data-chart-type="ctr" v-bind:data-chart-config="trendsChartConfig(item)" v-bind:data-chart-title="trendsChartTitle(item)" data-title="Down trend" onclick="return false;"><span class="dashicons dashicons-arrow-up-alt reverse"></span></a>
                            </template>
                        </template>
                    </td>
                    <td class="">
                        {{item.bounce_rate}}
                    </td>
                    <td class="space-around">
                        <template v-if="item.bounce_rate !== null">
                            {{item.bounce_rate}}%
                            <template v-if="trend(item,'bounce_rate') === 0">
                                <a href="#" class="cmsk-action-trends-modal-chart cmsk-icon cmsk-trend-neutral-icon" data-chart-type="bounce_rate" v-bind:data-chart-config="trendsChartConfig(item)" v-bind:data-chart-title="trendsChartTitle(item)" data-title="Neutral trend" onclick="return false;"><span class="dashicons dashicons-minus"></span></a>
                            </template>
                            <template v-if="trend(item,'bounce_rate') === -1">
                                <a href="#" class="cmsk-action-trends-modal-chart cmsk-icon cmsk-trend-down-icon" data-chart-type="bounce_rate" v-bind:data-chart-config="trendsChartConfig(item)" v-bind:data-chart-title="trendsChartTitle(item)" data-title="Up trend" onclick="return false;"><span class="dashicons dashicons-arrow-up-alt"></span></a>
                            </template>
                            <template v-if="trend(item,'bounce_rate') === 1">
                                <a href="#" class="cmsk-action-trends-modal-chart cmsk-icon cmsk-trend-up-icon" data-chart-type="bounce_rate" v-bind:data-chart-config="trendsChartConfig(item)" v-bind:data-chart-title="trendsChartTitle(item)" data-title="Down trend" onclick="return false;"><span class="dashicons dashicons-arrow-up-alt reverse"></span></a>
                            </template>
                        </template>
                    </td>
                    <?php if ( models\Options::getIsShowConversion() ) : ?>
                        <td class="">
                            {{item.conversion}}
                        </td>
                        <td class="space-around">
                            <template v-if="item.conversion !== null">
                                {{item.conversion}}
                                <template v-if="trend(item,'conversion') === 0">
                                    <a href="#" class="cmsk-action-trends-modal-chart cmsk-icon cmsk-trend-neutral-icon" data-chart-type="conversion" v-bind:data-chart-config="trendsChartConfig(item)" v-bind:data-chart-title="trendsChartTitle(item)" data-title="Neutral trend" onclick="return false;"><span class="dashicons dashicons-minus"></span></a>
                                </template>
                                <template v-if="trend(item,'conversion') === 1">
                                    <a href="#" class="cmsk-action-trends-modal-chart cmsk-icon cmsk-trend-up-icon" data-chart-type="conversion" v-bind:data-chart-config="trendsChartConfig(item)" v-bind:data-chart-title="trendsChartTitle(item)" data-title="Up trend" onclick="return false;"><span class="dashicons dashicons-arrow-up-alt"></span></a>
                                </template>
                                <template v-if="trend(item,'conversion') === -1">
                                    <a href="#" class="cmsk-action-trends-modal-chart cmsk-icon cmsk-trend-down-icon" data-chart-type="conversion" v-bind:data-chart-config="trendsChartConfig(item)" v-bind:data-chart-title="trendsChartTitle(item)" data-title="Down trend" onclick="return false;"><span class="dashicons dashicons-arrow-up-alt reverse"></span></a>
                                </template>
                            </template>
                        </td>
                    <?php endif; ?>
                    <?php if ( models\Options::getIsShowConversionValue() ) : ?>
                        <td class="">
                            {{item.conversion_value}}
                        </td>
                        <td class="space-around">
                            <template v-if="item.conversion_value !== null">
                                ${{item.conversion_value}}
                                <template v-if="trend(item,'conversion_value') === 0">
                                    <a href="#" class="cmsk-action-trends-modal-chart cmsk-icon cmsk-trend-neutral-icon" data-chart-type="conversion_value" v-bind:data-chart-config="trendsChartConfig(item)" v-bind:data-chart-title="trendsChartTitle(item)" data-title="Neutral trend" onclick="return false;"><span class="dashicons dashicons-minus"></span></a>
                                </template>
                                <template v-if="trend(item,'conversion_value') === 1">
                                    <a href="#" class="cmsk-action-trends-modal-chart cmsk-icon cmsk-trend-up-icon" data-chart-type="conversion_value" v-bind:data-chart-config="trendsChartConfig(item)" v-bind:data-chart-title="trendsChartTitle(item)" data-title="Up trend" onclick="return false;"><span class="dashicons dashicons-arrow-up-alt"></span></a>
                                </template>
                                <template v-if="trend(item,'conversion_value') === -1">
                                    <a href="#" class="cmsk-action-trends-modal-chart cmsk-icon cmsk-trend-down-icon" data-chart-type="conversion_value" v-bind:data-chart-config="trendsChartConfig(item)" v-bind:data-chart-title="trendsChartTitle(item)" data-title="Down trend" onclick="return false;"><span class="dashicons dashicons-arrow-up-alt reverse"></span></a>
                                </template>
                            </template>
                        </td>
                    <?php endif; ?>
                    <?php
                    foreach ( $additionalColumns as $key => $value ) :
                        if ( empty( $value ) ) {
                            continue;
                        }
                        ?>
                        <td>
                            <!--<input type="text" class="hidden cmsk2-short-inline-input" data-field="<?php echo esc_attr( $key ); ?>" />-->
                            <textarea class="hidden cmsk2-short-inline-input" data-field="<?php echo esc_attr( $key ); ?>" ></textarea>
                            <span data-drag-disabled="true" class="cmsk3-toggle-input cmsk3-togglable" v-bind:data-index="index" v-bind:data-post_id="item.post_id">
                                <template v-if="item.custom_data && item.custom_data.fields && item.custom_data.fields[<?php echo esc_attr( $key ); ?>]">
                                    {{item.custom_data.fields[<?php echo esc_attr( $key ); ?>]}}
                                </template>
                                <template v-else>
                                    <span class="cmsk-small-text"><span class="dashicons dashicons-edit"></span></span>
                                </template>
                            </span>
                        </td>
                        <?php
                    endforeach;
                    ?>
                </tr>
            </tbody>
        </table>
        <p v-cloak></p>
        <div style="float: right;">
            <template v-if="items.length == 1">
                First entry of {{total}} total.
            </template>
            <template v-else>
                First {{items.length}} entries of {{total}} total.
            </template>
            <template v-if="items.length < total">
                Load <a href="javascript:void(0)" v-on:click="more">more</a>.
            </template>
        </div>
    </div>
</div>