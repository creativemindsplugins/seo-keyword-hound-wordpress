<?php

namespace com\cminds\seokeywords\plugin\seo\contentpolicies;

class UrlPolicy extends PolicyAbstract implements PolicyInterface {

    public function getOccurrence() {
        if (empty($this->keyword)) {
            return 0;
        }
        $this->keyword = mb_strtolower($this->keyword);
        $this->content = mb_strtolower($this->content);
        return preg_match_all(sprintf("/%s/i", preg_quote($this->keyword)), $this->content);
    }

}
