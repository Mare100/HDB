<?php
/* =========================================================
 * HDB DEBUG MODUS
 * ========================================================= */
define('HDB_DEBUG', true);      // <<< AUF false SETZEN IM PRODUKTIVBETRIEB
define('HDB_DEBUG_XML', false); // true = SOAP XML anzeigen

$HDB_DEBUG = (isset($_GET['debug']) && $_GET['debug'] == '1');

$soapclient = new SoapClient($wsdl, array(
    'trace' => 1,
    'exceptions' => 1,
    'cache_wsdl' => WSDL_CACHE_NONE
));

function hdb_debug($title, $response, $soapclient) {
    global $HDB_DEBUG;
    if (!$HDB_DEBUG) return;

    echo "\n==================== ".$title." ====================\n";
    if ($soapclient) {
        echo "\n--- SOAP REQUEST ---\n";
        echo htmlspecialchars($soapclient->__getLastRequest());
        echo "\n--- SOAP RESPONSE ---\n";
        echo htmlspecialchars($soapclient->__getLastResponse());
    }
    echo "\n--- PARSED RESPONSE ---\n";
    print_r($response);
    echo "\n====================================================\n";
}

/* =========================================================
 * BASIC SETTINGS
 * ========================================================= */
ini_set('error_reporting', E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);
header('Content-Type: text/plain; charset=utf-8');

/* =========================================================
 * PARAMETER CHECK
 * ========================================================= */
function hdb_debug($label, $data = null, $soapclient = null) {
    if (!HDB_DEBUG) {
        return;
    }

    echo "\n==================== HDB DEBUG ====================\n";
    echo $label . "\n";

    if ($data !== null) {
        echo "\n--- DATA ---\n";
        print_r($data);
    }

    if (HDB_DEBUG_XML && $soapclient instanceof SoapClient) {
        echo "\n--- SOAP REQUEST ---\n";
        echo htmlspecialchars($soapclient->__getLastRequest());

        echo "\n--- SOAP RESPONSE ---\n";
        echo htmlspecialchars($soapclient->__getLastResponse());
    }

    echo "\n===================================================\n";
}

if (!isset($_GET['transponder'])) {
    http_response_code(400);
    echo "Fehlender Parameter: transponder\n";
    echo "Beispiel: index.php?transponder=ABC123456";
    exit;
}

if (!preg_match('/^[A-Z0-9]+$/', $_GET['transponder'])) {
    http_response_code(400);
    echo "Ungültige Transpondernummer";
    exit;
}



$transponder = $_GET['transponder'];

/* =========================================================
 * DECLARATIONS / LOOKUP TABLES
 * ========================================================= */
$CatGeschlechtTyp = array(
    'Herr' => '1',
    'Frau' => '3'
);

$CatGeschlechtTypTier = array(
    '1' => '1',
    '2' => '3',
    '4' => '2',
    '5' => '4'
);

/* Länder-Tabelle bleibt UNVERÄNDERT
   (inhaltlich korrekt, nur weiter unten verwendet) */
$CatLand = array(
    'AF' => '1',
    'EG' => '2',
    'AL' => '3',
    'DZ' => '4',
    'AD' => '5',
    // … (rest unverändert)
    'AT' => '253', 'A' => '253'
);

$CatAusweisTyp = array(
    'F hrerschein'      => '1',
    'Personalausweis'  => '2',
    'Reisepass'        => '3',
    'Sch lerausweis'   => '4',
    'Studentenausweis' => '5',
    'Identit tskarte'  => '6',
    'Identit tsausweis'=> '6',
    'Dienstausweis'    => '7',
    'Behindertenpass'  => '8'
);

$CatTyp = array(
    'H'  => '1',
    'E'  => '2',
    'HE' => '3'
);

/* =========================================================
 * HELPER FUNCTIONS
 * ========================================================= */
function objectToArray($object) {
    if (!is_object($object) && !is_array($object)) {
        return $object;
    }
    if (is_object($object)) {
        $object = get_object_vars($object);
    }
    return array_map('objectToArray', $object);
}

function logfile($transponder, $text) {
    file_put_contents(
        '/var/log/tierreg/hdb.log',
        '[' . date('d-M-Y H:i:s') . '] ' . $transponder . ' ' . $text . "\n",
        FILE_APPEND | LOCK_EX
    );
}

/* =========================================================
 * DATABASE CONNECTION (LEGACY mysql_*)
 * ========================================================= */
$conn = mysql_connect('localhost', 'ifta', 'rv:d4S5FSxJWnNJp');
if (!$conn) {
    http_response_code(500);
    echo "DB-Verbindung fehlgeschlagen";
    exit;
}

if (!mysql_select_db('db00000000001', $conn)) {
    http_response_code(500);
    echo "DB-Auswahl fehlgeschlagen";
    exit;
}

/* =========================================================
 * AB HIER: BLOCK 2 (SQL)
 * ========================================================= */

/* =========================================================
 * SQL: DATEN LADEN
 * ========================================================= */
$sql = "
SELECT SQL_CALC_FOUND_ROWS
    i_tiere.id,
    i_tiere.adr_id,
    i_tiere.transponder,
    i_tiere.tname,
    IFNULL(i_tierart.de_lang, i_tiere.art) AS art,
    i_tiere.art_id,
    IFNULL(i_tierrasse.de_lang, i_tiere.rasse) AS rasse,
    i_tierrasse.HDB_id AS HDB_id,
    i_tiere.geschlecht_id,
    i_tiere.geburt,
    i_tiere.geburtsland,
    i_tiere.htausweis,
    i_tiere.besitz,
    i_tiere.status,
    i_tiere.status_date,
    i_tiere.hdb_uebernahme_reg_id,
    IF(i_tiere.regdatum IS NULL OR i_tiere.regdatum = '0000-00-00',
       i_tiere.awdatum,
       i_tiere.regdatum) AS regdatum,
    i_adressen.anrede,
    i_adressen.titel,
    i_adressen.vorname,
    i_adressen.name,
    i_adressen.gebhalter,
    i_adressen.plz,
    i_adressen.land,
    i_adressen.ort,
    i_adressen.strasse,
    IFNULL(i_adressen.telefon_priv, i_adressen.telefon_ges) AS telefon,
    i_adressen.telefon_mobil,
    i_adressen.fax,
    i_adressen.email,
    i_adressen.ausweisart,
    i_adressen.prausweis,
    i_heimtierdb_at.reg_id,
    i_heimtierdb_at.data
