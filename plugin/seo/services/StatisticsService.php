<?php

namespace com\cminds\seokeywords\plugin\seo\services;

use com\cminds\seokeywords\plugin\seo;
use com\cminds\seokeywords\plugin\models;

class StatisticsService {

    const NAMESPACE_CONTENTS_POST = 'com\\cminds\\seokeywords\\plugin\\seo\\contents\\posts';
    const NAMESPACE_CONTENTS_COMPETITOR = 'com\\cminds\\seokeywords\\plugin\\seo\\contents\\competitors';

    private $context;
    private $cache;
    private $namespace;

    public function __construct() {
        $this->namespace = static::NAMESPACE_CONTENTS_POST;
    }

    public function setContext($context) {
        $this->context = $context;
        return $this;
    }

    public function usePrecomputedCache($dtos) {
        $this->cache = $dtos;
        return $this;
    }

    public function setContentsNamespace($namespace) {
        $this->namespace = $namespace;
        return $this;
    }

    public function compute($dtos) {
        $dtos = $this->computeStatistics($dtos);
        $dtos = $this->computeDensity($dtos);
        return $dtos;
    }

    protected function computeStatistics($dtos) {

        $time = -microtime(true);

        foreach ($dtos as &$item) {

            $dto = new seo\dto\KeywordStatsDTO();

            foreach ($dto as $k => $v) {
                $dto->$k = 0;
            }

            foreach (array_merge([$item->keyword], (array) $item->altkeywords) as $keyword) {
                if (empty($keyword)) {
                    continue;
                }
                $cache = $this->getCache($keyword);
                if (!empty($cache)) {
                    foreach ($cache->stats as $k => $v) {
                        $dto->$k += $v;
                    }
                } else {
                    $contentClassName = $this->getNamespaceClassName('Title');
                    $dto->title += (new seo\contentpolicies\TextPolicy($keyword, new $contentClassName($this->context)))->getOccurrence();
                    $contentClassName = $this->getNamespaceClassName('Headers');
                    $dto->headers += (new seo\contentpolicies\TextPolicy($keyword, new $contentClassName($this->context)))->getOccurrence();
                    $contentClassName = $this->getNamespaceClassName('Content');
                    $dto->content += (new seo\contentpolicies\TextPolicy($keyword, new $contentClassName($this->context)))->getOccurrence();
                    $contentClassName = $this->getNamespaceClassName('FirstXWords');
                    $dto->first100 += (new seo\contentpolicies\TextPolicy($keyword, new $contentClassName($this->context, models\Options::getFirstXWords())))->getOccurrence();
                    $contentClassName = $this->getNamespaceClassName('Permalink');
                    $dto->url += (new seo\contentpolicies\UrlPolicy($keyword, new $contentClassName($this->context)))->getOccurrence();
                    $contentClassName = $this->getNamespaceClassName('MarkedContent');
                    $dto->marked += (new seo\contentpolicies\TextPolicy($keyword, new $contentClassName($this->context)))->getOccurrence();
                    $contentClassName = $this->getNamespaceClassName('Images');
                    $dto->images += (new seo\contentpolicies\ImagePolicy($keyword, new $contentClassName($this->context)))->getOccurrence();
                }
            }

            $item->stats = $dto;
        }

        $time += microtime(true);
        //error_log('comstats: ' . $time);

        return $dtos;
    }

    protected function computeDensity($dtos) {

        foreach ($dtos as &$item) {

            $dto = new seo\dto\KeywordDensityDTO();

            $contentClassName = $this->getNamespaceClassName('Content');

            $total = count(explode(' ', (new $contentClassName($this->context))->getContent()));
            $total = max(1, $total);

            $dto->content = $item->stats->content / $total * 100;
            $dto->first100 = $item->stats->first100 / models\Options::getFirstXWords() * 100;

            if ($dto->content > 0 && $dto->content < 0.1) {
                $dto->content = 0.1;
            }
            if ($dto->first100 > 0 && $dto->first100 < 0.1) {
                $dto->first100 = 0.1;
            }

            $item->density = $dto;
        }

        return $dtos;
    }

    protected function getCache($keyword) {
        if (empty($this->cache)) {
            return;
        }
        foreach ($this->cache as $item) {
            if ($item->keyword === $keyword) {
                return $item;
            }
        }
    }

    private function getNamespaceClassName($className) {
        return sprintf('%s\\%s', $this->namespace, $className);
    }

}
