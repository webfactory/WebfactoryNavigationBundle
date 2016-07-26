Changelog
=========

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
   Details zur Verwendung in 3677ff13b448.


    
