<?php
  $mainMenu = json_decode('
    [
      {"title": "ABOUT US", "href": "'.G5_URL.'/sub01_01.php"},
      {"title": "APPLICATIONS", "href": "'.G5_URL.'/sub02_01.php"},
      {"title": "PRODUCTS", "href": "'.G5_BBS_URL.'/board.php?bo_table=pro_en"},
      {"title": "CATALOG", "href": "'.G5_BBS_URL.'/board.php?bo_table=catalog_en"},
      {"title": "CONTACT", "href": "'.G5_URL.'/sub04_02.php"}
    ]
  ');
  $subMenu = json_decode('
    [
      [
        {"title": "Über das Unternehmen", "p_title":"ABOUT US", "href": "'.G5_URL.'/sub01_01.php"},
        {"title": "Unternehmensgeschichte", "p_title":"ABOUT US", "href": "'.G5_BBS_URL.'/board.php?bo_table=history_en"},
        {"title": "Hauptkunden", "p_title":"ABOUT US", "text": "MEK wird durch unermüdliche Forschung den technologischen Fortschritt der Branche anführen und die besten Produkte liefern.", "href": "'.G5_BBS_URL.'/board.php?bo_table=partner_en"},
        {"title": "Zertifizierungen", "p_title":"ABOUT US", "text": "MEK wird durch unermüdliche Forschung den technologischen Fortschritt der Branche anführen und die besten Produkte liefern.", "href": "'.G5_URL.'/sub01_04.php"},
        {"title": "F&E-Zentrum", "href": "'.G5_URL.'/sub01_06.php"},
        {"title": "Ethisches Management", "p_title":"ABOUT US", "href": "'.G5_URL.'/sub01_05.php"}
      ],
      [
        {"title": "Batterie", "p_title":"APPLICATIONS", "href": "'.G5_URL.'/sub02_01.php"},
        {"title": "Elektronische Materialien", "p_title":"APPLICATIONS", "href": "'.G5_URL.'/sub02_02.php"},
        {"title": "Display", "p_title":"APPLICATIONS", "href": "'.G5_URL.'/sub02_03.php"},
        {"title": "Metall", "p_title":"APPLICATIONS", "href": "'.G5_URL.'/sub02_04.php"},
        {"title": "Verpackung", "p_title":"APPLICATIONS", "href": "'.G5_URL.'/sub02_05.php"},
        {"title": "Sonstiges", "p_title":"APPLICATIONS", "href": "'.G5_URL.'/sub02_06.php"}
      ],
      [
        {"title": "Dicken-/Dichtemessgerät", "p_title":"PRODUCTS", "text": "Der Sensor unterstützt die Bewegung entlang eines vordefinierten Pfades und liefert Messdaten über das Bedienfeld an den Benutzer.", "href": "'.G5_BBS_URL.'/board.php?bo_table=pro_en"},
        {"title": "Web-Reiniger", "p_title":"PRODUCTS", "text": "Der MEK Web-Reiniger ist ein berührungsloses Gerät, das Fremdkörper von den Oberflächen von Folien und Filmen entfernt.", "href": "'.G5_URL.'/sub03_02.php"},
        {"title": "Pinning-System", "p_title":"PRODUCTS", "text": "Ein Gerät, das flüssiges Harz von der Extrusionsdüse auf die Kühlwalze aufträgt. Im Gegensatz zu Luftmessern oder Perlenkästen arbeitet es elektrisch und bietet gleichmäßige und überlegene Leistung.", "href": "'.G5_URL.'/sub03_03.php"},
        {"title": "Partnerprodukte", "p_title":"PRODUCTS", "href": "'.G5_URL.'/sub03_04.php"}
      ],
      [
        {"title": "KATALOG", "p_title":"CATALOG", "href": "'.G5_BBS_URL.'/board.php?bo_table=catalog_en"}
      ],
      [
        {"title": "Dickenmessgerät Anfrage", "p_title":"CONTACT", "text": "Bitte kontaktieren Sie uns bei Fragen zu Produkten oder Technologie, wir werden uns so schnell wie möglich bei Ihnen melden.", "href": "'.G5_URL.'/sub04_02.php"},
        {"title": "Produktanfrage", "p_title":"CONTACT", "text": "Bitte kontaktieren Sie uns bei Fragen zu Produkten oder Technologie, wir werden uns so schnell wie möglich bei Ihnen melden.", "href": "'.G5_URL.'/sub04_01.php"},
        {"title": "AS-Anfrage", "p_title":"CONTACT", "text": "Bitte kontaktieren Sie uns bei Fragen zu Produkten oder Technologie, wir werden uns so schnell wie möglich bei Ihnen melden.", "href": "'.G5_URL.'/sub04_03.php"},
        {"title": "NEWS", "p_title":"NEWS", "text":"Bleiben Sie auf dem Laufenden mit den neuesten Nachrichten von MEK.","href": "'.G5_BBS_URL.'/board.php?bo_table=news_en"},
        {"title": "Newsletter abonnieren", "p_title":"SUBSCRIBE", "text":"🌈 MEK+ Newsletter abonnieren und neueste Informationen und besondere Ereignisse erhalten.","href": "'.G5_URL.'/plus/subscribe/index.php"}
      ]
    ]
  ');
?>
