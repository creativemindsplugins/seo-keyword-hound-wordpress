<?php

namespace com\cminds\seokeywords\plugin\models;

use com\cminds\seokeywords\App;
use com\cminds\seokeywords\plugin\helpers;
use com\cminds\seokeywords\plugin\misc;

class PageMetrics {

    const DB_TABLE               = 'cmsk_page_metrics';
    const DB_VERSION_KEY         = 'cmsk_page_metrics_db_version';
    const DB_VERSION             = 170019;
    const DATA_POINT             = 0;
    const TITLE_CHANGE           = 1;
    const SEO_TITLE_CHANGE       = 2;
    const SNAPSHOT               = 3;
    const CUSTOM_EVENT           = 4;
    const SEO_DESCRIPTION_CHANGE = 5;

    public static function getColorsArr() {
        $colors    = Options::getEventsColors();
        $events    = [self::TITLE_CHANGE, self::SEO_TITLE_CHANGE, self::SEO_DESCRIPTION_CHANGE, self::SNAPSHOT, self::DATA_POINT ];
        $colorsArr = array_combine( $events, $colors );
        return $colorsArr;
    }

    public static function getColorForEvent( $eventType ) {
        $result   = '';
        $colorArr = self::getColorsArr();
        if ( !empty( $colorArr[ (int) $eventType ] ) ) {
            $result = $colorArr[ $eventType ];
        }
        return $result;
    }

    public static function getColorForCustomEvent( $subtype ) {
        $result   = '#999';
        $colorArr = Options::getCustomEventsColors();
        if ( !empty( $colorArr[ (int) $subtype ] ) ) {
            $result = $colorArr[ $subtype ];
        }
        return $result;
    }

    public static function getSubtypeOfColor( $color ) {
        $result   = '';
        $colorArr = array_flip( Options::getCustomEventsColors() );
        if ( isset( $colorArr[ $color ] ) ) {
            $result = $colorArr[ $color ];
        }
        return $result;
    }

    public static function getColorForEntry( $entry ) {
        $result = '';
        if ( isset( $entry[ 'type' ] ) ) {
            if ( self::CUSTOM_EVENT !== (int) $entry[ 'type' ] ) {
                $result = self::getColorForEvent( $entry[ 'type' ] );
            } else {
                if ( isset( $entry[ 'subtype' ] ) ) {
                    $result = self::getColorForCustomEvent( $entry[ 'subtype' ] );
                } else {
                    throw new Exception( 'Custom event entry has no subtype!' );
                }
            }
        } else {
            throw new Exception( 'Entry has no type!' );
        }
        return $result;
    }

    public static function getColorLabelForEntry( $entry ) {
        $result = '';
        if ( isset( $entry[ 'type' ] ) ) {
            if ( !isset( $entry[ 'color' ] ) ) {
                throw new Exception( 'Entry has no color set! Assign color first!' );
            }
            if ( self::CUSTOM_EVENT !== (int) $entry[ 'type' ] ) {
                $result = Options::getEventsColorLabel( $entry[ 'color' ] );
            } else {
                if ( isset( $entry[ 'subtype' ] ) ) {
                    $result = Options::getCustomEventsColorLabel( $entry[ 'color' ] );
                } else {
                    throw new Exception( 'Custom event entry has no subtype!' );
                }
            }
        } else {
            throw new Exception( 'Entry has no type!' );
        }
        return $result;
    }