FROM i_tiere
LEFT JOIN i_tierrasse
    ON i_tiere.rasse_id = i_tierrasse.id
LEFT JOIN i_tierart
    ON i_tiere.art_id = i_tierart.id
LEFT JOIN i_heimtierdb_at
    ON i_tiere.id = i_heimtierdb_at.tier_id
   AND i_tiere.adr_id = i_heimtierdb_at.adr_id,
     i_adressen
WHERE
    i_tiere.art_id IN (1,2)
AND i_tiere.adr_id = i_adressen.id
AND i_tiere.beswechsel IS NULL
AND i_adressen.land = 'A'
AND i_tiere.transponder = '" . mysql_real_escape_string($transponder) . "'
ORDER BY i_tiere.id
";

$result = mysql_query($sql, $conn);
if (!$result) {
    http_response_code(500);
    echo "SQL Fehler: " . mysql_error();
    exit;
}

if (!mysql_num_rows($result)) {
    http_response_code(404);
    echo "Kein Datensatz für Transponder " . $transponder;
    exit;
}

/* =========================================================
 * ROW COUNT
 * ========================================================= */
$result2 = mysql_query("SELECT FOUND_ROWS()", $conn);
$maxcount = ($result2) ? mysql_result($result2, 0) : 0;
mysql_free_result($result2);

/* =========================================================
 * DATEN VALIDIEREN & NORMALISIEREN
 * ========================================================= */
$error = '';

while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

    if (!preg_match('/^[A-Z0-9]+$/', $row['transponder'])) continue;

    if (!isset($CatLand[$row['geburtsland']])) {
        $error .= "Geburtsland ungültig\n";
        break;
    }

    if (!isset($CatGeschlechtTypTier[$row['geschlecht_id']])) {
        $error .= "Geschlecht ungültig\n";
        break;
    }

    /* === Datum Tier === */
    if (!empty($row['geburt'])) {
        $ts = strtotime(str_replace('.', '-', $row['geburt']));
        if ($ts === false) {
            $error .= "Geburtsdatum Tier ungültig\n";
            break;
        }
        $row['geburt'] = date('Y-m-d', $ts);
    }

    /* === Besitzdatum === */
    if (empty($row['besitz'])) {
        $row['besitz'] = $row['regdatum'];
    }
    $row['besitz'] = date('Y-m-d', strtotime(str_replace('.', '-', $row['besitz'])));

    /* === Halter Geburtstag === */
    $ts = strtotime(str_replace('.', '-', $row['gebhalter']));
    if ($ts === false) {
        $error .= "Geburtsdatum Halter ungültig\n";
        break;
    }
    $row['gebhalter'] = date('Y-m-d', $ts);

    /* === UTF-8 / XML Safe === */
    foreach ($row as $key => $value) {
        if ($key == 'data' || $value === null) continue;
        $row[$key] = utf8_encode(str_replace('&', '&amp;', trim($value)));
    }

    /* =====================================================
     * AB HIER: BLOCK 3 (Tier & Halter Mapping)
     * ===================================================== */

/* =========================================================
 * BLOCK 3: TIER & HALTER (SOAP- / WSDL-KONFORM)
 * ========================================================= */

/* ---------- TIER ---------- */
$tier = new stdClass();
$tier->ChipCode = $row['transponder'];
$tier->ChipTyp  = preg_match('/^[0-9]{15}$/', $row['transponder']) ? 1 : 2;

/* TierArt */
if ($row['art_id'] == 2) {
    $tier->TierArt = 8; // Katze
} else {
    $tier->TierArt = 1; // Hund
}

if (!empty($row['tname'])) {
    $tier->Name = $row['tname'];
}

/* WSDL-PFLICHTFELDER */
$tier->Rasse     = '';                    // MUSS existieren (string)
$tier->TierRasse = (int)$row['HDB_id'];   // ID aus i_tierrasse (int)

/* weitere Tierdaten */
$tier->Geschlecht   = (int)$CatGeschlechtTypTier[$row['geschlecht_id']];
$tier->Geburtsdatum = $row['geburt'];
$tier->Geburtsland  = (int)$CatLand[$row['geburtsland']];

if (!empty($row['htausweis'])) {
    $tier->HeimtierausweisNr = $row['htausweis'];
}

/* Todesdatum */
if ($row['status'] == 1 && !empty($row['status_date'])) {
    $ts = strtotime(str_replace('.', '-', $row['status_date']));
    if ($ts !== false) {
        $tier->Todesdatum = date('Y-m-d', $ts);
    }
}

/* ---------- HALTER / EIGENTÜMER ---------- */
$halter = new stdClass();
$halter->Beginn = $row['besitz'];
$halter->Typ    = $CatTyp['HE'];

/* Person */
$halter->Person = new stdClass();
$halter->Person->Typ          = 'nat';
$halter->Person->Vorname      = $row['vorname'];
$halter->Person->Nachname     = $row['name'];
$halter->Person->Geburtsdatum = $row['gebhalter'];
$halter->Person->Geschlecht   = (int)$CatGeschlechtTyp[$row['anrede']];

if (!empty($row['titel'])) {
    $halter->Person->Titel = $row['titel'];
}

/* Adresse */
$halter->Adresse = new stdClass();
$halter->Adresse->Land = (int)$CatLand[$row['land']];
$halter->Adresse->PLZ  = $row['plz'];
$halter->Adresse->Ort  = $row['ort'];

if (preg_match('/^([0-9]*[^0-9]+)\s*([0-9]+.*)$/u', $row['strasse'], $tmp)) {
    $halter->Adresse->Str = trim($tmp[1]);
    $halter->Adresse->Nr  = trim($tmp[2]);
} else {
    $halter->Adresse->Str = $row['strasse'];
    $halter->Adresse->Nr  = '';
}

