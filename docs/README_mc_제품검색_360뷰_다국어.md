# MC 플러그인 다국어 사이트 검토 보고서

## 1. 현재 상황 분석

### 1.1 다국어 사이트 구조
- **이전**: home(kr), en, jp, cn 4개국어 사이트가 하나의 DB 공유
- **현재**: home(kr), en, jp, cn, de, es 6개국어 사이트가 각각 별도 DB 사용
  - home(kr): `mek_kr` DB
  - en: `mek_en` DB
  - jp: `mek_jp` DB
  - cn: `mek_cn` DB
  - de: `mek_de` DB 
  - es: `mek_es` DB 

### 1.2 게시판 테이블명 규칙
- **home(kr)**: `pro_ko` (또는 `pro`)
- **en**: `pro_en`
- **jp**: `pro_jp`
- **cn**: `pro_cn`
- **de**: `pro_en` (en 사이트 복사)
- **es**: `pro_en` (en 사이트 복사)

### 1.3 MC 플러그인 구조
각 언어별로 별도의 MC 플러그인 폴더 존재:
- `/home/plugin/mc/`
- `/en/plugin/mc/`
- `/jp/plugin/mc/`
- `/cn/plugin/mc/`
- `/de/plugin/mc/`
- `/es/plugin/mc/`

## 2. MC 설정 파일 분석

### 2.1 설정 파일 위치
각 언어별 `data/mc/` 폴더에 게시판별 설정 파일 저장:
- 형식: `{bo_table}.js`
- 예: `pro_en.js`, `pro_jp.js`, `pro_cn.js`, `pro_ko.js`, `pro_en.js`(스페인어), `pro_en.js`(독일어)

### 2.2 Type 및 Sensor 컬럼 설정 현황

| 언어 | 게시판 | Type 카테고리 ID | Sensor 카테고리 ID | 설정 파일 |
|------|--------|------------------|-------------------|-----------|
| home(kr) | pro_ko | 53 | 64 | `home/data/mc/pro_ko.js` |
| en | pro_en | 76 | 114 | `en/data/mc/pro_en.js` |
| jp | pro_jp | 87 | 127 | `jp/data/mc/pro_jp.js` |
| cn | pro_cn | 98 | 140 | `cn/data/mc/pro_cn.js` |
| de | pro_en | 76 | 114 | `de/data/mc/pro_en.js` |
| es | pro_en | 76 | 114 | `es/data/mc/pro_en.js` |

### 2.3 설정 파일 구조
```json
{
    "bo_table": "pro_en",
    "columns": {
        "wr_1": {
            "title": "Type",
            "data_type": "category",
            "data": "76",  // 카테고리 ID
            "searchable": "1",
            "list_type": "checkbox"
        },
        "wr_2": {
            "title": "Sensor",
            "data_type": "category",
            "data": "114",  // 카테고리 ID
            "searchable": "1",
            "list_type": "checkbox"
        }
    }
}
```

## 3. 문제점 및 이슈

### 3.1 de, es 사이트의 문제점
1. **게시판 테이블명**: `pro_en`을 그대로 사용 (언어별 구분 없음)
2. **MC 설정 파일**: en과 동일한 카테고리 ID(76, 114) 사용
3. **카테고리 데이터**: 각 언어별 DB(`mek_de`, `mek_es`)에 카테고리 데이터 존재 여부 불확실 -> 존재

### 3.2 잠재적 문제
- **카테고리 ID 불일치**: de, es의 DB에 카테고리 ID 76, 114가 존재하지 않을 수 있음
- **카테고리 데이터 부재**: 각 언어별 DB의 `mc_category` 테이블에 Type, Sensor 카테고리가 없을 수 있음 -> 존재
- **검색 기능 실패**: 카테고리 데이터가 없으면 type, sensor 검색이 작동하지 않음

## 4. 필요한 조치 사항

### 4.1 즉시 확인 필요 사항

#### 4.1.1 각 언어별 MC 카테고리 데이터 확인
각 언어별 관리자 페이지에서 확인:
- `/home/adm/plugin/mc/adm/list.php` - home(kr) 카테고리 확인
- `/en/adm/plugin/mc/adm/list.php` - en 카테고리 확인
- `/jp/adm/plugin/mc/adm/list.php` - jp 카테고리 확인
- `/cn/adm/plugin/mc/adm/list.php` - cn 카테고리 확인
- `/de/adm/plugin/mc/adm/list.php` - **de 카테고리 확인 필요** -> en 사이트와 동일
- `/es/adm/plugin/mc/adm/list.php` - **es 카테고리 확인 필요** -> en 사이트와 동일

