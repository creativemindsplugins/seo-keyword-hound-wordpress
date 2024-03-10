<?php

namespace com\cminds\seokeywords\plugin\models;

use com\cminds\seokeywords\App;
use com\cminds\seokeywords\plugin\helpers;
use com\cminds\seokeywords\plugin\misc;

class KeywordStatistics {

    const DB_TABLE = 'cmsk_keywords_stats';
    const DB_VERSION_KEY = 'cmsk_keywords_stats_db_version';
    const DB_VERSION = 170020;

    private $_keyword;

    public function __construct($keyword) {
        $this->_keyword = $keyword;
    }

    public static function dbInit() {

        global $wpdb;

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $charset = ( defined('DB_CHARSET' && '' !== DB_CHARSET) ) ? DB_CHARSET : 'utf8_general_ci';
        $collate = ( defined('DB_COLLATE' && '' !== DB_COLLATE) ) ? DB_COLLATE : 'utf8_general_ci';
        $tablename = $wpdb->prefix . static::DB_TABLE;

        $sql = "CREATE TABLE `{$tablename}` (
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
	`keyword` VARCHAR(250) NOT NULL COLLATE '{$charset}',
	`post_id` BIGINT(20) NULL DEFAULT NULL,
	`date` DATE NOT NULL,
        `period` INT(10) NOT NULL,
	`impressions` INT(10) NULL DEFAULT NULL,
        `clicks` INT(10) NULL DEFAULT NULL,
        `ctr` DECIMAL(10,2) NULL DEFAULT NULL,
        `position` DECIMAL(10,2) NULL DEFAULT NULL,
        `is_manual` TINYINT(1) NOT NULL DEFAULT '0',
        `cmsk_density` DECIMAL(10,2) NULL DEFAULT NULL,
        `cmsk_title` INT(10) NULL DEFAULT NULL,
        `cmsk_headers` INT(10) NULL DEFAULT NULL,
        `cmsk_content` INT(10) NULL DEFAULT NULL,
        `cmsk_url` INT(10) NULL DEFAULT NULL,
        `cmsk_first100` INT(10) NULL DEFAULT NULL,
        `cmsk_marked` INT(10) NULL DEFAULT NULL,
        `cmsk_images` INT(10) NULL DEFAULT NULL,
        `post_title` TEXT NULL DEFAULT NULL,
        `permalink` TEXT NULL DEFAULT NULL,
        `post_html_title` TEXT NULL DEFAULT NULL,
        `custom_text` TEXT NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
        )
        COLLATE='{$collate}'
        ";

        dbDelta($sql);
    }

    public function addManualEntry($post_id, $data) {
        return $this->addEntry($post_id, array_merge(['is_manual' => 1], $data));
    }

    public function addEntry($post_id, $data) {
        global $wpdb;

        $data = array_merge([
            'post_id' => $post_id,
            'keyword' => $this->_keyword
                ], $data);

        if (empty($data['ctr'])) {
            if (isset($data['impressions']) && isset($data['clicks']) && intval($data['impressions']) > 0 && is_numeric($data['clicks'])) {
                $data['ctr'] = 100 * intval($data['clicks']) / intval($data['impressions']);
            }
        }

        $data = array_merge($this->getEntryAdditionalData($post_id), $data);

        $res = $wpdb->insert($wpdb->prefix . static::DB_TABLE, $data);

        $this->updateKeywordTrend($post_id);

        return $res;
    }

    public function removeEntry($post_id, $id) {
        global $wpdb;
        $res = $wpdb->delete($wpdb->prefix . static::DB_TABLE, ['keyword' => $this->_keyword, 'id' => $id]);
        $this->updateKeywordTrend($post_id);
        return $res;
    }

    public function getEntriesByPostId($post_id, $limit = 3) {
        global $wpdb;
        $tablename = $wpdb->prefix . static::DB_TABLE;
        $sql = "select * from {$tablename} where post_id = %s and keyword = %s order by date desc limit {$limit}";
        return $wpdb->get_results($wpdb->prepare($sql, $post_id, $this->_keyword), ARRAY_A);
    }

    public function getEntriesByPostIdAndDate($post_id, $date1, $date2) {
        global $wpdb;
        $tablename = $wpdb->prefix . static::DB_TABLE;
        $sql = "select * from {$tablename} where post_id = %s and date >= %s and date <= %s and keyword = %s order by date desc";
        return $wpdb->get_results($wpdb->prepare($sql, $post_id, $date1, $date2, $this->_keyword), ARRAY_A);
    }

    public function getEntriesCountByPostId($post_id) {
        global $wpdb;
        $tablename = $wpdb->prefix . static::DB_TABLE;
        $sql = "select count(*) from {$tablename} where post_id = %s and keyword = %s";
        return $wpdb->get_var($wpdb->prepare($sql, $post_id, $this->_keyword));
    }

    private function getEntryAdditionalData($post_id) {
        $generic = GenericPost::getInstance($post_id);
        $data = $generic->getSeoKeywordsDataForKeyword($this->_keyword);
        if (empty($data)) {
            return [];
        }
        return [
            'cmsk_density' => $data->density->content,
            'cmsk_title' => $data->stats->title,
            'cmsk_headers' => $data->stats->headers,
            'cmsk_content' => $data->stats->content,
            'cmsk_url' => $data->stats->url,
            'cmsk_first100' => $data->stats->first100,
            'cmsk_marked' => $data->stats->marked,
            'cmsk_images' => $data->stats->images,
            'post_html_title' => misc\Misc::wpRemoteGetTitle(get_permalink($post_id)),
            'post_title' => get_the_title($post_id),
            'permalink' => get_permalink($post_id)
        ];
    }

    public function updateKeywordTrend($post_id) {
        $generic = GenericPost::getInstance($post_id);
        $items = $this->getEntriesByPostId($post_id, 3);
        $factor = NULL;
        if (count($items) > 1) {
            $arr = [];
            $total = 0;
            foreach ($items as $item) {
                $arr[(strtotime($item['date']) - strtotime($items[0]['date'])) / (60 * 60 * 24)] = $item['impressions'];
                $total += $item['impressions'];
            }
            $trend = helpers\TrendHelper::getLinearRegression($arr);
            $factor = $trend['slope'] / $trend['intercept'];
        }
        $generic->setSeoKeywordTrend($this->_keyword, $factor);
    }

}
