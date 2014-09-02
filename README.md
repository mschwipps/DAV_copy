DAV_copy
========

Copy/Upload files to a WebDAV-Server via command line interface.

DAV-Copy kopiert eine Menge von Dateien auf einen WebDAV-Server.

Hintergrund / Vorgeschichte

Im Rahmen von Backups wollte ich einige Dateien auf einen WebDAV-Server
stellen.
Die Aufgabe wird durch n�chtliche Cronjobs durchgef�hrt.
Es musste also etwas Skriptf�higes sein.

F�r die Kommandozeile gibt es seit Jahren cadaver. Im interaktiven Betrieb hat
sich dieses Tool bereits bew�hrt. Leider hat cadaver keine Optionen, um es
unmittelbar in Skripten zu nutzen. Es gibt nat�rlich expect. Mit diesem l�sst
sich diese Aufgabe zun�chst zurechtfrickeln. Was passiert mit dem interaktiven
Output wie Fortschrittsbalken? Der landet dann als Output in den cronjob-Mails
und man sieht den Wald vor B�umen nicht oder filtert nach. :(

Als Alternative steht seit langem davfs2 zur Verf�gung. davfs2 bietet die
M�glichkeit WebDAV-Server unter Linux via fuse als Filesystem einzubinden.
Das Hochladen funktionierte f�r einige dutzend MegaByte gro�e Dateien jahrelange
gut.
Da davfs2 praktisch alles cached, ist es f�r den interaktiven Betrieb ebenfalls
ganz brauchbar. Wobei hier nat�rlich auch noch die ganze Unix-Toolchain zur
Verf�gung steht. Eigentlich ist die Idee imho das Eleganteste, was unter
unix-artigen OS geht.

Leider wurden durch davfs2 irgendwann nicht mehr alle Dateien sauber �bertragen.
Es gab weder Fehlermeldungen noch brauchbare Debugging-Logs. Die vermeintlich
kopierten Dateien fehlten einfach. Au�erdem werden die Dateien praktisch erst
beim umount aus dem Cache hochgeladen. Das macht Uploads, die zwei Stunden
laufen, nicht mehr wirklich vertrauensw�rdig. Da die hochzuladenden Dateien auch
noch auf Platte gecached wurden, verblieben auch ab und zu Reste aus abgebrochenen
DSL-Verbindungen im Cache.

Da der zu erwartende Debugging-Aufwand irgendwann den des PHP-Skripten
�berstieg, entschloss ich mich f�r letzteres.
Ich setze davfs2 weiterhin in Cronjobs ein, z.B. zum L�schen alter Dateien auf
dem Server.

Aus einem fr�hen Projekt hatte ich bereits Erfahrung mit der �bertragung von
Terminen auf CalDAV-Server.
Der Datei-Upload ist demgegen�ber nochmal deutlich einfacher.
Das Ergebnis ist DAV-copy.

Warum php? Weil es gerade da war.

Links:
davfs2 - http://savannah.nongnu.org/projects/davfs2
cadaver - http://www.webdav.org/cadaver/
davical - http://www.davical.org

