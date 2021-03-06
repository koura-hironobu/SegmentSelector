<?php
namespace SegmentSelector;


class Entry
{
    private $_range;
    private $_source;
    private $_action;

    public function initWithSource($source) {
        $a = 0;
        $b = 0;

        $this->_source = $source;
        $source = preg_replace("/#.*$/", "", $source);
            
        if (strpos($source, '-') !== false) {
            list($a, $b) = preg_split('/-/', $source);
            $a = ip2long($a);
            $b = ip2long($b);
        } else if (strpos($source, '/') !== false) {
            list($p, $q) = preg_split('#/#', $source);
            $a = ip2long($p);
            if ($a !== 0) {
                $b = $a + ((1 << (32 - $q)) - 1);
            }
        } else {
            $a = ip2long($source);
            $b = $a;
        }

        if ($a !== 0 && $b !== 0) {
            $this->_range = array($a, $b);
        }
    }

    public function matched($ipaddr) {
        $x = ip2long($ipaddr);
        return ($this->_range[0] <= $x && $x <= $this->_range[1]);
    }

    public function range() { return $this->_range; }
    public function source() { return $this->_source; }
    public function action() { return $this->_action; }
}




class SegmentSelector
{
    private $_entries = array();

    public function initWithSource($source) {
        $this->_source = $source;

        $lines = preg_split('/$\R*^/m', str_replace("\r", "\n", $source));

        foreach ($lines as $l) {
            $l = preg_replace("/#.*$/", "", $l);
            if (trim($l) === '') {
                continue;
            }

            $entry = new Entry();
            $entry->initWithSource($l);

            $this->_entries []= $entry;
        }
    }

    public function loadJSON($json) {
        $this->_entries = array();

        $objs = json_decode($json);
        foreach ($objs->entries as $o) {
            $e = new Entry();
            $e->initWithSource($o->source);

            $this->_entries []= $e;
        }
    }

    public function dumpJSON() {
        return json_encode(array("entries" => array_map(function($e) {
            $iprange = array_map(function($a) {
                return long2ip($a);
            }, $e->range());
            return array("range" => $iprange, "source" => $e->source(), "action" => $e->action());
        }, $this->_entries)));
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
