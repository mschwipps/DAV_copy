DAV_copy
========

Copy/Upload files to a WebDAV-Server via command line interface.

DAV-Copy kopiert eine Menge von Dateien auf einen WebDAV-Server.

Hintergrund / Vorgeschichte

Im Rahmen von Backups wollte ich einige Dateien auf einen WebDAV-Server
stellen.
Die Aufgabe wird durch nächtliche Cronjobs durchgeführt.
Es musste also etwas Skriptfähiges sein.

Für die Kommandozeile gibt es seit Jahren cadaver. Im interaktiven Betrieb hat
sich dieses Tool bereits bewährt. Leider hat cadaver keine Optionen, um es
unmittelbar in Skripten zu nutzen. Es gibt natürlich expect. Mit diesem lässt
sich diese Aufgabe zunächst zurechtfrickeln. Was passiert mit dem interaktiven
Output wie Fortschrittsbalken? Der landet dann als Output in den cronjob-Mails
und man sieht den Wald vor Bäumen nicht oder filtert nach. :(

Als Alternative steht seit langem davfs2 zur Verfügung. davfs2 bietet die
Möglichkeit WebDAV-Server unter Linux via fuse als Filesystem einzubinden.
Das Hochladen funktionierte für einige dutzend MegaByte große Dateien jahrelange
gut.
Da davfs2 praktisch alles cached, ist es für den interaktiven Betrieb ebenfalls
ganz brauchbar. Wobei hier natürlich auch noch die ganze Unix-Toolchain zur
Verfügung steht. Eigentlich ist die Idee imho das Eleganteste, was unter
unix-artigen OS geht.

Leider wurden durch davfs2 irgendwann nicht mehr alle Dateien sauber übertragen.
Es gab weder Fehlermeldungen noch brauchbare Debugging-Logs. Die vermeintlich
kopierten Dateien fehlten einfach. Außerdem werden die Dateien praktisch erst
beim umount aus dem Cache hochgeladen. Das macht Uploads, die zwei Stunden
laufen, nicht mehr wirklich vertrauenswürdig. Da die hochzuladenden Dateien auch
noch auf Platte gecached wurden, verblieben auch ab und zu Reste aus abgebrochenen
DSL-Verbindungen im Cache.

Da der zu erwartende Debugging-Aufwand irgendwann den des PHP-Skripten
überstieg, entschloss ich mich für letzteres.
Ich setze davfs2 weiterhin in Cronjobs ein, z.B. zum Löschen alter Dateien auf
dem Server.

Aus einem frühen Projekt hatte ich bereits Erfahrung mit der Übertragung von
Terminen auf CalDAV-Server.
Der Datei-Upload ist demgegenüber nochmal deutlich einfacher.
Das Ergebnis ist DAV-copy.

Warum php? Weil es gerade da war.

Links:
davfs2 - http://savannah.nongnu.org/projects/davfs2
cadaver - http://www.webdav.org/cadaver/
davical - http://www.davical.org

