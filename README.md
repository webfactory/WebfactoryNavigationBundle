# navigation-bundle #


## Motivation ##

## Installation ##
The bundle is installed like any other Symfony2 bundle.

## Credits, Copyright and License ##

This project was started at webfactory GmbH, Bonn.

- <http://www.webfactory.de>
- <http://twitter.com/webfactory>

Copyright 2015 webfactory GmbH, Bonn. Code released under [the MIT license](LICENSE).

## webfactory/navigation-bundle Version 1.* ##

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

## webfactory/navigation-bundle Version 2.* ##

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