/* Kontaktdaten */
$halter->Kontaktdaten = new stdClass();
if (!empty($row['telefon']))        $halter->Kontaktdaten->Tel   = $row['telefon'];
if (!empty($row['telefon_mobil']))  $halter->Kontaktdaten->Mobil= $row['telefon_mobil'];
if (!empty($row['fax']))            $halter->Kontaktdaten->Fax   = $row['fax'];
if (!empty($row['email']))          $halter->Kontaktdaten->eMail = $row['email'];

/* Ausweis */
$halter->Ausweis = new stdClass();
$halter->Ausweis->Typ    = (int)$CatAusweisTyp[utf8_decode($row['ausweisart'])];
$halter->Ausweis->Nummer = $row['prausweis'];
$halter->Ausweis->Land   = (int)$CatLand['AT'];

/* Zeitmessung */
$hdb_time = microtime(true);


/* =========================================================
 * BLOCK 3b: HALTERWECHSEL / CACHE / ABGABE
 * ========================================================= */

$transfer_id = null;

/* ---------- Prüfe Halterwechsel ---------- */
if (!isset($row['reg_id'])) {

    $sql = sprintf(
        "SELECT `reg_id`,`data`,`transfer_id`
         FROM `i_heimtierdb_at`
         WHERE `tier_id` = %d
           AND `transponder` = '%s'
           AND (`transfer_id` IS NULL OR `transfer_id` = 0)",
        $row['id'],
        mysql_real_escape_string($row['transponder'])
    );

    $result2 = mysql_query($sql, $conn);
    if ($result2) {
        $transfer_row = mysql_fetch_array($result2, MYSQL_ASSOC);
        mysql_free_result($result2);
    } else {
        $transfer_row = false;
    }

    if ($transfer_row !== false && !empty($transfer_row['data'])) {

        $transfer_data = unserialize($transfer_row['data']);
        if ($transfer_data !== false) {

            $transfer_id = $transfer_row['reg_id'];

            if (!isset($transfer_row['transfer_id'])) {

                $response_time = microtime(true);
                $response = $soapclient->Abgabe(
                    $row['transponder'],
                    preg_match('/^[0-9]{15}$/', $row['transponder']) ? 1 : 2,
                    $transfer_row['reg_id'],
                    $transfer_data['HalterEigentuemer'],
                    date('Y-m-d'),
                    $halter->Person->Vorname,
                    $halter->Person->Nachname,
                    $halter->Ausweis
                );
                $response_time = microtime(true) - $response_time;

                logfile($row['transponder'], "Abgabe (Status: ".$response->Status.")");

                if ($response->Status == 0) {

                    if (isset($transponder)) {
                        print "Abgabe (ChipCode \"".$row['transponder']."\") an ".
                              $halter->Person->Vorname." ".
                              $halter->Person->Nachname.": OK\n";
                    }

                    $sql = sprintf(
                        "UPDATE `i_heimtierdb_at`
                         SET `transfer_id` = 0
                         WHERE `reg_id` = %.0f
                           AND `transfer_id` IS NULL",
                        $transfer_id
                    );
                    mysql_query($sql, $conn);

                } else {

                    $errormsg = "Fehler bei Abgabe (ChipCode \"".$row['transponder']."\"):\n".
                                print_r($response, true);
                    print $errormsg;

                    $sql = sprintf(
                        "INSERT INTO `i_heimtierdb_at:error`
                         SET `transponder` = '%s',
                             `tier_id` = %d,
                             `adr_id` = %d,
                             `error` = '%s',
                             `timestamp` = NOW()
                         ON DUPLICATE KEY UPDATE
                             `error` = '%s',
                             `timestamp` = NOW()",
                        mysql_real_escape_string($row['transponder']),
                        $row['id'],
                        $row['adr_id'],
                        mysql_real_escape_string($errormsg),
                        mysql_real_escape_string($errormsg)
                    );
                    mysql_query($sql, $conn);

                    continue;
                }
            }
        }

    } elseif (isset($row['hdb_uebernahme_reg_id'])) {
        $transfer_id = $row['hdb_uebernahme_reg_id'];
    }
}

/* ---------- Cache prüfen / Abfrage ---------- */
$data = isset($row['data']) ? unserialize($row['data']) : false;

if (isset($row['reg_id']) && $data === false) {

    print "Cache fuer ChipCode \"".$row['transponder']."\" fehlerhaft, frage HDB-Daten ab...\n";

    $response = $soapclient->Abfrage(
        $row['transponder'],
        preg_match('/^[0-9]{15}$/', $row['transponder']) ? 1 : 2,
        $row['reg_id']
    );

    logfile($row['transponder'], "Abfrage (Status: ".$response['Status']->Status.")");

    if ($response['Status']->Status == 0) {

        if (isset($transponder)) {
            print "Abfrage (ChipCode \"".$row['transponder']."\"): OK\n";
        }

        $data = objectToArray($response['Daten']);

        unset($data['HalterEigentuemer']['Adresse']['GKZ']);
        unset($data['HalterEigentuemer']['Adresse']['Gemeinde']);
        unset($data['HalterEigentuemer']['Adresse']['OKZ']);
        unset($data['HalterEigentuemer']['Adresse']['SKZ']);

        $sql = sprintf(
            "UPDATE `i_heimtierdb_at`
             SET `data` = '%s',
                 `hdb_time` = %.3f,
                 `updated` = NOW()
             WHERE `reg_id` = %.0f",
            mysql_real_escape_string(serialize($data)),
            microtime(true) - $hdb_time,
            $row['reg_id']
        );
        mysql_query($sql, $conn);

    } else {

        $errormsg = "Fehler bei Abfrage (ChipCode \"".$row['transponder']."\"):\n".
                    print_r($response, true);
        print $errormsg;
    }
}

/* =========================================================
 * BLOCK 3c: ÄNDERUNGEN (TIER / HALTER)
 * ========================================================= */

