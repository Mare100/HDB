<?php
  /* PHP settings */
  ini_set("error_reporting", E_ALL & ~E_NOTICE);
  ini_set("display_errors", 1);

  $transponder = null;
  if (isset($_GET['transponder'])) {
      if (!preg_match('/^[A-Z0-9]+$/', $_GET['transponder'])) die("Die Transpondernummer \"".$_GET['transponder']."\" ist ung&uuml;ltig!");
      $transponder = $_GET['transponder'];
  }

  /* Declaration */
  $CatGeschlechtTyp = array(
    'Herr' => '1', //Herr
    'Frau' => '3', //Frau
  );
  $CatGeschlechtTypTier = array(
    '1' => '1', //M
    '2' => '3', //W
    '4' => '2', //MK
    '5' => '4'  //WK
  );
  $CatLand = array(
    'AF' => '1',
    'EG' => '2',
    'AL' => '3',
    'DZ' => '4',
    'AD' => '5',
    'AO' => '6',
    'AI' => '7',
    'AQ' => '8',
    'AG' => '9',
    'GQ' => '10',
    'AR' => '11',
    'AM' => '12',
    'AW' => '13',
    'AZ' => '14',
    'ET' => '15',
    'AU' => '16',
    'BS' => '17',
    'BH' => '18',
    'BD' => '19',
    'BB' => '20',
    'BY' => '21',
    'BE' => '22', 'B' => '22',
    'BZ' => '23',
    'BJ' => '24',
    'BM' => '25',
    'BT' => '26',
    'BO' => '27',
    'BA' => '28',
    'BW' => '29',
    'BV' => '30',
    'BR' => '31',
    'IO' => '32',
    'BN' => '33',
    'BG' => '34',
    'BF' => '35',
    'BI' => '36',
    'CT' => '37',
    'XC' => '38',
    'CL' => '39',
    'CN' => '40',
    'CK' => '41',
    'CR' => '42',
    'DK' => '43',
    'DE' => '44', 'D' => '44',
    'DM' => '45',
    'DO' => '46',
    'DJ' => '47',
    'EC' => '48',
    'SV' => '49',
    'CI' => '50',
    'ER' => '51',
    'EE' => '52',
    'FK' => '53',
    'FO' => '54',
    'FJ' => '55',
    'FI' => '56',
    'FR' => '57', 'F' => '57',
    'GF' => '58',
    'PF' => '59',
    'TF' => '60',
    'FQ' => '61',
    'GA' => '62',
    'GM' => '63',
    'GE' => '64',
    'GH' => '65',
    'GI' => '66',
    'GD' => '67',
    'GR' => '68',
    'GL' => '69',
    'GB' => '70',
    'GP' => '71',
    'GU' => '72',
    'GT' => '73',
    'GG' => '74',
    'GN' => '75',
    'GW' => '76',
    'GY' => '77',
    'HT' => '78',
    'HM' => '79',
    'HN' => '80',
    'HK' => '81',
    'IN' => '82',
    'ID' => '83',
    'IQ' => '84',
    'IR' => '85',
    'IE' => '86',
    'IS' => '87',
    'IL' => '88',
    'IT' => '89', 'I' => '89',
    'JM' => '90',
    'JP' => '91',
    'YE' => '92',
    'JE' => '93',
    'JT' => '94',
    'JO' => '95',
    'VG' => '96',
    'VI' => '97',
    'KY' => '98',
    'KH' => '99',
    'CM' => '100',
    'CA' => '101',
    'IC' => '102',
    'CV' => '103',
    'KZ' => '104',
    'QA' => '105',
    'KE' => '106',
    'KG' => '107',
    'KI' => '108',
    'CC' => '109',
    'CO' => '110',
    'KM' => '111',
    'CG' => '112',
    'CD' => '113',
    'KP' => '114',
    'KR' => '115',
    'HR' => '116',
    'CU' => '117',
    'KW' => '118',
    'LA' => '119',
    'LS' => '120',
    'LV' => '121',
    'LB' => '122',
    'LR' => '123',
    'LY' => '124',
    'LI' => '125',
    'LT' => '126',
    'LU' => '127', 'L' => '127',
    'MO' => '128',
    'MG' => '129',
    'MW' => '130',
    'MY' => '131',
    'MV' => '132',
    'ML' => '133',
    'MT' => '134',
    'IM' => '135',
    'MA' => '136',
    'MH' => '137',
    'MQ' => '138',
    'MR' => '139',
    'MU' => '140',
    'YT' => '141',
    'MK' => '142',
    'XL' => '143',
    'MX' => '144',
    'MI' => '145',
    'FM' => '146',
    'MD' => '147',
    'MC' => '148',
    'MN' => '149',
    'ME' => '150',
    'MS' => '151',
    'MZ' => '152',
    'MM' => '153',
    'NA' => '154',
    'NR' => '155',
    'NP' => '156',
    'NC' => '157',
    'NZ' => '158',
    'NI' => '159',
    'NL' => '160',
    'AN' => '161',
    'NE' => '162',
    'NG' => '163',
    'NU' => '164',
    'MP' => '165',
    'NF' => '166',
    'NO' => '167', 'N' => '167',
    'OM' => '168',
    'PK' => '169',
    'PS' => '170',
    'PW' => '171',
    'PA' => '172',
    'PZ' => '173',
    'PG' => '174',
    'PY' => '175',
    'PU' => '176',
    'PE' => '177',
    'PH' => '178',
    'PN' => '179',
    'PL' => '180',
    'PT' => '181', 'P' => '181',
    'PR' => '182',
    'RE' => '183',
    'RW' => '184',
    'RO' => '185',
    'RU' => '186',
    'SB' => '187',
    'ZM' => '188',
    'AS' => '189',
    'WS' => '190',
    'SM' => '191',
    'ST' => '192',
    'SA' => '193',
    'SE' => '194', 'S' => '194',
    'CH' => '195',
    'SN' => '196',
    'RS' => '197',
    'SC' => '198',
    'SL' => '199',
    'ZW' => '200',
    'SG' => '201',
    'SK' => '202',
    'SI' => '203',
    'SO' => '204',
    'ES' => '205', 'E' => '205',
    'SJ' => '206',
    'LK' => '207',
    'SH' => '208',
    'KN' => '209',
    'LC' => '210',
    'PM' => '211',
    'VC' => '212',
    'ZA' => '213',
    'SD' => '214',
    'GS' => '215',
    'SR' => '216',
    'SZ' => '217',
    'SY' => '218',
    'TJ' => '219',
    'TW' => '220',
    'TZ' => '221',
    'TH' => '222',
    'TL' => '223',
    'TG' => '224',
    'TK' => '225',
    'TO' => '226',
    'TT' => '227',
    'TD' => '228',
    'CZ' => '229',
    'TN' => '230',
    'TR' => '231',
    'TM' => '232',
    'TC' => '233',
    'TV' => '234',
    'UG' => '235',
    'UA' => '236',
    'HU' => '237', 'H' => '237',
    'UY' => '238',
    'UM' => '239',
    'UZ' => '240',
    'VU' => '241',
    'VA' => '242',
    'VE' => '243',
    'AE' => '244',
    'US' => '245', 'USA' => '245',
    'VN' => '246',
    'WK' => '247',
    'WF' => '248',
    'CX' => '249',
    'EH' => '250',
    'CF' => '251',
    'CY' => '252',
    'AT' => '253', 'A' => '253'
  );

  $CatAusweisTyp = array(
    'F hrerschein' => '1',
    'Personalausweis' => '2',
    'Reisepass' => '3',
    'Sch lerausweis' => '4',
    'Studentenausweis' => '5',
    'Identit tskarte' => '6',
    'Identit tsausweis' => '6',
    'Dienstausweis' => '7',
    'Behindertenpass' => '8'
  );

  $CatTyp = array(
    'H' => '1',
    'E' => '2',
    'HE' => '3'
  );

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
      file_put_contents('/var/log/tierreg/hdb.log', "[".date("d-M-Y H:i:s")."] ".$transponder." ".$text."\n", FILE_APPEND | LOCK_EX);
  }

  /* Connect to the database */
  $conn = mysql_connect ("localhost", "ifta", "rv:d4S5FSxJWnNJp");
  /* Select database */
  mysql_select_db("db00000000001", $conn);
?>
<html>
<head>
</head>
<body>
<pre>
<?php
  /* Prepare sql statement */
