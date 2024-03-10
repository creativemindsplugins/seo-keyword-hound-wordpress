<?php

use com\cminds\seokeywords\plugin\misc\Assets;
use com\cminds\seokeywords\plugin\models;
use com\cminds\seokeywords\plugin\metaboxes;
use com\cminds\seokeywords\plugin\ga;

wp_enqueue_script(Assets::JS_VUEJS);
wp_enqueue_script(Assets::JS_VALIDURL);
wp_enqueue_script(Assets::JS_SWEETALERT2);

$component = uniqid('cmsk');

global $post;
?>

<script type="text/javascript">
    (function ($) {
        $(function () {

            swal.setDefaults({
                animation: false
            });

            var uid = '<?php echo $uniqid; ?>';
            var metabox = '<?php echo $metabox; ?>';
            var component = '<?php echo $component; ?>';

            var config = eval(uid);
            var data = $.extend({
                label1: '',
                url1: '',
                label2: '',
                url2: '',
                note: '',
                notification_send: '',
                notification_last_reset: '',
                notification_email: '',
                notification_email_sent: false,
                searchphrases: [],
                snapshots: []
            }, config.data);

            $('#{0}'.replace('{0}', metabox))
                    .find('h2')
                    .first()
                    .append('<span id="{0}"><{1}></{1}></span>'
                            .replace('{0}', uid)
                            .replace('{1}', component));

            Vue.component(component, {
                template: $('#{0}-tpl1'.replace('{0}', uid)).html(),
                data: function () {
                    return data;
                },
                methods: {
                    metricCheckboxChanged: function (forceUpdate) {
                        if (forceUpdate) {
                            vm.$forceUpdate();
                        }
                        if (typeof data.metrics_show_title !== 'undefined') {
                            $(document).trigger('cmsk-metrics-filter-change',
                                    {
                                        'metrics_show_metrics': data.metrics_show_metrics,
                                        'metrics_show_title': data.metrics_show_title,
                                        'metrics_show_description': data.metrics_show_description,
                                        'metrics_show_snapshot': data.metrics_show_snapshot,
                                        'metrics_show_custom': data.metrics_show_custom,
                                        'metrics_show_in_dashboard': data.metrics_show_in_dashboard,
                                    });
                        }
                    },
                }
            });

            if (typeof window.cmsk_dashboard_vm === 'undefined') {
                window.cmsk_dashboard_vm = [];
            }

            var stop_update_loop = false;
            var vm = window.cmsk_dashboard_vm[uid] = new Vue({
                el: '#{0}'.replace('{0}', uid),
                data: data,
                methods: {
                    getMozScore: function () {
                        $('.cmsk-moz-pa-loading').removeClass('hidden');
                        $.ajax({
                            method: 'POST',
                            url: ajaxurl,
                            cache: false,
                            data: {
                                action: 'cmsk_get_moz_ranks',
                                post_id: config.post_id,
                                query: data.currenturl
                            }
                        }).done(function (result_data) {
                            try {
                                if (true === result_data.success && result_data.data.result && result_data.data.result) {
                                    $('#swa-page-authority').val(Math.round(result_data.data.result.moz_pagerank));
                                } else {
                                }
                            } catch (e) {
                            }
                        }).fail(function () {
                        }).always(function () {
                            $('.cmsk-moz-pa-loading').addClass('hidden');
                        });
                    },
                    getWordsCount: function () {
                        $('.cmsk-moz-words-loading').removeClass('hidden');
                        $.ajax({
                            method: 'POST',
                            url: ajaxurl,
                            cache: false,
                            data: {
                                action: 'cmsk_get_words_count',
                                post_id: config.post_id,
                                query: data.currenturl
                            }
                        }).done(function (result_data) {
                            try {
                                if (true === result_data.success && result_data.data.result && result_data.data.result) {
                                    $('#swa-words').val(Math.round(result_data.data.result));
                                } else {
                                }
                            } catch (e) {
                            }
                        }).fail(function () {
                        }).always(function () {
                            $('.cmsk-moz-words-loading').addClass('hidden');
                        });
                    }
                },
                updated: function () {
                    var x = 1;
                    if (stop_update_loop) {
                        stop_update_loop = false;
                        return;
                    }

                    $.ajax({
                        method: 'POST',
                        url: ajaxurl,
                        cache: false,
                        data: {
                            action: config.action,
                            nonce: config.nonce,
                            post_id: config.post_id,
                            data: JSON.stringify(data)
                        }
                    }).done(function (result_data) {
                        if (typeof result_data.data !== 'undefined') {
                            stop_update_loop = true;
                            $.each(result_data.data, function (index, item) {
                                vm[index] = item;
                            });
                        }
                    });
                }
            });

            $(document).on('click', '.cmsk-showhide', function () {
                $(this).siblings('.cmsk-showhide-container').toggle();
                $(this).siblings('.cmsk-showhide-container').toggleClass('hidden');
            });

            $('#{0} .cmsk-mb-settings'.replace('{0}', uid)).on('click', function () {
                swal({
                    title: '',
                    html: $('#{0}-tpl2'.replace('{0}', uid)).html(),
                    showCancelButton: true,
                    confirmButtonText: 'Update',
                    width: 600,
                    onOpen: function (modal) {
                        $('#swal-label1').val(data.label1);
                        $('#swal-url1').val(data.url1);
                        $('#swal-label2').val(data.label2);
                        $('#swal-url2').val(data.url2);
                    },
                    preConfirm: function () {
                        return new Promise(function (resolve, reject) {
                            var isValid = true;
                            $('#swal-url1').removeClass('swal2-inputerror');
                            $('#swal-url2').removeClass('swal2-inputerror');
                            if ($('#swal-url1').val() != '' && !validUrl.isUri($('#swal-url1').val())) {
                                $('#swal-url1').addClass('swal2-inputerror');
                                reject('Incorrect URL!');
                                isValid = false;
                            }
                            if ($('#swal-url2').val() != '' && !validUrl.isUri($('#swal-url2').val())) {
                                $('#swal-url2').addClass('swal2-inputerror');
                                reject('Incorrect URL!');
                                isValid = false;
                            }
                            if (isValid) {
                                data.label1 = $('#swal-label1').val();
                                data.url1 = $('#swal-url1').val();
                                data.label2 = $('#swal-label2').val();
                                data.url2 = $('#swal-url2').val();
                                vm.$forceUpdate();
                                resolve();
                            }
                        });
                    }
                }).catch(swal.noop);
            });

            $('#{0} .cmsk-add-note'.replace('{0}', uid)).on('click', function (e) {
                swal({
                    title: '',
                    html: $('#{0}-tpl3'.replace('{0}', uid)).html(),
                    showCancelButton: true,
                    confirmButtonText: 'Update',
                    width: 600,
                    onOpen: function (modal) {
                        $('#swa-note').val(data.note);
                    },
                    preConfirm: function () {
                        return new Promise(function (resolve, reject) {
                            data.note = $('#swa-note').val();
                            vm.$forceUpdate();
                            resolve();
                        });
                    }
                }).catch(swal.noop);
                e.stopPropagation();
            });

            $('#{0} .cmsk-notifications'.replace('{0}', uid)).on('click', function (e) {
                var delta = Math.round((+new Date - data.notification_last_reset) / 1000);

                var minute = 60,
                        hour = minute * 60,
                        day = hour * 24,
                        days_ago;

                days_ago = Math.floor(delta / day);
                if (days_ago > 1) {
                    days_ago = ' (' + days_ago + ' days ago)';
                } else {
                    days_ago = ' (less than a day ago)';
                }
                var timestamp = (data.notification_last_reset ? ' Last reset was on: {0}'.replace('{0}', new Date(data.notification_last_reset).toLocaleString()) + days_ago : 'Notification was never set. Click Reset button to enable.');

                swal({
                    title: '',
                    html: $('#{0}-tpl4'.replace('{0}', uid)).html(),
                    showCancelButton: true,
                    confirmButtonText: 'Update',
                    width: 600,
                    onOpen: function (modal) {
                        $('#swa-send-notification').attr('checked', data.notification_send);
                        $('#swa-notification-email').val(data.notification_email);

                        $('#swa-last-reset').html(timestamp);
                        $('#swa-notification-reset').on('click', function () {
                            $(this).data('clicked', 1);
                            swal.clickConfirm();
                        });
                    },
                    preConfirm: function () {
                        return new Promise(function (resolve, reject) {
                            data.notification_send = $('#swa-send-notification').is(':checked');
                            data.notification_email = $('#swa-notification-email').val();

                            var wasResetClicked = $('#swa-notification-reset').data('clicked');
                            if (wasResetClicked) {
                                data.notification_last_reset = +new Date();
                                data.notification_email_sent = false;
                            }
                            vm.$forceUpdate();
                            resolve();
                        });
                    }
                }).catch(swal.noop);
                e.stopPropagation();
            });

            $('#{0} .cmsk-take-snapshot'.replace('{0}', uid)).on('click', function (e) {
                var time = new Date();
                var timestring = 'Snapshot from: {0}'.replace('{0}', time.toLocaleString());
                swal({
                    title: '',
                    html: $('#{0}-tpl5'.replace('{0}', uid)).html(),
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                    width: 600,
                    onOpen: function (modal) {
                        $('#swa-snapshot-date').html(timestring);
                    },
                    preConfirm: function () {
                        return new Promise(function (resolve, reject) {
                            data.new_snapshot_desc = $('#swa-snapshot-desc').val();
                            data.new_snapshot_date = Math.floor(time.getTime() / 1000);
                            vm.$forceUpdate();
                            resolve();
                        });
                    }
                }).catch(swal.noop);
                e.stopPropagation();
            });

            $('#{0} .cmsk-recalculate-statistics'.replace('{0}', uid)).on('click', function (e) {
                $(document).trigger('cmsk-recalculate-statistics');
                e.stopPropagation();
                return false;
            });

            $('#{0} #cmsk2-search-phrases'.replace('{0}', uid)).on('click', function () {
                swal({
                    title: '',
                    html: $('#{0}-tpl8'.replace('{0}', uid)).html(),
                    showCancelButton: true,
                    confirmButtonText: 'Update',
                    width: 600,
                    onOpen: function (modal) {
                        app = new Vue({
                            el: '#{0}-tpl8-content'.replace('{0}', uid),
                            data: {
                                searchphrases: data.searchphrases,
                                newphrase: ''
                            },
                            methods: {
                                addPhrase: function (event) {
                                    this.searchphrases.push(this.newphrase);
                                    this.newphrase = '';
                                },
                                removePhrase: function (event) {
                                    var target = $(event.target).closest('li');
                                    var confirmed = target.data('confirmed');
                                    var index = target.data('index');

                                    if (confirmed) {
                                        this.confirmRemovePhrase(index);
                                        this.unremovePhrase(event);
                                    } else {
                                        target.find('.cmsk-removal-confirmation').removeClass('hidden');
                                        target.data('confirmed', true);
                                    }
                                },
                                unremovePhrase: function (event) {
                                    var target = $(event.target).closest('li');
                                    target.find('.cmsk-removal-confirmation').addClass('hidden');
                                    target.data('confirmed', false);
                                },
                                confirmRemovePhrase: function (index) {
                                    if (index > -1) {
                                        this.searchphrases.splice(index, 1);
                                    }
                                }
                            }
                        });
                    },
                    preConfirm: function () {
                        return new Promise(function (resolve, reject) {
                            resolve();
                            vm.$forceUpdate();
                        });
                    }
                }).catch(swal.noop);
            });

            $(document).on('click', '#cmsk3-set-pa', function (e) {
                swal({
                    title: '',
                    html: $('#{0}-tpl7'.replace('{0}', uid)).html(),
                    showCancelButton: true,
                    confirmButtonText: 'Set PA',
                    width: 600,
                    onOpen: function (modal) {
                        $('#swa-page-authority').val(data.pa || null);
                        if (!data.mozready) {
                            $('.moz-content-wrapper').addClass('hidden');
                        }
                    },
                    preConfirm: function () {
                        return new Promise(function (resolve, reject) {
                            data.pa = $('#swa-page-authority').val();
                            vm.$forceUpdate();
                            resolve();
                        });
                    }
                }).then(function (value) {
//                    $( document ).trigger( 'cmsk-metrics-filter-change', { 'metrics_show_title': data.metrics_show_title, 'metrics_show_in_dashboard': data.metrics_show_in_dashboard } );
                    $(document).trigger('cmsk-pa-update', {'pa': data.pa});
                }).catch(swal.noop);

                e.stopPropagation();
            });

            $(document).on('click', '#cmsk3-set-words', function (e) {
                swal({
                    title: '',
                    html: $('#{0}-tpl7a'.replace('{0}', uid)).html(),
                    showCancelButton: true,
                    confirmButtonText: 'Set Words',
                    width: 600,
                    onOpen: function (modal) {
                        $('#swa-words').val(data.words || null);
                        if (!data.mozready) {
                            $('.moz-content-wrapper').addClass('hidden');
                        }
                    },
                    preConfirm: function () {
                        return new Promise(function (resolve, reject) {
                            data.words = $('#swa-words').val();
                            vm.$forceUpdate();
                            resolve();
                        });
                    }
                }).then(function (value) {
                    $(document).trigger('cmsk-words-update', {'words': data.words});
                }).catch(swal.noop);

                e.stopPropagation();
            });

        });
    })(jQuery);</script>

