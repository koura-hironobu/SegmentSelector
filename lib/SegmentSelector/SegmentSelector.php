<?php
namespace SegmentSelector;


class Entry
{
    private $_range;
    private $_source;
    private $_action;

    public function initWithString($source) {
        $a = 0;
        $b = 0;

        if (strpos($source, '-') !== false) {
            list($a, $b) = preg_split('/-/', $source);
            $a = ip2long($a);
            $b = ip2long($b);
        } else if (strpos($source, '/') !== false) {
            list($p, $q) = preg_split('#/#', $source);
            $a = ip2long($p);
            $b = $a + ((1 << $q) - 1);
        }

        $this->_range = array($a, $b);
        $this->_source = $source;
    }

    public function matched($ipaddr) {
        $x = ip2long($ipaddr);
        return ($this->_range[0] <= $x && $x <= $this->_range[1]);
    }

    public function source() { return $this->_source; }
}




class SegmentSelector
{
    private $_entries = array();

    public function initWithSource($source) {
        $lines = preg_split('/$\R*^/m', str_replace("\r", "\n", $source));

        foreach ($lines as $l) {
            $entry = new Entry();
            $entry->initWithString($l);

            $this->_entries []= $entry;
        }
    }

    public function evaluate($ipaddr) {
        foreach ($this->_entries as $e) {
            if ($e->matched($ipaddr)) {
                return $e;
            }
        }
        return null;
    }

    public function entries() {
        return $this->_entries;
    }
}

?>
