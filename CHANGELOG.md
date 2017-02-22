Changelog
=========

Version 2.6.0
-------------

 * Neue Twig Function `navigation_active_at_level(level)` gibt den
   \Webfactory\Bundle\NavigationBundle\Tree\Node zurück, der auf der
   gegebenen Ebene aktiv ist. Damit lässt sich beispielsweise prüfen,
   ob dieser Knoten in der Navigation dargestellt (sichtbar) ist. Lässt
   sich auch verwenden, um mit NavigationController::navigationAction
   die dort wurzelnde Navigation auszugeben.
   
Version 2.5.0
-------------

 * Neue NavigationController::ancestryAction zur Ausgabe von Teilbäumen z. B. der 
   dritten und vierten Navigationsebene oberhalb des aktiven Knotens.
 * Verbesserung whitespace-control um ausgegebenes HTML
 
Version 2.4.1
-------------

 * Deprecation notice für neuere Twig-Versionen vermeiden
 
 * Zugriff auf den Baum beim Start von bin/console vermeiden. Das könnte zu Zugriffen
   auf eine möglicherweise noch nicht initialisierte Datenbank führen.
    
Version 2.4.0
-------------
 
 * Neues Kommando webfactory:navigation:dump-tree zur Ausgabe des gesamten Baums
   auf der Konsole.

 * Neues Kommando webfactory:navigation:lookup-node zum Testen des Node-Lookups
   über key-value-Paare.

Version 2.3.0
-------------

 * webfactory_navigation.tree_initialized-Event über den regulüren EventDispatcher verteilen, sobald der Baum erzeugt wurde.
   Damit können Klienten den Baum verändern/erzeugen/ergänzen - und zwar genau in dem Moment, wo er erstmals benötigt wird.
   Details zur Verwendung in 818daa580f243344b183e67438dea3592eb24b8a.


    
