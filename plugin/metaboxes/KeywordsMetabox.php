<?php

namespace com\cminds\seokeywords\plugin\metaboxes;

use com\cminds\seokeywords\App;
use com\cminds\seokeywords\plugin\helpers;
use com\cminds\seokeywords\plugin\models;
use com\cminds\seokeywords\plugin\misc;
use com\cminds\seokeywords\plugin\seo;

class KeywordsMetabox extends MetaboxAbstract {

    const METABOX = 'cmsk_keywords';
    const NONCE = 'cmsk_keywords_nonce';
    const AJAX_ACTION1 = 'e9aae15bad0532527f8222573bc4b77222d867c2'; // dashboard url

    public function __construct() {

        parent::__construct();

        // dashboard url
        add_action(sprintf('wp_ajax_%s', static::AJAX_ACTION1), function() {
            $postId = filter_input(INPUT_POST, 'post_id');
            $generic = models\GenericPost::getInstance($postId);
            if (!wp_verify_nonce(filter_input(INPUT_POST, 'nonce'), static::AJAX_ACTION1)) {
                wp_send_json(['result' => FALSE]);
            }
            $json = json_decode(filter_input(INPUT_POST, 'data'), TRUE);
            if ($json === NULL) {
                wp_send_json(['result' => FALSE]);
            }
            if ($json['update_snapshots']) {
                $generic->setSnapshotsData($json['snapshots']);
                unset($json['update_snapshots']);
            }
            if ($json['new_snapshot_date']) {
                $generic->setStoreSnapshotsData($json);
                $PageMetrics = new models\PageMetrics();
                $PageMetrics->addEntry($postId, [
                    'date' => date('Y-m-d'),
                    'period' => models\Options::getDataCollectPeriod(),
                    'is_title_changed' => 0,
                    'custom_text' => 'New Snapshot labeled: "' . $json['new_snapshot_desc'] . '" created.',
                    'type' => models\PageMetrics::SNAPSHOT,
                ]);
                unset($json['new_snapshot_date']);
                unset($json['new_snapshot_desc']);
            }
            if ($json['remove_snapshot_date']) {
                $generic->removeSnapshotData($json['remove_snapshot_date']);
                unset($json['remove_snapshot_date']);
            }
            $generic->setSeoKeywordsDashboardUrlData($json);
            $generic->setSendNotificationData($json);
            $json['snapshots'] = $generic->getSnapshotsData();
            wp_send_json(['result' => TRUE, 'data' => $json]);
        });

        // data grid update
        add_action(sprintf('wp_ajax_%s', 'fe872a92eacbbda07d755d29195e4e5a3bfaa5a2'), function() {
            $post_id = filter_input(INPUT_POST, 'post_id');
            $post = get_post($post_id);

            $private_or_protected = !empty($post->post_password) || $post->post_status == 'private';
            $error = '';
            if ($private_or_protected) {
                $save_result = FALSE;
                $error = 'Post is private or password protected! Could not obtain content';
            } else {
            // nonce checked in save method
            $save_result = $this->save($post_id, $post);
            }

            if (!$save_result) {
                wp_send_json(['result' => $save_result, 'error' => $error]);
            }
            wp_send_json(array_merge(['result' => TRUE, 'error' => ''], $save_result));
        });

        // swal keyword app data
        add_action(sprintf('wp_ajax_%s', 'CE41340FB7A5B1ADCA871A563907E5C0B8D4F082'), function() {
            $model = new models\KeywordStatistics(filter_input(INPUT_POST, 'keyword'));
            $post_id = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);
            $limit = filter_input(INPUT_POST, 'limit', FILTER_VALIDATE_INT);
            $data = [
                'data' => $model->getEntriesByPostId($post_id, $limit),
                'count' => $model->getEntriesCountByPostId($post_id)
            ];
            wp_send_json($data);
        });

        // swal keyword app add statisctics
        add_action(sprintf('wp_ajax_%s', 'A9B5B82C1ED20F2E1DB13FEB70A0BC655ED3BDCD'), function() {
            $post_id = filter_input(INPUT_POST, 'post_id');
            // TODO: nonce
            $model = new models\KeywordStatistics(filter_input(INPUT_POST, 'keyword'));
            $data = [
                'date' => filter_input(INPUT_POST, 'date'),
                'impressions' => filter_input(INPUT_POST, 'impressions', FILTER_VALIDATE_INT),
                'clicks' => filter_input(INPUT_POST, 'clicks', FILTER_VALIDATE_INT),
                'period' => filter_input(INPUT_POST, 'period', FILTER_VALIDATE_INT),
                'position' => filter_input(INPUT_POST, 'position', FILTER_VALIDATE_FLOAT)
            ];
            foreach ($data as $k => $v) {
                if ($v === FALSE) {
                    $data[$k] = NULL;
                }
            }
            $model->addManualEntry($post_id, $data);
            wp_send_json(['result' => TRUE]);
        });

