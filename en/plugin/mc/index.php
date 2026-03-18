<!doctype html>
<html lang="ko">
<head>
    <title>MC Tools</title>
    <style>
    body{font-size:12px}
    </style>
</head>
<body>
<h1>MC Tools</h1>
<p>
    계층형 카테고리(확장컬럼) 관리 플러그인 입니다.
</p>



<ul>
    <li>구느보드 5.4 이상 (테스트환경 : 그누보드 5.4.1.9, PHP 7.4, mysql 8 )</li>
    <li>PHP 5.6 이상 :
    <?php if (version_compare('5.6', PHP_VERSION)) {
        echo '설치가능';
    }else{
        echo '<strong style="color:red">설치하실 수 없습니다</strong>';
    }?>
        (사용하시는 버전 : <?php echo PHP_VERSION;?>)
    </li>
</ul>



<ul>
    <li> 2020-11-10 (2.1.2)
        <ul>
            <li>write 스킨 필수입력에 대한 label css 추가</li>
            <li>숫자형, 문자분할 숫자형 컬럼 출력시 number_format 으로 출력되도록 변경</li>
            <li>전화번호 유효성체크 0000-0000 국번없이 될수 있도록 수정</li>
            <li>목록보기 버튼클릭 검색모드 스킨 추가(basic_btn_search)</li>
            <li>숫자형 범위검색 추가(basic_btn_search)</li>
            <li>기타 버그 픽스</li>
        </ul>
    </li>

    <li>2020-09-03 (2.1.1)
        <ul>
            <li>그누보드 5.3 버전 지원</li>
            <li>자동출력 비활성화 저장 오류 fix</li>
            <li>숫자 범위검색 제거</li>
        </ul>
    </li>
    <li>2020-04-27 (2.0.3)
        <ul>
            <li>날짜 범위검색 추가</li>
            <li>숫자 범위검색 추가</li>
            <li>폼 컬럼 출력순서 변경 기능 추가</li>
            <li>Ajax 를 사용하지 않고 스킨에서 바로 출력 지원(inline)</li>
            <li>mc/skins/list/basic 스킨 목록보기 초기화 버튼 지원 </li>
            <li>멀티카테고리 다중입력시 검색오류 fix</li>
        </ul>
    </li>
    <li>2020-02-14 (2.0.2)
        <ul>
            <li>브라우져 캐쉬문제 fix</li>
        </ul>
    </li>
    <li>2020-02-12 (2.0.1)
        <ul>
            <li>심볼릭링크 계정 출력안되는 문제 fix</li>
            <li>목록보기 page 파라메터 문제 fix</li>
            <li>관리자모드 스킨명 출력 셀렉트박스 fix</li>
            <li>모바일 목록보기 자동출력 fix</li>
        </ul>
    </li>
    <li>2020-02-11 (2.0)
        <ul>
            <li>신규설치만 지원(1.x 지원종료)</li>
            <li>카테고리 데이타 마지막에 "." 들어가는것 제거</li>
            <li>체크박스 or 검색 추가지원</li>
            <li>기본 확장컬럼 이외 컬럼 사용가능</li>
            <li>컬럼추가기능 제공</li>
            <li>input 타입 text, data, tel, number, url 지원</li>
            <li>카테고리 멀티 저장 제공</li>
            <li>그누 카테고리식 데이타 처리 기능 추가</li>
        </ul>
    </li>
    <li>2019-10-08 (1.2)
        <ul>
            <li>데이타 저장형식 변경</li>
            <li>체크박스 or 검색 추가</li>
            <li>작성양식과 검색양식을 다르게 설정 가능하도록 추가</li>
            <li>확장컬럼과 사용자에 의해 추가된 컬럼이 자동 추가되어 설정가능하도록 추가</li>
        </ul>
    </li>
	<li>2019-09-10 (1.2)
		<ul>
			<li>그누보드(5.3.3.2) 기본스킨 변경에 따른 basic-5332 출력스킨(내용보기, 글쓰기) 추가</li>
			<li>버그 수정</li>
		</ul>
	</li>
    <li>2017-08-10 (1.1)
        <ul>
            <li>카테고리 스킨 사용안함 설정가능하도록 추가
            <li>이미나보드 기본스킨 글쓰기 카테고리 스킨 추가 (amina.basic.php)
            <li>목록에서 검색시 멀티카테고리 필터링 유지 패치
            <li>필터링시 공지가 퐇마될경우 게시물번호 오류 패치
            <li>depth 에 따른 정렬문제 패치(JKWang)
            <li>셀렉트박스 ▒ 테코문자 제거
        </ul>
    </li>
    <li>
        2016.10.28 배포 (1.0)
    </li>
</ul>


<h2>기능</h2>

<ul>
<li>스킨수정없이 기본스킨에서 크게 벋어나지 않으면 모두 적용가능합니다
<li>확장카테고리이외에 사용자가 추가한 카테고리도 사용할 수 있습니다
<li>관리자모드에서 컬럼추가 기능이 추가되었습니다(컬럼은 수동으로 추가하셔도 상관없습니다)</li>
<li>스킨수정방시그로도 사용가능합니다    
<li>멀티카테고리값을 확장필드에 적용하고 검색할 수 있습니다
<li>그누보드식 카테고리값을 확장필드에 적용하고 검색할 수 있습니다
<li>멀티카테고리값을 한컬럼에 복수입력가능합니다
<li>그누보그식 카테고리값을 한컬럼에 복수입력가능합니다
<li>중앙관리식 멀티카테고리 (카테고리관리에서 추가되면 자동으로 스킨에도 적용됩니다)</li>
<li>input 형식의 입력을 지원합니다(text, tel, date, number, url, email)</li>
</ul>


<h2>설치방법 2.0 이상</h2>
<ol>
<li>압출해제후 mc 디렉토리를 그누보드 plugin 디렉토리에 업로드.
<li>/common.php 의 하단에
<pre>
include_once G5_PLUGIN_PATH.'/mc/common.hook.php';
</pre>
코드 추가

<li>/bbs/board.php 236~237
<pre>
//<<< MC Tools 가 설치되었고 확장겁색값이 있는경우 처리.
if ($member['mb_level'] >= $board['bo_list_level'] && $board['bo_use_list_view'] || empty($wr_id)){
	if(defined('MC') && $mc_search = mc_board($bo_table)->getSearchSql($_GET)){
		include_once MC_PLUGIN_PATH . '/bbs.list.php';
	}else{
		include_once (G5_BBS_PATH . '/list.php');
	}
}
//>>>
</pre>
코드 로 수정

<li>mc/adm 폴더안에 admin.menu800.php 파일을 /adm 폴더로 이동 (관리자모드 메뉴출력용)
<li>관리자모드 접속후 MC Tools 메뉴에서 설치.
</ol>


</body>
</html>