<script type="text/html" id="<?php echo sprintf('%s-tpl1', $uniqid); ?>">
    <span class="cmsk-tooltips-area">
<!--        <template v-if="url1">
            <span>&nbsp;</span>
            <template v-if="label1">
                <a v-bind:href="url1" v-bind:data-title="url1" data-action="cmsk-mb-dashboard" target="_blank">
                    {{label1}}
                </a>
            </template>
            <template v-else>
                <a v-bind:href="url1" v-bind:data-title="url1" data-action="cmsk-mb-dashboard" target="_blank">
                    <span class="dashicons dashicons-share-alt2"></span>
                </a>
            </template>
            </a>
        </template>
        <template v-if="url2">
            <span>&nbsp;</span>
            <template v-if="label2">
                <a v-bind:href="url2" v-bind:data-title="url2" data-action="cmsk-mb-dashboard" target="_blank">
                    {{label2}}
                </a>
            </template>
            <template v-else>
                <a v-bind:href="url2" v-bind:data-title="url2" data-action="cmsk-mb-dashboard" target="_blank" style="text-decoration: none;">
                    <span class="dashicons dashicons-share-alt2"></span>
                </a>
            </template>
            </a>
        </template>-->
        <span>&nbsp;</span>
        <span class="cmsk-dropdown cmsk-dropdown-dashboard">
            <a href="javascript:void(0)" class="cmsk-dropbtn cmsk-icon" onclick="return false;"><span class="dashicons dashicons-admin-generic"></span></a>
            <span class="cmsk-dropdown-content">
                <hr />
                <?php if ($metabox == metaboxes\KeywordsMetabox::METABOX): ?>
                    <hr />
                    <a href="javascript:void(0)" class="cmsk-recalculate-statistics">Update Keywords Stats</a>
                    <hr />
                    <a href="javascript:void(0)" id="cmsk1-import-help">CSV Import and Export</a>
                <?php endif; ?>
                <hr />
                <a href="https://www.cminds.com/seo-keyword-hound-video-course/" target="_blank">Video Course<span class="dashicons dashicons-editor-video"></span></a>
                <?php if ($metabox == metaboxes\KeywordsMetabox::METABOX): ?>
                    <a href="https://creativeminds.helpscoutdocs.com/article/2047-seo-keyword-hound-metaboxes-seo-keywords" target="_blank">Documentation <span class="dashicons dashicons-book-alt"></span></a>
                <?php endif; ?>
            </span>
        </span>
        <a href="javascript:void(0)" data-id="cmsk-mb-dashboard-note" data-action="cmsk-mb-dashboard-note" class="cmsk-icon cmsk-add-note" v-bind:data-title="note" style="text-decoration: none;">
            <span class="dashicons dashicons-admin-comments" v-bind:class="[note.length ? 'has-content' : '']"></span>
        </a>
        <?php if ($metabox == metaboxes\KeywordsMetabox::METABOX): ?>
            <a href="javascript:void(0)" class="cmsk-icon cmsk-dialog-opener" data-title="Show Pinned Keywords Modal" style="text-decoration: none;">
                <span class="dashicons dashicons-sticky"></span>
            </a>
        <?php endif; ?>
        <!--</template>-->
    </span>