**확인 항목**:
1. Type 카테고리 존재 여부 (ID: 76 또는 해당 언어별 ID)
2. Sensor 카테고리 존재 여부 (ID: 114 또는 해당 언어별 ID)
3. 카테고리 하위 항목들이 제대로 설정되어 있는지

#### 4.1.2 각 언어별 게시판 설정 확인
각 언어별 관리자 페이지에서 확인:
- `/home/adm/plugin/mc/adm/config.php?bo_table=pro_ko` - home(kr)
- `/en/adm/plugin/mc/adm/config.php?bo_table=pro_en` - en
- `/jp/adm/plugin/mc/adm/config.php?bo_table=pro_jp` - jp
- `/cn/adm/plugin/mc/adm/config.php?bo_table=pro_cn` - cn
- `/de/adm/plugin/mc/adm/config.php?bo_table=pro_en` - **de 확인 필요** -> en 사이트와 동일
- `/es/adm/plugin/mc/adm/config.php?bo_table=pro_en` - **es 확인 필요** -> en 사이트와 동일

**확인 항목**:
1. wr_1 (Type) 컬럼이 설정되어 있는지
2. wr_2 (Sensor) 컬럼이 설정되어 있는지
3. 각 컬럼의 `data` 값(카테고리 ID)이 올바른지
4. `searchable`이 "1"로 설정되어 있는지

#### 방안 1: 카테고리 데이터 확인 및 생성 (권장)
1. **de 사이트**:
   - `/de/adm/plugin/mc/adm/list.php` 접속
   - Type 카테고리(mc=76) 존재 확인
   - Sensor 카테고리(mc=114) 존재 확인
   - 없을 경우 en 사이트의 카테고리 구조를 참고하여 생성

2. **es 사이트**:
   - `/es/adm/plugin/mc/adm/list.php` 접속
   - Type 카테고리(mc=76) 존재 확인
   - Sensor 카테고리(mc=114) 존재 확인
   - 없을 경우 en 사이트의 카테고리 구조를 참고하여 생성

#### 방안 2: 카테고리 ID 재설정
만약 de, es의 DB에 카테고리 ID가 다르다면:
1. 각 언어별 DB에서 실제 Type, Sensor 카테고리 ID 확인
2. `/de/data/mc/pro_en.js` 및 `/es/data/mc/pro_en.js` 파일 수정
3. `data` 값을 실제 카테고리 ID로 변경

### 4.3 게시판 테이블 컬럼 확인
각 언어별 DB에서 게시판 테이블의 여분필드 컬럼 확인:
```sql
-- 예시: en 사이트 DB에서 확인
SHOW COLUMNS FROM g5_write_pro_en WHERE Field LIKE 'wr_%';
```

**확인 항목**:
- `wr_1`, `wr_2` 컬럼이 존재하는지
- 컬럼 타입이 `VARCHAR(255)`인지

## 5. MC 관리자 기능 사용 가이드

### 5.1 카테고리 관리
**경로**: `/adm/plugin/mc/adm/list.php`

**기능**:
- 카테고리 그룹 생성
- 카테고리 추가/수정/삭제
- 카테고리 순서 변경

**사용법**:
1. 관리자 페이지 접속
2. 플러그인 > MC > 카테고리 관리 메뉴 선택
3. Type, Sensor 카테고리 그룹 확인
4. 필요시 하위 카테고리 추가

### 5.2 게시판 설정
**경로**: `/adm/plugin/mc/adm/config.php?bo_table={게시판명}`


#### 5.2.1 de, es 사이트 조치 방안 [다국어 최초 복사 시 pro_ko, pro_en 두 곳에 wr_1, wr_2 중복설정되어 있음]
    - `/(각다국어폴더)/adm/plugin/mc/adm/config.php` 접속 ; /adm 폴더 접속 후 좌측 폴더 아이콘
    > 멀티카테고리관리 > 게시판관리 > 설정된컬럼 > pro_ko, pro_jp, pro_en 등 해당하는 곳 1곳에만 wr_1, wr_2 설정되어 있으면 됨.
    > wr_1, wr_2 중복 설정되어 있는 경우, 불필요한 pro_?? 항목의 우측 '설정' 버튼 진입하여 type, sensor 설정된 것 삭제.


