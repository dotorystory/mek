 <?php
  $mainMenu = json_decode('
    [
      {"title": "ABOUT US", "p_title":"公司介绍", "href": "'.G5_URL.'/sub01_01.php"},
      {"title": "APPLICATIONS", "p_title":"业务/产业", "href": "'.G5_URL.'/sub02_01.php"},
      {"title": "PRODUCTS", "p_title":"产品介绍", "href": "'.G5_BBS_URL.'/board.php?bo_table=pro_cn"},
      {"title": "CATALOG", "p_title":"产品目录", "href": "'.G5_BBS_URL.'/board.php?bo_table=catalog_ko"},
      {"title": "CONTACT", "p_title":"联系我们", "href": "'.G5_URL.'/sub04_02.php"}
    ]
  ');
  $subMenu = json_decode('
      [
        [
          {"title": "公司介绍", "p_title": "About MEK", "href": "'.G5_URL.'/sub01_01.php"},
      		{"title": "公司历史", "p_title": "History", "href": "'.G5_BBS_URL.'/board.php?bo_table=history_cn"},
          {"title": "重要客户", "p_title": "Key Clients", "text": "持续的研究和开发使MEK不仅在国内市场，还在国际市场扩大份额，实现持续增长。", "href": "'.G5_BBS_URL.'/board.php?bo_table=partner_cn"},
          {"title": "认证情况", "p_title": "Certifications", "text": "通过不断的研究，MEK将始终引领行业技术发展，提供最优质的产品。", "href": "'.G5_URL.'/sub01_04.php"},
          {"title": "R&D Center", "href": "'.G5_URL.'/sub01_06.php"},
          {"title": "伦理经营", "p_title": "Ethical Management", "href": "'.G5_URL.'/sub01_05.php"}
    	  ],
        [
          {"title": "电池", "p_title": "Battery", "href": "'.G5_URL.'/sub02_01.php"},
          {"title": "电子材料", "p_title": "Electronic Materials", "href": "'.G5_URL.'/sub02_02.php"},
          {"title": "显示屏", "p_title": "Display", "href": "'.G5_URL.'/sub02_03.php"},
          {"title": "金属", "p_title": "Metal", "href": "'.G5_URL.'/sub02_04.php"},
          {"title": "包装材料", "p_title": "Packaging", "href": "'.G5_URL.'/sub02_05.php"},
          {"title": "其他", "p_title": "Other", "href": "'.G5_URL.'/sub02_06.php"}
        ],
        [
          {"title": "厚度/密度 测量仪", "p_title": "Thickness/Density Meter", "text": "支持传感器按照固定路径移动，通过操作面板传递测量数据给用户。", "href": "'.G5_BBS_URL.'/board.php?bo_table=pro_cn"},
          {"title": "网络清洁器", "p_title": "Web Cleaner", "text": "MEK网络清洁器是一种非接触式的设备，用于去除片材和膜表面的杂质。", "href": "'.G5_URL.'/sub03_02.php"},
          {"title": "定位系统", "p_title": "Pinning System", "text": "将挤出模出来的液体树脂粘附在冷却辊上的装置。与气刀箱和配料箱试比，具有均匀且出色的性能。", "href": "'.G5_URL.'/sub03_03.php"},
          {"title": "合作伙伴产品", "p_title": "Partner Product", "href": "'.G5_URL.'/sub03_04.php"}
        ],
        [
          {"title": "产品目录", "p_title":"CATALOG", "href": "'.G5_BBS_URL.'/board.php?bo_table=catalog_cn"}
        ],
        [
          {"title": "厚度测量仪咨询", "p_title": "Thickness Gauge Inquiry", "text": "有关产品/技术咨询等任何问题，请随时与我们联系，我们将尽快回复您。 ","href": "'.G5_URL.'/sub04_02.php"},
          {"title": "产品咨询", "p_title": "Inquiry", "text": "有关产品/技术咨询等任何问题，请随时与我们联系，我们将尽快回复您。 ","href": "'.G5_URL.'/sub04_01.php"},
          {"title": "售后服务咨询", "p_title": "AS Inquiry", "text": "有关产品/技术咨询等任何问题，请随时与我们联系，我们将尽快回复您。 ","href": "'.G5_URL.'/sub04_03.php"},
          {"title": "新闻", "p_title":"新闻", "text":"快速了解MEK的最新消息。","href": "'.G5_BBS_URL.'/board.php?bo_table=news_cn"},
        {"title": "订阅新闻", "p_title":"SUBSCRIBE", "text":"🌈 MEK+ 新闻订阅，获取最新信息和特别活动消息。 ","href": "'.G5_URL.'/plus/subscribe/index.php"}
        ]
      ]
    ');

?>