if ($data !== false) {

    $TierAenderung = false;
    $HEAenderung   = false;

    /* ---------- Tier-Vergleich ---------- */
    if (isset($data['Tier']['Name']) && $data['Tier']['Name'] != (isset($tier->Name) ? $tier->Name : null)) {
        $TierAenderung = new stdClass();
        $TierAenderung->Name = $tier->Name;
    }

    if ($data['Tier']['Geschlecht'] != $tier->Geschlecht) {
        if ($TierAenderung === false) $TierAenderung = new stdClass();
        $TierAenderung->Geschlecht = $tier->Geschlecht;
    }

    if (
        isset($tier->HeimtierausweisNr) &&
        (!isset($data['Tier']['HeimtierausweisNr']) ||
         $data['Tier']['HeimtierausweisNr'] != $tier->HeimtierausweisNr)
    ) {
        if ($TierAenderung === false) $TierAenderung = new stdClass();
        $TierAenderung->HeimtierausweisNr = $tier->HeimtierausweisNr;
    }

    if (
        isset($tier->Todesdatum) &&
        (!isset($data['Tier']['Todesdatum']) ||
         $data['Tier']['Todesdatum'] != $tier->Todesdatum)
    ) {
        if ($TierAenderung === false) $TierAenderung = new stdClass();
        $TierAenderung->Todesdatum = $tier->Todesdatum;
    }

    /* DEBUG */
hdb_debug('Erstmeldung RESPONSE', $response, $soapclient);

/* HDB-Fehler sofort sichtbar machen */
if (!isset($response->Status) || $response->Status != 0) {
    hdb_debug('HDB FEHLER – Erstmeldung fehlgeschlagen', $response, $soapclient);
}

    /* ---------- Halter-Vergleich ---------- */
    if ($data['HalterEigentuemer']['Typ'] != $halter->Typ) {
        $HEAenderung = new stdClass();
        $HEAenderung->Typ = $halter->Typ;
    }

    if (
        isset($halter->Person->Titel) &&
        $data['HalterEigentuemer']['Person']['Titel'] != $halter->Person->Titel
    ) {
        if ($HEAenderung === false) $HEAenderung = new stdClass();
        $HEAenderung->Titel = $halter->Person->Titel;
    }

    if ($data['HalterEigentuemer']['Person']['Nachname'] != $halter->Person->Nachname) {
        if ($HEAenderung === false) $HEAenderung = new stdClass();
        $HEAenderung->Familienname = $halter->Person->Nachname;
    }

    /* Adresse */
    if (
        $data['HalterEigentuemer']['Adresse']['Land'] != $halter->Adresse->Land ||
        $data['HalterEigentuemer']['Adresse']['PLZ']  != $halter->Adresse->PLZ  ||
        $data['HalterEigentuemer']['Adresse']['Ort']  != $halter->Adresse->Ort  ||
        $data['HalterEigentuemer']['Adresse']['Str']  != $halter->Adresse->Str  ||
        $data['HalterEigentuemer']['Adresse']['Nr']   != $halter->Adresse->Nr
    ) {
        if ($HEAenderung === false) $HEAenderung = new stdClass();
        $HEAenderung->Adresse = new stdClass();
        $HEAenderung->Adresse->Land = $halter->Adresse->Land;
        $HEAenderung->Adresse->PLZ  = $halter->Adresse->PLZ;
        $HEAenderung->Adresse->Ort  = $halter->Adresse->Ort;
        $HEAenderung->Adresse->Str  = $halter->Adresse->Str;
        $HEAenderung->Adresse->Nr   = $halter->Adresse->Nr;
    }

    /* Kontaktdaten */
    if (
        (isset($halter->Kontaktdaten->Tel)    && $data['HalterEigentuemer']['Kontaktdaten']['Tel']    != $halter->Kontaktdaten->Tel) ||
        (isset($halter->Kontaktdaten->Mobil)  && $data['HalterEigentuemer']['Kontaktdaten']['Mobil']  != $halter->Kontaktdaten->Mobil) ||
        (isset($halter->Kontaktdaten->Fax)    && $data['HalterEigentuemer']['Kontaktdaten']['Fax']    != $halter->Kontaktdaten->Fax) ||
        (isset($halter->Kontaktdaten->eMail)  && $data['HalterEigentuemer']['Kontaktdaten']['eMail']  != $halter->Kontaktdaten->eMail)
    ) {
        if ($HEAenderung === false) $HEAenderung = new stdClass();
        $HEAenderung->Kontaktdaten = new stdClass();

        if (isset($halter->Kontaktdaten->Tel))   $HEAenderung->Kontaktdaten->Tel   = $halter->Kontaktdaten->Tel;
        if (isset($halter->Kontaktdaten->Mobil))$HEAenderung->Kontaktdaten->Mobil = $halter->Kontaktdaten->Mobil;
        if (isset($halter->Kontaktdaten->Fax))   $HEAenderung->Kontaktdaten->Fax   = $halter->Kontaktdaten->Fax;
        if (isset($halter->Kontaktdaten->eMail)) $HEAenderung->Kontaktdaten->eMail = $halter->Kontaktdaten->eMail;
    }

    /* =====================================================
     * AB HIER GEHT ES MIT BLOCK 4 (SOAP-CALLS) WEITER
     * ===================================================== */
}

/* =========================================================
 * BLOCK 3d: AENDERUNG HALTEREIGENTUEMER (AenderungHE)
 * ========================================================= */

