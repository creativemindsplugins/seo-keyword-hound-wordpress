<?php

namespace com\cminds\seokeywords\plugin\models;

use com\cminds\seokeywords\plugin\seo;
use com\cminds\seokeywords\plugin\misc;

class GenericPost extends PostTypeAbstract {

    const META_SEO_KEYWORDS_DATA = '_cmsk_seo_keywords_data';
    const META_SEO_KEYWORDS_TRENDS_DATA = '_cmsk_seo_keywords_trends_data';
    const META_SEO_KEYWORDS_DASHBOARD_URL_DATA = '_cmsk_seo_keywords_dashboard_url';
    const META_METRICS_TABLE_ADDITIONAL_DATA = '_cmsk_metrics_table_additional_data';
    const META_SEO_COMPETITORS_DATA = '_cmsk_seo_competitors_data';
    const META_SEO_COMPETITORS_DASHBOARD_URL_DATA = '_cmsk_seo_competitors_dashboard_url';
    const META_PAGE_METRICS_DASHBOARD_URL_DATA = '_cmsk_page_metrics_dashboard_url';
    const META_PAGE_METRICS_TREND_DATA = '_cmsk_page_metrics_trend_data';
    const META_SEO_DELETED_KEYWORDS = '_cmsk_seo_deleted_keywords_keywords';
    const META_SEO_DELETED_COMPETITORS_URLS = '_cmsk_seo_deleted_competitors_urls';
    const META_ALL_KEYWORDS_DATA = '_cmsk_seo_all_keywords_data';
    const META_NOTIFICATION_DATE = '_cmsk_notification_date';
    const META_SEO_KEYWORDS_SNAPSHOTS_DATA = '_cmsk_seo_keywords_snapshots_data';

    public function getSeoKeywordsData() {
        $res = $this->getPostMeta(static::META_SEO_KEYWORDS_DATA);
        if (!is_array($res)) {
            $res = [];
        }
        return seo\KeywordsHelper::fixItems($res);
    }

    public function setSeoKeywordsData($value) {
        $old = $this->getSeoKeywordsDataItemsKeywords();
        $new = seo\KeywordsHelper::getKeywords($value['items']);
        $deletedKeywords = $this->getSeoDeletedKeywords();

        $undeletedKeywords = array_merge(array_intersect($deletedKeywords, $new), $value['undeletedkeywords']);
        $this->removeSeoDeletedKeywords($undeletedKeywords);

        $this->addSeoDeletedKeywords(array_diff($old, $new));
        unset($value['undeletedkeywords']);
        return $this->setPostMeta(static::META_SEO_KEYWORDS_DATA, $value);
    }

    public function getAllKeywordsData() {
        $res = $this->getPostMeta(static::META_ALL_KEYWORDS_DATA);
        if (!is_array($res)) {
            $res = [];
        }
        return seo\KeywordsHelper::fixItems($res);
    }

    public function setAllKeywordsData($value) {
        return $this->setPostMeta(static::META_ALL_KEYWORDS_DATA, $value);
    }

    public function getSeoKeywordsTrendsData() {
        return (array) $this->getPostMeta(static::META_SEO_KEYWORDS_TRENDS_DATA);
    }

    public function setSeoKeywordsTrendsData($value) {
        return $this->setPostMeta(static::META_SEO_KEYWORDS_TRENDS_DATA, $value);
    }

    public function getSeoKeywordTrend($keyword) {
        $arr = $this->getSeoKeywordsTrendsData();
        return empty($arr[$keyword]) ? NULL : $arr[$keyword];
    }

    public function setSeoKeywordTrend($keyword, $trend) {
        $arr = $this->getSeoKeywordsTrendsData();
        $arr[$keyword] = $trend;
        return $this->setSeoKeywordsTrendsData($arr);
    }

    public function getSeoKeywordsDashboardUrlData() {
        return (array) $this->getPostMeta(static::META_SEO_KEYWORDS_DASHBOARD_URL_DATA);
    }

    public function setSeoKeywordsDashboardUrlData($value) {
        return $this->setPostMeta(static::META_SEO_KEYWORDS_DASHBOARD_URL_DATA, $value);
    }

    public function getMetricTableAdditionalData() {
        $data = $this->getPostMeta(static::META_METRICS_TABLE_ADDITIONAL_DATA);
        if (empty($data)) {
            $data = null;
        }
        return $data;
    }

