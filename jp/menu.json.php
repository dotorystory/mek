 <?php
 $mainMenu = json_decode('
   [
     {"title": "ABOUT US", "p_title":"会社紹介", "href": "'.G5_URL.'/sub01_01.php"},
     {"title": "APPLICATIONS", "p_title":"事業/産業", "href": "'.G5_URL.'/sub02_01.php"},
     {"title": "PRODUCTS", "p_title":"製品紹介", "href": "'.G5_BBS_URL.'/board.php?bo_table=pro_jp"},
     {"title": "CATALOG", "p_title":"カタログ", "href": "'.G5_BBS_URL.'/board.php?bo_table=catalog_jp"},
     {"title": "CONTACT", "p_title":"お問い合わせ", "href": "'.G5_URL.'/sub04_02.php"}
   ]
 ');

 $subMenu = json_decode('
   [
     [
       {"title": "会社紹介", "p_title":"About MEK", "href": "'.G5_URL.'/sub01_01.php"},
       {"title": "会社歴史", "p_title":"History", "href": "'.G5_BBS_URL.'/board.php?bo_table=history_jp"},
       {"title": "主要顧客", "p_title":"Key Clients", "text":"持続的な研究と開発により、国内市場だけでなく海外市場でもシェアを拡大し、着実な成長を遂げるMEKです。", "href": "'.G5_BBS_URL.'/board.php?bo_table=partner_jp"},
       {"title": "認証状況", "p_title":"Certifications","text":"絶え間ない研究を通じて、常に業界の技術発展をリードし、最高の製品を提供するMEKになります。", "href": "'.G5_URL.'/sub01_04.php"},
       {"title": "R&D Center", "href": "'.G5_URL.'/sub01_06.php"},
       {"title": "倫理経営", "p_title":"Ethical Management", "href": "'.G5_URL.'/sub01_05.php"}
     ],
     [
       {"title": "バッテリー", "p_title":"Battery", "href": "'.G5_URL.'/sub02_01.php"},
       {"title": "電子材料", "p_title":"Electronic Materials", "href": "'.G5_URL.'/sub02_02.php"},
       {"title": "ディスプレイ", "p_title":"Display", "href": "'.G5_URL.'/sub02_03.php"},
       {"title": "金属", "p_title":"Metal", "href": "'.G5_URL.'/sub02_04.php"},
       {"title": "包装材料", "p_title":"Packaging", "href": "'.G5_URL.'/sub02_05.php"},
       {"title": "その他", "p_title":"Other", "href": "'.G5_URL.'/sub02_06.php"}
     ],
     [
       {"title": "厚さ/密度測定器", "p_title":"Thickness/Density Meter", "text":"センサーが一定の経路を移動しながら、測定データをユーザーに表示できるようにオペレーティングパネルで伝えます。", "href": "'.G5_BBS_URL.'/board.php?bo_table=pro_jp"},
       {"title": "ウェブクリーナー", "p_title":"Web Cleaner", "text":"MEKウェブクリーナーは非接触でシートやフィルム表面の異物を除去する装置です。", "href": "'.G5_URL.'/sub03_02.php"},
       {"title": "ピニングシステム", "p_title":"Pinning System", "text":"押出ダイから出る液体樹脂を冷却ロールに付着させる装置。エアーナイフボックスや配料ボックスとは異なり、電気的な方法で均一かつ優れた性能を発揮します。", "href": "'.G5_URL.'/sub03_03.php"},
       {"title": "パートナー製品", "p_title":"Partner Product",  "href": "'.G5_URL.'/sub03_04.php"}
     ],
     [
       {"title": "CATALOG", "p_title":"カタログ", "href": "'.G5_BBS_URL.'/board.php?bo_table=catalog_jp"}
     ],
     [
       {"title": "厚み計のお問い合わせ", "p_title":"Thickness Gauge Inquiry", "text":"製品/技術に関するお問い合わせなど、何か質問があればお気軽にお問い合わせください。 ","href": "'.G5_URL.'/sub04_02.php"},
       {"title": "製品お問い合わせ", "p_title":"Inquiry", "text":"製品/技術に関するお問い合わせなど、何か質問があればお気軽にお問い合わせください。 ","href": "'.G5_URL.'/sub04_01.php"},
       {"title": "ASお問い合わせ", "p_title":"AS Inquiry", "text":"製品/技術に関するお問い合わせなど、何か質問があればお気軽にお問い合わせください。 ","href": "'.G5_URL.'/sub04_03.php"},
       {"title": "ニュース", "p_title":"ニュース", "text":"MEKの最新情報をいち早くチェックしましょう。 ","href": "'.G5_BBS_URL.'/board.php?bo_table=news_jp"},
        {"title": "ニュースレター購読", "p_title":"SUBSCRIBE", "text":"🌈 MEK+ ニュースレターを購読して、最新情報と特別なイベントメッセージを受け取りましょう。 ","href": "'.G5_URL.'/plus/subscribe/index.php"}
     ]
   ]
 ');

?>
