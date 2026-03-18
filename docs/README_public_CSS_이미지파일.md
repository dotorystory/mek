# 공통 CSS 폴더 (Common CSS Directory)

## 📁 위치
`/var/www/html/mekeng.com/public/css/`

## 🌍 사용 언어
이 폴더의 CSS 파일들은 모든 다국어 사이트에서 공통으로 사용됩니다:
- 한국어 (KO) - `/home`
- 영어 (EN) - `/en`
- 일본어 (JP) - `/jp`
- 중국어 (CN) - `/cn`
- 스페인어 (ES) - `/es`
- 독일어 (DE) - `/de`

## ⚙️ 설정 [중요]
각 언어 폴더의 `config.php`에서 다음과 같이 설정되어 있습니다:
```php
define('G5_CSS_URL', preg_replace('#/(home|en|jp|cn|es|de)$#', '', G5_URL).'/public/css');
```

## 🖼️ 이미지 경로
CSS 파일 내의 모든 이미지 경로는 `/public/img/` 폴더를 참조합니다:
```css
/* 메인 비주얼 배경 */
background-image: url(../img/vi_bg01.png);

/* 기타 이미지 */
background: url('../img/captcha.png');
```

**경로 해석:**
- CSS 위치: `/public/css/style.css`
- `../` → 상위 디렉토리 `/public/`로 이동
- `img/` → `/public/img/` 폴더 참조

## ✏️ CSS 수정 방법
1. 이 폴더(`/public/css/`)의 CSS 파일을 수정합니다.
2. 한 번만 수정하면 모든 언어 사이트에 자동으로 적용됩니다.
3. 필요시 `extend/version.extend.php`의 `G5_CSS_VER`을 업데이트하여 캐시를 무효화합니다.

## 🖼️ 이미지 관리
1. `/public/img/` 폴더에서 모든 이미지를 관리합니다.
2. 이미지 추가/수정 시 `/public/img/`에만 업로드하면 됩니다.
3. CSS에서는 `../img/파일명` 형식으로 참조합니다.

## 📦 백업
- 각 언어 폴더의 `/css/` 폴더는 기존 그대로 유지 (CSS 백업)
- 각 언어 폴더의 `/img/` 폴더는 기존 그대로 유지 (이미지 백업)

## 🔄 롤백 방법
문제 발생 시 각 언어 폴더의 `config.php`를 다음과 같이 되돌립니다:
```php
define('G5_CSS_URL', G5_URL.'/'.G5_CSS_DIR);
```

## 📅 변경 이력
- **2025-10-31**: 공통 CSS 폴더 구조로 변경
  - 목적: 모든 언어 사이트의 CSS를 한 곳에서 관리
  - 변경자: AI Assistant
  - 백업: 각 언어 폴더의 기존 `/css/` 폴더 유지
  
- **2025-10-31**: 이미지 경로 1차 수정
  - 모든 CSS 파일의 이미지 경로를 `../../home/img/`로 수정
  - 이유: 공통 CSS 폴더 이동으로 인한 상대 경로 변경 필요
  
- **2025-10-31**: 공통 이미지 폴더 구조로 변경 ✨
  - 목적: CSS와 이미지를 `/public/` 폴더에서 통합 관리
  - `/public/img/` 폴더 생성 및 이미지 복사 (16MB)
  - 모든 CSS 파일의 이미지 경로를 `../img/`로 최종 단순화
  - 장점: 경로 단순화, 통합 관리, 유지보수 편의성 향상
  - 백업: 각 언어 폴더의 기존 `/img/` 폴더 유지

## 🎯 구조 요약
```
/public/
├── css/              ← 공통 CSS (모든 언어 공통)
│   ├── style.css
│   ├── mo_style.css
│   └── ...
└── img/              ← 공통 이미지 (모든 언어 공통)
    ├── vi_bg01.png
    ├── logo.png
    └── ...
```

**모든 수정은 `/public/` 폴더에서만 하면 됩니다!** 🚀