if ($HEAenderung !== false) {

    $sent++;

    /* Typ ist Pflichtfeld */
    if (!isset($HEAenderung->Typ)) {
        $HEAenderung->Typ = $halter->Typ;
    }

    $response_time = microtime(true);
    $response = $soapclient->AenderungHE(
        $row['transponder'],
        preg_match('/^[0-9]{15}$/', $row['transponder']) ? 1 : 2,
        $row['reg_id'],
        $HEAenderung
    );
    $response_time = microtime(true) - $response_time;

    logfile($row['transponder'], "AenderungHE (Status: ".$response->Status.")");

    $errormsg = null;

    if ($response->Status == 0) {

        if (isset($transponder)) {
            print "AenderungHE (ChipCode \"".$row['transponder']."\"): OK\n";
        }

        $success++;

        $sql = sprintf(
            "DELETE FROM `i_heimtierdb_at:error`
             WHERE `transponder` = '%s'",
            mysql_real_escape_string($row['transponder'])
        );
        mysql_query($sql, $conn);

    } else {

        $errormsg = "Fehler bei AenderungHE (ChipCode \"".$row['transponder']."\"):\n".
                    print_r($response, true).
                    "\nHEAenderung:\n".
                    print_r($HEAenderung, true);

        print $errormsg;

        $sql = sprintf(
            "INSERT INTO `i_heimtierdb_at:error`
             SET `transponder` = '%s',
                 `tier_id` = %d,
                 `adr_id` = %d,
                 `error` = '%s',
                 `timestamp` = NOW()
             ON DUPLICATE KEY UPDATE
                 `error` = '%s',
                 `timestamp` = NOW()",
            mysql_real_escape_string($row['transponder']),
            $row['id'],
            $row['adr_id'],
            mysql_real_escape_string($errormsg),
            mysql_real_escape_string($errormsg)
        );
        mysql_query($sql, $conn);
    }

    /* History */
    $sql = sprintf(
        "INSERT INTO `i_heimtierdb_at:history`
         SET `transponder` = '%s',
             `reg_id` = %.0f,
             `tier_id` = %d,
             `adr_id` = %d,
             `data` = '%s',
             `type` = 'AenderungHE',
             `parameter` = '%s',
             `hdb_time` = %.3f,
             `status` = %d,
             `response` = '%s',
             `error` = %s,
             `timestamp` = NOW()",
        mysql_real_escape_string($row['transponder']),
        $row['reg_id'],
        $row['id'],
        $row['adr_id'],
        mysql_real_escape_string(serialize($data)),
        mysql_real_escape_string(serialize(array(
            $row['transponder'],
            preg_match('/^[0-9]{15}$/', $row['transponder']) ? 1 : 2,
            $row['reg_id'],
            $HEAenderung
        ))),
        $response_time,
        $response->Status,
        mysql_real_escape_string(serialize($response)),
        isset($errormsg) ? "'".mysql_real_escape_string($errormsg)."'" : "NULL"
    );
    mysql_query($sql, $conn);
}

/* ---------- Erfolgreich aktualisiert ---------- */
if ($success) {

    if ($success == $sent) {

        $sql = sprintf(
            "UPDATE `i_heimtierdb_at`
             SET `data` = '%s',
                 `hdb_time` = %.3f,
                 `updated` = NOW()
             WHERE `reg_id` = %.0f",
            mysql_real_escape_string(serialize(array(
                'Tier'              => $tier,
                'HalterEigentuemer' => $halter
            ))),
            microtime(true) - $hdb_time,
            $row['reg_id']
        );
        mysql_query($sql, $conn);

    } else {

        $response = $soapclient->Abfrage(
            $row['transponder'],
            preg_match('/^[0-9]{15}$/', $row['transponder']) ? 1 : 2,
            $row['reg_id']
        );

        logfile($row['transponder'], "Abfrage (Status: ".$response['Status']->Status.")");

        if ($response['Status']->Status == 0) {

            if (isset($transponder)) {
                print "Abfrage (ChipCode \"".$row['transponder']."\"): OK\n";
            }

            $reg_data = objectToArray($response['Daten']);

            unset($reg_data['HalterEigentuemer']['Adresse']['GKZ']);
            unset($reg_data['HalterEigentuemer']['Adresse']['Gemeinde']);
            unset($reg_data['HalterEigentuemer']['Adresse']['OKZ']);
            unset($reg_data['HalterEigentuemer']['Adresse']['SKZ']);

            $sql = sprintf(
                "UPDATE `i_heimtierdb_at`
                 SET `data` = '%s',
                     `hdb_time` = %.3f,
                     `updated` = NOW()
                 WHERE `reg_id` = %.0f",
                mysql_real_escape_string(serialize($reg_data)),
                microtime(true) - $hdb_time,
                $row['reg_id']
            );
            mysql_query($sql, $conn);
        }
    }

    /* Counter */
    $count_update++;
}

/* =========================================================
 * BLOCK 3e: UEBERNAHME (Halterwechsel mit neuer Reg-ID)
 * ========================================================= */

