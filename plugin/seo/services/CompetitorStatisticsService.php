<?php

namespace com\cminds\seokeywords\plugin\seo\services;

use com\cminds\seokeywords\plugin\seo;
use com\cminds\seokeywords\plugin\models;

class CompetitorStatisticsService {

    private $contextGenericPost;
    private $url;
    private $ua;
    private $uaType;

    public function __construct() {
        $this->setUADesktop();
    }

    public function setContext(models\GenericPost $post) {
        $this->contextGenericPost = $post;
        return $this;
    }

    public function setUrl($url) {
        $this->url = $url;
        return $this;
    }

    public function setUAType($type = 'desktop') {
        if ($type == 'desktop') {
            $this->setUADesktop();
        }
        if ($type == 'mobile') {
            $this->setUAmobile();
        }
        return $this;
    }

    public function setUADesktop() {
        $this->ua = models\Options::getUADesktop();
        $this->uaType = 'desktop';
        return $this;
    }

    public function setUAmobile() {
        $this->ua = models\Options::getUAMobile();
        $this->uaType = 'mobile';
        return $this;
    }

    public function clearCache($do = TRUE) {
        if ($do) {
            delete_transient($this->getCacheKey());
        }
        return $this;
    }

    public function getStatistics() {
        $cache = get_transient($this->getCacheKey());
        if ($cache !== FALSE) {
            return $cache;
        }
        $data = $this->getStatistics2();
        set_transient($this->getCacheKey(), $data, models\Options::getKeywordsCompareCacheTTL());
        return $data;
    }

    protected function getStatistics2() {
        $items1 = [];
        $items2 = [];
        $data = $this->contextGenericPost->getSeoKeywordsData();
        foreach ($data['items'] as $item) {
            $dto = new seo\dto\KeywordDTO($item->keyword);
            $dto->altkeywords = $item->altkeywords;
            $items1 [] = $dto;
        }
        $keywords = seo\KeywordsHelper::getAllKeywords($data['items']);
        foreach ($keywords as $keyword) {
            $items2 [] = new seo\dto\KeywordDTO($keyword);
        }
        $response = wp_remote_get($this->url, ['user-agent' => $this->ua]);
        $service = new StatisticsService();
        if (!is_wp_error($response)) {
            $items2 = $service->setContext(array_merge($response, ['url' => $this->url]))
                    ->setContentsNamespace(StatisticsService::NAMESPACE_CONTENTS_COMPETITOR)
                    ->compute($items2);
            $items1 = $service->setContext(array_merge($response, ['url' => $this->url]))
                    ->setContentsNamespace(StatisticsService::NAMESPACE_CONTENTS_COMPETITOR)
                    ->usePrecomputedCache($items2)
                    ->compute($items1);
        }
        return [
            'ts' => time(),
            'ua' => $this->ua,
            'uatype' => $this->uaType,
            'items1' => $items1, // main keywords
            'items2' => $items2, // alternate keywords
            'status' => is_wp_error($response) ? $response->get_error_message() : $response['response']['message']
        ];
    }

    protected function getCacheKey() {
        return sprintf('_cmsk_%s', sha1($this->contextGenericPost->getId() . $this->url . $this->ua . $this->uaType));
    }

}
