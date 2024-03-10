<?php

namespace com\cminds\seokeywords\plugin\seo\dto;

class KeywordDTO {

    public $uuid;
    public $keyword;
    public $altkeywords;
    public $note;
    public $stats;
    public $density;
    public $order;
    public $pinned;

    public function __construct($keyword = NULL) {
        $this->keyword = $keyword;
        $this->uuid = $this->keyword ? sprintf('_%s', md5($this->keyword)) : uniqid('_');
        $this->altkeywords = [];
        $this->note = new KeywordNoteDTO();
        $this->stats = new KeywordStatsDTO();
        $this->density = new KeywordDensityDTO();
        $this->order = 0;
        $this->pinned = 0;
    }

}
