Ich habe am Projekt folgende Änderungen vorgenommen:

- routen.php & routen_edit.php: der aktuelle Standort der User wird lokalisiert und die Karte wird automatisch auf diese Position gezoomt
- register.php: Das Geburtsdatum des Hundes kann spätestens der aktuelle Tag sein, somit kommt kein negatives Alter zustande
- register.php: Es existiert nun das Feld "Passwort wiederholen". Stimmt das dort eingegebene Passwort nicht mit  dem 1 Passwort überein, wird die Registrierung abgebrochen.
- routen_edit.php: SRI für Scripts hinzugefügt
- routen_edit.php: Gehzeit wird auch während dem Zeichnen der Route berechnet und angezeigt
- Alle Queries sind nun mit prepared statements gegen Sql-Injection geschützt
- profil.php: Beim Löschen des Profils wird nachgefragt, ob das Profil wirklich gelöscht werden soll
- Kommentarfunktion habe ich entfernt (wie mit Brigitte besprochen)
- routen_sql.php: statt Minus als Trennzeichen habe ich jetzt ein Dollarzeichen
