<?php

use com\cminds\seokeywords\App;
use com\cminds\seokeywords\plugin;
?>

<div class="clear"></div>
<hr />

<?php echo do_shortcode(sprintf('[cminds_free_activation id=%s]', App::SLUG)); ?>

<div id="cminds_settings_container">
    
    <?php add_thickbox(); ?>

    <div class="cmsk">

        <h2 class="nav-tab-wrapper">
            <?php
            $tabs = array(
                'guide' => 'Installation Guide',
                'general' => 'Plugin Options',
                'seo-stats' => 'Keywords Analysis',
                'upgrade' => 'Upgrade'
            );

            foreach ($tabs as $k => $v):

                echo sprintf('<a href="#tab-%s" data-for="%s" class="nav-tab">%s</a>', $k, $k, $v);

            endforeach;
            ?>
        </h2>
        <form method="post">

            <?php do_action('cmsk_options_form_begin'); ?>

            <?php
            foreach (array_keys($tabs) as $k):

                echo sprintf('<div data-role="tab" data-tab="%s" style="display: none;">', $k);

                $content = '';
                $filename = sprintf('views/backend/pages/options/tabs/%s.php', str_replace('-', '_', $k));
                if (file_exists(plugin_dir_path(App::PLUGIN_FILE) . $filename)) {
                    $content = plugin\helpers\ViewHelper::load($filename);
                }

                echo apply_filters('cmsk_options_tab_content', $content, $k);

                echo '</div>';

            endforeach;
            ?>

            <?php wp_nonce_field(plugin\controllers\OptionsPageController::ACTION_SAVE_OPTIONS); ?>

        </form>

    </div>

    <style type="text/css">
        .cmsk h2.nav-tab-wrapper{
            margin-top: 20px;
        }
        .cmsk .nav-tab{
            box-shadow: none;
        }
        .cmsk .card{
            max-width: none;
        }
        .cmsk-month-text1{
            width: 50px;
        }
        .cmsk-month-text2{
            width: 150px;
        }
        .cmsk-form-table-months{
            width: auto;
        }
        .cmsk-form-table-months td{
            width: auto;
            padding: 0 10px 0 0;
        }
        #modal-window-restore-defaults-step-a,
        #modal-window-restore-defaults-step-b,
        #modal-window-restore-defaults-step-a .submit,
        #modal-window-restore-defaults-step-b .submit{
            text-align: center;
        }
    </style>

    <script type="text/javascript">
        (function ($) {
            "use strict";
            $('.cmsk .nav-tab').on('click', function () {
                if ($(this).hasClass('thickbox')) {
                    return;
                }
                $('.cmsk .nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                $('.cmsk *[data-role="tab"]').hide();
                $('.cmsk *[data-role="tab"][data-tab="' + $(this).data('for') + '"]').show();
            });
            if ($('.cmsk a[href="' + window.location.hash + '"]').click().length != 1) {
                $('.cmsk a.nav-tab').first().click();
            }
            $('.cmsk input[type="submit"]').on('click', function () {
                if ($('.cmsk form').find(':invalid')) {
                    var tab = $('.cmsk form').find(':invalid').first().parents('*[data-role="tab"]').data('tab');
                    $('.cmsk').find('a[data-for="' + tab + '"]').click();
                }
            });
        })(jQuery);
    </script>

</div>