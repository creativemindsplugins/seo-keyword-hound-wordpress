<?php

namespace com\cminds\seokeywords\plugin\models;

use com\cminds\seokeywords\App;

class Options extends OptionsAbstract {

    const KEYWORDS_METABOX_SCREEN = 'keywords_metabox_screen';
    const KEYWORDS_METABOX_ROLES = 'keywords_metabox_roles';
    const LOCAL_PAGES_LOADED_EXTERNALLY = 'local_pages_loaded_externally';
    const SHOW_CONVERSION = 'show_conversion';
    const SHOW_CONVERSION_VALUE = 'show_conversion_value';
    const SITE_DOMAINAUTHORITY = 'site_domainauthority';
    const ALLOW_MULTIPLE_EXPANDED = 'keywords_metabox_allow_multiple_expanded';
    const FIRST_X_WORDS = 'first_x_words';
    const STAT_TABLE_COLUMNS = 'stat_table_columns';
    const CONTENT_HEADERS = 'content_headers';
    const DENSITY_THRESHOLD = 'density_threshold';
    const DATA_COLLECT_PERIOD = 'keywords_data_collection_period';
    const SHOW_ON_DASHBOARD = 'show_on_dashboard';
    const TRENDS_MARGIN = 'trends_margin';
    const TRENDS_POINTS = 'trends_points';
    const GA_IS_LANDINGPAGES_WITH_HOSTNAME = 'ga_is_landingpages_with_hotsname';
    const IS_KEYWORD_CHANGELOG = 'is_keyword_changelog';
    const KEYWORDS_COMPARE_CACHE_TTL = 'keywords_compare_cache_ttl';
    const METABOX_GRID_HEIGHT = 'metabox_grid_height';
    const UA_DESKTOP = 'keywords_compare_ua_desktop';
    const UA_MOBILE = 'keywords_compare_ua_mobile';
    const KEYWORDS_METABOX_WARNING_NOT_FOUND_ENABLED = 'keyword_metabox_warning_not_found_enabled';
    const KEYWORDS_METABOX_WARNING_DENSITY_ENABLED = 'keyword_metabox_warning_density_enabled';
    const METABOX_GRID_DISPLAY_DENSITY = 'metabox_grid_display_density';
    /*
     * Keywords Colors
     */
    const METABOX_GRID_KEYWORD_NOTE_COLORS = 'metabox_grid_keyword_colors';
    const METABOX_GRID_KEYWORD_NOTE_COLORS_LABELS = 'metabox_grid_keyword_colors_labels';
    /*
     * Events Colors
     */
    const METABOX_GRID_EVENT_COLORS = 'metabox_grid_event_colors';
    const METABOX_GRID_EVENT_COLORS_LABELS = 'metabox_grid_event_colors_labels';
    /*
     * Custom Events Colors
     */
    const METABOX_GRID_CUSTOM_EVENT_COLORS = 'metabox_grid_custom_event_colors';
    const METABOX_GRID_CUSTOM_EVENT_COLORS_LABELS = 'metabox_grid_custom_event_colors_labels';
    /*
     * Metrics Labels
     */
    const METRICS_TABLE_COLUMN_LABELS = 'metrics_table_additional_column_labels';