        // swal keyword app remove
        add_action(sprintf('wp_ajax_%s', 'A3A5EA13EF31D73DE7562F1003652B8F361571E0'), function() {
            $id = filter_input(INPUT_POST, 'id');
            $post_id = filter_input(INPUT_POST, 'post_id');
            // TODO: nonce
            $model = new models\KeywordStatistics(filter_input(INPUT_POST, 'keyword'));
            $model->removeEntry($post_id, $id);
            wp_send_json(['result' => TRUE]);
        });

        // export keywords
        add_action(sprintf('wp_ajax_%s', 'A46F92E668FF6005D8B5B08D4F58A664C28C4B27'), function() {
            check_ajax_referer('A46F92E668FF6005D8B5B08D4F58A664C28C4B27', 'nonce');
            $post_id = filter_input(INPUT_POST, 'post_id');
            $post = get_post($post_id);
            if (empty($post)) {
                wp_die('Invalid post');
            }
            $generic = models\GenericPost::getInstance($post);
            $data = $generic->getSeoKeywordsData();
            $temp = new \SplTempFileObject();
            $temp->fputcsv(['KEYWORD', 'ALTERNATE KEYWORD', 'NOTE']);
            foreach ($data['items'] as $item) {
                $temp->fputcsv([$item->keyword, '', $item->note->content]);
                if (is_array($item->altkeywords)) {
                    foreach ($item->altkeywords as $altkeyword) {
                        $temp->fputcsv([$item->keyword, $altkeyword, '']);
                    }
                }
            }
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment;filename=' . sprintf('keywords-%s-%d.csv', sanitize_title($generic->getTitle()), time()));
            $temp->rewind();
            $temp->fpassthru();
            wp_die();
        });

        // import keywords
        add_action(sprintf('wp_ajax_%s', 'FB75D63B02DFFC9BD6C7013D6698DCA9CB666FBA'), function() {
            check_ajax_referer('FB75D63B02DFFC9BD6C7013D6698DCA9CB666FBA', 'nonce');
            $post_id = filter_input(INPUT_POST, 'post_id');
            $post = get_post($post_id);
            if (empty($post)) {
                wp_send_json(['result' => FALSE, 'message' => 'Invalid post']);
            }
            $generic = models\GenericPost::getInstance($post);
            if (empty($_FILES['file'])) {
                wp_send_json(['result' => FALSE, 'message' => 'No file']);
            }
            ini_set("auto_detect_line_endings", true);
            //$keywords = $generic->getSeoKeywordsDataItemsKeywords();
            $res = [];
            $filename = $_FILES['file']['tmp_name'];
            if (file_exists($filename)) {
                $file = new \SplFileObject($filename);
                if (!$file->eof()) {
                    // header
                    $row = $file->fgetcsv();
                    if (empty($row[0]) || $row[0] != 'KEYWORD') {
                        wp_send_json(['result' => FALSE, 'message' => 'Invalid file format']);
                        return;
                    }
                }
                while (!$file->eof()) {
                    $row = $file->fgetcsv();
                    if ($row == array(NULL)) {
                        continue;
                    }
                    if (!isset($res[$row[0]])) {
                        $res[$row[0]] = ['keyword' => $row[0]];
                    }
                    if (!empty($row[2])) {
                        $res[$row[0]]['note'] = [
                            'content' => isset($row[2]) ? $row[2] : NULL,
                            'timestamp' => isset($row[2]) ? time() * 1000 : NULL
                        ];
                    }
                    if (!empty($row[1])) {
                        if (!isset($res[$row[0]]['altkeywords']) || !is_array($res[$row[0]]['altkeywords'])) {
                            $res[$row[0]]['altkeywords'] = [];
                        }
                        $res[$row[0]]['altkeywords'][] = $row[1];
                    }
                }
            }
            wp_send_json(['result' => TRUE, 'data' => array_values($res)]);
        });