elseif (isset($transfer_id)) {

    $response_time = microtime(true);
    $response = $soapclient->Uebernahme(
        $row['transponder'],
        preg_match('/^[0-9]{15}$/', $row['transponder']) ? 1 : 2,
        $transfer_id,
        date("Y-m-d"),
        $halter
    );
    $response_time = microtime(true) - $response_time;

    logfile($row['transponder'], "Uebernahme (Status: ".$response->Status.")");

    if ($response->Status == 0) {

        if (isset($transponder)) {
            print "Uebernahme (ChipCode \"".$row['transponder']."\"): OK\n";
        }

        /* neue Registrierungsnummer */
        $row['reg_id'] = $response->Registrierungsnummer;

        $sql = sprintf(
            "INSERT INTO `i_heimtierdb_at`
             SET `reg_id` = %.0f,
                 `tier_id` = %d,
                 `adr_id` = %d,
                 `transponder` = '%s',
                 `hdb_time` = %.3f,
                 `created` = NOW()",
            $row['reg_id'],
            $row['id'],
            $row['adr_id'],
            mysql_real_escape_string($row['transponder']),
            microtime(true) - $hdb_time
        );
        mysql_query($sql, $conn);

        $sql = sprintf(
            "UPDATE `i_heimtierdb_at`
             SET `transfered` = NOW(),
                 `transfer_id` = %.0f
             WHERE `reg_id` = %.0f
               AND `transfer_id` = 0",
            $row['reg_id'],
            $transfer_id
        );
        mysql_query($sql, $conn);

        /* History */
        $sql = sprintf(
            "INSERT INTO `i_heimtierdb_at:history`
             SET `transponder` = '%s',
                 `reg_id` = %.0f,
                 `tier_id` = %d,
                 `adr_id` = %d,
                 `data` = '%s',
                 `type` = 'Uebernahme',
                 `parameter` = '%s',
                 `hdb_time` = %.3f,
                 `status` = %d,
                 `response` = '%s',
                 `timestamp` = NOW()",
            mysql_real_escape_string($row['transponder']),
            $transfer_id,
            $row['id'],
            $row['adr_id'],
            mysql_real_escape_string(serialize($data)),
            mysql_real_escape_string(serialize(array(
                $row['transponder'],
                preg_match('/^[0-9]{15}$/', $row['transponder']) ? 1 : 2,
                $transfer_id,
                date("Y-m-d"),
                $halter
            ))),
            $response_time,
            $response->Status,
            mysql_real_escape_string(serialize($response))
        );
        mysql_query($sql, $conn);

        /* --------- Abfrage nach Übernahme --------- */
        $response = $soapclient->Abfrage(
            $row['transponder'],
            preg_match('/^[0-9]{15}$/', $row['transponder']) ? '1' : '2',
            $row['reg_id']
        );

        logfile($row['transponder'], "Abfrage (Status: ".$response['Status']->Status.")");

        if ($response['Status']->Status == 0) {

            if (isset($transponder)) {
                print "Abfrage (ChipCode \"".$row['transponder']."\"): OK\n";
            }

            $data = objectToArray($response['Daten']);

            $sql = sprintf(
                "UPDATE `i_heimtierdb_at`
                 SET `data` = '%s',
                     `hdb_time` = %.3f
                 WHERE `reg_id` = %.0f",
                mysql_real_escape_string(serialize($data)),
                microtime(true) - $hdb_time,
                $row['reg_id']
            );
            mysql_query($sql, $conn);

            /* Nachabgleich (Tier / Halter) */
            $TierAenderung = false;
            $HEAenderung   = false;

            if ($data['Tier']['Name'] != $tier['Name'])
                $TierAenderung->Name = $tier['Name'];

            if ($data['Tier']['Geschlecht'] != $tier['Geschlecht'])
                $TierAenderung->Geschlecht = $tier['Geschlecht'];

            if ($data['Tier']['HeimtierausweisNr'] != $tier['HeimtierausweisNr'])
                $TierAenderung->HeimtierausweisNr = $tier['HeimtierausweisNr'];

            if ($data['HalterEigentuemer']['Typ'] != $halter->Typ)
                $HEAenderung->Typ = $halter->Typ;

            if ($data['HalterEigentuemer']['Person']['Titel'] != $halter->Person->Titel)
                $HEAenderung->Titel = $halter->Person->Titel;

            if ($data['HalterEigentuemer']['Person']['Nachname'] != $halter->Person->Nachname)
                $HEAenderung->Familienname = $halter->Person->Nachname;

            if ($data['HalterEigentuemer']['Adresse'] != objectToArray($halter->Adresse))
                $HEAenderung->Adresse = $halter->Adresse;

            if ($data['HalterEigentuemer']['Kontaktdaten'] != objectToArray($halter->Kontaktdaten))
                $HEAenderung->Kontaktdaten = $halter->Kontaktdaten;

            $sent = 0;
            $success = 0;
        }

        $count_insert++;

    } else {

        $errormsg = "Fehler bei Uebernahme (ChipCode \"".$row['transponder']."\"):\n".
                    print_r($response, true);

        print $errormsg;

        $sql = sprintf(
            "INSERT INTO `i_heimtierdb_at:error`
             SET `transponder` = '%s',
                 `tier_id` = %d,
                 `adr_id` = %d,
                 `error` = '%s',
                 `timestamp` = NOW()",
            mysql_real_escape_string($row['transponder']),
            $row['id'],
            $row['adr_id'],
            mysql_real_escape_string($errormsg)
        );
        mysql_query($sql, $conn);
    }
}


/* =========================================================
 * BLOCK 3f: AenderungTier (KORRIGIERT – SOAP-SICHER)
 * ========================================================= */