</script>

<script type="text/html" id="<?php echo sprintf('%s-tpl2', $uniqid); ?>">
    <div class="swal2-title">Dashboard URLs to external services</div>
    <div style="margin-left: 80px; margin-top: 20px;">
        <span class="swal2-custom-dot1" style="margin-left:-80px; margin-top: 5px;">1</span>
        <label class="swal2-custom-label1" for="swal-label1">Dashboard URL name:</label>
        <input type="text" id="swal-label1" class="swal2-input" placeholder="name">
        <label class="swal2-custom-label1" for="swal-url1">Dashboard URL:</label>
        <input type="url" id="swal-url1" class="swal2-input" placeholder="URL">
    </div>
    <hr />
    <div style="margin-left: 80px; margin-top: 20px;">
        <span class="swal2-custom-dot1" style="margin-left:-80px; margin-top: 5px;">2</span>
        <label class="swal2-custom-label1" for="swal-label2">Dashboard URL name:</label>
        <input type="text" id="swal-label2" class="swal2-input" placeholder="name">
        <label class="swal2-custom-label1" for="swal-url2">Dashboard URL:</label>
        <input type="url" id="swal-url2" class="swal2-input" placeholder="URL">
    </div>
</script>

<script type="text/html" id="<?php echo sprintf('%s-tpl3', $uniqid); ?>">
    <div v-cloak>
        <div class="swal2-title"><?php echo $metabox_name; ?> Dashboard Note</div>
        <div class="swal2-content">
            <textarea class="swal2-textarea" id="swa-note" v-model="all"></textarea>
        </div>
    </div>