    public function setMetricTableAdditionalData($value) {
        return $this->setPostMeta(static::META_METRICS_TABLE_ADDITIONAL_DATA, $value);
    }

    public function getSendNotificationData() {
        return $this->getPostMeta(static::META_NOTIFICATION_DATE);
    }

    public function setSendNotificationData($value) {
        $lastReset = $value['notification_last_reset'];
        $intervalDays = Options::getOption(Options::NOTIFICATION_INTERVAL);

        $date = date('Y-m-d', $lastReset / 1000);
        $notificationTime = strtotime(' +' . $intervalDays . ' DAYS', strtotime($date));
        $this->setPostMeta(static::META_NOTIFICATION_DATE, $notificationTime);
        return true;
    }

    public function removeSnapshotsData() {
        $res = $this->setPostMeta(static::META_SEO_KEYWORDS_SNAPSHOTS_DATA, []);
        return $res;
    }

    public function removeSnapshotData($timestamp) {
        $currentSnapshots = $this->getSnapshotsData();
        if (isset($currentSnapshots[$timestamp])) {
            unset($currentSnapshots[$timestamp]);
        }
        return $this->setPostMeta(static::META_SEO_KEYWORDS_SNAPSHOTS_DATA, $currentSnapshots);
    }

    public function setSnapshotsData($snapshots) {
        $res = $this->setPostMeta(static::META_SEO_KEYWORDS_SNAPSHOTS_DATA, $snapshots);
        return $res;
    }

    public function getSnapshotsData() {
        $res = $this->getPostMeta(static::META_SEO_KEYWORDS_SNAPSHOTS_DATA);
        return $res;
    }

    public function getSnapshotData($timestamp) {
        $res = $this->getSnapshotsData();
        if (isset($res[$timestamp])) {
            return seo\KeywordsHelper::fixItems($res[$timestamp]);
        } else {
            return null;
        }
    }

    public function setStoreSnapshotsData($value) {
        $currentKeywords = $this->getSeoKeywordsData();
        $currentAllKeywords = $this->getAllKeywordsData();
        $currentSnapshots = $this->getSnapshotsData();
        $currentSnapshots[$value['new_snapshot_date']] = [
            'desc' => $value['new_snapshot_desc'],
            'items1' => $currentKeywords,
            'items2' => $currentAllKeywords,
        ];
        return $this->setPostMeta(static::META_SEO_KEYWORDS_SNAPSHOTS_DATA, $currentSnapshots);
    }

    public function getSeoCompetitorsData() {
        $res = $this->getPostMeta(static::META_SEO_COMPETITORS_DATA);
        if (!is_array($res)) {
            $res = [];
        }
        return seo\CompetitorsHelper::fixItems($res);
    }

    public function setSeoCompetitorsData($value) {
        $oldData = $this->getSeoCompetitorsData();
        $old = $this->getSeoCompetitorsDataItemsUrls();
        $new = seo\CompetitorsHelper::getUrls($value['items']);
        $this->addSeoDeletedCompetitorsUrls(array_diff($old, $new));

        foreach ($value['items'] as $key => $competitor) {
            $refreshTitle = $competitor->refresh_title;
            if ($refreshTitle) {
                $title = misc\Misc::wpRemoteGetTitle($competitor->url);
                $value['items'][$key]->title = $title;
                $meta = misc\Misc::wpRemoteGetMeta($competitor->url);
                $value['items'][$key]->meta = $meta;
            } else {
                $value['items'][$key]->meta = $oldData['items'][$key]->meta;
            }
            $value['items'][$key]->words = misc\Misc::wpRemoteGetWordCount($competitor->url);
            $value['items'][$key]->refresh_title = 0;
        }
        return $this->setPostMeta(static::META_SEO_COMPETITORS_DATA, $value);
    }

    public function getSeoCompetitorsDashboardUrl() {
        return (array) $this->getPostMeta(static::META_SEO_COMPETITORS_DASHBOARD_URL_DATA);
    }

    public function setSeoCompetitorsDashboardUrl($value) {
        return $this->setPostMeta(static::META_SEO_COMPETITORS_DASHBOARD_URL_DATA, $value);
    }