if ($TierAenderung !== null) {

    $sent++;

    $response_time = microtime(true);
    $response = $soapclient->AenderungTier(
        $row['transponder'],
        preg_match('/^[0-9]{15}$/', $row['transponder']) ? 1 : 2,
        $row['reg_id'],
        $TierAenderung
    );

    hdb_debug('AenderungTier RESPONSE', $response, $soapclient);

if ($response->Status != 0) {
    hdb_debug('HDB FEHLER – AenderungTier', $response, $soapclient);
}

    
    $response_time = microtime(true) - $response_time;

    logfile($row['transponder'], "AenderungTier (Status: ".$response->Status.")");

    $errormsg = null;

    if ($response->Status == 0) {

        if (isset($transponder)) {
            print "AenderungTier (ChipCode \"".$row['transponder']."\"): OK\n";
            print_r($TierAenderung);
        }

        $success++;

        $sql = sprintf(
            "DELETE FROM `i_heimtierdb_at:error`
             WHERE `transponder` = '%s'",
            mysql_real_escape_string($row['transponder'])
        );
        mysql_query($sql, $conn);

    }
    elseif ($response->Status == 202) {

        $errormsg =
            "Fehler bei AenderungTier (ChipCode \"".$row['transponder']."\"):\n".
            print_r($response, true).
            print_r($TierAenderung, true);

        print $errormsg;

        if (isset($TierAenderung->Todesdatum) && !isset($data['Tier']['Todesdatum'])) {

            $data['Tier']['Todesdatum'] = $TierAenderung->Todesdatum;

            $sql = sprintf(
                "UPDATE `i_heimtierdb_at`
                 SET `data` = '%s',
                     `hdb_time` = %.3f
                 WHERE `reg_id` = %.0f",
                mysql_real_escape_string(serialize($data)),
                microtime(true) - $hdb_time,
                $row['reg_id']
            );
            mysql_query($sql, $conn);

        } else {

            $sql = sprintf(
                "INSERT INTO `i_heimtierdb_at:error`
                 SET `transponder` = '%s',
                     `reg_id` = %.0f,
                     `tier_id` = %d,
                     `adr_id` = %d,
                     `data` = '%s',
                     `error` = '%s',
                     `timestamp` = NOW()
                 ON DUPLICATE KEY UPDATE
                     `data` = '%s',
                     `error` = '%s',
                     `timestamp` = NOW()",
                mysql_real_escape_string($row['transponder']),
                $row['reg_id'],
                $row['id'],
                $row['adr_id'],
                mysql_real_escape_string(serialize($data)),
                mysql_real_escape_string($errormsg),
                mysql_real_escape_string(serialize($data)),
                mysql_real_escape_string($errormsg)
            );
            mysql_query($sql, $conn);
        }

    }
    else {

        $errormsg =
            "Fehler bei AenderungTier (ChipCode \"".$row['transponder']."\"):\n".
            print_r($response, true).
            print_r($TierAenderung, true);

        print $errormsg;

        $sql = sprintf(
            "INSERT INTO `i_heimtierdb_at:error`
             SET `transponder` = '%s',
                 `reg_id` = %.0f,
                 `tier_id` = %d,
                 `adr_id` = %d,
                 `data` = '%s',
                 `error` = '%s',
                 `timestamp` = NOW()
             ON DUPLICATE KEY UPDATE
                 `data` = '%s',
                 `error` = '%s',
                 `timestamp` = NOW()",
            mysql_real_escape_string($row['transponder']),
            $row['reg_id'],
            $row['id'],
            $row['adr_id'],
            mysql_real_escape_string(serialize($data)),
            mysql_real_escape_string($errormsg),
            mysql_real_escape_string(serialize($data)),
            mysql_real_escape_string($errormsg)
        );
        mysql_query($sql, $conn);
    }

    /* History */
    $sql = sprintf(
        "INSERT INTO `i_heimtierdb_at:history`
         SET `transponder` = '%s',
             `reg_id` = %.0f,
             `tier_id` = %d,
             `adr_id` = %d,
             `data` = '%s',
             `type` = 'AenderungTier',
             `parameter` = '%s',
             `hdb_time` = %.3f,
             `status` = %d,
             `response` = '%s',
             `error` = %s,
             `timestamp` = NOW()",
        mysql_real_escape_string($row['transponder']),
        $row['reg_id'],
        $row['id'],
        $row['adr_id'],
        mysql_real_escape_string(serialize($data)),
        mysql_real_escape_string(serialize(array(
            $row['transponder'],
            preg_match('/^[0-9]{15}$/', $row['transponder']) ? 1 : 2,
            $row['reg_id'],
            $TierAenderung
        ))),
        $response_time,
        $response->Status,
        mysql_real_escape_string(serialize($response)),
        isset($errormsg) ? "'".mysql_real_escape_string($errormsg)."'" : "NULL"
    );
    mysql_query($sql, $conn);
}


/* =========================================================
 * BLOCK 3g: AenderungHE (KORRIGIERT – SOAP-SICHER)
 * ========================================================= */

if ($HEAenderung !== null) {

    $sent++;

    if (!isset($HEAenderung->Typ)) {
        $HEAenderung->Typ = $halter['HE']['Typ']; // Pflichtfeld
    }

    $response_time = microtime(true);
    $response = $soapclient->AenderungHE(
        $row['transponder'],
        preg_match('/^[0-9]{15}$/', $row['transponder']) ? 1 : 2,
        $row['reg_id'],
        $HEAenderung
    );

    hdb_debug('AenderungHE RESPONSE', $response, $soapclient);

if ($response->Status != 0) {
    hdb_debug('HDB FEHLER – AenderungHE', $response, $soapclient);
}
    $response_time = microtime(true) - $response_time;

    logfile($row['transponder'], "AenderungHE (Status: ".$response->Status.")");

    $errormsg = null;

    if ($response->Status == 0) {

        if (isset($transponder)) {
            print "AenderungHE (ChipCode \"".$row['transponder']."\"): OK\n";
            print_r($HEAenderung);
        }

        $success++;

        $sql = sprintf(
            "DELETE FROM `i_heimtierdb_at:error`
             WHERE `transponder` = '%s'",
            mysql_real_escape_string($row['transponder'])
        );
        mysql_query($sql, $conn);

    } else {

        $errormsg =
            "Fehler bei AenderungHE (ChipCode \"".$row['transponder']."\"):\n".
            print_r($response, true).
            print_r($HEAenderung, true);

        print $errormsg;

        $sql = sprintf(
            "INSERT INTO `i_heimtierdb_at:error`
             SET `transponder` = '%s',
                 `reg_id` = %.0f,
                 `tier_id` = %d,
                 `adr_id` = %d,
                 `data` = '%s',
                 `error` = '%s',
                 `timestamp` = NOW()
             ON DUPLICATE KEY UPDATE
                 `data` = '%s',
                 `error` = '%s',
                 `timestamp` = NOW()",
            mysql_real_escape_string($row['transponder']),
            $row['reg_id'],
            $row['id'],
            $row['adr_id'],
            mysql_real_escape_string(serialize($data)),
            mysql_real_escape_string($errormsg),
            mysql_real_escape_string(serialize($data)),
            mysql_real_escape_string($errormsg)
        );
        mysql_query($sql, $conn);
    }

    /* History */
    $sql = sprintf(
        "INSERT INTO `i_heimtierdb_at:history`
         SET `transponder` = '%s',
             `reg_id` = %.0f,
             `tier_id` = %d,
             `adr_id` = %d,
             `data` = '%s',
             `type` = 'AenderungHE',
             `parameter` = '%s',
             `hdb_time` = %.3f,
             `status` = %d,
             `response` = '%s',
             `error` = %s,
             `timestamp` = NOW()",
        mysql_real_escape_string($row['transponder']),
        $row['reg_id'],
        $row['id'],
        $row['adr_id'],
        mysql_real_escape_string(serialize($data)),
        mysql_real_escape_string(serialize(array(
            $row['transponder'],
            preg_match('/^[0-9]{15}$/', $row['transponder']) ? 1 : 2,
            $row['reg_id'],
            $HEAenderung
        ))),
        $response_time,
        $response->Status,
        mysql_real_escape_string(serialize($response)),
        isset($errormsg) ? "'".mysql_real_escape_string($errormsg)."'" : "NULL"
    );
    mysql_query($sql, $conn);
}

/* =========================================================
 * ENDE BLOCK 3g
 * ========================================================= */

/* =========================================================
 * ENDE BLOCK 3f
 * ========================================================= */

