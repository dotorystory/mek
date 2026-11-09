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
        {"title": "Sobre la Empresa", "p_title":"ABOUT US", "href": "'.G5_URL.'/sub01_01.php"},
        {"title": "Historia de la Empresa", "p_title":"ABOUT US", "href": "'.G5_BBS_URL.'/board.php?bo_table=history_en"},
        {"title": "Clientes Clave", "p_title":"ABOUT US", "text": "MEK liderará el avance tecnológico de la industria a través de una investigación incansable y proporcionará los mejores productos.", "href": "'.G5_BBS_URL.'/board.php?bo_table=partner_en"},
        {"title": "Certificaciones", "p_title":"ABOUT US", "text": "MEK liderará el avance tecnológico de la industria a través de una investigación incansable y proporcionará los mejores productos.", "href": "'.G5_URL.'/sub01_04.php"},
        {"title": "Centro de I+D", "href": "'.G5_URL.'/sub01_06.php"},
        {"title": "Gestión Ética", "p_title":"ABOUT US", "href": "'.G5_URL.'/sub01_05.php"}
      ],
      [
        {"title": "Battery", "p_title":"APPLICATIONS", "href": "'.G5_URL.'/sub02_01.php"},
        {"title": "Electronic Materials", "p_title":"APPLICATIONS", "href": "'.G5_URL.'/sub02_02.php"},
        {"title": "Display", "p_title":"APPLICATIONS", "href": "'.G5_URL.'/sub02_03.php"},
        {"title": "Metal", "p_title":"APPLICATIONS", "href": "'.G5_URL.'/sub02_04.php"},
        {"title": "Packaging", "p_title":"APPLICATIONS", "href": "'.G5_URL.'/sub02_05.php"},
        {"title": "Other", "p_title":"APPLICATIONS", "href": "'.G5_URL.'/sub02_06.php"}
      ],
      [
        {"title": "Medidor de Espesor/Densidad", "p_title":"PRODUCTS", "text": "El sensor soporta el movimiento a lo largo de una ruta predeterminada mientras entrega datos de medición al usuario a través del panel de operación.", "href": "'.G5_BBS_URL.'/board.php?bo_table=pro_en"},
        {"title": "Limpiador Web", "p_title":"PRODUCTS", "text": "El Limpiador Web MEK es un dispositivo sin contacto que elimina sustancias extrañas de las superficies de láminas y películas.", "href": "'.G5_URL.'/sub03_02.php"},
        {"title": "Sistema de Pinning", "p_title":"PRODUCTS", "text": "Un dispositivo que adhiere resina líquida desde el dado de extrusión al rodillo de enfriamiento. A diferencia de los cuchillos de aire o cajas de perlas, opera eléctricamente, proporcionando un rendimiento uniforme y superior.", "href": "'.G5_URL.'/sub03_03.php"},
        {"title": "Producto de Socio", "p_title":"PRODUCTS", "href": "'.G5_URL.'/sub03_04.php"}
      ],
      [
        {"title": "CATALOG", "p_title":"Catálogo", "href": "'.G5_BBS_URL.'/board.php?bo_table=catalog_en"}
      ],
      [
        {"title": "Consulta de Medidor de Espesor", "p_title":"CONTACT", "text": "Por favor, contáctenos para cualquier consulta sobre productos o tecnología, y le responderemos lo antes posible.", "href": "'.G5_URL.'/sub04_02.php"},
        {"title": "Consulta de Producto", "p_title":"CONTACT", "text": "Por favor, contáctenos para cualquier consulta sobre productos o tecnología, y le responderemos lo antes posible.", "href": "'.G5_URL.'/sub04_01.php"},
        {"title": "Consulta de A/S", "p_title":"CONTACT", "text": "Por favor, contáctenos para cualquier consulta sobre productos o tecnología, y le responderemos lo antes posible.", "href": "'.G5_URL.'/sub04_03.php"},
        {"title": "NEWS", "p_title":"NEWS", "text":"Manténgase actualizado con las últimas noticias de MEK. ","href": "'.G5_BBS_URL.'/board.php?bo_table=news_en"},
        {"title": "Suscribirse al Boletín", "p_title":"SUBSCRIBE", "text":"🌈 Suscríbase al Boletín MEK+ y reciba la información más reciente y mensajes de eventos especiales. ","href": "'.G5_URL.'/plus/subscribe/index.php"}
      ]
    ]
  ');
?>