    public static function assignColorsNotifications( $entries ) {
        $result = $entries;
        if ( !empty( $entries ) && is_array( $entries ) ) {
            foreach ( $result as $key => $entry ) {
                if ( '0' == $entry[ 'type' ] && empty( $entry[ 'impressions' ] ) ) {
                    if ( $entry[ 'is_title_changed' ] ) {
                        if ( empty( $entry[ 'custom_text' ] ) ) {
                            $result[ $key ][ 'custom_text' ] = 'Title Change to "' . $entry[ 'post_title' ] . '"';
                        }
                        $result[ $key ][ 'type' ] = PageMetrics::TITLE_CHANGE;
                    } else {
                        $result[ $key ][ 'type' ] = PageMetrics::CUSTOM_EVENT;
                    }
                }
            }
            foreach ( $result as $key => $entry ) {
                $result[ $key ][ 'color' ]       = self::getColorForEntry( $entry );
                $result[ $key ][ 'color_label' ] = self::getColorLabelForEntry( $result[ $key ] );
                if ( !empty( $result[ $key ][ 'notifications' ] ) ) {
                    $result[ $key ][ 'notifications' ] = maybe_unserialize( $result[ $key ][ 'notifications' ] );
                } else {
                    unset( $result[ $key ][ 'notifications' ] );
                }
            }
        }
        return $result;
    }

    public static function dbInit() {

        global $wpdb;

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $charset   = ( defined( 'DB_CHARSET' && '' !== DB_CHARSET ) ) ? DB_CHARSET : 'utf8_general_ci';
        $collate   = ( defined( 'DB_COLLATE' && '' !== DB_COLLATE ) ) ? DB_COLLATE : 'utf8_general_ci';
        $tablename = $wpdb->prefix . static::DB_TABLE;

        $sql = "CREATE TABLE `{$tablename}` (
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
	`post_id` BIGINT(20) NULL DEFAULT NULL,
	`date` DATE NOT NULL,
        `period` INT(10) NOT NULL,
	`impressions` INT(10) NULL DEFAULT NULL,
        `clicks` INT(10) NULL DEFAULT NULL,
        `ctr` DECIMAL(10,2) NULL DEFAULT NULL,
        `bounce_rate` DECIMAL(10,2) NULL DEFAULT NULL,
        `position` DECIMAL(10,2) NULL DEFAULT NULL,
        `conversion` INT(10) NULL DEFAULT NULL,
        `conversion_value` DECIMAL(10,2) NULL DEFAULT NULL,
        `is_manual` TINYINT(1) NOT NULL DEFAULT '0',
        `post_title` TEXT NULL DEFAULT NULL,
        `permalink` TEXT NULL DEFAULT NULL,
        `post_html_title` TEXT NULL DEFAULT NULL,
        `note_content` TEXT NULL DEFAULT NULL,
        `note_timestamp` TIMESTAMP NULL DEFAULT NULL,
        `is_title_changed` TINYINT(1) NOT NULL DEFAULT '0',
        `type` TINYINT(1) NOT NULL DEFAULT '0',
        `subtype` TINYINT(1) NOT NULL DEFAULT '0',
        `custom_text` TEXT NULL DEFAULT NULL,
        `notifications` TEXT NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
        )
        COLLATE='{$collate}'
        ";