    protected static $_defaultOptions = [
        self::KEYWORDS_METABOX_SCREEN => [],
        self::KEYWORDS_METABOX_ROLES => ['administrator'],
        self::LOCAL_PAGES_LOADED_EXTERNALLY => true,
        self::SHOW_CONVERSION => false,
        self::SHOW_CONVERSION_VALUE => false,
        self::SITE_DOMAINAUTHORITY => '',
        self::ALLOW_MULTIPLE_EXPANDED => false,
        self::STAT_TABLE_COLUMNS => ['density', 'title', 'headers', 'content', 'url', 'first100', 'marked', 'images'],
        self::FIRST_X_WORDS => 100,
        self::CONTENT_HEADERS => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
        self::DENSITY_THRESHOLD => 6,
        self::DATA_COLLECT_PERIOD => 10,
        self::SHOW_ON_DASHBOARD => [],
        self::TRENDS_MARGIN => 5,
        self::TRENDS_POINTS => 5,
        self::IS_KEYWORD_CHANGELOG => 1,
        self::KEYWORDS_COMPARE_CACHE_TTL => 86400,
        self::METABOX_GRID_HEIGHT => 300,
        self::UA_DESKTOP => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
        self::UA_MOBILE => 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A356 Safari/604.1',
        self::KEYWORDS_METABOX_WARNING_NOT_FOUND_ENABLED => 1,
        self::KEYWORDS_METABOX_WARNING_DENSITY_ENABLED => 1,
        self::METABOX_GRID_DISPLAY_DENSITY => 'normal',
        self::METABOX_GRID_KEYWORD_NOTE_COLORS => ['#61BB46', '#FDB827', '#F5821F', '#E03A3E', '#963D97', '#009DDC'],
        self::METABOX_GRID_KEYWORD_NOTE_COLORS_LABELS => [],
        self::METABOX_GRID_EVENT_COLORS => ['#61BB46', '#FDB827', '#F5821F', '#E03A3E', '#963D97'],
        self::METABOX_GRID_EVENT_COLORS_LABELS => ['Title Change', 'SEO Title Change', 'Meta Description Change', 'Snapshot', 'Analytics'],
        self::METABOX_GRID_CUSTOM_EVENT_COLORS => ['#009DDC', '#8D2F5D', '#15044C', '#FFEE3B', '#0D5F00'],
        self::METABOX_GRID_CUSTOM_EVENT_COLORS_LABELS => [],
        self::METRICS_TABLE_COLUMN_LABELS => ['Note', '', '', ''],
        ];

    public static function getSiteDomainAuthority() {
        return static::getOption(static::SITE_DOMAINAUTHORITY);
    }

    public static function setSiteDomainAuthority($value) {
        return static::setOption(static::SITE_DOMAINAUTHORITY, $value);
    }

    public static function getIsLocalPagesLoadedExternally() {
//        return !empty( static::getOption( static::LOCAL_PAGES_LOADED_EXTERNALLY ) );
        return true;
    }

    public static function getIsShowConversion() {
        return !empty(static::getOption(static::SHOW_CONVERSION));
    }

    public static function getIsShowConversionValue() {
        return !empty(static::getOption(static::SHOW_CONVERSION_VALUE));
    }

    public static function getIsAllowMultipleExpanded() {
        return !empty(static::getOption(static::ALLOW_MULTIPLE_EXPANDED));
    }

    public static function getDashboardMetricsIds() {
        $ids = static::getOption(static::SHOW_ON_DASHBOARD);
        return is_array($ids) ? $ids : [];
    }

    public static function setDashboardMetricsIds($ids) {
        return static::updateOption(static::SHOW_ON_DASHBOARD, $ids);
    }

    public static function getKeywordsMetaboxScreen() {
        return static::getOption(static::KEYWORDS_METABOX_SCREEN);
    }

    public static function getKeywordsMetaboxRoles() {
        return static::getOption(static::KEYWORDS_METABOX_ROLES);
    }

    public static function getFirstXWords() {
        $i = intval(static::getOption(static::FIRST_X_WORDS));
        if ($i <= 0) {
            $i = 100;
        }
        return $i;
    }

    public static function getStatTableColumns() {
        return array_values(array_intersect(array_keys(static::getAllStatTableColumnsAssoc()), (array) static::getOption(static::STAT_TABLE_COLUMNS)));
    }

    public static function getAllStatTableColumnsAssoc() {
        return [
            'density' => 'Density',
            'title' => 'Title',
            'headers' => 'Headers',
            'content' => 'Content',
            'url' => 'URL',
            'first100' => sprintf('First %s', static::getFirstXWords()),
            'marked' => 'Marked Out',
            'images' => 'Images'
        ];
    }

    public static function getContentHeaders() {
        return array_values(array_intersect(array_keys(static::getAllContentHeadersAssoc()), (array) static::getOption(static::CONTENT_HEADERS)));
    }

    public static function getAllContentHeadersAssoc() {
        return [
            'h1' => 'H1',
            'h2' => 'H2',
            'h3' => 'H3',
            'h4' => 'H4',
            'h5' => 'H5',
            'h6' => 'H6'
        ];
    }

    public static function getDensityThreshold() {
        $i = intval(static::getOption(static::DENSITY_THRESHOLD));
        $i = min($i, 100);
        $i = max($i, 0);
        return $i;
    }

    public static function getDataCollectPeriod() {
        return static::getOption(static::DATA_COLLECT_PERIOD);
    }

    public static function setDataCollectPeriod($value) {
        return static::setOption(static::DATA_COLLECT_PERIOD, $value);
    }