        add_action('wp_ajax_cmsk_get_synonym', array(&$this, 'get_synonym'));
    }

    public function get_synonym() {
        if (!empty($_POST['keyword'])) {
            $keyword = $_POST['keyword'];
            if (!empty($keyword)) {
                $apiUrl = 'http://words.bighugelabs.com/api/2';
                $apiKey = models\Options::getThesarusApiKey();
                if (!empty($apiKey)) {
                    $url = $apiUrl . '/' . $apiKey . '/' . stripslashes($keyword) . '/json';
                    $result = wp_remote_get($url);
                    if (!is_wp_error($result)) {
                        $response = wp_remote_retrieve_body($result);
                        if ($result['response']['code'] !== 200) {
                            wp_send_json_success();
                        }

                        try {
                            $array = json_decode($response, true);
                            if (is_array($array)) {
                                wp_send_json_success($array);
                            }
                        } catch (Exception $e) {
                            
                        }
                        wp_send_json_error('Wrong response!');
                    }

                    wp_send_json_error('Wrong API Key!');
                }

                wp_send_json_error('No API Key!');
            }
        }

        wp_send_json_error();
    }

    public function init() {
        if (function_exists('get_current_screen')) {
            if (in_array(get_current_screen()->post_type, models\Options::getKeywordsMetaboxScreen())) {
                add_editor_style(plugin_dir_url(App::PLUGIN_FILE) . 'assets/css/editor.css');
                wp_add_inline_style(misc\Assets::CSS_KEYWORDSMETABOX, sprintf('#cmsk1-list .cmsk1-section-area { max-height: %dpx; }', models\Options::getMetaboxGridHeight()));
            }
            if (get_current_screen()->action == 'add') {
                // no metabox for post-new.php
                return;
            }
        }
        add_action('add_meta_boxes', function() {
            $title = 'SEO Keywords';
            $title .= helpers\ViewHelper::load('views/backend/metaboxes/parts/ajax_indicator.php', ['css_class_prefix' => 'cmsk_keywords_ajax1']);
            add_meta_box(
                    static::METABOX, $title, array($this, 'render'), models\Options::getKeywordsMetaboxScreen(), 'advanced', 'default'
            );
        }, 200);
    }

    public function render($post) {

        $generic = models\GenericPost::getInstance($post);

        $data = $generic->getSeoKeywordsData();
        $data2 = $generic->getAllKeywordsData();
        $uniqueId = 'de8ffd6dc29d09d1992db7bc5905039d77356ecc';

        wp_localize_script(misc\Assets::JS_KEYWORDSMETABOX, 'cmsk1Config', [
            'post_id' => $generic->getId(),
            'densitythreshold' => models\Options::getDensityThreshold(),
            'ischangelog' => models\Options::getIsKeywordChangelog(),
            'columns' => models\Options::getStatTableColumns(),
            'data' => $data,
            'data2' => $data2,
            'trends' => $generic->getSeoKeywordsTrendsData(),
            'trendsmargin' => models\Options::getTrendsMargin() / 100,
            'datacollectperiod' => models\Options::getDataCollectPeriod(),
            'deletedkeywords' => $generic->getSeoDeletedKeywords(),
            'colorlabels' => models\Options::getKeywordsColorsWithLabels(),
            'allowmultipleexpanded' => models\Options::getIsAllowMultipleExpanded(),
            'examplekeywords' => $generic->getSeoExampleKeywords(),
            'unique_id' => $uniqueId,
        ]);

        $dashboardUrlData = $generic->getSeoKeywordsDashboardUrlData();
        $dashboardUrlData['snapshots'] = $generic->getSnapshotsData();
        wp_localize_script(misc\Assets::JS_KEYWORDSMETABOX, $uniqueId, [
            'post_id' => $generic->getId(),
            'action' => static::AJAX_ACTION1,
            'nonce' => wp_create_nonce(static::AJAX_ACTION1),
            'data' => $dashboardUrlData,
        ]);

        echo helpers\ViewHelper::load('views/backend/metaboxes/parts/counters.php', [
            'data' => [
//                'words'     => count( explode( ' ', (new seo\contents\posts\Content( $generic ) )->getContent() ) ),
                'words' => 'n/a',
                'keywords' => count($data['items']),
                'alternate' => count($data2['items']) - count($data['items'])
            ],
            'metabox' => static::METABOX,
            'unique_id' => $uniqueId,
        ]);

        echo helpers\ViewHelper::load('views/backend/metaboxes/parts/dashboard_url.php', [
            'uniqid' => $uniqueId,
            'metabox' => static::METABOX,
            'metabox_title' => 'SEO Keywords',
            'metabox_name' => 'Keywords',
        ]);

        echo helpers\ViewHelper::load('views/backend/metaboxes/keywords.php', [
            'columns' => models\Options::getStatTableColumns(),
            'dialog_content' => helpers\ViewHelper::load('views/backend/metaboxes/parts/pinned.php', []),
            'nonce' => static::NONCE
        ]);
    }

    public function save($post_id, $post) {
        if (!wp_verify_nonce(filter_input(INPUT_POST, static::NONCE), 'metabox')) {
            return;
        }
        if (wp_is_post_autosave($post_id)) {
            return;
        }
        if (wp_is_post_revision($post_id)) {
            return;
        }

        $data = $this->getPostData();

        if ($data === NULL) {
            return false;
        }

        $generic = models\GenericPost::getInstance($post);

        $service = new seo\services\StatisticsService();

        $data2 = $this->prepareAllData(seo\KeywordsHelper::getAllKeywords($data['items']));

        $external = models\Options::getIsLocalPagesLoadedExternally();
        if ($external) {
            $url = get_permalink($post_id);
            $ua = models\Options::getUADesktop();
            $response = wp_remote_get($url, ['user-agent' => $ua]);

            $externalContext = array_merge($response, ['url' => $url]);

            $data2['items'] = $service->setContext($externalContext)
                    ->setContentsNamespace(seo\services\StatisticsService::NAMESPACE_CONTENTS_COMPETITOR)
                    ->compute($data2['items']);

            $data['items'] = $service->setContext($externalContext)
                    ->setContentsNamespace(seo\services\StatisticsService::NAMESPACE_CONTENTS_COMPETITOR)
                    ->usePrecomputedCache($data2['items'])
                    ->compute($data['items']);
        } else {
            $data2['items'] = $service->setContext($generic)
                    ->setContentsNamespace(seo\services\StatisticsService::NAMESPACE_CONTENTS_POST)
                    ->usePrecomputedCache([])
                    ->compute($data2['items']);

            $data['items'] = $service->setContext($generic)
                    ->setContentsNamespace(seo\services\StatisticsService::NAMESPACE_CONTENTS_POST)
                    ->usePrecomputedCache($data2['items'])
                    ->compute($data['items']);
        }

        $generic->setSeoKeywordsData($data);
        $generic->setAllKeywordsData($data2);

        return ['data' => $data, 'data2' => $data2];
    }

    private function prepareAllData($items) {
        $data = ['items' => []];
        foreach ($items as $item) {
            if (empty($item)) {
                continue;
            }
            $data['items'][] = new seo\dto\KeywordDTO($item);
        }
        return $data;
    }

    private function getPostData() {
        $json = json_decode(filter_input(INPUT_POST, 'cmsk1_data'), TRUE);
        if ($json === NULL || !is_array($json['items'])) {
            return;
        }
        $data = ['items' => []];
        foreach ($json['items'] as $item) {
            if (!isset($item['keyword']) || trim($item['keyword']) === '') {
                continue;
            }
            $item['keyword'] = mb_strtolower(trim($item['keyword']));
            if (is_array($item['altkeywords'])) {
                foreach ($item['altkeywords'] as $k => &$v) {
                    $v = mb_strtolower(trim($v));
                }
            }
            $dto = new seo\dto\KeywordDTO();
            $dto->keyword = $item['keyword'];
            $dto->uuid = sprintf('_%s', md5($dto->keyword));
            $dto->altkeywords = $item['altkeywords'];
            if (!empty($item['note'])) {
                $dto->note->content = !empty($item['note']['content']) ? $item['note']['content'] : '';
                $dto->note->timestamp = !empty($item['note']['timestamp']) ? $item['note']['timestamp'] : '';
                $dto->note->color = !empty($item['note']['color']) ? $item['note']['color'] : '';
            }
            $dto->order = $item['order'];
            $dto->pinned = $item['pinned'];
            $data['items'][] = $dto;
        }
        $data['undeletedkeywords'] = isset($json['undeletedkeywords']) ? array_unique($json['undeletedkeywords']) : array();
        return $data;
    }

}
