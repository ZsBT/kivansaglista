-- ezert szeretem a PostgreSQL-t, mert szemely szerint egy szekvenciat hasznalok minden tablara.
-- ebbol kovetkezik, hogy egy ID csak egyszer szerepel az egesz adatbazisban, ebbol kovetkeztetni
-- lehet visszamenoleg is a rekordok rogzitesenek sorrendjere. 

-- mas hozzaallas persze, ha pl myslq-ben ugysem hasznalunk szekvenciat csak autoincrementet,
-- hogy ne legyenek "lukak" es elkeruljuk az eszrevetlen, utolagos adattorlest.
-- mondjuk az adatgazda altal vegzett manipulacio ellenorzesere blockchain algoritmust hasznalnek, de ez kulon sztori.

create sequence nextid;


-- 'nagyon' PG-specifikus oszloptipusokat nemigen szeretek hasznalni, mert kesobb kiderulhet hogy mas DB szerver kell.
-- igyekezzunk csokkenteni a remalmon, hogy migralas soran sokat kelljen masszirozni az alkalmazas kodjat.
-- emiatt boolean sincs hasznalatban, mert eltero lehet a szintaxis. amugy is 32 vagy 64 biten fogja tarolni azt a nyomorult egy bitet 

-- a jog oszlopban levo integer ha 1 akkor user level, 2 pedig admin level.
-- aktiv: 1 ha aktiv, 0 ha inaktiv.
create table felhasznalo(id bigint primary key default nextval('nextid')
    ,azonosito varchar(50) not null
    ,jelszo char(64) not null
    ,jog smallint default 1
    ,nev varchar(100) not null
    ,letrehozva timestamptz default now()
    ,modositva timestamptz
    ,aktiv smallint default 1
);

-- csak egyedi felhasznalonev lehet
create unique index felhasznalo_uix on felhasznalo(azonosito);


-- kivansaglista
create table kivansaglista(id bigint primary key default nextval('nextid')
    ,felhasznalo bigint references felhasznalo(id) on delete cascade
    ,nev text not null
    ,linkhash text
    ,letrehozva timestamptz default now()
);

create index kivansaglista_linkhash_ix on kivansaglista(linkhash);
-- csak egyedi kivansaglista nev lehet felhasznalonkent
create unique index kivansaglista_uix on kivansaglista(felhasznalo,nev);

-- kivansag
create table kivansag(id bigint primary key default nextval('nextid')
    ,lista bigint references kivansaglista(id) on delete cascade
    ,nev text not null
    ,ar bigint
    ,letrehozva timestamptz default now()
);
-- csak egyedi kivansag lehet listankent
create unique index kivansag_uix on kivansag(lista,nev);

