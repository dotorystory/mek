# qr-catalog-jp.php 파일을 루트에 업로드
큐알 생성 경로: https://www.mekeng.com/qr-catalog-jp.php
아래처럼 복잡하게 경로 설정할 필요 없음


# qr.php > 큐알코드 리다이렉트
# https://industrialmarket.biz/qr.php?utm_source=qr_code&utm_medium=redirect&utm_campaign=qr_namecard

******************************************

# ref.php > 디바이스 구분 후 리다이렉트
# https://industrialmarket.biz/ref.php?utm_source=viral_blog&utm_medium=outsourcing&utm_campaign=blog_naver&utm_platform=$platform


******************************************

다른 매체의 경우 큐알 링크 수정 방법 >
캠페인 분류를 통해 분석 결과를 볼 수 있으며,
링크의 해당 부분 ..._campaign= 다음에 있는 qr_namecard 부분만 변경하는 방법으로 사용

소스: utm_source= 
> qr_code 또는 viral_blog 등 수정해서 사용

매체: utm_medium= 
> redirect 또는 outsourcing 등 수정해서 사용 

캠페인: utm_campaign= 
> qr_namecard 또는 blog_naver 등 수정해서 사용

플랫폼: utm_platform=$platform [수정금지]
> android / ios / desktop 으로도 애널리틱스 보고서를 활용할 수 있음 