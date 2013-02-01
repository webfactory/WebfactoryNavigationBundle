<?php

namespace Webfactory\Bundle\NavigationBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Webfactory\Bundle\NavigationBundle\DependencyInjection\Compiler\BuildDirectorPass;

class WebfactoryNavigationBundle extends Bundle {

    public function build(ContainerBuilder $container) {
        $container->addCompilerPass(new BuildDirectorPass());
    }

}
