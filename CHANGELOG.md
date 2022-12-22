Changelog
=========

## Version 5

* Das Attribut `aria-label` macht die Navigationen für assistive Technologien lesbar. Dies ist insbesondere wertvoll, wenn es mehr als eine Navigation gibt.

* BC-Break:
  * Das Template `navigation-BEM` wirft nun eine Exception wenn die Variable `navigation_landmark` nicht gesetzt wird. Es gibt bewusst keinen Default, um zu     verhindern, dass unter Umständen mehrere Navigationen den gleichen Landmark erhalten. 

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

### Features

* Kompatibilität zu Twig 3
* Mit Version 4 entfernen wir das `navigation_theme`-Eigengebräu und ersetzen es durch Standard-Twig-Mechanismen:
  * Die separaten Theme-Dateien (z.B. `main-nav-theme.html.twig` neben einer weiterhin bestehenden
    `main-nav-layout.html.twig`) entfallen.
  * Das Styling von Menüs ist künftig/für neue Mitarbeiter leichter verständlich.
  * Im Projekt definierte Blöcke können oftmals entfallen, die wir schon im NavigationBundle bereitstellen - und dort zentral
    weiterentwickeln können.

### Vorschlag für Vorgehensweise
* `composer update webfactory/navigation-bundle` auf `^4.0`
* Alle Navigation-Aufrufe in Twig erstmal auskommentieren, damit wir sicher sind, dass die Seite lokal sonst
  funktioniert und wir auftretende Fehler in der Benutzung des NavigationBundles suchen müssen. 
* Dann pro auskommentiertem Navigation-Aufruf:
  * Aufruf wieder aktivieren und für Version 4 anpassen. Der NavigationController ist entfallen, dafür gibt es jetzt
    Twig-Funktionen. Aus:
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
    wird:
    ```
    {{ navigation_tree(root = {"webfactory_pages.page_id": root_page_id}) }}
    ```

    Aus:
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
    wird:
    ```
    {{ navigation_ancestry(startLevel = 1) }}
    ```

    Und aus:
    ```
    {{ render(
      controller(
        'Webfactory\\Bundle\\NavigationBundle\\Controller\\NavigationController::breadcrumbsAction'
      )
    ) }}
    ```
    wird:
    ```
    {{ navigation_breadcrumbs() }}`
    ```

    Parameter der alten Aufrufe kannst Du übernehmen, musst dabei nur die Syntax anpassen: etwa im ersten Beispiel wird
    `root` ein named Parameter, dessen Wert mit `=` statt `:` wie in der Object-Notation zugewiesen wird). Details zu
    den Parametern findest Du in `NavigationExtension::renderTree`, `NavigationExtension::renderAncestry` und
    `NavigationExtension::renderBreadcrumbs`.
  * Falls in einer `main-nav-layout.html.twig` ein eigenes Theme verwendet wurde, sah die Datei beispielsweise so aus:
    ```
    {% if root is defined %}
      {% navigation_theme root "AppBundle:Navigation:main-nav-theme.html.twig" %}
      {{ navigation(root, maxLevels, expandedLevels) }}
    {% endif %}
    ```
    und in `main-nav-theme.html.twig` waren dann Twig-Blöcke definiert. In diesem Fall solltest Du den Inhalt der
    `main-nav-layout.html.twig` durch
    ```
    {% extends '@WebfactoryNavigation/Navigation/navigation.html.twig' %} 
    ```
    ersetzen. Wenn Du die Seite lokal aufrufst, solltest Du ein Vanilla-Navigation-Bundle-Rendering des jeweiligen Menüs
    sehen.
  * Kopiere dann die Blöcke aus der `main-nav-theme.html.twig` in die `main-nav-layout.html.twig` und kommentiere sie da
    erstmal aus.
  * Für jeden auskommentierten Block:
    * Wenn Du den gleichen bzw. einen besseren gleichnamigen Block in `navigationBlocks.html.twig` (via
      `@WebfactoryNavigation/Navigation/navigation.html.twig` genutzt), findest, kannst ihn aus Deiner
      `main-nav-layout.html.twig` löschen.
    * Wenn der Block z.B. eigene CSS-Klassen setzt, willst Du ihn wieder aktivieren und für Version 4 aktualisieren.
      Dazu musst Du die Aufrufe der entfallenen Twig-Funktionen `navigation_list`, `navigation_list_class`, `navigation_item`,
      `navigation_item_class`, `navigation_text`, `navigation_text_class`, `navigation_url` und `navigation_caption` durch
      entsprechende Aufrufe der `block()`-Funktion (ohne Parameter) ersetzen:

      Aus `{{ navigation_caption(themeRoot, node, level) }}` wird `{{ block('navigation_caption') }}`.
    * Falls es dabei zu einem Fehler kommt, z.B. die Variable `nodes` nicht gefunden wurde, sollte ein Blick in die neuen
      Blöcke in `navigationBlocks.html.twig` helfen. Dann sieht man z.B. schnell, dass jetzt die `visibleNodes` verwendet
      werden sollten. 
    * Nach jeder Block-Aktualisierung solltest Du die Seite lokal aufrufen können.
  * Lösche die `main-nav-layout.html.twig`.
  * Jetzt hast Du eine Navigation aktualisiert: guter Zeitpunkt zum Comitten.

### Tipps
* Achte darauf, dass Du lokal eine Seite aufrufst, die die zu aktualisierende Navigation auch rendert. Beispielsweise
  haben Startseiten nicht immer eine Breadcrumb-Navigation. Passende Seiten kann man i.d.R. auf der Live-Seite suchen.
* Wenn Dir die aktualisierten Navigationen visuell falsch vorkommen, kannst Du Dich dem Fehler möglicherweise schnell
  über ein Diff des Production- und lokalen HTMLs annähern. 
* Mit Version 4.1 (s.o.) wurden BEM-Templates eingeführt. Du kannst via `{% extends '@WebfactoryNavigation/Navigation/navigation-BEM.html.twig' %}`
  die Blöcke aus `navigation-blocks-BEM.html.twig` zu nutzen, bzw. via `breadcrumbs-BEM.html.twig` die aus `breadcrumbs-blocks-BEM.html.twig`. 
 
### BC-Breaks
* Entfallener Twig-Tag: `navigation_theme`
* Entfallene Twig-Funktionen: `navigation_list`, `navigation_list_class`, `navigation_item`, `navigation_item_class`,
  `navigation_text`, `navigation_text_class`, `navigation_url`, `navigation_caption`
* Ersatzlos entfallene Twig-Funktion: `navigation`.
* Entfallene Services: `webfactory_navigation.twig_theme_extension`, `Webfactory\Bundle\NavigationBundle\Twig\NavigationThemeExtension`
  (waren vermutlich nur intern genutzt).
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
