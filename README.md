WebfactoryNavigationBundle
==========================

Installation
------------

    composer require webfactory/navigation-bundle 

... and activate the bundle in your kernel, depending on your Symfony version.


Usage
-----

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
overwrite each block. Please find the default blocks in `Resources/views/Navigation/navigationBlocks.html.twig`.

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


Credits, Copyright and License
------------------------------

This project was started at webfactory GmbH, Bonn.

- <http://www.webfactory.de>
- <http://twitter.com/webfactory>

Copyright 2015 webfactory GmbH, Bonn. Code released under [the MIT license](LICENSE).
