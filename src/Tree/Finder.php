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
    /** @var array(string => int[]) */
    protected $reverseIndex = [];

    /** @var int[] */
    protected $requireCount = [];

    /** @var Node[] */
    protected $objects = [];

    /** @var string[] */
    protected $idToHash = [];

    public function add(Node $object, array $requirements): void
    {
        $hash = spl_object_hash($object);
        $this->objects[$hash] = $object;

        $id = \count($this->idToHash);
        $this->idToHash[$id] = $hash;

        foreach ($requirements as $key => $value) {
            $r = "$key=$value";
            if (!isset($this->reverseIndex[$r])) {
                $this->reverseIndex[$r] = [$id];
            } else {
                $this->reverseIndex[$r][] = $id;
            }
        }

        $this->requireCount[$id] = \count($requirements);
    }

    /**
     * @param string|array|object $provided
     */
    public function lookup($provided): ?Node
    {
        $remainingCount = [];
        $maxMatch = 0;
        $bestId = null;

        foreach ($provided as $key => $value) {
            if (\is_array($value) || \is_object($value) || (string) $value != $value) {
                continue;
            }
            $p = "$key=$value";
            if (isset($this->reverseIndex[$p])) {
                foreach ($this->reverseIndex[$p] as $match) {
                    if (!isset($remainingCount[$match])) {
                        $remainingCount[$match] = $this->requireCount[$match];
                    }

                    $s = --$remainingCount[$match];

                    if (0 == $s && ($size = $this->requireCount[$match]) > $maxMatch) {
                        $bestId = $match;
                        $maxMatch = $size;
                    }
                }
            }
        }

        if (null === $bestId) {
            return null;
        }

        return $this->objects[$this->idToHash[$bestId]];
    }
}
