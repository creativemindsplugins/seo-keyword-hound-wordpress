<?php
$cminds_plugin_config = array(
    'plugin-is-pro' => FALSE,
    'plugin-has-addons' => FALSE,
    'plugin-show-shortcodes' => TRUE,
    'plugin-version' => com\cminds\seokeywords\App::VERSION,
    'plugin-abbrev' => com\cminds\seokeywords\App::SLUG,
    'plugin-short-slug' => com\cminds\seokeywords\App::SLUG,
    'plugin-parent-short-slug' => '',
    'plugin-affiliate' => '',
    'plugin-show-guide' => TRUE,
    'plugin-show-upgrade' => TRUE,
    'plugin-show-upgrade-first' => TRUE,
    'plugin-guide-text' => '<br>Welcome to The SEO Keyword Hound! Here’s how to start:
<ol>
<li><strong>Integrate</strong> - Go through the Plugin options and Keywords Analysis settings to define your own keywords preferences.</li><br>
<li> <strong>Choose a Post</strong> - Create or edit the post / page you want to optimize keywords on.</li><br>
<li><strong>Save the Post</strong></li> - After you save or publish the post, you will see three new SEO panel (metabox) at the bottom of the post where you can build and monitor<strong>keywords</strong> lists. <a href="https://creativeminds.helpscoutdocs.com/article/2050-seo-keyword-hound-how-can-i-analyze-the-seo-of-a-page">Read the Documentation about the interface</a>.</li><br><br>
<li><strong>Boost Your Page’s SEO</strong> - Optimize your keywords and track them using the Keyword Hound.</li>
</ol>',
    'plugin-guide-video-height' => 180,
    'plugin-guide-videos' => array(
        array('title' => 'Video Guide', 'video_id' => '266504280'),
    ),
    'plugin-upgrade-text-list'      => array(
        array( 'title' => 'Why you should upgrade to Pro', 'video_time' => '0:00' ),
        array( 'title' => 'Tracking Dashboard', 'video_time' => '0:03' ),
        array( 'title' => 'Improved Keywords List ', 'video_time' => '0:28' ),
        array( 'title' => 'Competitors Metabox', 'video_time' => '1:17' ),
        array( 'title' => 'Compare Keywords with Competitors', 'video_time' => '1:55' ),
        array( 'title' => 'MOZ Ranks', 'video_time' => '2:25' ),
        array( 'title' => 'Google Analytics Integration', 'video_time' => '2:53' ),
        array( 'title' => 'Graphs', 'video_time' => '3:25' ),
    ),
    'plugin-upgrade-video-height' => 240,
    'plugin-upgrade-videos'       => array(
        array( 'title' => 'Glossary Introduction', 'video_id' => '312962253' ),
    ), 
    'plugin-upgrade-text' => '
    <p>Once upgrading the plugin to the premium version you gain access to additional components that streamline the keywords management and comparison process and help you track your progress easily</p>
  ',
   'plugin-redirect-after-install' => admin_url('admin.php?page=cm-seo-keywords'),
    'plugin-file' => com\cminds\seokeywords\App::PLUGIN_FILE,
    'plugin-dir-path' => plugin_dir_path(com\cminds\seokeywords\App::PLUGIN_FILE),
    'plugin-dir-url' => plugin_dir_url(com\cminds\seokeywords\App::PLUGIN_FILE),
    'plugin-basename' => plugin_basename(com\cminds\seokeywords\App::PLUGIN_FILE),
    'plugin-icon' => '',
    'plugin-name' => com\cminds\seokeywords\App::PLUGIN_NAME_EXTENDED,
    'plugin-license-name' => com\cminds\seokeywords\App::PLUGIN_NAME_EXTENDED,
    'plugin-slug' => '',
    'plugin-menu-item' => com\cminds\seokeywords\App::SLUG,
    'plugin-textdomain' => com\cminds\seokeywords\App::SLUG,
    'plugin-userguide-key' => '2036-seo-keyword-hound',
    'plugin-store-url' => 'https://www.cminds.com/wordpress-plugins-library/seo-keyword-hound-wordpress',
    'plugin-support-url' => '',
    'plugin-review-url' => '',
    'plugin-changelog-url' => 'https://www.cminds.com/wordpress-plugins-library/',
    'plugin-licensing-aliases' => array('SEO Keywords Aid', com\cminds\seokeywords\App::PLUGIN_NAME_EXTENDED),
    'plugin-compare-table' => '
            <div class="pricing-table" id="pricing-table"><h2 style="padding-left:10px;">Upgrade The Keyword Hound Plugin:</h2>
                <ul>
                    <li class="heading" style="background-color:red;">Current Edition</li>
                    <li class="price">FREE<br /></li>
                 <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Manage multiple keywords <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Manage and track multiple keywords, arrange keywords in groups"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Tailored Keyword Analysis <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Should the plugin look for keywords inside H1, H2, or H3? Should it analyze the first 100 or 200 words of the text separately? Define this and much more."></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Easy Import/Export <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Quickly manage keyword lists through CSV import and export."></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Keywords sticky note <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Use sticky note to easily edit your content using the keywords you choose"></span></li>
                    <hr>
                    Other CreativeMinds Offerings
                    <hr>
                <a href="https://www.cminds.com/store/cm-wordpress-plugins-yearly-membership/" target="blank"><img src="' . plugin_dir_url( __FILE__ ). 'views/banner_yearly-membership_220px.png"  width="220"></a><br>
                 </ul>
                <ul>
                    <li class="heading">Pro<a href="https://www.cminds.com/wordpress-plugins-library/seo-keyword-hound-wordpress/af/4806?utm_source=Wordpress&utm_medium=Hound" style="float:right;font-size:11px;color:white;" target="_blank">More</a></li>
                    <li class="price">$79.00<br /> <span style="font-size:14px;">(For one Year / Site)<br />Additional pricing options available <a href="https://www.cminds.com/wordpress-plugins-library/seo-keyword-hound-wordpress/af/4806?utm_source=WordPress&utm_medium=Hound" target="_blank"> >>> </a></span> <br /></li>
                    <li class="action"><a href="https://www.cminds.com/af/4806?utm_source=WordPress&utm_medium=Glossary&edd_action=add_to_cart&download_id=261167&edd_options[price_id]=1" style="font-size:18px;" target="_blank">Upgrade Now</a></li>
                     <li style="text-align:left;"><span class="dashicons dashicons-plus-alt"></span><span style="background-color:lightyellow">&nbsp;All Free Version Features </span><span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="All free features are supported in the pro"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Manage your competitors <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Manage a list of all your competitors, import and export competitors, mark important ones"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>See keywords your competitors use <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Compare your page’s keyword incidence, density, and location with your competitors. Analyze keyword use between individual competitors or a group of competitor "></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>See competitors by search queries <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Find competitors while connecting to Google search API and using search queries you choose"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Record changes you make <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Track your keyword list building and keyword use with a detailed log. Track page edit events made by which user, to track what changes are effective, made by whom."></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Track your page SEO KPIs <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Manage over time conversions, impressions and bounce rate"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Keep track of your page SEO events <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Automaticaly and manually create events related to your page SEO to help you understand trends and progress"></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Smart Notifications <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Set reminders for page owners to check page stats and regularly optimize content per specific events."></span></li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Manage all your site SEO from one dashboard  <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Manage all important SEO assets from one dashboard while helping your focus on the important pages first"></span></li>
                   <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Visualize your progress using graphs <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="All related statistics can be visualized using graphs to help you better understand trends and what causes them"></span></li>
                   <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Great Integrations <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Integrated with Google Analytics and Moz, to quickly fetch essential metrics from each source and put them side by side with your keyword and competitor list. "></span></li>
                     <li class="support" style="background-color:lightgreen; text-align:left; font-size:14px;"><span class="dashicons dashicons-yes"></span> One year of expert support <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="You receive 365 days of WordPress expert support. We will answer questions you have and also support any issue related to the plugin. We will also provide on-site support."></span><br />
                         <span class="dashicons dashicons-yes"></span> Unlimited product updates <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="During the license period, you can update the plugin as many times as needed and receive any version release and security update"></span><br />
                        <span class="dashicons dashicons-yes"></span> Plugin can be used forever <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="Once license expires, If you choose not to renew the plugin license, you can still continue to use it as long as you want."></span><br />
                        <span class="dashicons dashicons-yes"></span> Save 40% once renewing license <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="Once license expires, If you choose to renew the plugin license you can do this anytime you choose. The renewal cost will be 35% off the product cost."></span></li>
                 </ul>
                 </div>',
);