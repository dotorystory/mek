 <?php
  $mainMenu = json_decode('
    [
      {"title": "ABOUT US", "p_title":"회사소개", "href": "'.G5_URL.'/sub01_01.php"},
      {"title": "APPLICATIONS", "p_title":"사업/산업", "href": "'.G5_URL.'/sub02_01.php"},
      {"title": "PRODUCTS", "p_title":"제품소개", "href": "'.G5_BBS_URL.'/board.php?bo_table=pro_ko"},
      {"title": "CATALOG", "p_title":"카달로그", "href": "'.G5_BBS_URL.'/board.php?bo_table=catalog_ko"},
      {"title": "CONTACT", "p_title":"문의하기", "href": "'.G5_URL.'/sub04_02.php"}
    ]
  ');
  $subMenu = json_decode('
    [
      [
        {"title": "회사 소개", "p_title":"About MEK", "href": "'.G5_URL.'/sub01_01.php"},
    		{"title": "회사 연혁", "p_title":"History", "href": "'.G5_BBS_URL.'/board.php?bo_table=history_ko"},
        {"title": "주요 고객사", "p_title":"Key Clients", "text":"지속적인 연구와 개발로 국내 시장 뿐만 아니라 해외 시장까지 점유율을 확대하며 <br>꾸준한 성장을 이뤄내는 MEK 입니다.", "href": "'.G5_BBS_URL.'/board.php?bo_table=partner_ko"},
        {"title": "인증 현황", "p_title":"Certifications","text":"끊임 없는 연구를 통해 언제나 업계의 기술발전을 이끌고 <br>최상의 제품을 공급하는 MEK가 되겠습니다.", "href": "'.G5_URL.'/sub01_04.php"},
        {"title": "R&D Center", "href": "'.G5_URL.'/sub01_06.php"},
        {"title": "윤리 경영", "p_title":"Ethical Management", "href": "'.G5_URL.'/sub01_05.php"}
  	  ],
      [
        {"title": "전지", "p_title":"Battery", "href": "'.G5_URL.'/sub02_01.php"},
        {"title": "전자 소재", "p_title":"Electronic Materials", "href": "'.G5_URL.'/sub02_02.php"},
        {"title": "디스플레이", "p_title":"Display", "href": "'.G5_URL.'/sub02_03.php"},
        {"title": "금속", "p_title":"Metal", "href": "'.G5_URL.'/sub02_04.php"},
        {"title": "포장용", "p_title":"Packaging", "href": "'.G5_URL.'/sub02_05.php"},
        {"title": "기타", "p_title":"Other", "href": "'.G5_URL.'/sub02_06.php"}
      ],
      [
        {"title": "두께/밀도 측정기", "p_title":"Thickness/Density Meter", "text":"센서가 일정한 경로로 움직일 수 있도록 지지하면서 <br>측정 데이터를 사용자가 볼 수 있도록 오퍼레이팅 판넬로 전달합니다.", "href": "'.G5_BBS_URL.'/board.php?bo_table=pro_ko"},
        {"title": "웹크리너", "p_title":"Web Cleaner", "text":"MEK웹크리너는 비접촉식으로 시트, 필름 표면의 이물질을 제거해 주는 장치입니다.", "href": "'.G5_URL.'/sub03_02.php"},
        {"title": "피닝시스템", "p_title":"Pinning System", "text":"압출다이에서 나오는 액상의 수지를 냉각롤에 붙여주는 장치. 에어나이프나 배큠박스와 달리 <br>전기적 방식으로 균일하고 우수한 성능을 보여줍니다.", "href": "'.G5_URL.'/sub03_03.php"},
        {"title": "파트너사 제품", "p_title":"Partner Product",  "href": "'.G5_URL.'/sub03_04.php"}
      ],
      [
        {"title": "CATALOG", "p_title":"카달로그", "href": "'.G5_BBS_URL.'/board.php?bo_table=catalog_ko"}
      ],
      [
        {"title": "두께 측정기 문의", "p_title":"Thickness Gauge Inquiry", "text":"제품 / 기술 문의 등 무엇이든 문의 주시면 빠른 시일 내에 연락 드리겠습니다. ","href": "'.G5_URL.'/sub04_02.php"},
        {"title": "제품 문의", "p_title":"Inquiry", "text":"제품 / 기술 문의 등 무엇이든 문의 주시면 빠른 시일 내에 연락 드리겠습니다. ","href": "'.G5_URL.'/sub04_01.php"},
        {"title": "AS 문의", "p_title":"AS Inquiry", "text":"제품 / 기술 문의 등 무엇이든 문의 주시면 빠른 시일 내에 연락 드리겠습니다. ","href": "'.G5_URL.'/sub04_03.php"},
        {"title": "NEWS", "p_title":"NEWS", "text":"MEK의 새로운 소식을 빠르게 알아보세요. ","href": "'.G5_BBS_URL.'/board.php?bo_table=news_ko"},
        {"title": "뉴스레터 구독", "p_title":"SUBSCRIBE", "text":"🌈 MEK+ 뉴스레터를 구독하시고 최신 정보와 특별한 이벤트 소식을 받아보세요. ","href": "'.G5_URL.'/plus/subscribe/index.php"}
      ]
    ]
  ');
?>
