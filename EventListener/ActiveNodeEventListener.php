<?php

namespace Webfactory\Bundle\NavigationBundle\EventListener;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Webfactory\Bundle\NavigationBundle\Tree\Tree;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ActiveNodeEventListener {

    protected $tree;

    public function __construct(Tree $tree) {
        $this->tree = $tree;
    }

    public function onKernelController(FilterControllerEvent $event) {
        if ($event->getRequestType() == HttpKernelInterface::MASTER_REQUEST) {

            $req = $event->getRequest();
            if ($node = $this->tree->find($req->attributes->all())) {
                $node->setActive();
            }
        }
    }
}