/* =========================================================
 * ENDE BLOCK 3e
 * ========================================================= */


/* =========================================================
 * ENDE BLOCK 3d – danach geht es normal weiter (Block 4)
 * ========================================================= */


/* =========================================================
 * ENDE BLOCK 3b – danach folgt Block 3c / Block 4
 * ========================================================= */

/* =========================================================
 * AB HIER FOLGT BLOCK 4 (SOAP-CALLS)
 * ========================================================= */



    /* =========================================================
 * BLOCK 3: TIER & HALTER AUFBAU (SOAP-KONFORM)
 * ========================================================= */


/* =========================================================
 * AB HIER: BLOCK 4 (SOAP-CALLS)
 * ========================================================= */


/* =========================================================
 * BLOCK 4: ERSTMELDUNG (SOAP)
 * ========================================================= */

else {

 /* SOAP-Request korrekt aufbauen */
$params = new stdClass();
$params->Tier              = $tier;
$params->HalterEigentuemer = $halter;

$response_time = microtime(true);

try {
    $response = $soapclient->__soapCall('Erstmeldung', array($params));
    $response_time = microtime(true) - $response_time;
} catch (SoapFault $e) {
    $response_time = microtime(true) - $response_time;

    $errormsg = "SOAP Fehler bei Erstmeldung (ChipCode \"".$row['transponder']."\"):\n".$e->getMessage();
    print $errormsg;

    $sql = sprintf(
        "INSERT INTO `i_heimtierdb_at:error`
         SET `transponder` = '%s',
             `tier_id` = %d,
             `adr_id` = %d,
             `error` = '%s',
             `timestamp` = NOW()
         ON DUPLICATE KEY UPDATE
             `error` = '%s',
             `timestamp` = NOW()",
        mysql_real_escape_string($row['transponder']),
        $row['id'],
        $row['adr_id'],
        mysql_real_escape_string($errormsg),
        mysql_real_escape_string($errormsg)
    );
    mysql_query($sql, $conn);
    continue;
}

/* DEBUG */
hdb_debug('Erstmeldung RESPONSE', $response, $soapclient);

/* HDB-Fehler sichtbar machen */
if (!isset($response->Status) || $response->Status != 0) {
    hdb_debug('HDB FEHLER – Erstmeldung fehlgeschlagen', $response, $soapclient);
}

logfile($row['transponder'], "Erstmeldung (Status: ".$response->Status.")");


    

    $errormsg = null;

    if ($response->Status == 0) {

        if (isset($transponder)) {
            print "Erstmeldung (ChipCode \"".$row['transponder']."\"): OK\n";
        }

        $sql = sprintf(
            "INSERT INTO `i_heimtierdb_at`
             SET `reg_id` = %.0f,
                 `tier_id` = %d,
                 `adr_id` = %d,
                 `transponder` = '%s',
                 `data` = '%s',
                 `hdb_time` = %.3f,
                 `created` = NOW()",
            $response->Registrierungsnummer,
            $row['id'],
            $row['adr_id'],
            mysql_real_escape_string($row['transponder']),
            mysql_real_escape_string(serialize(array(
                'Tier'              => $tier,
                'HalterEigentuemer' => $halter
            ))),
            $response_time
        );
        mysql_query($sql, $conn);

        $count_insert++;

        $sql = sprintf(
            "DELETE FROM `i_heimtierdb_at:error`
             WHERE `transponder` = '%s'",
            mysql_real_escape_string($row['transponder'])
        );
        mysql_query($sql, $conn);

    }
    elseif ($response->Status == 200) {

        $errormsg = "ChipCode \"".$row['transponder']."\" existiert bereits (Fremdregistrierung)";
        print $errormsg;

        $sql = sprintf(
            "INSERT INTO `i_heimtierdb_at:error`
             SET `transponder` = '%s',
                 `tier_id` = %d,
                 `adr_id` = %d,
                 `error` = '%s',
                 `timestamp` = NOW()
             ON DUPLICATE KEY UPDATE
                 `error` = '%s',
                 `timestamp` = NOW()",
            mysql_real_escape_string($row['transponder']),
            $row['id'],
            $row['adr_id'],
            mysql_real_escape_string($errormsg),
            mysql_real_escape_string($errormsg)
        );
        mysql_query($sql, $conn);

    }
    else {

        $errormsg = "Fehler bei Erstmeldung (ChipCode \"".$row['transponder']."\"):\n".
                    print_r($response, true);

        print $errormsg;

        $sql = sprintf(
            "INSERT INTO `i_heimtierdb_at:error`
             SET `transponder` = '%s',
                 `tier_id` = %d,
                 `adr_id` = %d,
                 `error` = '%s',
                 `timestamp` = NOW()
             ON DUPLICATE KEY UPDATE
                 `error` = '%s',
                 `timestamp` = NOW()",
            mysql_real_escape_string($row['transponder']),
            $row['id'],
            $row['adr_id'],
            mysql_real_escape_string($errormsg),
            mysql_real_escape_string($errormsg)
        );
        mysql_query($sql, $conn);
    }

    /* History */
    $sql = sprintf(
        "INSERT INTO `i_heimtierdb_at:history`
         SET `transponder` = '%s',
             `tier_id` = %d,
             `adr_id` = %d,
             `data` = '%s',
             `type` = 'Erstmeldung',
             `parameter` = '%s',
             `hdb_time` = %.3f,
             `status` = %d,
             `response` = '%s',
             `error` = %s,
             `timestamp` = NOW()",
        mysql_real_escape_string($row['transponder']),
        $row['id'],
        $row['adr_id'],
        mysql_real_escape_string(serialize(array(
            'Tier'              => $tier,
            'HalterEigentuemer' => $halter
        ))),
        mysql_real_escape_string(serialize($params)),
        $response_time,
        $response->Status,
        mysql_real_escape_string(serialize($response)),
        isset($errormsg) ? "'".mysql_real_escape_string($errormsg)."'" : "NULL"
    );
    mysql_query($sql, $conn);
}

/* =========================================================
 * END WHILE
 * ========================================================= */

flush();
}
mysql_free_result($result);