    public static function getTrendsMargin() {
        return static::getOption(static::TRENDS_MARGIN);
    }

    public static function setTrendsMargin($value) {
        return static::setOption(static::TRENDS_MARGIN, $value);
    }

    public static function getTrendsPoints() {
        return static::getOption(static::TRENDS_POINTS);
    }

    public static function setTrendsPoints($value) {
        return static::setOption(static::TRENDS_POINTS, $value);
    }

    public static function getIsLandingpagesWithHostname() {
        return !empty(static::getOption(static::GA_IS_LANDINGPAGES_WITH_HOSTNAME));
    }

    public static function setIsLandingpagesWithHostname($value) {
        return static::setOption(static::GA_IS_LANDINGPAGES_WITH_HOSTNAME, $value);
    }

    public static function getIsKeywordChangelog() {
        return !empty(static::getOption(static::IS_KEYWORD_CHANGELOG));
    }

    public static function setIsKeywordChangelog($value) {
        return static::setOption(static::IS_KEYWORD_CHANGELOG, $value);
    }

    public static function getKeywordsCompareCacheTTL() {
        return static::getOption(static::KEYWORDS_COMPARE_CACHE_TTL);
    }

    public static function setKeywordsCompareCacheTTL($value) {
        return static::setOption(static::KEYWORDS_COMPARE_CACHE_TTL, $value);
    }

    public static function getAllKeywordsCompareCacheTTLAssoc() {
        return [
            3600 => '1 Hour',
            43200 => '12 Hours',
            86400 => '1 Day',
            604800 => '1 Week',
            2592000 => '1 Month'
        ];
    }

    public static function getMetaboxGridHeight() {
        return static::getOption(static::METABOX_GRID_HEIGHT);
    }

    public static function setMetaboxGridHeight($value) {
        return static::setOption(static::METABOX_GRID_HEIGHT, $value);
    }

    public static function getAllMetaboxGridHeightAssoc() {
        return [
            100 => 'Tiny',
            200 => 'Small',
            300 => 'Normal',
            400 => 'Large',
            600 => 'Huge'
        ];
    }

    public static function getUADesktop() {
        return static::getOption(static::UA_DESKTOP);
    }

    public static function setUADesktop($value) {
        return static::setOption(static::UA_DESKTOP, $value);
    }

    public static function getUAMobile() {
        return static::getOption(static::UA_MOBILE);
    }

    public static function setUAMobile($value) {
        return static::setOption(static::UA_MOBILE, $value);
    }

    public static function getKeywordMetaboxWarningNotFoundEnabled() {
        return static::getOption(static::KEYWORDS_METABOX_WARNING_NOT_FOUND_ENABLED);
    }

    public static function setKeywordMetaboxWarningNotFoundEnabled($value) {
        return static::setOption(static::KEYWORDS_METABOX_WARNING_NOT_FOUND_ENABLED, $value);
    }

    public static function getKeywordMetaboxWarningDensityEnabled() {
        return static::getOption(static::KEYWORDS_METABOX_WARNING_DENSITY_ENABLED);
    }

    public static function setKeywordMetaboxWarningDensityEnabled($value) {
        return static::setOption(static::KEYWORDS_METABOX_WARNING_DENSITY_ENABLED, $value);
    }

    public static function getMetaboxGridDisplayDensity() {
        return static::getOption(static::METABOX_GRID_DISPLAY_DENSITY);
    }

    public static function setMetaboxGridDisplayDensity($value) {
        return static::setOption(static::METABOX_GRID_DISPLAY_DENSITY, $value);
    }

    public static function getAllMetaboxGridDisplayDensityAssoc() {
        return [
            'comfortable' => 'Comfortable',
            'cozy' => 'Cozy',
            'compact' => 'Compact'
        ];
    }

    public static function getKeywordsColors($index = null) {
        $arr = (array) static::getOption(static::METABOX_GRID_KEYWORD_NOTE_COLORS);
        if (null === $index) {
            return $arr;
        } else {
            return isset($arr[$index]) ? $arr[$index] : '';
        }
    }

    public static function setKeywordsColors($value) {
        return static::setOption(static::METABOX_GRID_KEYWORD_NOTE_COLORS, $value);
    }

    public static function getKeywordsColorsLabels($index = null) {
        $arr = (array) static::getOption(static::METABOX_GRID_KEYWORD_NOTE_COLORS_LABELS);
        if (null === $index) {
            return $arr;
        } else {
            return isset($arr[$index]) ? $arr[$index] : '';
        }
    }

