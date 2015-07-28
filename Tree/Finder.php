<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\Bundle\NavigationBundle\Tree;

class Finder
{
    protected $reverseIndex = array();
    protected $requireCount = array();
    protected $objects = array();
    protected $idToHash = array();

    public function add($object, array $requirements)
    {
        $hash = spl_object_hash($object);
        $this->objects[$hash] = $object;

        $id = count($this->idToHash);
        $this->idToHash[$id] = $hash;

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

    public function lookup($provided)
    {
        $remainingCount = array();
        $maxMatch = 0;
        $bestId = null;

        foreach ($provided as $key => $value) {
            if (is_string($value) === false) {
                continue;
            }
            $p = "$key=$value";
            if (isset($this->reverseIndex[$p])) {
                foreach ($this->reverseIndex[$p] as $match) {

                    if (!isset($remainingCount[$match])) {
                        $remainingCount[$match] = $this->requireCount[$match];
                    }

                    $s = --$remainingCount[$match];

                    if ($s == 0 && ($size = $this->requireCount[$match]) > $maxMatch) {
                        $bestId = $match;
                        $maxMatch = $size;
                    }
                }
            }
        }

        if ($bestId !== null) {
            return $this->objects[$this->idToHash[$bestId]];
        }
    }
}
