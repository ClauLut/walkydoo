CREATE TABLE profil (
    mail            VARCHAR(100)    NOT NULL    PRIMARY KEY,
    passwort        TEXT            NOT NULL,
    username        VARCHAR(100)    NOT NULL,
    vorname         VARCHAR(100)    NOT NULL,
    nachname        VARCHAR(100)    NOT NULL,
    hundename       VARCHAR(100)    NOT NULL,
    rasse           VARCHAR(100)    NOT NULL,
    geburtsdatum    DATE            NOT NULL,
    beschreibung    TEXT            NULL,
    profilbild      VARCHAR(200)    NULL
);

CREATE TABLE routen (
    id              SERIAL           NOT NULL       PRIMARY KEY,
    meter           VARCHAR(20)      NOT NULL
);

CREATE TABLE koordinaten (
    id              SERIAL           NOT NULL       PRIMARY KEY,
    lat             VARCHAR(50)      NOT NULL,
    lng             VARCHAR(50)      NOT NULL,
    routen_id       SERIAL           NOT NULL       REFERENCES routen(id),
    idx             INT              NOT NULL
);

CREATE TABLE kommentare (
    id          SERIAL          NOT NULL    PRIMARY KEY,
    datum       VARCHAR(20)     NOT NULL,
    kommentar   TEXT            NOT NULL,
    userid      VARCHAR(100)    NOT NULL    REFERENCES profil(mail)
);