</script>

<script type="text/html" id="<?php echo sprintf('%s-tpl4', $uniqid); ?>">
    <div v-cloak>
        <div class="swal2-title">Notification Manager</div>
        <div class="swal2-content">
            <p>
                <span id="swa-last-reset"></span>
                <input type="button" value="Reset days count" name="notification-reset" id="swa-notification-reset" class="notification-reset swal2-styled swal2-custom-btn-green"/>
            </p>
        </div>
    </div>
</script>

<script type="text/html" id="<?php echo sprintf('%s-tpl5', $uniqid); ?>">
    <div v-cloak>
        <div class="swal2-title">New Snapshot</div>
        <p><span id="swa-snapshot-date"></date></p>
        <div class="swal2-content">
            <label><input type="text" id="swa-snapshot-desc" name="snapshot-desc" class="snapshot-desc cmsk-input-long swal2-input" placeholder="Enter the snapshot description (optional)" /></label>
        </div>
    </div>
</script>

<script type="text/html" id="<?php echo sprintf('%s-tpl7', $uniqid); ?>">
    <div v-cloak>
        <div class="swal2-title">Page Authority Settings</div>
        <div class="swal2-content">
            <label>
                <input type="text" id="swa-page-authority" name="pa" class="page-authority cmsk-input-short swal2-input" placeholder="PA" />
            </label>
            <div class="moz-content-wrapper" style="display:inline-block">
                <span class="dashicons dashicons-download" data-index="" onclick="<?php echo sprintf('window.cmsk_dashboard_vm[\'%s\']', $uniqid); ?>.getMozScore()" title="Load PageRank from MOZ API"></span>
                <span class="loading hidden cmsk-moz-pa-loading"><span class="cmsk-spinner"></span>Loading...</span>
            </div>
        </div>
    </div>
</script>

<script type="text/html" id="<?php echo sprintf('%s-tpl7a', $uniqid); ?>">
    <div v-cloak>
        <div class="swal2-title">Page Words Settings</div>
        <div class="swal2-content">
            <label>
                <input type="text" id="swa-words" name="words" class="page-authority cmsk-input-short swal2-input" placeholder="Words" />
            </label>
            <div class="moz-content-wrapper" style="display:inline-block">
                <span class="dashicons dashicons-download" data-index="" onclick="<?php echo sprintf('window.cmsk_dashboard_vm[\'%s\']', $uniqid); ?>.getWordsCount()" title="Load Words Count"></span>
                <span class="loading hidden cmsk-moz-words-loading"><span class="cmsk-spinner"></span>Loading...</span>
            </div>
        </div>
    </div>
</script>