$sql = sprintf("SELECT ".
                 "SQL_CALC_FOUND_ROWS ".
                 "`i_tiere`.`id`,".
                 "`i_tiere`.`adr_id`,".
                 "`i_tiere`.`transponder`,".
                 "`i_tiere`.`tname`,".
                 "IFNULL(`i_tierart`.`de_lang`,`i_tiere`.`art`) AS `art`,".
                 "`i_tiere`.`art_id`,".
                 "IFNULL(`i_tierrasse`.`de_lang`,`i_tiere`.`rasse`) AS `rasse`,".
                 "`i_tierrasse`.`hdb_id` AS `HDB_id`,".   /* <-- HIER DER FIX */
                 "`i_tiere`.`geschlecht_id`,".
                 "`i_tiere`.`geburt`,".
                 "`i_tiere`.`geburtsland`,".
                 "`i_tiere`.`htausweis`,".
                 "`i_tiere`.`besitz`,".
                 "`i_tiere`.`status`,".
                 "`i_tiere`.`status_date`,".
                 "`i_tiere`.`hdb_uebernahme_reg_id`,".
                 "IF(`i_tiere`.`regdatum` IS NULL OR `i_tiere`.`regdatum` = '0000-00-00',`i_tiere`.`awdatum`,`i_tiere`.`regdatum`) AS `regdatum`,".
                 "`i_adressen`.`anrede`,".
                 "`i_adressen`.`titel`,".
                 "`i_adressen`.`vorname`,".
                 "`i_adressen`.`name`,".
                 "`i_adressen`.`gebhalter`,".
                 "`i_adressen`.`plz`,".
                 "`i_adressen`.`land`,".
                 "`i_adressen`.`ort`,".
                 "`i_adressen`.`strasse`,".
                 "IFNULL(`i_adressen`.`telefon_priv`,`i_adressen`.`telefon_ges`) AS `telefon`,".
                 "`i_adressen`.`telefon_mobil`,".
                 "`i_adressen`.`fax`,".
                 "`i_adressen`.`email`,".
                 "`i_adressen`.`ausweisart`,".
                 "`i_adressen`.`prausweis`,".
                 "`i_heimtierdb_at`.`reg_id`,".
                 "`i_heimtierdb_at`.`data`".
               " FROM ".
                 "`i_tiere`".
                 " LEFT JOIN `i_tierrasse` ON `i_tiere`.`rasse_id` = `i_tierrasse`.`id`".
                 " LEFT JOIN `i_tierart` ON `i_tiere`.`art_id` = `i_tierart`.`id`".
                 " LEFT JOIN `i_heimtierdb_at` ON `i_tiere`.`id` = `i_heimtierdb_at`.`tier_id` AND `i_tiere`.`adr_id` = `i_heimtierdb_at`.`adr_id`,".
                 "`i_adressen`".
               " WHERE ".
                 "`i_tiere`.`art_id` IN(1,2) AND ".
                 "`i_tiere`.`adr_id` = `i_adressen`.`id` AND ".
                 "`i_tiere`.`beswechsel` IS NULL AND ".
                 "`i_adressen`.`land` = 'A'".
                 (isset($transponder) ? " AND `i_tiere`.`transponder` = '".mysql_real_escape_string($transponder)."'" : " AND `i_tiere`.`transponder` != ALL(SELECT `transponder` FROM `i_heimtierdb_at:error`)").
               " ORDER BY ".
                 "`i_tiere`.`id`");
  /* Execute sql statement */
  $result = mysql_query($sql, $conn) or die("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")");