    public function getPageMetricsDashboardUrlData() {
        $defaults = [
            'pa' => false,
            'words' => '',
            'metrics_show_metrics' => true,
            'metrics_show_title' => true,
            'metrics_show_description' => true,
            'metrics_show_snapshot' => true,
            'metrics_show_custom' => true,
        ];
        $result = array_merge($defaults, (array) $this->getPostMeta(static::META_PAGE_METRICS_DASHBOARD_URL_DATA));
        return (array) $result;
    }

    public function setPageMetricsDashboardUrlData($value) {
        return $this->setPostMeta(static::META_PAGE_METRICS_DASHBOARD_URL_DATA, $value);
    }

    public function getPageMetricsTrendData() {
        $res = $this->getPostMeta(static::META_PAGE_METRICS_TREND_DATA);
        if (!is_array($res)) {
            $temp = array_fill_keys(PageMetrics::getParameters(), '');
            if (!empty($res) && is_numeric($res)) {
                $temp['impressions'] = $res;
            }
            $res = $temp;
        }
        return $res;
    }

    public function setPageMetricsTrendData($valueArr) {
        if (!empty($valueArr) && is_array($valueArr)) {
            foreach ($valueArr as $key => $value) {
                if (is_numeric($value)) {
                    $valueArr[$key] = $value * 1;
                }
            }
        }
        return $this->setPostMeta(static::META_PAGE_METRICS_TREND_DATA, $valueArr);
    }

    public function getSeoKeywordsDataItemsKeywords() {
        $data = $this->getSeoKeywordsData();
        return seo\KeywordsHelper::getKeywords($data['items']);
    }

    public function getSeoKeywordsDataForKeyword($keyword) {
        $data = $this->getSeoKeywordsData();
        foreach ($data['items'] as $item) {
            if ($item->keyword == $keyword) {
                return $item;
            }
        }
        return NULL;
    }

    public function getSeoCompetitorsDataItemsUrls() {
        $data = $this->getSeoCompetitorsData();
        return seo\CompetitorsHelper::getUrls($data['items']);
    }

    public function getSeoCompetitorsDataItemsSearchPhrases() {
        $data = $this->getSeoCompetitorsData();
        return seo\CompetitorsHelper::getSearchPhrases($data['items']);
    }

    public function getSeoCompetitorsDataItemsTopCompetitors() {
        $data = $this->getSeoCompetitorsData();
        return seo\CompetitorsHelper::getIsTop($data['items']);
    }

    public function getSeoDeletedKeywords() {
        $res = $this->getPostMeta(static::META_SEO_DELETED_KEYWORDS);
        return is_array($res) ? $res : [];
    }

    public function setSeoDeletedKeywords($value) {
        return $this->setPostMeta(static::META_SEO_DELETED_KEYWORDS, $value);
    }

    public function addSeoDeletedKeywords($data) {
        if (empty($data)) {
            return;
        }
        $arr = $this->getSeoDeletedKeywords();
        return $this->setSeoDeletedKeywords(array_unique(array_merge($arr, $data)));
    }

    public function removeSeoDeletedKeywords($data) {
        if (empty($data)) {
            return;
        }
        $arr = $this->getSeoDeletedKeywords();
        $result = array_diff($arr, array_unique($data));
        return $this->setSeoDeletedKeywords($result);
    }

    public function getSeoDeletedCompetitorsUrls() {
        $res = $this->getPostMeta(static::META_SEO_DELETED_COMPETITORS_URLS);
        return is_array($res) ? $res : [];
    }

    public function setSeoDeletedCompetitorsUrls($value) {
        return $this->setPostMeta(static::META_SEO_DELETED_COMPETITORS_URLS, $value);
    }

    public function addSeoDeletedCompetitorsUrls($data) {
        if (empty($data)) {
            return;
        }
        $arr = $this->getSeoDeletedCompetitorsUrls();
        return $this->setSeoDeletedCompetitorsUrls(array_merge($arr, $data));
    }

    public function getSeoExampleKeywords() {
        $exampleKeywords = [];
        $tags = wp_get_post_tags($this->getId());
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $exampleKeywords[] = strtolower($tag->name);
            }
        }
        $categories = wp_get_post_categories($this->getId(), ['fields' => 'all']);
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $exampleKeywords[] = strtolower($category->name);
            }
        }
        return $exampleKeywords;
    }

}
