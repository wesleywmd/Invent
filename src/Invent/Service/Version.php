<?php
namespace Invent\Service;

class Version {
    private $sections = [];

    public function __construct($currentVersion) {
        $this->sections = explode(".",$currentVersion);
    }

    public function increment($value=1,$section=null)
    {
        if( is_null($section) ) {
            $section = count($this->sections);
        }
        $this->sections[$section-1] += $value;
    }

    public function getString()
    {
        return implode(".",$this->sections);
    }
}