Das aufwändigste File ist das routen_edit.php, es unterscheidte sich zum routen.php darin, dass es nur für eingeloggt UserInnen verfügbar ist. Sie können nämlich selbst auf der Karte Routen implementieren, indem durch eine onclick-function die Koordinaten von der Karte gelesen werden. Mit 'Speichern' werden die Daten anschließend in die Datenbank gespeichert und können dann auf der Karte angezeigt werden. Mit 'Punkt löschen' kann beim Zeichnen der Routen der letzte Punkt gelöscht werden und mit 'Abbrechen' passiert keine Änderung in der DB. 
Auf das routen.php können auch nicht registrierte UserInnen zugreifen, sie können bereits vorhandene Routen ansehen, jedoch nicht Routen selbst implementieren oder Kommentare schreiben.

Die '_sql.php'-Files dienen für die Ajax-Requests und werden über eine URL aufgrufen. 

Die Profilbilder der UserInnen werden im Ordner 'profilbilder' abgespeichert und auch wieder herausgelöscht, wenn das Profil gelöscht wird.

Die Daten von OpenStrettMap für die Hundewiesen-/und Hundetoilettenmarker werden aus einem JSON-File gelesen.

Die restlichen Files sind ziemlich selbsterklärend, ich habe ein header.php und ein footer.php, die in jedem anderen php-File mit 'include' eingebunden werden. In functions.php wird die Verbindung zu Datenbank hergestellt. Diese nutze ich auch in fast jedem File, z.B. beim resiger.php, profil.php, profil_edit, ...