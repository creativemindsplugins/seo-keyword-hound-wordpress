<?php

use com\cminds\seokeywords\plugin\models;
use com\cminds\seokeywords\plugin\misc;

wp_localize_script(misc\Assets::JS_EVENTS, 'cmskEventsConfig', [
]);

$colors = ['transparent', '#ff000033', '#00ff0033', '#0000ff33', '#ff00a533', '#a900ff33', '#efff0033', '#ffa50033'];

wp_enqueue_script(misc\Assets::JS_TOOLTIPS);
wp_enqueue_script(misc\Assets::JS_EVENTS);
wp_enqueue_script(misc\Assets::JS_DATATABLES);
wp_enqueue_style(misc\Assets::CSS_SWEETALERT2);
wp_enqueue_style(misc\Assets::CSS_EVENTSMETABOX);
wp_enqueue_style(misc\Assets::CSS_DATATABLES);
?>
<div class="cmsk-tooltips-area">
    <div id="cmsk4" class="cmsk-init" v-cloak>

        <div v-cloak>
            <select id="cmsk-metrics-filters">
                <option value="all">Show all events</option>
                <option value="title-update">Show only Title Update events</option>
                <option value="seo-title-update">Show only SEO Title Update events</option>
                <option value="seo-description-update">Show only Description Update events</option>
                <option value="snapshot">Show only Snapshot events</option>
                <option value="analytics">Show only Analytics events</option>
                <option value="custom">Show all Custom Events</option>
                <?php
                $customEventTypes = models\Options::getCustomEventsColorsLabels();
                foreach ($customEventTypes as $key => $event) :
                    if (empty($event)) {
                        continue;
                    }
                    ?>
                    <option value="custom-<?php echo esc_attr($key); ?>">Show only Custom Event with label: <?php echo $event; ?></option>
                <?php endforeach; ?>
            </select>
            <a href="javascript:void(0);" class="cmsk-metrics-filter-apply button">Filter</a>

        </div>

        <div class="toggle-vis" v-cloak>
            <label>Toggle column visibility:</label>
            <?php
            $columnIndex = 0;
            ?>
            <span data-column="<?php echo $columnIndex++; ?>">
                Page Title
            </span>
            <span data-column="<?php echo $columnIndex++; ?>">
                Date of Event
            </span>
            <span data-column="<?php echo $columnIndex++; ?>">
                Event Type
            </span>
            <span data-column="<?php echo $columnIndex++; ?>">
                Event Note
            </span>
        </div>

        <div class="loading" v-cloak><span class="cmsk-spinner"></span></div>

        <p v-cloak v-if="items.length == 0">
            No events.
        </p>

        <table id="cmsk4-list" class="display" v-cloak v-if="items.length > 0" style="width: 100%">
            <thead>
                <tr>
                    <th>
                        Page Title
                    </th>
                    <th class="hidden">
                        Date(hidden)
                    </th>
                    <th class="space-around">
                        Date of Event
                    </th>
                    <th>
                        Event Type
                    </th>
                    <th>
                        Event Note
                    </th>
                    <th>
                        Notification
                    </th>
                </tr>
            </thead>
            <tbody style="position: relative;" class="cmsk4-section-area">
                <tr v-for="(item, index) in items" v-bind:data-index="item.index" v-bind:data-id="item.id" v-bind:data-postid="item.post_id" >
                    <td class="cmsk_metrics_title">
                        <a v-bind:href="item.edit_link" target="_blank" style="margin-right: 5px;">
                            {{item.post_title}}
                        </a>
                        <a v-bind:href="item.permalink" target="_blank" class="cmsk-icon"><span class="dashicons dashicons-visibility"></span></a>
                    </td>
                    <td class="hidden">
                        <span>
                            {{item.date}}
                        </span>
                    </td>
                    <td>
                        <span style="margin-right: 5px;">
                            {{getDate(item.date)}}
                        </span>
                    </td>
                    <td class="left">
                        <span class="cmsk-color-sample" :style="'background-color:'+item.color"></span>
                        <template v-if="item.color_label">
                            {{item.color_label}}
                        </template>
                        <template v-else>Event</template>
                    </td>
                    <td class="left">
                        {{item.custom_text}}
                    </td>
                    <td class="left">
                        <template v-if="item.notifications">
                            <span>{{getDate(addDays(item.date, item.notifications.days))}}</span> <span v-bind:class="{'cmsk-blink': showAlert(item) === true}">({{getDaysAgo(addDays(item.date, item.notifications.days))}})</span>
                            <br>
                            <template v-if="item.notifications.type == 'all' || item.notifications.type == 'email'">
                                <span class="dashicons dashicons-email"></span>
                            </template>
                            <template v-if="item.notifications.type == 'all' || item.notifications.type == 'notice'">
                                <span v-bind:class="{'cmsk-blink': showAlert(item) === true}" class="dashicons dashicons-megaphone" v-bind:data-title="item.notifications.text"></span>
                                <template v-if="showAlert(item) === true">
                                    <button class="cmsk-disable-notification">[disable alert]</button>
                                    <div class="cmsk_events_ajax1_loading hidden" v-cloak><span class="cmsk-spinner"></span></div>
                                </template>
                            </template>
                        </template>
                    </td>
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