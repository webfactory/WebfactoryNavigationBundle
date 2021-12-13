Changelog
=========

## Version 4.1

* Neu:
  * BEM-Templates: Navigation und Breadcrumbs können nun mit Klassen-Namen nach BEM Methode gerendert werden. Dabei ist zu beachten:
      * Das `<nav>`-Element wird nun im Bundle erzeugt und muss ggf. aus dem Base-(Layout/Template/...) entfernt werden.
      * Der aktive Navigationspunkt wird nun nicht mehr als `<span>`-Element ausgegeben sondern als `<a aria-current="page" href="">`. Hier muss ggf. das Styling angepasst werden. Dies gilt für Navigation und Breadcrumbs.
      * `*_text`-Blocks werden zu `<span>`-Elementen, die die Captions beinhalten. Im Link-Kontext werden sie ersetzt durch `*_link`-Blocks.

      * Navigation- und Breadcrumbs-Klassen können geprefixed werden: `{% block prefix %}` in lokalen Templates.
      * Zusätzliche Klassen-Blöcke erlauben kontextuelles Setzen weiterer Klassen wie z.B. Util-Klassen oder JS-Hooks.
      * Blöcke in den Breadcrumbs lassen sich nun tatsächlich überschreiben.
    

## Version 4.0

* Kompatibilität zu Twig 3
* BC-Breaks:
  * Entfallener Twig-Tag: `navigation_theme` (z.B. `{% navigation_theme root "AppBundle:Navigation:navigation.html.twig" %}`).
    Man kann im Projekt stattdessen direkt `@WebfactoryNavigation/Navigation/navigation.html.twig` extenden und
    blockweise überschreiben. Beispiel:
      
    ```
    {# Datei: src/AppBundle/Resources/views/Navigation/navigation.html.twig #}
    
    {% extends "@WebfactoryNavigation/Navigation/navigation.html.twig" %}
        
    {% block navigation_list %}
      <nav class="projektspezifische-wrapperklasse">
        {{ parent() }}
      </nav>
    {% endblock %}
    
    {%- block navigation_caption -%}
        {{ node.caption |upper }}
    {%- endblock -%}
    ```

  * Entfallene Twig-Funktionen: `navigation_list`, `navigation_list_class`, `navigation_item`, `navigation_item_class`,
    `navigation_text`, `navigation_text_class`, `navigation_url`, `navigation_caption`. Aufrufe dieser
    Wrapper-Funktionen wie z.B. `{{ navigation_caption(themeRoot, node, level) }}` durch
    `{{ block('navigation_caption') }}` (ohne Parameter) ersetzen.
     
     Ersatzlos entfallen: `navigation`.

  * Entfallene Services: `webfactory_navigation.twig_theme_extension`, `Webfactory\Bundle\NavigationBundle\Twig\NavigationThemeExtension`
    (waren vermutlich nur intern genutzt).

  * Entfallender Controller: NavigationController. Aufrufe wie folgt ersetzen:
  
    Alt:
    ```
    {{ render(
      controller(
        'Webfactory\\Bundle\\NavigationBundle\\Controller\\NavigationController::treeAction',
        {
          root: {"webfactory_pages.page_id": root_page_id}
        }
      )
    ) }}
    ```
    
    Neu:
    ```
    {{ navigation_tree(root = {"webfactory_pages.page_id": root_page_id}) }}
    ```
    (für weitere Parameter siehe \Webfactory\Bundle\NavigationBundle\Twig\NavigationExtension::renderTree)
    
    Alt:
    ```
    {{ render(
      controller(
        'Webfactory\\Bundle\\NavigationBundle\\Controller\\NavigationController::ancestryAction',
        {
          startLevel: 1
        }
      )
    ) }}
    ```
    
    Neu:
    ```
    {{ navigation_ancestry(startLevel = 1) }}
    ```
    (für weitere Parameter siehe \Webfactory\Bundle\NavigationBundle\Twig\NavigationExtension::renderAncestry)
    
    alt:
    ```
    {{ render(
      controller(
        'Webfactory\\Bundle\\NavigationBundle\\Controller\\NavigationController::breadcrumbsAction'
      )
    ) }}
    ```
    
    neu:
     ```
     {{ navigation_breadcrumbs() }}`
    ```
    (für weitere Parameter siehe \Webfactory\Bundle\NavigationBundle\Twig\NavigationExtension::renderBreadcrumbs)

  * Entfallender Parameter `webfactory_navigation.default_theme.file` (war vermutlich nur intern genutzt)


## Version 3.1.0

* Execute BuildDirectors ordered by their priority.

## Version 3.0.0

* Config-Einstellung `refresh_tree_for_tables`/`wfd_meta_refresh` und  Service `webfactory_navigation.tree_factory.meta_query` entfernt. Um zu bestimmen, von welchen Dingen der `Tree` abhängt, ist es jetzt nicht mehr notwendig, diese Konfigurationseinstellungen zu nutzen. Erzeuge stattdessen geeignete `ResourceInterface`-Instanzen wie z. B. `\Webfactory\Bundle\WfdMetaBundle\Config\DoctrineEntityClassResource` oder `\Webfactory\Bundle\WfdMetaBundle\Config\WfdTableResource` aus dem `WebfactoryWfdMetaBundle` (ab ^3.0.0) und füge sie dem `BuildDispatcher` hinzu, während der Baum gebaut wird. Hintergrund: Das `WebfactoryWfdMetaBundle` kann jetzt solche Resourcen über den `ConfigCacheFactory`-Mechanismus auswerten und den Cache zur Laufzeit neu erstellen. Dieses Bundle hier wird damit die Abhängigkeit vom `WebfactoryWfdMeta`-Bundle los.

## Version 2.8.0

 * Neue Twig Functions `navigation_active_node()` und `navigation_active_path()` 
   zum Zugirff auf den aktiven Navigationsknoten bzw. den nächstgelegenen Knoten
   auf dem aktiven Pfad.

## Version 2.7.0

 * Neue Twig Function `navigation_find(provisions)` findet von Twig
   aus einen Knoten im Navigationsbaum. Lässt sich zum Beispiel verwenden,
   wenn an einer Stelle gezielt ausgewählte Knoten nebeneinander ausgegeben
   werden sollen (Side- oder Extra-Navs).

## Version 2.6.0

 * Neue Twig Function `navigation_active_at_level(level)` gibt den
   \Webfactory\Bundle\NavigationBundle\Tree\Node zurück, der auf der
   gegebenen Ebene aktiv ist. Damit lässt sich beispielsweise prüfen,
   ob dieser Knoten in der Navigation dargestellt (sichtbar) ist. Lässt
   sich auch verwenden, um mit NavigationController::navigationAction
   die dort wurzelnde Navigation auszugeben.

## Version 2.5.0

 * Neue NavigationController::ancestryAction zur Ausgabe von Teilbäumen z. B. der 
   dritten und vierten Navigationsebene oberhalb des aktiven Knotens.
 * Verbesserung whitespace-control um ausgegebenes HTML
 
## Version 2.4.1

 * Deprecation notice für neuere Twig-Versionen vermeiden
 
 * Zugriff auf den Baum beim Start von bin/console vermeiden. Das könnte zu Zugriffen
   auf eine möglicherweise noch nicht initialisierte Datenbank führen.
    
## Version 2.4.0
 
 * Neues Kommando webfactory:navigation:dump-tree zur Ausgabe des gesamten Baums
   auf der Konsole.

 * Neues Kommando webfactory:navigation:lookup-node zum Testen des Node-Lookups
   über key-value-Paare.

## Version 2.3.0

 * webfactory_navigation.tree_initialized-Event über den regulüren EventDispatcher verteilen, sobald der Baum erzeugt wurde.
   Damit können Klienten den Baum verändern/erzeugen/ergänzen - und zwar genau in dem Moment, wo er erstmals benötigt wird.
   Details zur Verwendung in 818daa580f243344b183e67438dea3592eb24b8a.

## Version 2

Dieses Version fasst zunächst die Definition des Baumes und die Ausgabe einschließlich der Haken und Ösen (TM)
zur Symfony-Integration in einem Bundle zusammen (keine externen Abhängigkeiten mehr), um das Leben in der Entwicklung
zu vereinfachen, bis es mal einen guten Grund zur Aufteilung gibt.

Der Ansatz, wie der Baum aufgebaut wird, ist neu:

Es erfolgt (getrieben von einem Dispatcher) eine Breitensuche. Der aktuelle "Suchkontext" wird dann allen registrieren
"TreeBuildern" präsentiert, die dann für den Kontext einen oder mehrere Knoten in den Baum einfügen können und/oder neue
Kontexte zur weiteren Suche bilden.

Nach Abschluss der Suche ist der Baum fertig und wird mit Hilfe von Symfony ConfigCache gesichert. Er steht dann in
jedem Request in der Form, wie er gebaut wurde, sofort zur Verfügung.

Innerhalb des Requests wird dieser "Prototyp" dann noch modifiziert, z. B. um den aktiven Knoten zu markieren oder auch
Unterknoten für dynamische Zustände (z. B. von Controllern aus) einzufügen.

Es ist ein Standard-Controller verfügbar, der den Baum als Navigation und als Breadcrumbs rendern kann (non-standalone),
wobei das Theming anpassbar ist.

## Version 1
    
webfactory/navigation-bundle 1.* enthält den Code, um webfactory/navigation(1.*)-basierte
Navigationen in Symfony-Projekten zu rendern. Für das Rendering von Bäumen
wird ein Theme bereitgestellt.

webfactory/navigation(1.*) selbst basiert auf webfactory/tree und fügt nur den Code hinzu,
um Ausschnitte eines solchen Baums zu betrachten - einen zur Navigation teilweise
ausgeklappten Teilbaum, eine Liste beliebiger Knoten, Breadcrumbs etc.

Das heavy lifting passiert in webfactory/tree. Dessen Ziel ist es, den Aufbau
einer Navigation möglichst effizient zu gestalten, indem die API das Laden ganzer Teilbäume
auf einmal ermöglicht. Auf diese Weise wird ein "Gesamtbaum" zusammengesetzt, der allerdings
nur für den Request lebt (aber eben "schnell" gebaut werden kann).

Das Problem ist, dass es nicht ganz einfach ist, für webfactory/tree effiziente Mapper zu schreiben.

Deshalb ist über die Zeit in webfactory/tree das "ActiveNode" Konzept entstanden, mit dem man nur noch
Knotenklassen (z. B. für ein Dokument) schreiben muss, die dann jeweils die "nächste" Ebene Knoten anbieten.

Damit ist natürlich die API von webfactory/tree unnötig kompliziert, weil das Teilbaum-basierte
Laden nicht genutzt wird (aber vorgesehen ist) und gleichzeitig ist die Performance (für die die API so
kompliziert wurde) wieder zurückfällt (z. B. ein DB-Hit je "Kinder von Dokument X").

Wir haben uns darüber hinweggeholfen, indem die End-Projekte, die Navigationen mit webfactory/navigation-bundle
ausgeben möchten, diese Ausgabe immer mittels eines standalone-embedded Controller durchführen mussten, dessen
Ausgabe dann per ESI gecached werden konnte.