**기능**:
- 게시판별 MC 컬럼 설정
- 검색 기능 활성화/비활성화
- 스킨 설정

**사용법**:
1. 관리자 페이지 접속
2. 플러그인 > MC > 게시판 설정 메뉴 선택
3. 게시판 선택 (예: pro_en)
4. wr_1 (Type), wr_2 (Sensor) 컬럼 설정 확인
5. 각 컬럼의 `data` 값이 올바른 카테고리 ID인지 확인

## 6. 검증 체크리스트

### 6.1 각 언어별 검증 항목

- [ ] MC 플러그인이 설치되어 있는가? (`mc_category` 테이블 존재)
- [ ] Type 카테고리가 존재하는가?
- [ ] Sensor 카테고리가 존재하는가?
- [ ] 게시판 설정 파일(`{bo_table}.js`)이 존재하는가?
- [ ] wr_1 (Type) 컬럼이 설정되어 있는가?
- [ ] wr_2 (Sensor) 컬럼이 설정되어 있는가?
- [ ] 각 컬럼의 `data` 값이 올바른 카테고리 ID인가?
- [ ] 각 컬럼의 `searchable`이 "1"로 설정되어 있는가?
- [ ] 게시판 테이블에 `wr_1`, `wr_2` 컬럼이 존재하는가?
- [ ] 실제 게시판에서 type, sensor 검색이 작동하는가?

### 6.2 de, es 사이트 특별 확인

- [ ] de 사이트 DB에 카테고리 ID 76 (Type) 존재 확인
- [ ] de 사이트 DB에 카테고리 ID 114 (Sensor) 존재 확인
- [ ] es 사이트 DB에 카테고리 ID 76 (Type) 존재 확인
- [ ] es 사이트 DB에 카테고리 ID 114 (Sensor) 존재 확인
- [ ] de, es 사이트에서 실제 검색 기능 테스트

## 7. 권장 사항

### 7.1 단기 조치
1. **즉시**: de, es 사이트의 MC 카테고리 데이터 확인
2. **즉시**: de, es 사이트의 게시판 설정 확인
3. **즉시**: 각 언어별 검색 기능 테스트

### 7.2 중장기 개선
1. **게시판 테이블명 통일**: de, es도 `pro_de`, `pro_es`로 변경 고려
2. **카테고리 데이터 동기화**: 각 언어별 카테고리 구조 일관성 유지
3. **설정 파일 관리**: 각 언어별 설정 파일의 카테고리 ID 자동 검증 기능 추가

## 8. 참고 정보

### 8.1 MC 플러그인 주요 파일
- **설정 파일**: `{언어}/data/mc/{bo_table}.js`
- **카테고리 관리**: `{언어}/adm/plugin/mc/adm/list.php`
- **게시판 설정**: `{언어}/adm/plugin/mc/adm/config.php`
- **라이브러리**: `{언어}/plugin/mc/lib/Board.php`, `Category.php`

### 8.2 카테고리 테이블 구조
```sql
CREATE TABLE mc_category (
    mc INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    parent_id INT(11) UNSIGNED NULL,
    lft INT(11) UNSIGNED NOT NULL,
    rgt INT(11) UNSIGNED NOT NULL,
    depth TINYINT(3) UNSIGNED NOT NULL,
    title VARCHAR(32) NOT NULL,
    path VARCHAR(255) NOT NULL,
    path_id VARCHAR(255),
    PRIMARY KEY (mc)
);
```

### 8.3 검색 기능 작동 원리
1. 게시판 목록에서 type, sensor 필터 선택
2. MC 플러그인이 선택된 값을 GET 파라미터로 받음
3. `Board::getSearchSql()` 메서드가 SQL 조건 생성
4. 카테고리 기반 검색: `FIND_IN_SET` 또는 `LIKE` 쿼리 사용
5. 결과 필터링 및 표시

---

**작성일**: 2024년
**검토 대상**: MC 플러그인 다국어 사이트 지원 현황
**다음 단계**: de, es 사이트 카테고리 데이터 확인 및 검증

