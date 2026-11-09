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
        {"title": "About Company", "p_title":"ABOUT US", "href": "'.G5_URL.'/sub01_01.php"},
        {"title": "Company History", "p_title":"ABOUT US", "href": "'.G5_BBS_URL.'/board.php?bo_table=history_en"},
        {"title": "Key Clients", "p_title":"ABOUT US", "text": "MEK will lead the industry\'s technological advancement through relentless research and will provide the best products.", "href": "'.G5_BBS_URL.'/board.php?bo_table=partner_en"},
        {"title": "Certifications", "p_title":"ABOUT US", "text": "MEK will lead the industry\'s technological advancement through relentless research and will provide the best products.", "href": "'.G5_URL.'/sub01_04.php"},
        {"title": "R&D Center", "href": "'.G5_URL.'/sub01_06.php"},
        {"title": "Ethical Management", "p_title":"ABOUT US", "href": "'.G5_URL.'/sub01_05.php"}
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
        {"title": "Thickness/Density Meter", "p_title":"PRODUCTS", "text": "The sensor supports movement along a predetermined path while delivering measurement data to the user through the operating panel.", "href": "'.G5_BBS_URL.'/board.php?bo_table=pro_en"},
        {"title": "Web Cleaner", "p_title":"PRODUCTS", "text": "MEK Web Cleaner is a non-contact device that removes foreign substances from the surfaces of sheets and films.", "href": "'.G5_URL.'/sub03_02.php"},
        {"title": "Pinning System", "p_title":"PRODUCTS", "text": "A device that attaches liquid resin from the extrusion die to the cooling roll. Unlike air knives or bead boxes, it operates electrically, providing uniform and superior performance.", "href": "'.G5_URL.'/sub03_03.php"},
        {"title": "Partner Product", "p_title":"PRODUCTS", "href": "'.G5_URL.'/sub03_04.php"}
      ],
      [
        {"title": "CATALOG", "p_title":"카달로그", "href": "'.G5_BBS_URL.'/board.php?bo_table=catalog_en"}
      ],
      [
        {"title": "Thickness Gauge Inquiry", "p_title":"CONTACT", "text": "Please contact us for any inquiries regarding products or technology, and we will get back to you as soon as possible.", "href": "'.G5_URL.'/sub04_02.php"},
        {"title": "Product Inquiry", "p_title":"CONTACT", "text": "Please contact us for any inquiries regarding products or technology, and we will get back to you as soon as possible.", "href": "'.G5_URL.'/sub04_01.php"},
        {"title": "AS Inquiry", "p_title":"CONTACT", "text": "Please contact us for any inquiries regarding products or technology, and we will get back to you as soon as possible.", "href": "'.G5_URL.'/sub04_03.php"},
        {"title": "NEWS", "p_title":"NEWS", "text":"Stay updated with the latest news from MEK. ","href": "'.G5_BBS_URL.'/board.php?bo_table=news_en"},
        {"title": "Newsletter Subscribe", "p_title":"SUBSCRIBE", "text":"🌈 Subscribe to MEK+ Newsletter and receive the latest information and special event messages. ","href": "'.G5_URL.'/plus/subscribe/index.php"}
      ]
    ]
  ');
?>
