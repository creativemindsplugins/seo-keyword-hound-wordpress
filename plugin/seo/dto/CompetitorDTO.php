<?php

namespace com\cminds\seokeywords\plugin\seo\dto;

class CompetitorDTO {

    public $uuid;
    public $url;
    public $title;
    public $meta;
    public $refresh_title;
    public $label;
    public $note;
    public $phrase1;
    public $phrase2;
    public $phrase3;
    public $phrases;
    public $moz_domain;
    public $moz_pagerank;
    public $is_main_competitor;
    public $words;

    public function __construct() {
        $this->uuid = uniqid( '_' );
        $this->meta = array();
        $this->note = new CompetitorNoteDTO();
    }

    public static function getColumns() {
        return ['URL', 'NAME', 'NOTE', 'TITLE', 'PHRASE1', 'PHRASE2', 'PHRASE3', 'PA', 'DA' ];
    }

    public function getValues( $searchPhrases = [ ] ) {
        $phrase1 = isset( $searchPhrases[ $this->phrase1 ] ) ? $searchPhrases[ $this->phrase1 ] : '';
        $phrase2 = isset( $searchPhrases[ $this->phrase2 ] ) ? $searchPhrases[ $this->phrase2 ] : '';
        $phrase3 = isset( $searchPhrases[ $this->phrase3 ] ) ? $searchPhrases[ $this->phrase3 ] : '';
        return [$this->url, $this->label, $this->note->content, $this->title, $phrase1, $phrase2, $phrase3, $this->moz_pagerank, $this->moz_domain, $this->phrases, $this->words ];
    }

    public static function setValues( $row ) {
        $result = [
            'url'          => $row[ 0 ],
            'label'        => isset( $row[ 1 ] ) ? $row[ 1 ] : NULL,
            'note'         => [
                'content'   => isset( $row[ 2 ] ) ? $row[ 2 ] : NULL,
                'timestamp' => isset( $row[ 2 ] ) ? time() * 1000 : NULL,
            ],
            'title'        => isset( $row[ 3 ] ) ? $row[ 3 ] : NULL,
            'phrase1'      => isset( $row[ 4 ] ) ? $row[ 4 ] : NULL,
            'phrase2'      => isset( $row[ 5 ] ) ? $row[ 5 ] : NULL,
            'phrase3'      => isset( $row[ 6 ] ) ? $row[ 6 ] : NULL,
            'moz_pagerank' => isset( $row[ 7 ] ) ? $row[ 7 ] : NULL,
            'moz_domain'   => isset( $row[ 8 ] ) ? $row[ 8 ] : NULL,
            'phrases'   => isset( $row[ 9 ] ) ? $row[ 9 ] : NULL,
            'words'   => isset( $row[ 10 ] ) ? $row[ 10 ] : NULL,
        ];
        return $result;
    }

}