//print $sql."\n";
//exit;
  if (isset($transponder) && !mysql_num_rows($result)) {
      die("Die Transpondernummer \"".$transponder."\" lieferte keinen Datensatz f&uuml;r den Export zur&uuml;ck!");
  }

  /* Fetch max rows returned by sql (without checks) */
  $sql = sprintf("SELECT FOUND_ROWS()");
  $result2 = mysql_query($sql) or die("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")");
  $maxcount = mysql_result($result2, 0);
  mysql_free_result($result2);

  //$soapclient = new SoapClient("hdb_hunde_20090915.wsdl", array('location' => 'https://ahdb.ehealth.gv.at/hdbservice/HDB_H_Service.asmx', 'style' => SOAP_RPC, 'local_cert' => 'cert.pem', 'trace' => 1));
	$context = stream_context_create(array(
	    'ssl' => array(
	        'ciphers' => 'SHA1'
	    )
	));
  $soapclient = new SoapClient("hdb_hunde_20090915.wsdl", array('location' => 'https://hdb.ehealth.gv.at/hdbservice/HDB_H_Service.asmx', 'style' => SOAP_RPC, 'local_cert' => 'cert.pem', 'trace' => 1, 'stream_context' => $context));

  /* loop through the database rows */
  $count_insert = 0;
  $count_update = 0;
  $error = '';
  while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
      /* Check the database variables */

      if (!isset($row['transponder']) || !preg_match('/^[A-Z0-9]+$/', $row['transponder'])) continue;
      if (!isset($row['geburtsland']) || !isset($CatLand[ $row['geburtsland'] ])) { if (isset($transponder)) $error .= "Geburtsland bei Transponder \"".$row['transponder']."\" fehlerhaft oder nicht gesetzt: ".$row['geburtsland']."\n"; else continue; }
      if (!isset($row['rasse']) || trim($row['rasse']) == '') { if (isset($transponder)) $error .= "Rasse bei Transponder \"".$row['transponder']."\" fehlerhaft oder nicht gesetzt: ".$row['rasse']."\n"; else continue; }
      if (!isset($row['geschlecht_id']) || !isset($CatGeschlechtTypTier[ $row['geschlecht_id'] ])) { if (isset($transponder)) $error .= "Geschlecht (geschlecht_id) bei Transponder \"".$row['transponder']."\" fehlerhaft oder nicht gesetzt: ".$row['geschlecht_id']."\n"; else continue; }

      if (preg_match('/^([0-9]{1,2})\.([0-9]{1,2})\.((19|20)?[0-9]{2})?$/', $row['geburt'], $tmp)) {
          $row['geburt'] = date("Y-m-d", mktime(0,0,0, $tmp[2],$tmp[1], strlen($tmp[3]) == 2 ? ($tmp[3] <= date("y") ? 2000+$tmp[3] : 1900+$tmp[3]) : (strlen($tmp[3]) == 0 ? date("Y") : $tmp[3])));
      }
      elseif (preg_match('/^([0-9]{1,2})\.((19|20)?[0-9]{2})$/', $row['geburt'], $tmp)) {
          $row['geburt'] = date("Y-m-d", mktime(0,0,0, $tmp[1],1, strlen($tmp[2]) == 2 ? ($tmp[2] <= date("y") ? 2000+$tmp[2] : 1900+$tmp[2]) : (strlen($tmp[2]) == 0 ? date("Y") : $tmp[2])));
      }
      elseif (preg_match('/^((19|20)?[0-9]{2})$/', $row['geburt'], $tmp)) {
          $row['geburt'] = date("Y-m-d", mktime(0,0,0, 1, 1, strlen($tmp[1]) == 2 ? ($tmp[1] <= date("y") ? 2000+$tmp[1] : 1900+$tmp[1]) : (strlen($tmp[1]) == 0 ? date("Y") : $tmp[1])));
      }
      else {
          if (isset($transponder)) $error .= "Geburtstag Tier (geburt) bei Transponder \"".$row['transponder']."\" fehlerhaft oder nicht gesetzt: ".$row['geburt']."\n";
          else continue;
      }

      if (!isset($row['besitz']) || $row['besitz'] == '') $row['besitz'] = date("Y-m-d", strtotime($row['regdatum']));
      if (preg_match('/^([0-9]{1,2})\.([0-9]{1,2})\.((19|20)?[0-9]{2})?$/', $row['besitz'], $tmp)) {
          $row['besitz'] = date("Y-m-d", mktime(0,0,0, $tmp[2],$tmp[1], strlen($tmp[3]) == 2 ? ($tmp[3] <= date("y") ? 2000+$tmp[3] : 1900+$tmp[3]) : (strlen($tmp[3]) == 0 ? date("Y") : $tmp[3])));
      }
      elseif (preg_match('/^([0-9]{1,2})\.((19|20)?[0-9]{2})$/', $row['besitz'], $tmp)) {
          $row['besitz'] = date("Y-m-d", mktime(0,0,0, $tmp[1],1, strlen($tmp[2]) == 2 ? ($tmp[2] <= date("y") ? 2000+$tmp[2] : 1900+$tmp[2]) : (strlen($tmp[2]) == 0 ? date("Y") : $tmp[2])));
      }
      elseif (preg_match('/^((19|20)?[0-9]{2})$/', $row['besitz'], $tmp)) {
          $row['besitz'] = date("Y-m-d", mktime(0,0,0, 1, 1, strlen($tmp[1]) == 2 ? ($tmp[1] <= date("y") ? 2000+$tmp[1] : 1900+$tmp[1]) : (strlen($tmp[1]) == 0 ? date("Y") : $tmp[1])));
      }
      else {
          $row['besitz'] = date("Y-m-d", strtotime($row['regdatum']));
      }

      if (preg_match('/^([0-9]{1,2})\.([0-9]{1,2})\.((19|20)?[0-9]{2})?$/', $row['gebhalter'], $tmp)) {
          $row['gebhalter'] = date("Y-m-d", mktime(0,0,0, $tmp[2],$tmp[1], strlen($tmp[3]) == 2 ? ($tmp[3] <= date("y") ? 2000+$tmp[3] : 1900+$tmp[3]) : (strlen($tmp[3]) == 0 ? date("Y") : $tmp[3])));
      }
      else {
          if (isset($transponder)) $error .= "Geburtstag Halter bei Transponder \"".$row['transponder']."\" fehlerhaft oder nicht gesetzt!\n";
          else continue;
      }

      if (!isset($row['ausweisart']) || trim($row['ausweisart']) == '') { if (isset($transponder)) $error .= "Ausweisart bei Transponder \"".$row['transponder']."\" fehlerhaft oder nicht gesetzt: ".$row['ausweisart']."\n"; else continue; }
      if (!isset($row['prausweis']) || trim($row['prausweis']) == '') { if (isset($transponder)) $error .= "Ausweisnummer (prausweis) bei Transponder \"".$row['transponder']."\" fehlerhaft oder nicht gesetzt: ".$row['prausweis']."\n"; else continue; }
      if (!isset($row['ausweisart']) || !isset($CatAusweisTyp[ $row['ausweisart'] ])) { if (isset($transponder)) $error .= "Ausweisart bei Transponder \"".$row['transponder']."\" nicht definiert: ".$row['ausweisart']."\n"; else continue; }
      if (!isset($row['vorname']) || trim($row['vorname']) == '') { if (isset($transponder)) $error .= "Vorname bei Transponder \"".$row['transponder']."\" fehlerhaft oder nicht gesetzt: ".$row['vorname']."\n"; else continue; }
      if (!isset($row['name']) || trim($row['name']) == '') { if (isset($transponder)) $error .= "Nachname (name) bei Transponder \"".$row['transponder']."\" fehlerhaft oder nicht gesetzt: ".$row['name']."\n"; else continue; }
      if (!isset($row['anrede']) || !isset($CatGeschlechtTyp[ $row['anrede'] ])) { if (isset($transponder)) $error .= "Anrede bei Transponder \"".$row['transponder']."\" fehlerhaft oder nicht gesetzt: ".$row['anrede']."\n"; else continue; }
      if (!isset($row['plz']) || trim($row['plz']) == '') { if (isset($transponder)) $error .= "PLZ bei Transponder \"".$row['transponder']."\" fehlerhaft oder nicht gesetzt: ".$row['plz']."\n"; else continue; }
      if (!isset($row['ort']) || trim($row['ort']) == '') { if (isset($transponder)) $error .= "Ort bei Transponder \"".$row['transponder']."\" fehlerhaft oder nicht gesetzt: ".$row['ort']."\n"; else continue; }
      if (!isset($row['strasse']) || trim(preg_replace('/^([^0-9]+).+$/', '$1', $row['strasse'])) == '') { if (isset($transponder)) $error .= "Strasse bei Transponder \"".$row['transponder']."\" fehlerhaft oder nicht gesetzt: ".$row['strasse']."\n"; else continue; }
      if (trim(preg_replace('/^[^0-9]+(.+)$/', '$1', $row['strasse'])) == '') { if (isset($transponder)) $error .= "Strasse bei Transponder \"".$row['transponder']."\" fehlerhaft oder nicht gesetzt: ".$row['strasse']."\n"; else continue; }

      // Output single transponder error
      if ($error != '') {
          print $error;
          continue;
      }

      /* Change variables to valid utf8 / xml */
      foreach ($row AS $key => $value) {
          if ($key == "data" || !isset($value)) continue;
          $row[$key] = utf8_encode(str_replace('&', '&amp;', trim($value)));
      }

      /* Erstelle Tier-Element */
      $tier = array();
      $tier['ChipCode'] = $row['transponder'];
      $tier['ChipTyp'] = preg_match('/^[0-9]{15}$/', $row['transponder']) ? 1 : 2;
      switch ($row['art_id']) {
          case 2: /* Katze */
            $tier['TierArt'] = 8; /* Katze */
            break;
          default:
            $tier['TierArt'] = 1; /* Hund */
      }
      if (isset($row['tname']) && trim($row['tname']) != '') $tier['Name'] = $row['tname'];
      $tier['TierRasse'] = $row['HDB_id'];
      $tier['Geschlecht'] = $CatGeschlechtTypTier[ $row['geschlecht_id'] ];
      $tier['Geburtsdatum'] = $row['geburt'];
      $tier['Geburtsland'] = $CatLand[ $row['geburtsland'] ];
      if (isset($row['htausweis']) && trim($row['htausweis']) != '') $tier['HeimtierausweisNr'] = $row['htausweis'];

      if ($row['status'] == 1 && isset($row['status_date'])) {
          if (preg_match('/^([0-9]{1,2})\.([0-9]{1,2})\.((19|20)?[0-9]{2})?$/', $row['status_date'], $tmp)) {
              $tier['Todesdatum'] = date("Y-m-d", mktime(0,0,0, $tmp[2],$tmp[1], strlen($tmp[3]) == 2 ? ($tmp[3] <= date("y") ? 2000+$tmp[3] : 1900+$tmp[3]) : (strlen($tmp[3]) == 0 ? date("Y") : $tmp[3])));
          }
          elseif (preg_match('/^([0-9]{1,2})\.((19|20)?[0-9]{2})$/', $row['status_date'], $tmp)) {
              $tier['Todesdatum'] = date("Y-m-d", mktime(0,0,0, $tmp[1],1, strlen($tmp[2]) == 2 ? ($tmp[2] <= date("y") ? 2000+$tmp[2] : 1900+$tmp[2]) : (strlen($tmp[2]) == 0 ? date("Y") : $tmp[2])));
          }
          elseif (preg_match('/^((19|20)?[0-9]{2})$/', $row['status_date'], $tmp)) {
              $tier['Todesdatum'] = date("Y-m-d", mktime(0,0,0, 1, 1, strlen($tmp[1]) == 2 ? ($tmp[1] <= date("y") ? 2000+$tmp[1] : 1900+$tmp[1]) : (strlen($tmp[1]) == 0 ? date("Y") : $tmp[1])));
          }
      }


      /* Erstelle Halter-Element */
      $halter = array('HE' => array());

      $halter['HE']['Beginn'] = $row['besitz'];
      $halter['HE']['Typ'] = $CatTyp['HE'];

      $halter['HE']['Person'] = array();
      $halter['HE']['Person']['Typ'] = 'nat';
      if (isset($row['titel']) && $row['titel'] != '') $halter['HE']['Person']['Titel'] = $row['titel'];
      $halter['HE']['Person']['Vorname'] = $row['vorname'];
      $halter['HE']['Person']['Nachname'] = $row['name'];
      $halter['HE']['Person']['Geburtsdatum'] = $row['gebhalter'];
      $halter['HE']['Person']['Geschlecht'] = $CatGeschlechtTyp[ $row['anrede'] ];

      $halter['HE']['Adresse'] = array();
      $halter['HE']['Adresse']['Land'] = $CatLand[ $row['land'] ];
      $halter['HE']['Adresse']['PLZ'] = $row['plz'];
      $halter['HE']['Adresse']['Ort'] = $row['ort'];
      if (preg_match('/^([0-9]*[^0-9]+)\s*([0-9]+.*)$/u', $row['strasse'], $tmp)) {
          $halter['HE']['Adresse']['Str'] = trim($tmp[1]);
          $halter['HE']['Adresse']['Nr'] = trim($tmp[2]);
      }
      else {
          $halter['HE']['Adresse']['Str'] = $row['strasse'];
          $halter['HE']['Adresse']['Nr'] = null;
      }

      $halter['HE']['Kontaktdaten'] = array();
      if (isset($row['telefon']) && $row['telefon'] != '') $halter['HE']['Kontaktdaten']['Tel'] = $row['telefon'];
      if (isset($row['telefon_mobil']) && $row['telefon_mobil'] != '') $halter['HE']['Kontaktdaten']['Mobil'] = $row['telefon_mobil'];
      if (isset($row['fax']) && $row['fax'] != '') $halter['HE']['Kontaktdaten']['Fax'] = $row['fax'];
      if (isset($row['email']) && $row['email'] != '') $halter['HE']['Kontaktdaten']['eMail'] = $row['email'];

      $halter['HE']['Ausweis'] = array();
      $halter['HE']['Ausweis']['Typ'] = $CatAusweisTyp[ utf8_decode($row['ausweisart']) ];
      $halter['HE']['Ausweis']['Nummer'] = $row['prausweis'];
      $halter['HE']['Ausweis']['Land'] = $CatLand['AT'];

      $hdb_time = microtime(true);

      // Pruefe auf Halterwechsel
      $transfer_id = null;
      if (!isset($row['reg_id'])) {
          $sql = sprintf("SELECT `reg_id`,`data`,`transfer_id` FROM `i_heimtierdb_at` WHERE `tier_id` = %d AND `transponder` = '%s' AND (`transfer_id` IS NULL OR `transfer_id` = 0)", $row['id'], mysql_real_escape_string($row['transponder']));
          $result2 = mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");
          $transfer_row = mysql_fetch_array($result2, MYSQL_ASSOC);
          mysql_free_result($result2);
          if ($transfer_row !== false) {
              $transfer_data = unserialize($transfer_row['data']);
              if ($transfer_data !== false) {
                  $transfer_id = $transfer_row['reg_id'];
                  if (!isset($transfer_row['transfer_id'])) {
                      $response_time = microtime(true);
                      $response = $soapclient->Abgabe($row['transponder'], preg_match('/^[0-9]{15}$/', $row['transponder']) ? '1' : '2', $transfer_row['reg_id'], $transfer_data['HalterEigentuemer'], date("Y-m-d"), $halter['HE']['Person']['Vorname'], $halter['HE']['Person']['Nachname'], $halter['HE']['Ausweis']);
                      $response_time = microtime(true)-$response_time;

                      logfile($row['transponder'], "Abgabe (Status: ".$response->Status.")");

                      if ($response->Status == 0) {
                          if (isset($transponder)) print "Abgabe (ChipCode \"".$row['transponder']."\") an ".$halter['HE']['Person']['Vorname']." ".$halter['HE']['Person']['Nachname'].", ".$halter['HE']['Ausweis']['Nummer'].": OK\n";
                          $sql = sprintf("UPDATE `i_heimtierdb_at` SET `transfer_id` = 0 WHERE `reg_id` = %.0f AND `transfer_id` IS NULL", $transfer_id);
                          mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");

                          $sql = sprintf("INSERT INTO `i_heimtierdb_at:history` SET `transponder` = '%s', `reg_id` = %.0f, `tier_id` = %d, `adr_id` = %d, `data` = '%s', `type` = '%s', `parameter` = '%s', `hdb_time` = %.3f, `status` = %d, `response` = '%s', `timestamp` = NOW()",
                                         mysql_real_escape_string($row['transponder']), $transfer_id, $row['id'], $row['adr_id'], mysql_real_escape_string(serialize($data)), 'Abgabe', mysql_real_escape_string(serialize(array($row['transponder'], preg_match('/^[0-9]{15}$/', $row['transponder']) ? '1' : '2', $transfer_row['reg_id'], $transfer_data['HalterEigentuemer'], date("Y-m-d"), $halter['HE']['Person']['Vorname'], $halter['HE']['Person']['Nachname'], $halter['HE']['Ausweis']))), $response_time, $response->Status, mysql_real_escape_string(serialize($response)));
                          mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");
                      }
                      else{
                          $errormsg = "Fehler bei Abgabe (ChipCode \"".$row['transponder']."\"):\n".'$response = '.print_r($response,true);
                          print $errormsg;
                          $sql = sprintf("INSERT INTO `i_heimtierdb_at:error` SET `transponder` = '%s', `reg_id` = %.0f, `tier_id` = %d, `adr_id` = %d, `data` = '%s', `error` = '%s', `timestamp` = NOW() ON DUPLICATE KEY UPDATE `reg_id` = %.0f, `tier_id` = %d, `adr_id` = %d, `data` = '%s', `error` = '%s', `timestamp` = NOW()", mysql_real_escape_string($row['transponder']), $row['reg_id'], $row['id'], $row['adr_id'], mysql_real_escape_string(serialize($data)), mysql_real_escape_string($errormsg), $row['reg_id'], $row['id'], $row['adr_id'], mysql_real_escape_string(serialize($data)), mysql_real_escape_string($errormsg));
                          mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");

                          $sql = sprintf("INSERT INTO `i_heimtierdb_at:history` SET `transponder` = '%s', `reg_id` = %.0f, `tier_id` = %d, `adr_id` = %d, `data` = '%s', `type` = '%s', `parameter` = '%s', `hdb_time` = %.3f, `status` = %d, `response` = '%s', `error` = '%s', `timestamp` = NOW()",
                                         mysql_real_escape_string($row['transponder']), $transfer_id, $row['id'], $row['adr_id'], mysql_real_escape_string(serialize($data)), 'Abgabe', mysql_real_escape_string(serialize(array($row['transponder'], preg_match('/^[0-9]{15}$/', $row['transponder']) ? '1' : '2', $transfer_row['reg_id'], $transfer_data['HalterEigentuemer'], date("Y-m-d"), $halter['HE']['Person']['Vorname'], $halter['HE']['Person']['Nachname'], $halter['HE']['Ausweis']))), $response_time, $response->Status, mysql_real_escape_string(serialize($response)), mysql_real_escape_string($errormsg));
                          mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");
                          continue;
                      }
                  }
              }
          }
          elseif (isset($row['hdb_uebernahme_reg_id'])) {
              $transfer_id = $row['hdb_uebernahme_reg_id'];
          }
      }

      $data = isset($row['data']) ? unserialize($row['data']) : false;
      if (isset($row['reg_id']) && $data === false) {
          print "Cache fuer ChipCode \"".$row['transponder']."\" fehlerhaft, frage HDB-Daten ab...\n";
          $response = $soapclient->Abfrage($row['transponder'], preg_match('/^[0-9]{15}$/', $row['transponder']) ? '1' : '2', $row['reg_id']);
          logfile($row['transponder'], "Abfrage (Status: ".$response['Status']->Status.")");
          if ($response['Status']->Status == 0) {
              if (isset($transponder)) print "Abfrage (ChipCode \"".$row['transponder']."\"): OK\n";
              $data = objectToArray($response['Daten']);
              unset($data['HalterEigentuemer']['Adresse']['GKZ']);
              unset($data['HalterEigentuemer']['Adresse']['Gemeinde']);
              unset($data['HalterEigentuemer']['Adresse']['OKZ']);
              unset($data['HalterEigentuemer']['Adresse']['SKZ']);

              $sql = sprintf("UPDATE `i_heimtierdb_at` SET `data` = '%s', `hdb_time` = %.3f, `updated` = NOW() WHERE `reg_id` = %.0f", mysql_real_escape_string(serialize($data)), microtime(true)-$hdb_time, $row['reg_id']);
              mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");
          }
          else{
              $errormsg = "Fehler bei Abfrage (ChipCode \"".$row['transponder']."\"):\n".print_r($response,true);
              print $errormsg;
          }
      }

      if ($data !== false) {
          $TierAenderung = false;
          $HEAenderung = false;

          // Tier ist tot,  nderungen ignorieren
          //if (isset($data['Tier']['Todesdatum'])) continue;

          if ($data['Tier']['Name'] != $tier['Name']) $TierAenderung['Name'] = $tier['Name'];
          if ($data['Tier']['Geschlecht'] != $tier['Geschlecht']) $TierAenderung['Geschlecht'] = $tier['Geschlecht'];
          if ($data['Tier']['HeimtierausweisNr'] != $tier['HeimtierausweisNr']) $TierAenderung['HeimtierausweisNr'] = $tier['HeimtierausweisNr'];
		  if ($data['Tier']['TierRasse'] != $tier['TierRasse'])
    		$TierAenderung['TierRasse'] = $tier['TierRasse'];
          if (isset($tier['Todesdatum']) && $data['Tier']['Todesdatum'] != $tier['Todesdatum']) $TierAenderung['Todesdatum'] = $tier['Todesdatum'];

          if ($data['HalterEigentuemer']['Typ'] != $halter['HE']['Typ']) $HEAenderung['Typ'] = $halter['HE']['Typ'];
          if ($data['HalterEigentuemer']['Person']['Titel'] != $halter['HE']['Person']['Titel']) $HEAenderung['Titel'] = $halter['HE']['Person']['Titel'];
          if ($data['HalterEigentuemer']['Person']['Nachname'] != $halter['HE']['Person']['Nachname']) $HEAenderung['Familienname'] = $halter['HE']['Person']['Nachname'];

          if ($data['HalterEigentuemer']['Adresse']['Land'] != $halter['HE']['Adresse']['Land'] ||
              $data['HalterEigentuemer']['Adresse']['PLZ'] != $halter['HE']['Adresse']['PLZ'] ||
              $data['HalterEigentuemer']['Adresse']['Ort'] != $halter['HE']['Adresse']['Ort'] ||
              $data['HalterEigentuemer']['Adresse']['Str'] != $halter['HE']['Adresse']['Str'] ||
              $data['HalterEigentuemer']['Adresse']['Nr'] != $halter['HE']['Adresse']['Nr']) {
              $HEAenderung['Adresse']['Land'] = $halter['HE']['Adresse']['Land'];
              $HEAenderung['Adresse']['PLZ'] = $halter['HE']['Adresse']['PLZ'];
              $HEAenderung['Adresse']['Ort'] = $halter['HE']['Adresse']['Ort'];
              $HEAenderung['Adresse']['Str'] = $halter['HE']['Adresse']['Str'];
              $HEAenderung['Adresse']['Nr'] = $halter['HE']['Adresse']['Nr'];
          }

          if ($data['HalterEigentuemer']['Kontaktdaten']['Tel'] != $halter['HE']['Kontaktdaten']['Tel'] ||
              $data['HalterEigentuemer']['Kontaktdaten']['Mobil'] != $halter['HE']['Kontaktdaten']['Mobil'] ||
              $data['HalterEigentuemer']['Kontaktdaten']['Fax'] != $halter['HE']['Kontaktdaten']['Fax'] ||
              $data['HalterEigentuemer']['Kontaktdaten']['eMail'] != $halter['HE']['Kontaktdaten']['eMail']) {
              $HEAenderung['Kontaktdaten']['Tel'] = $halter['HE']['Kontaktdaten']['Tel'];
              $HEAenderung['Kontaktdaten']['Mobil'] = $halter['HE']['Kontaktdaten']['Mobil'];
              $HEAenderung['Kontaktdaten']['Fax'] = $halter['HE']['Kontaktdaten']['Fax'];
              $HEAenderung['Kontaktdaten']['eMail'] = $halter['HE']['Kontaktdaten']['eMail'];
          }

          $sent = 0;
          $success = 0;

          if ($TierAenderung !== false) {
              $sent ++;
              $response_time = microtime(true);
              $response = $soapclient->AenderungTier($row['transponder'], preg_match('/^[0-9]{15}$/', $row['transponder']) ? 1 : 2, $row['reg_id'], $TierAenderung);
              $response_time = microtime(true)-$response_time;

              logfile($row['transponder'], "AenderungTier (Status: ".$response->Status.")");

              $errormsg = null;
              if ($response->Status == 0) {
                  if (isset($transponder)) { print "AenderungTier (ChipCode \"".$row['transponder']."\"): OK\n"; print_r($TierAenderung); }
                  $success++;
                  $sql = sprintf("DELETE FROM `i_heimtierdb_at:error` WHERE `transponder` = '%s'", mysql_real_escape_string($row['transponder']));
                  mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");
              }
              elseif ($response->Status == 202) {
                  $errormsg = "Fehler bei AenderungTier (ChipCode \"".$row['transponder']."\"):\n".'$response = '.print_r($response,true).'$TierAenderung = '.print_r($TierAenderung,true);
                  print $errormsg;
                  if (isset($TierAenderung['Todesdatum']) && !isset($data['Tier']['Todesdatum'])) {
                      $data['Tier']['Todesdatum'] = $TierAenderung['Todesdatum'];
                      $sql = sprintf("UPDATE `i_heimtierdb_at` SET `data` = '%s', `hdb_time` = %.3f WHERE `reg_id` = %.0f", mysql_real_escape_string(serialize($data)), microtime(true)-$hdb_time, $row['reg_id']);
                      mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");
                  }
                  else {
                      $sql = sprintf("INSERT INTO `i_heimtierdb_at:error` SET `transponder` = '%s', `reg_id` = %.0f, `tier_id` = %d, `adr_id` = %d, `data` = '%s', `error` = '%s', `timestamp` = NOW() ON DUPLICATE KEY UPDATE `reg_id` = %.0f, `tier_id` = %d, `adr_id` = %d, `data` = '%s', `error` = '%s', `timestamp` = NOW()", mysql_real_escape_string($row['transponder']), $row['reg_id'], $row['id'], $row['adr_id'], mysql_real_escape_string(serialize($data)), mysql_real_escape_string($errormsg), $row['reg_id'], $row['id'], $row['adr_id'], mysql_real_escape_string(serialize($data)), mysql_real_escape_string($errormsg));
                      mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");
                  }
              }
              else {
                  $errormsg = "Fehler bei AenderungTier (ChipCode \"".$row['transponder']."\"):\n".'$response = '.print_r($response,true).'$TierAenderung = '.print_r($TierAenderung,true);
                  print $errormsg;
                  $sql = sprintf("INSERT INTO `i_heimtierdb_at:error` SET `transponder` = '%s', `reg_id` = %.0f, `tier_id` = %d, `adr_id` = %d, `data` = '%s', `error` = '%s', `timestamp` = NOW() ON DUPLICATE KEY UPDATE `reg_id` = %.0f, `tier_id` = %d, `adr_id` = %d, `data` = '%s', `error` = '%s', `timestamp` = NOW()", mysql_real_escape_string($row['transponder']), $row['reg_id'], $row['id'], $row['adr_id'], mysql_real_escape_string(serialize($data)), mysql_real_escape_string($errormsg), $row['reg_id'], $row['id'], $row['adr_id'], mysql_real_escape_string(serialize($data)), mysql_real_escape_string($errormsg));
                  mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");
              }

              $sql = sprintf("INSERT INTO `i_heimtierdb_at:history` SET `transponder` = '%s', `reg_id` = %.0f, `tier_id` = %d, `adr_id` = %d, `data` = '%s', `type` = '%s', `parameter` = '%s', `hdb_time` = %.3f, `status` = %d, `response` = '%s', `error` = %s, `timestamp` = NOW()",
                             mysql_real_escape_string($row['transponder']), $row['reg_id'], $row['id'], $row['adr_id'], mysql_real_escape_string(serialize($data)), 'AenderungTier', mysql_real_escape_string(serialize(array($row['transponder'], preg_match('/^[0-9]{15}$/', $row['transponder']) ? 1 : 2, $row['reg_id'], $TierAenderung))), $response_time, $response->Status, mysql_real_escape_string(serialize($response)), isset($errormsg) ? "'".mysql_real_escape_string($errormsg)."'" : "NULL");
              mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");
          }

          if ($HEAenderung !== false) {
			  if (empty($row['reg_id'])) continue;
              $sent ++;
              if (!isset($HEAenderung['Typ'])) $HEAenderung['Typ'] = $halter['HE']['Typ']; // Mandatory

              $response_time = microtime(true);
print_r($HEAenderung);
              $response = $soapclient->AenderungHE($row['transponder'], preg_match('/^[0-9]{15}$/', $row['transponder']) ? 1 : 2, $row['reg_id'], $HEAenderung);
              $response_time = microtime(true)-$response_time;

              logfile($row['transponder'], "AenderungHE (Status: ".$response->Status.")");

              $errormsg = null;
              if ($response->Status == 0) {
                  if (isset($transponder)) { print "AenderungHE (ChipCode \"".$row['transponder']."\"): OK\n"; print_r($HEAenderung); }
                  $success++;
                  $sql = sprintf("DELETE FROM `i_heimtierdb_at:error` WHERE `transponder` = '%s'", mysql_real_escape_string($row['transponder']));
                  mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");
              }
              else{
                  $errormsg = "Fehler bei AenderungHE (ChipCode \"".$row['transponder']."\"):\n".'$response = '.print_r($response,true).'$HEAenderung = '.print_r($HEAenderung,true);
                  print $errormsg;
                  $sql = sprintf("INSERT INTO `i_heimtierdb_at:error` SET `transponder` = '%s', `reg_id` = %.0f, `tier_id` = %d, `adr_id` = %d, `data` = '%s', `error` = '%s', `timestamp` = NOW() ON DUPLICATE KEY UPDATE `reg_id` = %.0f, `tier_id` = %d, `adr_id` = %d, `data` = '%s', `error` = '%s', `timestamp` = NOW()", mysql_real_escape_string($row['transponder']), $row['reg_id'], $row['id'], $row['adr_id'], mysql_real_escape_string(serialize($data)), mysql_real_escape_string($errormsg), $row['reg_id'], $row['id'], $row['adr_id'], mysql_real_escape_string(serialize($data)), mysql_real_escape_string($errormsg));
                  mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");
              }

              $sql = sprintf("INSERT INTO `i_heimtierdb_at:history` SET `transponder` = '%s', `reg_id` = %.0f, `tier_id` = %d, `adr_id` = %d, `data` = '%s', `type` = '%s', `parameter` = '%s', `hdb_time` = %.3f, `status` = %d, `response` = '%s', `error` = %s, `timestamp` = NOW()",
                             mysql_real_escape_string($row['transponder']), $row['reg_id'], $row['id'], $row['adr_id'], mysql_real_escape_string(serialize($data)), 'AenderungHE', mysql_real_escape_string(serialize(array($row['transponder'], preg_match('/^[0-9]{15}$/', $row['transponder']) ? 1 : 2, $row['reg_id'], $HEAenderung))), $response_time, $response->Status, mysql_real_escape_string(serialize($response)), isset($errormsg) ? "'".mysql_real_escape_string($errormsg)."'" : "NULL");
              mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");
          }

          if ($success) {
              if ($success == $sent) {
                  $sql = sprintf("UPDATE `i_heimtierdb_at` SET `data` = '%s', `hdb_time` = %.3f, `updated` = NOW() WHERE `reg_id` = %.0f", mysql_real_escape_string(serialize(array('Tier'=>$tier,'HalterEigentuemer'=>$halter['HE']))), microtime(true)-$hdb_time, $row['reg_id']);
                  mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");
              }
              else {
                  $response = $soapclient->Abfrage($row['transponder'], preg_match('/^[0-9]{15}$/', $row['transponder']) ? '1' : '2', $row['reg_id']);
                  logfile($row['transponder'], "Abfrage (Status: ".$response['Status']->Status.")");
                  if ($response['Status']->Status == 0) {
                      if (isset($transponder)) print "Abfrage (ChipCode \"".$row['transponder']."\"): OK\n";
                      $reg_data = objectToArray($response['Daten']);
                      unset($reg_data['HalterEigentuemer']['Adresse']['GKZ']);
                      unset($reg_data['HalterEigentuemer']['Adresse']['Gemeinde']);
                      unset($reg_data['HalterEigentuemer']['Adresse']['OKZ']);
                      unset($reg_data['HalterEigentuemer']['Adresse']['SKZ']);

                      $sql = sprintf("UPDATE `i_heimtierdb_at` SET `data` = '%s', `hdb_time` = %.3f, `updated` = NOW() WHERE `reg_id` = %.0f", mysql_real_escape_string(serialize($reg_data)), microtime(true)-$hdb_time, $row['reg_id']);
                      mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");
                  }
                  else{
                      $errormsg = "Fehler bei Abfrage (ChipCode \"".$row['transponder']."\"):\n".print_r($response,true);
                      print $errormsg;
                  }
              }

              /* Increase counter of updated rows */
              $count_update ++;
          }
      }
      elseif (isset($transfer_id)) {
          $response_time = microtime(true);
          $response = $soapclient->Uebernahme($row['transponder'], preg_match('/^[0-9]{15}$/', $row['transponder']) ? 1 : 2, $transfer_id, date("Y-m-d"), $halter);
          $response_time = microtime(true)-$response_time;

          logfile($row['transponder'], "Uebernahme (Status: ".$response->Status.")");
          if ($response->Status == 0) {
              if (isset($transponder)) { print "Uebernahme (ChipCode \"".$row['transponder']."\"): OK\n"; print_r($halter); }

              $row['reg_id'] = $response->Registrierungsnummer;
              $sql = sprintf("INSERT INTO `i_heimtierdb_at` SET `reg_id` = %.0f, `tier_id` = %d, `adr_id` = %d, `transponder` = '%s', `hdb_time` = %.3f, `created` = NOW()", $row['reg_id'], $row['id'], $row['adr_id'], mysql_real_escape_string($row['transponder']), microtime(true)-$hdb_time);
              mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");

              $sql = sprintf("UPDATE `i_heimtierdb_at` SET `transfered` = NOW(), `transfer_id` = %.0f WHERE `reg_id` = %.0f AND `transfer_id` = 0", $row['reg_id'], $transfer_id);
              mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");

              $sql = sprintf("INSERT INTO `i_heimtierdb_at:history` SET `transponder` = '%s', `reg_id` = %.0f, `tier_id` = %d, `adr_id` = %d, `data` = '%s', `type` = '%s', `parameter` = '%s', `hdb_time` = %.3f, `status` = %d, `response` = '%s', `timestamp` = NOW()",
                             mysql_real_escape_string($row['transponder']), $transfer_id, $row['id'], $row['adr_id'], mysql_real_escape_string(serialize($data)), 'Uebernahme', mysql_real_escape_string(serialize(array($row['transponder'], preg_match('/^[0-9]{15}$/', $row['transponder']) ? 1 : 2, $transfer_id, date("Y-m-d"), $halter))), $response_time, $response->Status, mysql_real_escape_string(serialize($response)));
              mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");

              $response = $soapclient->Abfrage($row['transponder'], preg_match('/^[0-9]{15}$/', $row['transponder']) ? '1' : '2', $row['reg_id']);
              logfile($row['transponder'], "Abfrage (Status: ".$response['Status']->Status.")");
              if ($response['Status']->Status == 0) {
                  if (isset($transponder)) print "Abfrage (ChipCode \"".$row['transponder']."\"): OK\n";
                  $data = objectToArray($response['Daten']);
                  $row['data'] = serialize($data);

                  $sql = sprintf("UPDATE `i_heimtierdb_at` SET `data` = '%s', `hdb_time` = %.3f WHERE `reg_id` = %.0f", mysql_real_escape_string($row['data']), microtime(true)-$hdb_time, $row['reg_id']);
                  mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")");

                  $TierAenderung = false;
                  $HEAenderung = false;

                  if ($data['Tier']['Name'] != $tier['Name']) $TierAenderung['Name'] = $tier['Name'];
                  if ($data['Tier']['Geschlecht'] != $tier['Geschlecht']) $TierAenderung['Geschlecht'] = $tier['Geschlecht'];
                  if ($data['Tier']['HeimtierausweisNr'] != $tier['HeimtierausweisNr']) $TierAenderung['HeimtierausweisNr'] = $tier['HeimtierausweisNr'];
				  if ($data['Tier']['TierRasse'] != $tier['TierRasse'])
    				$TierAenderung['TierRasse'] = $tier['TierRasse'];

                  if ($data['HalterEigentuemer']['Typ'] != $halter['HE']['Typ']) $HEAenderung['Typ'] = $halter['HE']['Typ'];
                  if ($data['HalterEigentuemer']['Person']['Titel'] != $halter['HE']['Person']['Titel']) $HEAenderung['Titel'] = $halter['HE']['Person']['Titel'];
                  if ($data['HalterEigentuemer']['Person']['Nachname'] != $halter['HE']['Person']['Nachname']) $HEAenderung['Familienname'] = $halter['HE']['Person']['Nachname'];

                  if ($data['HalterEigentuemer']['Adresse']['Land'] != $halter['HE']['Adresse']['Land'] ||
                      $data['HalterEigentuemer']['Adresse']['PLZ'] != $halter['HE']['Adresse']['PLZ'] ||
                      $data['HalterEigentuemer']['Adresse']['Ort'] != $halter['HE']['Adresse']['Ort'] ||
                      $data['HalterEigentuemer']['Adresse']['Str'] != $halter['HE']['Adresse']['Str'] ||
                      $data['HalterEigentuemer']['Adresse']['Nr'] != $halter['HE']['Adresse']['Nr']) {
                      $HEAenderung['Adresse']['Land'] = $halter['HE']['Adresse']['Land'];
                      $HEAenderung['Adresse']['PLZ'] = $halter['HE']['Adresse']['PLZ'];
                      $HEAenderung['Adresse']['Ort'] = $halter['HE']['Adresse']['Ort'];
                      $HEAenderung['Adresse']['Str'] = $halter['HE']['Adresse']['Str'];
                      $HEAenderung['Adresse']['Nr'] = $halter['HE']['Adresse']['Nr'];
                  }

                  if ($data['HalterEigentuemer']['Kontaktdaten']['Tel'] != $halter['HE']['Kontaktdaten']['Tel'] ||
                      $data['HalterEigentuemer']['Kontaktdaten']['Mobil'] != $halter['HE']['Kontaktdaten']['Mobil'] ||
                      $data['HalterEigentuemer']['Kontaktdaten']['Fax'] != $halter['HE']['Kontaktdaten']['Fax'] ||
                      $data['HalterEigentuemer']['Kontaktdaten']['eMail'] != $halter['HE']['Kontaktdaten']['eMail']) {
                      $HEAenderung['Kontaktdaten']['Tel'] = $halter['HE']['Kontaktdaten']['Tel'];
                      $HEAenderung['Kontaktdaten']['Mobil'] = $halter['HE']['Kontaktdaten']['Mobil'];
                      $HEAenderung['Kontaktdaten']['Fax'] = $halter['HE']['Kontaktdaten']['Fax'];
                      $HEAenderung['Kontaktdaten']['eMail'] = $halter['HE']['Kontaktdaten']['eMail'];
                  }

                  $sent = 0;
                  $success = 0;

                  if ($TierAenderung !== false) {
                      $sent ++;
                      $response_time = microtime(true);
                      $response = $soapclient->AenderungTier($row['transponder'], preg_match('/^[0-9]{15}$/', $row['transponder']) ? 1 : 2, $row['reg_id'], $TierAenderung);
                      $response_time = microtime(true)-$response_time;

                      logfile($row['transponder'], "AenderungTier (Status: ".$response->Status.")");

                      $errormsg = null;
                      if ($response->Status == 0) {
                          if (isset($transponder)) { print "AenderungTier (ChipCode \"".$row['transponder']."\"): OK\n"; print_r($TierAenderung); }
                          $success++;
                          $sql = sprintf("DELETE FROM `i_heimtierdb_at:error` WHERE `transponder` = '%s'", mysql_real_escape_string($row['transponder']));
                          mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");
                      }
                      elseif ($response->Status == 202) {
                          $errormsg = "Fehler bei AenderungTier (ChipCode \"".$row['transponder']."\"):\n".'$response = '.print_r($response,true).'$TierAenderung = '.print_r($TierAenderung,true);
                          print $errormsg;
                          if (isset($TierAenderung['Todesdatum']) && !isset($data['Tier']['Todesdatum'])) {
                              $data['Tier']['Todesdatum'] = $TierAenderung['Todesdatum'];
                              $sql = sprintf("UPDATE `i_heimtierdb_at` SET `data` = '%s', `hdb_time` = %.3f WHERE `reg_id` = %.0f", mysql_real_escape_string(serialize($data)), microtime(true)-$hdb_time, $row['reg_id']);
                              mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");
                          }
                          else {
                              $sql = sprintf("INSERT INTO `i_heimtierdb_at:error` SET `transponder` = '%s', `reg_id` = %.0f, `tier_id` = %d, `adr_id` = %d, `data` = '%s', `error` = '%s', `timestamp` = NOW() ON DUPLICATE KEY UPDATE `reg_id` = %.0f, `tier_id` = %d, `adr_id` = %d, `data` = '%s', `error` = '%s', `timestamp` = NOW()", mysql_real_escape_string($row['transponder']), $row['reg_id'], $row['id'], $row['adr_id'], mysql_real_escape_string(serialize($data)), mysql_real_escape_string($errormsg), $row['reg_id'], $row['id'], $row['adr_id'], mysql_real_escape_string(serialize($data)), mysql_real_escape_string($errormsg));
                              mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");
                          }
                      }
                      else {
                          $errormsg = "Fehler bei AenderungTier (ChipCode \"".$row['transponder']."\"):\n".'$response = '.print_r($response,true).'$TierAenderung = '.print_r($TierAenderung,true);
                          print $errormsg;
                          $sql = sprintf("INSERT INTO `i_heimtierdb_at:error` SET `transponder` = '%s', `reg_id` = %.0f, `tier_id` = %d, `adr_id` = %d, `data` = '%s', `error` = '%s', `timestamp` = NOW() ON DUPLICATE KEY UPDATE `reg_id` = %.0f, `tier_id` = %d, `adr_id` = %d, `data` = '%s', `error` = '%s', `timestamp` = NOW()", mysql_real_escape_string($row['transponder']), $row['reg_id'], $row['id'], $row['adr_id'], mysql_real_escape_string(serialize($data)), mysql_real_escape_string($errormsg), $row['reg_id'], $row['id'], $row['adr_id'], mysql_real_escape_string(serialize($data)), mysql_real_escape_string($errormsg));
                          mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");
                      }

                      $sql = sprintf("INSERT INTO `i_heimtierdb_at:history` SET `transponder` = '%s', `reg_id` = %.0f, `tier_id` = %d, `adr_id` = %d, `data` = '%s', `type` = '%s', `parameter` = '%s', `hdb_time` = %.3f, `status` = %d, `response` = '%s', `error` = %s, `timestamp` = NOW()",
                                     mysql_real_escape_string($row['transponder']), $row['reg_id'], $row['id'], $row['adr_id'], mysql_real_escape_string(serialize($data)), 'AenderungTier', mysql_real_escape_string(serialize(array($row['transponder'], preg_match('/^[0-9]{15}$/', $row['transponder']) ? 1 : 2, $row['reg_id'], $TierAenderung))), $response_time, $response->Status, mysql_real_escape_string(serialize($response)), isset($errormsg) ? "'".mysql_real_escape_string($errormsg)."'" : "NULL");
                      mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");
                  }

                  if ($HEAenderung !== false) {
					  if (empty($row['reg_id'])) continue;
                      $sent ++;
                      if (!isset($HEAenderung['Typ'])) $HEAenderung['Typ'] = $halter['HE']['Typ']; // Mandatory

                      $response_time = microtime(true);
                      $response = $soapclient->AenderungHE($row['transponder'], preg_match('/^[0-9]{15}$/', $row['transponder']) ? 1 : 2, $row['reg_id'], $HEAenderung);
                      $response_time = microtime(true)-$response_time;

                      logfile($row['transponder'], "AenderungHE (Status: ".$response->Status.")");

                      $errormsg = null;
                      if ($response->Status == 0) {
                          if (isset($transponder)) { print "AenderungHE (ChipCode \"".$row['transponder']."\"): OK\n"; print_r($HEAenderung); }
                          $success++;
                          $sql = sprintf("DELETE FROM `i_heimtierdb_at:error` WHERE `transponder` = '%s'", mysql_real_escape_string($row['transponder']));
                          mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");
                      }
                      else{
                          $errormsg = "Fehler bei AenderungHE (ChipCode \"".$row['transponder']."\"):\n".'$response = '.print_r($response,true).'$HEAenderung = '.print_r($HEAenderung,true);
                          print $errormsg;
                          $sql = sprintf("INSERT INTO `i_heimtierdb_at:error` SET `transponder` = '%s', `reg_id` = %.0f, `tier_id` = %d, `adr_id` = %d, `data` = '%s', `error` = '%s', `timestamp` = NOW() ON DUPLICATE KEY UPDATE `reg_id` = %.0f, `tier_id` = %d, `adr_id` = %d, `data` = '%s', `error` = '%s', `timestamp` = NOW()", mysql_real_escape_string($row['transponder']), $row['reg_id'], $row['id'], $row['adr_id'], mysql_real_escape_string(serialize($data)), mysql_real_escape_string($errormsg), $row['reg_id'], $row['id'], $row['adr_id'], mysql_real_escape_string(serialize($data)), mysql_real_escape_string($errormsg));
                          mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");
                      }

                      $sql = sprintf("INSERT INTO `i_heimtierdb_at:history` SET `transponder` = '%s', `reg_id` = %.0f, `tier_id` = %d, `adr_id` = %d, `data` = '%s', `type` = '%s', `parameter` = '%s', `hdb_time` = %.3f, `status` = %d, `response` = '%s', `error` = %s, `timestamp` = NOW()",
                                     mysql_real_escape_string($row['transponder']), $row['reg_id'], $row['id'], $row['adr_id'], mysql_real_escape_string(serialize($data)), 'AenderungHE', mysql_real_escape_string(serialize(array($row['transponder'], preg_match('/^[0-9]{15}$/', $row['transponder']) ? 1 : 2, $row['reg_id'], $HEAenderung))), $response_time, $response->Status, mysql_real_escape_string(serialize($response)), isset($errormsg) ? "'".mysql_real_escape_string($errormsg)."'" : "NULL");
                      mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");
                  }

                  if ($success) {
                      if ($success == $sent) {
                          $sql = sprintf("UPDATE `i_heimtierdb_at` SET `data` = '%s', `hdb_time` = %.3f, `updated` = NOW() WHERE `reg_id` = %.0f", mysql_real_escape_string(serialize(array('Tier'=>$tier,'HalterEigentuemer'=>$halter['HE']))), microtime(true)-$hdb_time, $row['reg_id']);
                          mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");
                      }
                      else {
                          $response = $soapclient->Abfrage($row['transponder'], preg_match('/^[0-9]{15}$/', $row['transponder']) ? '1' : '2', $row['reg_id']);
                          logfile($row['transponder'], "Abfrage (Status: ".$response['Status']->Status.")");
                          if ($response['Status']->Status == 0) {
                              if (isset($transponder)) print "Abfrage (ChipCode \"".$row['transponder']."\"): OK\n";
                              $reg_data = objectToArray($response['Daten']);
                              unset($reg_data['HalterEigentuemer']['Adresse']['GKZ']);
                              unset($reg_data['HalterEigentuemer']['Adresse']['Gemeinde']);
                              unset($reg_data['HalterEigentuemer']['Adresse']['OKZ']);
                              unset($reg_data['HalterEigentuemer']['Adresse']['SKZ']);

                              $sql = sprintf("UPDATE `i_heimtierdb_at` SET `data` = '%s', `hdb_time` = %.3f, `updated` = NOW() WHERE `reg_id` = %.0f", mysql_real_escape_string(serialize($reg_data)), microtime(true)-$hdb_time, $row['reg_id']);
                              mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");
                          }
                          else{
                              $errormsg = "Fehler bei Abfrage (ChipCode \"".$row['transponder']."\"):\n".print_r($response,true);
                              print $errormsg;
                          }
                      }

                      /* Increase counter of updated rows */
                      $count_update ++;
                  }
              }
              else{
                  $errormsg = "Fehler bei Abfrage (ChipCode \"".$row['transponder']."\"):\n".print_r($response,true);
                  print $errormsg;
              }

              /* Increase counter of inserted rows */
              $count_insert ++;
          }
          else{
              $errormsg = "Fehler bei Uebernahme (ChipCode \"".$row['transponder']."\", Reg-ID \"".$transfer_id."\"):\n".'$response = '.print_r($response,true).'$halter = '.print_r($halter,true);
              print $errormsg;

              $sql = sprintf("INSERT INTO `i_heimtierdb_at:error` SET `transponder` = '%s', `reg_id` = %.0f, `tier_id` = %d, `adr_id` = %d, `data` = '%s', `error` = '%s', `timestamp` = NOW() ON DUPLICATE KEY UPDATE `reg_id` = %.0f, `tier_id` = %d, `adr_id` = %d, `data` = '%s', `error` = '%s', `timestamp` = NOW()", mysql_real_escape_string($row['transponder']), $row['reg_id'], $row['id'], $row['adr_id'], mysql_real_escape_string(serialize($data)), mysql_real_escape_string($errormsg), $row['reg_id'], $row['id'], $row['adr_id'], mysql_real_escape_string(serialize($data)), mysql_real_escape_string($errormsg));
              mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");

              $sql = sprintf("INSERT INTO `i_heimtierdb_at:history` SET `transponder` = '%s', `reg_id` = %.0f, `tier_id` = %d, `adr_id` = %d, `data` = '%s', `type` = '%s', `parameter` = '%s', `hdb_time` = %.3f, `status` = %d, `response` = '%s', `error` = '%s', `timestamp` = NOW()",
                             mysql_real_escape_string($row['transponder']), $transfer_id, $row['id'], $row['adr_id'], mysql_real_escape_string(serialize($data)), 'Uebernahme', mysql_real_escape_string(serialize(array($row['transponder'], preg_match('/^[0-9]{15}$/', $row['transponder']) ? 1 : 2, $transfer_id, date("Y-m-d"), $halter))), $response_time, $response->Status, mysql_real_escape_string(serialize($response)), mysql_real_escape_string($errormsg));
              mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");
          }
      }
      else {
          $response_time = microtime(true);
          $response = $soapclient->Erstmeldung($tier, $halter);
          $response_time = microtime(true)-$response_time;

          logfile($row['transponder'], "Erstmeldung (Status: ".$response->Status.")");

          $errormsg = null;
          if ($response->Status == 0) {
              if (isset($transponder)) { print "Erstmeldung (ChipCode \"".$row['transponder']."\"): OK\n"; print_r($tier); print_r($halter); }

              $sql = sprintf("INSERT INTO `i_heimtierdb_at` SET `reg_id` = %.0f, `tier_id` = %d, `adr_id` = %d, `transponder` = '%s', `data` = '%s', `hdb_time` = %.3f, `created` = NOW()", $response->Registrierungsnummer, $row['id'], $row['adr_id'], mysql_real_escape_string($row['transponder']), mysql_real_escape_string(serialize(array('Tier'=>$tier,'HalterEigentuemer'=>$halter['HE']))), microtime(true)-$hdb_time);
              mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");

              /* Increase counter of inserted rows */
              $count_insert ++;

              $sql = sprintf("DELETE FROM `i_heimtierdb_at:error` WHERE `transponder` = '%s'", mysql_real_escape_string($row['transponder']));
              mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");
          }
          elseif ($response->Status == 200) {
              $errormsg = "ChipCode \"".$row['transponder']."\" wurde von einer anderen Registrierungsstelle gemeldet (existiert bereits)!\n";
              print $errormsg;
              $sql = sprintf("INSERT INTO `i_heimtierdb_at:error` SET `transponder` = '%s', `reg_id` = %.0f, `tier_id` = %d, `adr_id` = %d, `data` = '%s', `error` = '%s', `timestamp` = NOW() ON DUPLICATE KEY UPDATE `reg_id` = %.0f, `tier_id` = %d, `adr_id` = %d, `data` = '%s', `error` = '%s', `timestamp` = NOW()", mysql_real_escape_string($row['transponder']), $row['reg_id'], $row['id'], $row['adr_id'], mysql_real_escape_string(serialize($data)), mysql_real_escape_string($errormsg), $row['reg_id'], $row['id'], $row['adr_id'], mysql_real_escape_string(serialize($data)), mysql_real_escape_string($errormsg));
              mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");
          }
          else{
              $errormsg = "Fehler bei Erstmeldung (ChipCode \"".$row['transponder']."\"):\n".'$response = '.print_r($response,true).'$tier = '.print_r($tier,true).'$halter = '.print_r($halter,true);
              print $errormsg;
              $sql = sprintf("INSERT INTO `i_heimtierdb_at:error` SET `transponder` = '%s', `reg_id` = %.0f, `tier_id` = %d, `adr_id` = %d, `data` = '%s', `error` = '%s', `timestamp` = NOW() ON DUPLICATE KEY UPDATE `reg_id` = %.0f, `tier_id` = %d, `adr_id` = %d, `data` = '%s', `error` = '%s', `timestamp` = NOW()", mysql_real_escape_string($row['transponder']), $row['reg_id'], $row['id'], $row['adr_id'], mysql_real_escape_string(serialize($data)), mysql_real_escape_string($errormsg), $row['reg_id'], $row['id'], $row['adr_id'], mysql_real_escape_string(serialize($data)), mysql_real_escape_string($errormsg));
              mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");
          }

          $sql = sprintf("INSERT INTO `i_heimtierdb_at:history` SET `transponder` = '%s', `reg_id` = %.0f, `tier_id` = %d, `adr_id` = %d, `data` = '%s', `type` = '%s', `parameter` = '%s', `hdb_time` = %.3f, `status` = %d, `response` = '%s', `error` = %s, `timestamp` = NOW()",
                         mysql_real_escape_string($row['transponder']), $row['reg_id'], $row['id'], $row['adr_id'], mysql_real_escape_string(serialize($data)), 'Erstmeldung', mysql_real_escape_string(serialize(array($tier, $halter))), $response_time, $response->Status, mysql_real_escape_string(serialize($response)), isset($errormsg) ? "'".mysql_real_escape_string($errormsg)."'" : "NULL");
          mysql_query($sql, $conn) or print("SQL error in ".basename(__FILE__)." (".__LINE__."): ".$sql." (".mysql_errno()." ".mysql_error().")\n");
      }

      flush();
//if ($count_update >= 1 || $count_insert >= 1) break;
  }
  mysql_free_result($result);

  print $count_insert." ".($count_insert != 1 ? "Datensaetze wurden" : "Datensatz wurde")." in der Heimtierdatenbank gemeldet\n";
  print $count_update." ".($count_update != 1 ? "Datensaetze wurden" : "Datensatz wurde")." wurden in der Heimtierdatenbank aktualisiert\n";
?>
</pre>
</body>
</html>