    public static function getKeywordsColorLabel($matchColor = null) {
        $colors = static::getKeywordsColors();
        foreach ($colors as $key => $color) {
            if ($matchColor == $color) {
                return static::getKeywordsColorsLabels($key);
            }
        }
        return null;
    }

    public static function getKeywordsColorsWithLabels() {
        $colorsLabels = ['#999' => '-No Label-'];
        $colors = static::getKeywordsColors();
        $labels = static::getKeywordsColorsLabels();
        foreach ($colors as $key => $color) {

            if (!empty($labels[$key])) {
                $colorsLabels[$color] = $labels[$key];
            }
        }
        return $colorsLabels;
    }

    public static function setKeywordsColorsLabels($value) {
        return static::setOption(static::METABOX_GRID_KEYWORD_NOTE_COLORS_LABELS, $value);
    }

    public static function getEventsColors($index = null) {
        $arr = (array) static::getOption(static::METABOX_GRID_EVENT_COLORS);
        if (null === $index) {
            return $arr;
        } else {
            return isset($arr[$index]) ? $arr[$index] : '';
        }
    }

    public static function setEventsColors($value) {
        return static::setOption(static::METABOX_GRID_EVENT_COLORS, $value);
    }

    public static function getEventsColorsLabels($index = null) {
        $arr = (array) static::getOption(static::METABOX_GRID_EVENT_COLORS_LABELS);
        if (null === $index) {
            return $arr;
        } else {
            return isset($arr[$index]) ? $arr[$index] : '';
        }
    }

    public static function getEventsColorLabel($matchColor = null) {
        $colors = static::getEventsColors();
        foreach ($colors as $key => $color) {
            if ($matchColor == $color) {
                return static::getEventsColorsLabels($key);
            }
        }
        return null;
    }

    public static function getEventsColorsWithLabels() {
        $colorsLabels = ['#999' => '-No Label-'];
        $colors = static::getEventsColors();
        $labels = static::getEventsColorsLabels();
        foreach ($colors as $key => $color) {

            if (!empty($labels[$key])) {
                $colorsLabels[$color] = $labels[$key];
            }
        }
        return $colorsLabels;
    }

    public static function getCustomEventsColors($index = null) {
        $arr = (array) static::getOption(static::METABOX_GRID_CUSTOM_EVENT_COLORS);
        if (null === $index) {
            return $arr;
        } else {
            return isset($arr[$index]) ? $arr[$index] : '';
        }
    }

    public static function setCustomEventsColors($value) {
        return static::setOption(static::METABOX_GRID_CUSTOM_EVENT_COLORS, $value);
    }

    public static function getCustomEventsColorsLabels($index = null) {
        $arr = (array) static::getOption(static::METABOX_GRID_CUSTOM_EVENT_COLORS_LABELS);
        if (null === $index) {
            return $arr;
        } else {
            return isset($arr[$index]) ? $arr[$index] : '';
        }
    }

    public static function getCustomEventsColorLabel($matchColor = null) {
        $colors = static::getCustomEventsColors();
        foreach ($colors as $key => $color) {
            if ($matchColor == $color) {
                return static::getCustomEventsColorsLabels($key);
            }
        }
        return null;
    }

    public static function getCustomEventsColorsWithLabels() {
        $colorsLabels = ['#999' => '-No Label-'];
        $colors = static::getCustomEventsColors();
        $labels = static::getCustomEventsColorsLabels();
        foreach ($colors as $key => $color) {

            if (!empty($labels[$key])) {
                $colorsLabels[$color] = $labels[$key];
            }
        }
        return $colorsLabels;
    }

    public static function getMetricTableLabels($index = null) {
        $arr = (array) static::getOption(static::METRICS_TABLE_COLUMN_LABELS);
        if (count($arr) < 4) {
            $arr = array_merge(['Note'], $arr);
        }
        if (null === $index) {
            return $arr;
        } else {
            return isset($arr[$index]) ? $arr[$index] : '';
        }
    }
    
    public static function getNotificationDefaults(){
        $arr = [
            'receiver' => self::getNotificationsReceiver(),
            'type' => self::getNotificationsType(),
            'text' => self::getNotificationsText(),
            'days' => self::getNotificationsInterval(),
            'emails' => self::getNotificationsEmails(),
            'disabled' => '1'
        ];
        return $arr;
    }

}
