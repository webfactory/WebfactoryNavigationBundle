<?php

namespace Webfactory\Bundle\NavigationBundle\EventListener;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Webfactory\Bundle\NavigationBundle\Build\TreeFactory;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ActiveNodeEventListener {

    /** @var TreeFactory */
    protected $factory;

    public function __construct(TreeFactory $factory) {
        $this->factory = $factory;
    }

    public function onKernelController(FilterControllerEvent $event) {
        if ($event->getRequestType() == HttpKernelInterface::MASTER_REQUEST) {
            $req = $event->getRequest();
            $this->factory->setNodeActivationParameters($req->attributes->all());
        }
    }
}
