<?php

namespace com\cminds\seokeywords\plugin\seo\contentpolicies;

class TextPolicy extends PolicyAbstract implements PolicyInterface {

    public function getOccurrence() {
        if (empty($this->keyword)) {
            return 0;
        }
        $this->keyword = mb_strtolower($this->keyword);

        $res = 0;
        if (!is_array($this->content)) {
            $this->content = [$this->content];
        }
        foreach ($this->content as $s) {
            $res += $this->getOccurrence2($this->keyword, $s);
        }
        return $res;
    }

    private function getOccurrence2($k, $s) {
        $s = preg_replace('~\x{00a0}~siu', ' ', $s);
        $s = mb_strtolower($s);
        $s = ' ' . $s . ' ';
        $s = str_replace(' ' . $k, '  ' . $k, $s);
        return preg_match_all(sprintf("/\s%s[\s|\,|\.|\:]/i", preg_quote($k)), $s);
    }

}
