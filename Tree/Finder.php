<?php

namespace Webfactory\Bundle\NavigationBundle\Tree;

class Finder {
    protected $reverseIndex = array();
    protected $requireCount = array();
    protected $objects = array();

    public function add($object, array $requirements) {
        $id = count($this->objects);
        $this->objects[$id] = $object;

        foreach ($requirements as $key => $value) {
            $r = "$key=$value";
            if (!isset($this->reverseIndex[$r])) {
                $this->reverseIndex[$r] = array($id);
            } else {
                $this->reverseIndex[$r][] = $id;
            }
        }

        $this->requireCount[$id] = count($requirements);
    }

    public function lookup($provided) {
        $remainingCount = array();
        $maxMatch = 0;
        $bestId = null;

        foreach ($provided as $key => $value) {
            $p = "$key=$value";
            if (isset($this->reverseIndex[$p])) {
                foreach ($this->reverseIndex[$p] as $match) {

                    if (!isset($remainingCount[$match]))
                        $remainingCount[$match] = $this->requireCount[$match];

                    $s = --$remainingCount[$match];

                    if ($s == 0 && ($size = $this->requireCount[$match]) > $maxMatch) {
                        $bestId = $match;
                        $maxMatch = $size;
                    }
                }
            }
        }

        if ($bestId !== null) {
            return $this->objects[$bestId];
        }
    }
}
