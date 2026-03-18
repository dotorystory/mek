# 공통 리소스 폴더 (Public Resources Directory)

## 📁 구조
```
/public/
├── css/              공통 CSS 파일
│   ├── style.css
│   ├── mo_style.css
│   ├── default.css
│   └── ...
└── img/              공통 이미지 파일
    ├── vi_bg01.png
    ├── logo.png
    └── ...
```

## 🌍 적용 범위
이 폴더의 모든 리소스는 다음 사이트에서 공통으로 사용됩니다:
- 한국어 (KO): `/home`
- 영어 (EN): `/en`
- 일본어 (JP): `/jp`
- 중국어 (CN): `/cn`

## ✏️ 관리 방법
- **CSS 수정**: `/public/css/` 파일 수정 → 모든 언어 사이트에 자동 반영
- **이미지 추가/수정**: `/public/img/` 폴더에 업로드 → 모든 사이트에서 사용 가능

## 📦 백업
각 언어 폴더의 기존 `/css/`, `/img/` 폴더는 백업으로 유지됩니다.

## 📅 생성일
2025-10-31
