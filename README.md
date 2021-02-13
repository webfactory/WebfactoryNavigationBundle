WebfactoryNavigationBundle
==========================

Symfony Bundle featuring:

- A factory for creating the navigation tree, using BuildDirectors which you can add to, if needed
- Twig functions for rendering navigation elements (tree, ancestry, breadcrumbs) and inspecting the navigation tree


Installation
------------

    composer require webfactory/navigation-bundle 

... and activate the bundle in your kernel, depending on your Symfony version.


Rendering navigation elements in Twig
-------------------------------------

### Simple Navigation List

#### Syntax

    {{ navigation_tree(rootHints, maxLevels=1, expandedLevel=1, template = 'WebfactoryNavigationBundle:Navigation:navigation.html.twig') }}

#### Examples
    
    {{ navigation_tree({"webfactory_pages.page_id": root_page_id}) }}
    
    {{ navigation_tree(
      {"webfactory_pages.page_id": root_page_id, "_locale": app.request.locale},
      2,
      2,
      'AppBundle:Navigation:navigation.html.twig'
    ) }}

### Ancestry List

An ancestry list is the active path from the given start level to the currently active node. Useful if you want to render
e.g. the third level navigation outside of the regular navigation.

#### Syntax
    {{ navigation_ancestry(startLevel, maxLevels=1, expandedLevels=1,template='WebfactoryNavigationBundle:Navigation:navigation.html.twig') }}

#### Examples

    {{ navigation_ancestry(1) }}
    
    {{ navigation_ancestry(1, 1, 1, 'AppBundle:Navigation:secondaryNav.html.twig') }}

### Breadcrumbs

#### Syntax

    {{ navigation_breadcrumbs(template='WebfactoryNavigationBundle:Navigation:breadcrumbs.html.twig') }}

#### Examples

    {{ navigation_breadcrumbs() }}
    {{ navigation_breadcrumbs('AppBundle:Navigation:breadcrumbs.html.twig') }}

### Customisation

For each function mentioned above you can provide a Twig template in which you can extend the base template and
overwrite each block. Please find the default blocks in `src/Resources/views/Navigation/navigationBlocks.html.twig`.

Example:

```twig
{# layout.html.twig: #}

...
{{ navigation_tree({"webfactory_pages.page_id": root_page_id}, 2, 2, 'AppBundle:Navigation:navigation.html.twig') }}
...
```

```twig
{# AppBundle:Navigation:navigation.html.twig: #}

{% extends "WebfactoryNavigationBundle:Navigation:navigation.html.twig" %}

{% block navigation_list %}
    <nav class="project-specific-classes">
        {{ parent() }}
    </nav>
{% endblock %}
```    


Modifying the NavigationTree
----------------------------

Implement a `Webfactory\Bundle\NavigationBundle\Build\BuildDirector`. Example:

```php
<?php

namespace AppBundle\Navigation;

use JMS\ObjectRouting\ObjectRouter;
use Symfony\Component\Config\Resource\FileResource;
use Webfactory\Bundle\NavigationBundle\Build\BuildContext;
use Webfactory\Bundle\NavigationBundle\Build\BuildDirector;
use Webfactory\Bundle\NavigationBundle\Build\BuildDispatcher;
use Webfactory\Bundle\NavigationBundle\Tree\Tree;
use Webfactory\Bundle\WfdMetaBundle\Config\DoctrineEntityClassResource;

final class KeyActionBuildDirector implements BuildDirector
{
    /** @var YourEntityRepository */
    private $repository;

    /** @var ObjectRouter */
    private $objectRouter;

    public function __construct(YourEntityRepository $repository, ObjectRouter $objectRouter)
    {
        $this->repository = $repository;
        $this->objectRouter = $objectRouter;
    }

    public function build(BuildContext $context, Tree $tree, BuildDispatcher $dispatcher): void
    {
        if (!$this->isInterestedInContext($context)) {
            return;
        }

        $this->addTreeCacheExpiryRule($dispatcher);

        foreach ($this->repository->findForMenu() as $entity) {
            $context->get('node')
                ->addChild()
                ->set('caption', $entity->getName())
                ->set('visible', true)
                ->set('url', $this->objectRouter->path('detail', $entity));
        }
    }

    private function addTreeCacheExpiryRule(BuildDispatcher $dispatcher): void
    {
        $dispatcher->addResource(new FileResource(__FILE__));
        $dispatcher->addResource(new DoctrineEntityClassResource(YourEntity::class));
    }
}
```

Define your implementation as a service and tag it `webfactory_navigation.build_director`. Example:

```xml
<service class="AppBundle\Navigation\YouEntityBuildDirector">
    <argument type="service" id="AppBundle\Repository\YourEntityRepository" />
    <argument type="service" id="JMS\ObjectRouting\ObjectRouter" />
    <tag name="webfactory_navigation.build_director"/>
</service>
```

See `src/Resources/doc/How-To-Use-Klassendiagramm.puml` for more.


Credits, Copyright and License
------------------------------

This project was started at webfactory GmbH, Bonn.

- <https://www.webfactory.de>
- <https://twitter.com/webfactory>

Copyright 2015 - 2021 webfactory GmbH, Bonn. Code released under [the MIT license](LICENSE).