        dbDelta( $sql );

//        $model = new PageMetrics();
//        $model->updateAllData();
    }

    public static function getParameters() {
        return ['impressions', 'clicks', 'ctr', 'bounce_rate', 'conversion', 'conversion_value' ];
    }

    public function updateAllData() {
        $entries = $this->getEntries();
        $this->updateEntriesData( $entries );
    }

    public function updateEntriesData( $entries ) {

        if ( !empty( $entries ) ) {
            foreach ( $entries as $key => $entry ) {
                $type            = $this->getEntryType( $entry );
                $entry[ 'type' ] = $type;
                unset( $entry[ 'trend' ] );
                $this->updateEntry( $entry[ 'post_id' ], $entry, $entry[ 'id' ], false );
            }
        }
    }

    /**
     * Function returns the right type of data based on it's values
     * @param type $data
     * @return type
     */
    public function getEntryType( $data ) {
        $type = static::DATA_POINT; //default type;

        if ( $data[ 'is_title_changed' ] ) {
            if ( empty( $data[ 'custom_text' ] ) ) {
                $type = static::TITLE_CHANGE;
            } else {
                $type = static::SEO_TITLE_CHANGE;
            }
        } else if ( empty( $data[ 'is_title_changed' ] ) && !empty( $data[ 'custom_text' ] ) ) {

            if ( !empty( $data[ 'type' ] ) ) {
                $type = $data[ 'type' ];
            } else {
                $type = static::SNAPSHOT;
            }
        } else {
            if ( empty( $data[ 'custom_text' ] ) && !empty( $data[ 'impressions' ] ) ) {
                $type = static::DATA_POINT;
            } else {
                $type = static::CUSTOM_EVENT;
            }
        }

        return $type;
    }

    public function addCustomEntry( $post_id, $data ) {
        return $this->addEntry( $post_id, array_merge( ['type' => static::CUSTOM_EVENT ], $data ) );
    }

    public function updateCustomEntry( $post_id, $data, $id, $updateTrends = false ) {
        return $this->updateEntry( $post_id, array_merge( ['type' => static::CUSTOM_EVENT ], $data ), $id, $updateTrends );
    }

    public function addManualEntry( $post_id, $data ) {
        return $this->addEntry( $post_id, array_merge( ['is_manual' => 1 ], $data ) );
    }

    public function updateManualEntry( $post_id, $data, $id ) {
        return $this->updateEntry( $post_id, array_merge( ['is_manual' => 1 ], $data ), $id );
    }

    public function addEntry( $post_id, $data, $updateTrends = true ) {
        global $wpdb;
        if ( !empty( $data[ 'notifications' ] ) ) {
            if ( !isset( $data[ 'notifications' ][ 'days' ] ) ) {
                unset( $data[ 'notifications' ] );
            } else {
                $data[ 'notifications' ] = maybe_serialize( $data[ 'notifications' ] );
            }
        }
        $res = $wpdb->insert( $wpdb->prefix . static::DB_TABLE, $this->getEntryData( $post_id, $data ) );
        if ( $updateTrends ) {
            $this->updatePageMatricTrends( $post_id );
        }
        return $res;
    }

    public function updateEntry( $post_id, $data, $id, $updateTrends = true ) {
        global $wpdb;
        if ( !empty( $data[ 'notifications' ] ) ) {
            if ( !isset( $data[ 'notifications' ][ 'days' ] ) ) {
                $data[ 'notifications' ] = null;
            } else {
                $data[ 'notifications' ] = maybe_serialize( $data[ 'notifications' ] );
            }
        }
        $res = $wpdb->update( $wpdb->prefix . static::DB_TABLE, $this->getEntryData( $post_id, $data ), ['id' => $id, 'post_id' => $post_id ] );
        if ( $res !== false && $updateTrends ) {
            $this->updatePageMatricTrends( $post_id );
        }
        return $res;
    }

    public function getEntry( $id ) {
        global $wpdb;
        $tablename        = $wpdb->prefix . static::DB_TABLE;
        $res = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$tablename} WHERE id = %d", $id ), ARRAY_A);
        
        if(!empty($res)){
            $res['notifications'] = maybe_unserialize($res['notifications']);
        }
        return $res;
    }
    
    public function removeEntry( $post_id, $id, $updateTrends = true ) {
        global $wpdb;
        $res = $wpdb->delete( $wpdb->prefix . static::DB_TABLE, ['id' => $id ] );
        if ( $updateTrends ) {
            $this->updatePageMatricTrends( $post_id );
        }
        return $res;
    }

    public function getEntriesByPostId( $post_id, $limit = 3, $filters = array( 0 ) ) {
        global $wpdb;
        $tablename        = $wpdb->prefix . static::DB_TABLE;
        $filtersSanitized = implode( ',', $filters );
        $filtersSQL       = 'AND type IN (' . $filtersSanitized . ')';
        $sql              = "select * from {$tablename} where post_id = %s {$filtersSQL} order by date desc, id desc limit {$limit}";
        $result           = $wpdb->get_results( $wpdb->prepare( $sql, $post_id ), ARRAY_A );
        return self::assignColorsNotifications( $result );
    }

    public function getTrendChangeEntriesByPostId( $post_id, $parameter = 'impressions', $limit = 3 ) {
        global $wpdb;
        $parameters = self::getParameters();
        $parameter  = in_array( $parameter, $parameters ) ? $parameter : 'impressions';
        $tablename  = $wpdb->prefix . static::DB_TABLE;
        $sql        = "select * from {$tablename} where post_id = %s and {$parameter} IS NOT NULL order by date desc limit {$limit}";
        return $wpdb->get_results( $wpdb->prepare( $sql, $post_id ), ARRAY_A );
    }

    public function getTitleChangeEntriesByPostId( $post_id, $limit = 3 ) {
        global $wpdb;
        $tablename = $wpdb->prefix . static::DB_TABLE;
        $sql       = "select * from {$tablename} where post_id = %s and is_title_changed = 1 order by date desc limit {$limit}";
        return $wpdb->get_results( $wpdb->prepare( $sql, $post_id ), ARRAY_A );
    }

    public function getEntriesByPostIdAndDate( $post_id, $date1, $date2 ) {
        global $wpdb;
        $tablename = $wpdb->prefix . static::DB_TABLE;
        $sql       = "select * from {$tablename} where post_id = %s and date >= %s and date <= %s order by date desc";
        return $wpdb->get_results( $wpdb->prepare( $sql, $post_id, $date1, $date2 ), ARRAY_A );
    }

    public function getTitleChangeEntriesByPostIdAndDate( $post_id, $date1, $date2 ) {
        global $wpdb;
        $tablename = $wpdb->prefix . static::DB_TABLE;
        $sql       = "select * from {$tablename} where post_id = %s and date >= %s and date <= %s and is_title_changed = 1 order by date desc";
        return $wpdb->get_results( $wpdb->prepare( $sql, $post_id, $date1, $date2 ), ARRAY_A );
    }

    public static function getEntries( $offset = 0, $limit = 9999, $filterPostTypes = 0 ) {
        global $wpdb;
        $tablename = $wpdb->prefix . static::DB_TABLE;

        $additionalCondition = '';
        if ( $filterPostTypes ) {
            $postTypesArr = Options::getKeywordsMetaboxScreen();
            if ( !empty( $postTypesArr ) && is_array( $postTypesArr ) ) {
                $postTypesArr = array_map( function($item) {
                    return '"' . $item . '"';
                }, $postTypesArr );
                $additionalCondition = 't4.post_type IN (' . implode( ',', $postTypesArr ) . ')';
            }
        }
        $pageMetricsTrendMetaKey = GenericPost::META_PAGE_METRICS_TREND_DATA;

        $sql = "SELECT t1.*, t3.meta_value AS trend, t4.post_type
                FROM {$tablename} t1
                INNER JOIN (SELECT ID, post_type FROM {$wpdb->posts}) AS t4
                ON t1.post_id = t4.ID
                LEFT JOIN (SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '{$pageMetricsTrendMetaKey}') AS t3
                ON t1.post_id = t3.post_id
                WHERE 1=1 AND {$additionalCondition}
                ORDER BY t1.date DESC
                LIMIT {$offset}, {$limit}";

        $result = $wpdb->get_results( $sql, ARRAY_A );
        return self::assignColorsNotifications( $result );
    }

    public static function getLastEntries( $ids, $data_type = 0, $offset = 0, $limit = PHP_INT_MAX ) {
        global $wpdb;
        $tablename = $wpdb->prefix . static::DB_TABLE;
        if ( !count( $ids ) ) {
            return [ ];
        }

        if ( $data_type == 0 ) {
            $additionalCondition = 'AND impressions IS NOT NULL';
        } else {
            $additionalCondition = 'AND impressions IS NULL';
        }

        $pageMetricsTrendMetaKey = GenericPost::META_PAGE_METRICS_TREND_DATA;

        $sql = "SELECT t1.*, t3.meta_value AS trend
                FROM {$tablename} t1
                INNER JOIN (
                SELECT post_id, max(date) as max_date
                FROM {$tablename}
                WHERE post_id IN(" . implode( ',', $ids ) . ") " . $additionalCondition . "
                GROUP BY post_id ) t2
                ON (t1.post_id = t2.post_id AND t1.date = t2.max_date)
                LEFT JOIN (SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '{$pageMetricsTrendMetaKey}') AS t3
                ON t1.post_id = t3.post_id
                ORDER BY `date` DESC
                LIMIT {$offset}, {$limit}";
        return $wpdb->get_results( $sql, ARRAY_A );
    }

    public static function getLastEntriesCount( $ids ) {
        global $wpdb;
        $tablename = $wpdb->prefix . static::DB_TABLE;
        if ( !count( $ids ) )
            return 0;

        $sql = "SELECT COUNT( DISTINCT post_id )
			FROM {$tablename}
			WHERE post_id IN(" . implode( ',', $ids ) . ")";
        return $wpdb->get_var( $sql );
    }

    public function getEntriesCountByPostId( $post_id ) {
        global $wpdb;
        $tablename = $wpdb->prefix . static::DB_TABLE;
        $sql       = "select count(*) from {$tablename} where post_id = %s";
        return $wpdb->get_var( $wpdb->prepare( $sql, $post_id ) );
    }

    public function getTitleChangeEntriesCountByPostId( $post_id ) {
        global $wpdb;
        $tablename = $wpdb->prefix . static::DB_TABLE;
        $sql       = "select count(*) from {$tablename} where post_id = %s and is_title_changed = 1";
        return $wpdb->get_var( $wpdb->prepare( $sql, $post_id ) );
    }

    public function setEntryNote( $id, $data ) {
        global $wpdb;
        return $wpdb->update(
        $wpdb->prefix . static::DB_TABLE, [
            'note_content'   => $data,
            'note_timestamp' => current_time( 'mysql' )
        ], ['id' => $id ], ['%s', '%s' ], ['%d' ]
        );
    }

    private function getEntryData( $post_id, $data ) {
        $data = array_merge( ['post_id' => $post_id ], $data );
        if ( empty( $data[ 'ctr' ] ) ) {
            if ( isset( $data[ 'impressions' ] ) && isset( $data[ 'clicks' ] ) && intval( $data[ 'impressions' ] ) > 0 && is_numeric( $data[ 'clicks' ] ) ) {
                $data[ 'ctr' ] = 100 * intval( $data[ 'clicks' ] ) / intval( $data[ 'impressions' ] );
            }
        }
        $data = array_merge( $this->getEntryAdditionalData( $post_id ), $data );
        return $data;
    }

    private function getEntryAdditionalData( $post_id ) {
        return [
            'post_html_title' => misc\Misc::wpRemoteGetTitle( get_permalink( $post_id ) ),
            'post_title'      => get_the_title( $post_id ),
            'permalink'       => get_permalink( $post_id )
        ];
    }

    public function updatePageMatricTrends( $post_id ) {
        $parameters = self::getParameters();
        foreach ( $parameters as $parameter ) {
            $factors[ $parameter ] = $this->updatePageMatricTrend( $post_id, $parameter );
        }
        $generic = GenericPost::getInstance( $post_id );
        $generic->setPageMetricsTrendData( $factors );
    }

    public function updatePageMatricTrend( $post_id, $parameter = 'impressions' ) {
        /*
         * Get last 3 last meaningful(!) events
         */
        $metricPoints = Options::getTrendsPoints();
        $items        = $this->getTrendChangeEntriesByPostId( $post_id, $parameter, $metricPoints );
        $factor       = null;
        if ( count( $items ) > 1 ) {
            $arr   = [ ];
            $total = 0;
            foreach ( $items as $item ) {
                $arr[ (strtotime( $item[ 'date' ] ) - strtotime( $items[ 0 ][ 'date' ] )) / (60 * 60 * 24) ] = $item[ $parameter ];
                $total += $item[ 'impressions' ];
            }
            $trend = helpers\TrendHelper::getLinearRegression( $arr );
            if ( !$trend[ 'intercept' ] == 0 ) {
                $factor = ($trend[ 'slope' ] / $trend[ 'intercept' ]) * 10;
            } else {
                $factor = 0;
            }
        }
        return $factor;
    }

}
