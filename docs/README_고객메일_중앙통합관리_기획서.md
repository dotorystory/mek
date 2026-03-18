# MEK Outlook PST 파일 중앙 DB 아카이브 시스템 기획서

## 📋 문서 정보
- **작성일**: 2024-12-30
- **프로젝트명**: MEK PST 아카이브 시스템 (MVP)
- **목적**: 사원별 Outlook PST 파일의 고객 메일을 중앙 DB에서 관리하여 중복 문제 해소 및 검색/감사 체계 구축

---

## 1. 프로젝트 배경 및 목표

### 1.1 현재 문제점
- **사원 PC별 PST 파일 분산**: 각 사원의 PC에 50GB 이상의 PST 파일이 개별 저장됨
- **중복 메일 문제**: 동일 고객에게 여러 사원이 보낸 메일이 각각의 PST에 중복 저장
- **검색 어려움**: 특정 고객과의 메일 히스토리를 확인하려면 여러 사원의 PC를 직접 확인해야 함
- **자산 관리 미흡**: 회사 차원의 메일 자산 관리 및 감사 추적 불가능
- **PC 용량 부족**: 대용량 PST 파일로 인한 사원 PC 디스크 공간 부족

### 1.2 프로젝트 목표
- ✅ **중앙 집중식 관리**: 모든 고객 메일을 중앙 DB에 통합 저장
- ✅ **중복 제거**: 동일 메일(Message-ID 기준)은 한 번만 저장
- ✅ **웹 기반 검색**: 그누보드 연동으로 웹에서 메일 검색/열람 가능
- ✅ **사원별 접근 제어**: 본인 메일만 조회 가능 (관리자는 전체 조회)
- ✅ **Outlook 의존도 감소**: 웹에서 메일 확인 가능하여 Outlook 필수 아님
- ✅ **PC 용량 문제 해소**: PST 파일을 DB로 이전하여 PC 디스크 공간 확보

### 1.3 MVP 범위 (1단계)
**포함 기능:**
- PST → EML 변환
- EML → DB 파싱 및 저장
- 중복 메일 제거 (Message-ID 기반)
- 웹 검색 UI (그누보드 연동)
- 사원별 메일 조회

**제외 기능 (향후 확장):**
- 첨부파일 저장
- 실시간 동기화
- 메일 서명 이미지 저장
- Outlook 자동 동기화

---

## 2. 시스템 아키텍처

### 2.1 전체 구조도

```
┌─────────────────────────────────────────────────────────────┐
│                    [사원 PC / 백업 PC]                       │
│                                                              │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐                  │
│  │ 사원1 PC │  │ 사원2 PC │  │ 사원N PC │                  │
│  │ *.pst    │  │ *.pst    │  │ *.pst    │                  │
│  │ (50GB)   │  │ (50GB)   │  │ (50GB)   │                  │
│  └──────────┘  └──────────┘  └──────────┘                  │
└──────────────────────┬─────────────────────────────────────┘
                       │
                       │ (1회 또는 배치 작업)
                       │ PST 파일 업로드
                       ↓
┌─────────────────────────────────────────────────────────────┐
│              [변환 서버 (PC2 또는 외부 서버)]                │
│                                                              │
│  ┌──────────────────────────────────────────┐              │
│  │  Step 1: PST → EML 변환                  │              │
│  │  - readpst (Linux)                       │              │
│  │  - pffexport (libpff)                    │              │
│  │  - Outlook + VBA (최후 수단)             │              │
│  └──────────────────────────────────────────┘              │
│                       │                                     │
│                       ↓                                     │
│  ┌──────────────────────────────────────────┐              │
│  │  Step 2: EML → DB 파싱                   │              │
│  │  - Python 또는 PHP 스크립트              │              │
│  │  - MIME 파싱                             │              │
│  │  - 중복 체크 (Message-ID)                │              │
│  │  - DB INSERT                             │              │
│  └──────────────────────────────────────────┘              │
└──────────────────────┬─────────────────────────────────────┘
                       │
                       ↓
┌─────────────────────────────────────────────────────────────┐
│              [외부 웹 서버 (mekeng.com)]                     │
│                                                              │
│  ┌──────────────────────────────────────────┐              │
│  │  MariaDB (메일 메타데이터)                │              │
│  │  - mail_archive 테이블                   │              │
│  │  - mail_archive_users 테이블             │              │
│  │  - 인덱스 최적화                          │              │
│  └──────────────────────────────────────────┘              │
│                                                              │
│  ┌──────────────────────────────────────────┐              │
│  │  웹 검색 UI (그누보드 연동)                │              │
│  │  - /plus/mail_archive/                   │              │
│  │  - 로그인 연동 (g5_member)               │              │
│  │  - 검색 기능 (제목/발신자/날짜)           │              │
│  │  - 본문 열람                              │              │
│  └──────────────────────────────────────────┘              │
└─────────────────────────────────────────────────────────────┘
```

### 2.2 데이터 흐름

1. **PST 파일 수집**: 사원 PC에서 PST 파일을 변환 서버로 전송 (USB, 네트워크 공유 등)
2. **EML 변환**: PST 파일을 표준 EML 형식으로 변환
3. **파싱 및 중복 체크**: EML 파일을 파싱하여 메일 정보 추출, Message-ID로 중복 확인
4. **DB 저장**: 중복이 아닌 메일만 DB에 저장
5. **웹 조회**: 그누보드 로그인 사용자가 웹에서 메일 검색 및 열람

---

## 3. 데이터베이스 스키마 설계

### 3.1 메인 테이블: `mail_archive`

**목적**: 모든 아카이브된 메일의 메타데이터와 본문 저장

```sql
CREATE TABLE `mail_archive` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '고유 ID',
  `message_id` VARCHAR(255) NOT NULL COMMENT 'Message-ID (중복 체크용)',
  `user_id` INT(11) NOT NULL COMMENT '사원 ID (g5_member.mb_id 참조)',
  `from_email` VARCHAR(255) NOT NULL COMMENT '발신자 이메일',
  `from_name` VARCHAR(255) DEFAULT NULL COMMENT '발신자 이름',
  `to_email` TEXT NOT NULL COMMENT '수신자 이메일 (쉼표 구분)',
  `cc_email` TEXT DEFAULT NULL COMMENT '참조 이메일 (쉼표 구분)',
  `bcc_email` TEXT DEFAULT NULL COMMENT '숨은 참조 이메일 (쉼표 구분)',
  `subject` VARCHAR(500) NOT NULL COMMENT '메일 제목',
  `body_text` MEDIUMTEXT DEFAULT NULL COMMENT '텍스트 본문',
  `body_html` MEDIUMTEXT DEFAULT NULL COMMENT 'HTML 본문',
  `sent_at` DATETIME NOT NULL COMMENT '발송일시',
  `received_at` DATETIME DEFAULT NULL COMMENT '수신일시',
  `pst_source` VARCHAR(255) DEFAULT NULL COMMENT '원본 PST 파일명',
  `eml_path` VARCHAR(500) DEFAULT NULL COMMENT 'EML 파일 경로 (참조용)',
  `has_attachment` TINYINT(1) DEFAULT 0 COMMENT '첨부파일 존재 여부',
  `attachment_count` INT(11) DEFAULT 0 COMMENT '첨부파일 개수',
  `mail_size` INT(11) DEFAULT 0 COMMENT '메일 크기 (bytes)',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'DB 저장일시',
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_message_id` (`message_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_from_email` (`from_email`),
  KEY `idx_to_email` (`to_email`(255)),
  KEY `idx_subject` (`subject`(255)),
  KEY `idx_sent_at` (`sent_at`),
  KEY `idx_received_at` (`received_at`),
  KEY `idx_created_at` (`created_at`),
  FULLTEXT KEY `ft_subject` (`subject`),
  FULLTEXT KEY `ft_body_text` (`body_text`),
  FULLTEXT KEY `ft_body_html` (`body_html`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='메일 아카이브 테이블';
```

**설명:**
- `message_id`: UNIQUE 제약으로 중복 메일 자동 방지
- `user_id`: 사원 식별 (g5_member.mb_id와 연동)
- `body_text`, `body_html`: MEDIUMTEXT로 최대 16MB까지 저장 가능
- FULLTEXT 인덱스: 제목/본문 검색 성능 향상
- `pst_source`, `eml_path`: 원본 추적용 (디버깅/감사)

### 3.2 사용자 매핑 테이블: `mail_archive_users`

**목적**: 사원과 메일의 다대다 관계 관리 (여러 사원이 동일 메일에 포함된 경우)

```sql
CREATE TABLE `mail_archive_users` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `mail_id` BIGINT(20) UNSIGNED NOT NULL COMMENT 'mail_archive.id',
  `user_id` INT(11) NOT NULL COMMENT '사원 ID (g5_member.mb_id)',
  `role` VARCHAR(20) DEFAULT 'recipient' COMMENT '역할: sender, recipient, cc, bcc',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_mail_user` (`mail_id`, `user_id`, `role`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_mail_id` (`mail_id`),
  CONSTRAINT `fk_mail_archive_users_mail` 
    FOREIGN KEY (`mail_id`) REFERENCES `mail_archive` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='메일-사원 매핑 테이블';
```

**설명:**
- 한 메일에 여러 사원이 포함된 경우 (To, Cc 등) 각각 기록
- `role`로 발신자/수신자 구분
- 사원이 조회 시 본인이 포함된 모든 메일 표시

### 3.3 처리 로그 테이블: `mail_archive_process_log`

**목적**: PST 변환 및 DB 저장 작업 로그

```sql
CREATE TABLE `mail_archive_process_log` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pst_filename` VARCHAR(255) NOT NULL COMMENT 'PST 파일명',
  `user_id` INT(11) NOT NULL COMMENT '사원 ID',
  `process_type` VARCHAR(20) NOT NULL COMMENT '처리 유형: pst_to_eml, eml_to_db',
  `total_count` INT(11) DEFAULT 0 COMMENT '전체 메일 수',
  `success_count` INT(11) DEFAULT 0 COMMENT '성공한 메일 수',
  `duplicate_count` INT(11) DEFAULT 0 COMMENT '중복 메일 수',
  `error_count` INT(11) DEFAULT 0 COMMENT '오류 메일 수',
  `started_at` DATETIME NOT NULL COMMENT '시작일시',
  `completed_at` DATETIME DEFAULT NULL COMMENT '완료일시',
  `status` VARCHAR(20) DEFAULT 'processing' COMMENT '상태: processing, completed, failed',
  `error_message` TEXT DEFAULT NULL COMMENT '오류 메시지',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_started_at` (`started_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='PST 처리 로그 테이블';
```

---

## 4. 중복 메일 제거 전략

### 4.1 중복 판단 기준

**1차 기준: Message-ID**
- RFC 5322 표준 메일 헤더
- 전 세계적으로 고유한 식별자
- 동일 메일은 동일 Message-ID를 가짐

**2차 기준 (Message-ID가 없는 경우)**
- 발신자 + 수신자 + 제목 + 발송일시 조합
- 해시값 생성하여 비교

### 4.2 중복 체크 프로세스

```
EML 파싱
  ↓
Message-ID 추출
  ↓
DB에서 Message-ID 검색
  ↓
존재 여부 확인
  ├─ 존재함 → 중복 메일 (건너뛰기, duplicate_count 증가)
  └─ 존재하지 않음 → 신규 메일 (DB 저장)
```

### 4.3 중복 메일 처리 옵션

**옵션 1: 완전 제거 (권장)**
- 중복 메일은 DB에 저장하지 않음
- `mail_archive_users`에만 추가 (여러 사원 PST에 동일 메일이 있는 경우)

**옵션 2: 메타데이터만 기록**
- 중복 메일도 `mail_archive_users`에 기록하여 "누가 이 메일을 가지고 있었는지" 추적

**MVP에서는 옵션 1 채택**

---

## 5. PST → DB 처리 흐름

### 5.1 Step 1: PST → EML 변환

**도구 선택:**
1. **readpst** (Linux, 무료)
   - 명령어: `readpst -o /output/dir input.pst`
   - 장점: 무료, 안정적
   - 단점: 대용량 PST 처리 시 시간 소요

2. **pffexport** (libpff 기반)
   - 더 빠른 처리 속도
   - 상세한 오류 로깅

3. **Outlook + VBA** (최후 수단)
   - Windows 환경에서만 가능
   - Outlook 설치 필수

**출력 구조:**
```
/pst-export/
  ├─ user1/
  │   ├─ 000001.eml
  │   ├─ 000002.eml
  │   └─ ...
  ├─ user2/
  │   ├─ 000001.eml
  │   └─ ...
  └─ ...
```

### 5.2 Step 2: EML → DB 파싱

**처리 단계:**

1. **EML 파일 읽기**
   - 파일 시스템에서 EML 파일 순회
   - MIME 파싱 라이브러리 사용 (Python: email, PHP: imap)

2. **헤더 파싱**
   - From, To, Cc, Bcc
   - Subject
   - Date (sent_at, received_at)
   - Message-ID

3. **본문 추출**
   - TEXT/HTML 본문 분리
   - 인코딩 변환 (UTF-8로 통일)
   - HTML 태그 정리 (선택사항)

4. **중복 체크**
   - Message-ID로 DB 조회
   - 중복이면 건너뛰기

5. **DB 저장**
   - `mail_archive` INSERT
   - `mail_archive_users` INSERT (발신자/수신자별)

6. **로그 기록**
   - `mail_archive_process_log` 업데이트

**배치 처리:**
- 한 번에 1000개씩 처리
- 트랜잭션 단위로 커밋
- 오류 발생 시 롤백 및 로그 기록

### 5.3 Step 3: 웹 검색 UI

**그누보드 연동:**
- 경로: `/plus/mail_archive/`
- 로그인 체크: `g5_member` 테이블 연동
- 권한: 본인 메일만 조회 (관리자는 전체 조회)

**검색 기능:**
- 제목 검색 (FULLTEXT)
- 발신자/수신자 이메일 검색
- 날짜 범위 검색
- 본문 내용 검색 (FULLTEXT)

**표시 항목:**
- 제목
- 발신자/수신자
- 발송일시
- 본문 미리보기
- 상세 열람 (모달 또는 별도 페이지)

---

## 6. 용량 추정 및 리소스 계획

### 6.1 PST 용량 구성 비율 (실무 평균)

| 구성 요소 | 비율 | 설명 |
|----------|------|------|
| 첨부파일 | 70~85% | PDF, 이미지, 영상 등 |
| HTML 서명/이미지 | 5~10% | 메일 서명에 포함된 이미지 |
| 메일 텍스트 본문 | 10~20% | 실제 텍스트 내용 |

### 6.2 50GB PST 기준 계산

**보수적 추정:**
- 메일 본문 + 제목 + 헤더: **15%**
- 50GB × 15% = **약 7.5GB** (RAW 텍스트)

**DB 저장 후 실제 사용량:**

| 항목 | 용량 | 비고 |
|------|------|------|
| `body_text` | 약 3~4GB | 텍스트 본문 |
| `body_html` | 약 2~3GB | HTML 본문 |
| 메타데이터 | < 500MB | 헤더 정보 |
| 인덱스 | 1~2GB | 검색 인덱스 |
| **총합** | **약 6~10GB** | InnoDB + utf8mb4 기준 |

### 6.3 최적화 옵션

**옵션 1: HTML 제거 (TEXT만 저장)**
- `body_html` 컬럼 미사용
- 용량: **약 4~5GB**로 감소

**옵션 2: DB 압축**
- InnoDB 압축 옵션 활성화
- 용량: **약 3~5GB**로 감소

**옵션 3: 오래된 메일 아카이빙**
- 5년 이상 된 메일은 별도 테이블로 이동
- 활성 테이블 용량 유지

### 6.4 서버 리소스 요구사항

**외부 웹 서버 (MVP):**

| 리소스 | 요구사항 | 비고 |
|--------|----------|------|
| 디스크 | 사원 1명당 5~10GB | 압축 시 3~5GB |
| RAM | 8GB 이상 권장 | DB 버퍼 풀용 |
| CPU | 변환 작업 시만 사용 | 평상시 낮음 |
| 네트워크 | 변환 서버와 연결 | FTP/SFTP 등 |

**10명 기준:**
- 디스크: **100GB** (여유 있게 150GB 권장)
- RAM: **8GB** 이상
- CPU: **2코어** 이상

---

## 7. 보안 및 접근 제어

### 7.1 접근 권한

**일반 사원:**
- 본인(`user_id`) 메일만 조회
- 검색/열람만 가능
- 수정/삭제 불가

**관리자:**
- 전체 사원 메일 조회 가능
- 통계/리포트 조회
- 시스템 설정 변경

### 7.2 데이터 보안

- **암호화**: 민감한 메일 본문은 필요 시 암호화 저장
- **로그 기록**: 조회 이력 기록 (감사 추적)
- **백업**: 정기적인 DB 백업
- **접근 로그**: IP, 시간, 조회한 메일 ID 기록

### 7.3 개인정보 보호

- **GDPR/개인정보보호법 준수**: 개인정보 포함 메일 처리 시 주의
- **보관 기간**: 회사 정책에 따른 보관 기간 설정
- **삭제 요청**: 법적 요구 시 메일 삭제 기능

---

## 8. 구현 단계별 계획

### Phase 1: 기반 구축 (1~2주)
- [ ] DB 스키마 생성
- [ ] PST → EML 변환 도구 설치 및 테스트
- [ ] EML 파싱 스크립트 개발 (Python/PHP)
- [ ] 중복 체크 로직 구현

### Phase 2: 변환 프로세스 (2~3주)
- [ ] 배치 처리 스크립트 개발
- [ ] 오류 처리 및 로깅
- [ ] 테스트 PST 파일로 검증
- [ ] 성능 최적화

### Phase 3: 웹 UI 개발 (2~3주)
- [ ] 그누보드 연동
- [ ] 검색 UI 개발
- [ ] 메일 열람 페이지
- [ ] 관리자 대시보드

### Phase 4: 실제 데이터 마이그레이션 (1~2주)
- [ ] 사원별 PST 파일 수집
- [ ] 순차적 변환 및 DB 저장
- [ ] 데이터 검증
- [ ] 사용자 교육

### Phase 5: 운영 및 모니터링 (지속)
- [ ] 성능 모니터링
- [ ] 사용자 피드백 수집
- [ ] 기능 개선

---

## 9. 리스크 및 대응 방안

### 9.1 기술적 리스크

| 리스크 | 영향도 | 대응 방안 |
|--------|--------|-----------|
| PST 파일 손상 | 높음 | 변환 전 PST 파일 무결성 검사, 백업 |
| 대용량 처리 시간 | 중간 | 배치 처리, 진행률 표시 |
| 중복 체크 성능 | 중간 | Message-ID 인덱스 최적화, 배치 조회 |
| DB 용량 부족 | 낮음 | 용량 모니터링, 오래된 데이터 아카이빙 |

### 9.2 운영 리스크

| 리스크 | 영향도 | 대응 방안 |
|--------|--------|-----------|
| 사원 협조 부족 | 중간 | 교육 및 가이드 문서 제공 |
| 데이터 유출 | 높음 | 접근 제어 강화, 로그 기록 |
| 성능 저하 | 중간 | 인덱스 최적화, 쿼리 튜닝 |

---

## 10. 향후 확장 계획 (Phase 2)

### 10.1 첨부파일 관리
- 첨부파일 별도 저장소 (파일 시스템 또는 객체 스토리지)
- DB에는 첨부파일 메타데이터만 저장
- 다운로드 기능

### 10.2 실시간 동기화
- Outlook 플러그인 개발
- 신규 메일 자동 동기화
- 변경사항 실시간 반영

### 10.3 고급 검색 기능
- 고객별 메일 히스토리 뷰
- 태그/라벨 기능
- 메일 통계 및 리포트

### 10.4 모바일 지원
- 반응형 웹 디자인
- 모바일 앱 (선택사항)

---

## 11. 성공 지표 (KPI)

### 11.1 정량적 지표
- PST 파일 변환 완료율: **100%**
- 중복 메일 제거율: **목표 30% 이상**
- DB 저장 성공률: **99% 이상**
- 검색 응답 시간: **3초 이내**

### 11.2 정성적 지표
- 사용자 만족도: 설문 조사
- PC 용량 절감 효과: 사원 피드백
- 메일 검색 편의성: 사용 빈도

---

## 12. 결론

본 기획서는 MEK의 Outlook PST 파일을 중앙 DB로 아카이브하는 MVP 시스템을 정의합니다. 

**핵심 가치:**
1. **중복 제거**: Message-ID 기반으로 동일 메일 한 번만 저장
2. **중앙 관리**: 모든 고객 메일을 한 곳에서 관리
3. **검색 편의성**: 웹에서 빠른 검색 및 열람
4. **확장 가능**: 향후 첨부파일, 실시간 동기화 등 확장 가능

**다음 단계:**
1. 프로젝트 승인 및 예산 확보
2. 개발 환경 구축
3. Phase 1 시작 (DB 스키마 생성 및 변환 도구 설치)

---

## 13. 메일 백업 및 복구 전략

> **참고**: 본 섹션은 서버 재해 복구 방안 문서의 메일 관련 내용을 통합한 것입니다. 상세한 서버 백업 및 복구 절차는 "README_외부서버_자동백업_및_재해복구_방안.md" 문서를 참조하세요.

### 13.1 현재 메일 시스템 현황

**메일 서버 구성:**

1. **다우 오피스 제공 메일 서버**
   - 도메인: `@mekeng.com`
   - 특성:
     - 저장 용량 제한 (많지 않음)
     - 자동 삭제 정책 적용
     - 사용자가 수동으로 백업 시 ZIP 파일로 다운로드
     - 백업 파일 내부: EML 형식 파일들이 압축되어 있음

2. **회사 웹사이트 서버 (Postfix 기반)**
   - 도메인: `@webmail.mekeng.com`, `@www.mekeng.com`
   - 서버: 카페24 서버 (Postfix 메일 서버)
   - 백업: 서버 백업 시 `/etc/postfix` 설정 파일 포함

**메일 백업 소스:**

1. **기존 Outlook 사용자들의 PST 파일**
   - 각 사원 PC에 저장된 Outlook PST 파일
   - 대용량 파일 (사원당 50GB 이상)
   - 과거 메일 히스토리 포함

2. **최신 메일 백업 파일 (EML 형식)**
   - 다우 오피스에서 사용자가 수동 백업한 ZIP 파일
   - ZIP 파일 내부: EML 형식 메일 파일들
   - 주기적 백업 필요 (자동 삭제 대비)
   - **저장 위치**: 웹 서버의 `/home/plus/mailer/` 폴더 또는 별도 폴더 (`/home/plus/mail_upload/`)
   - **업로드 방식**: FTP/SFTP 또는 웹 업로드

### 13.2 메일 백업 전략

**백업 저장소 구조:**

**웹 서버 내 저장소 (다우 메일 EML 백업):**

```
/var/www/html/mekeng.com/home/plus/mailer/
├─ upload/                      (사원별 업로드 폴더)
│   ├─ employee1/
│   │   ├─ 20250101_mail_backup.zip
│   │   ├─ 20250201_mail_backup.zip
│   │   └─ ...
│   ├─ employee2/
│   └─ ...
│
├─ processed/                   (처리 완료된 파일)
│   └─ ...
│
└─ logs/                        (처리 로그)
    └─ mail_import_YYYYMMDD.log
```

**로컬 백업 저장소 (선택사항):**

```
E:\backup\cafe24\mail\
├─ pst_archive\                 (기존 PST 파일 아카이브)
│   ├─ employee1\
│   │   └─ archive_YYYYMMDD.pst
│   ├─ employee2\
│   └─ ...
│
├─ eml_backup\                  (최신 EML 백업 파일 - 원본 보관)
│   ├─ employee1\
│   │   ├─ 20250101_mail_backup.zip
│   │   ├─ 20250201_mail_backup.zip
│   │   └─ ...
│   ├─ employee2\
│   └─ ...
│
└─ logs\                        (처리 로그)
    └─ mail_backup_YYYYMMDD.log
```

**백업 정책:**

1. **PST 파일 백업 (1회성)**
   - 각 사원으로부터 PST 파일 수집
   - 중앙 백업 저장소에 아카이브
   - 변환/처리 후 원본 PST 파일 보관 (참고용)

2. **EML ZIP 파일 백업 (주기적) - 다우 메일**
   - 주기: 월 1회 또는 분기 1회 (사원별 수동 백업)
   - **업로드 위치**: 웹 서버 `/home/plus/mailer/upload/{사원명}/` 또는 `/home/plus/mail_upload/{사원명}/`
   - 파일 명명 규칙: `{날짜}_mail_backup.zip` (예: `20250101_mail_backup.zip`)
   - 업로드 방법:
     - FTP/SFTP를 통한 파일 업로드
     - 웹 관리자 툴을 통한 파일 업로드
   - **처리 후**: 처리 완료된 파일은 `processed/` 폴더로 이동
   - 보관 기간: 최소 1년 (또는 회사 정책에 따라)

3. **Postfix 서버 설정 백업**
   - 서버 백업 시 자동 포함 (`/etc/postfix/main.cf`, `/etc/postfix/master.cf`)
   - 별도 백업 위치: 서버 백업과 동일 (`/var/www/.bak/backups/`)

### 13.3 메일 복구 전략

**재해 발생 시 복구 절차:**

**Step 1: Postfix 메일 서버 복구**
- 서버 재해 복구 문서의 Postfix 설정 복원 절차 참조
- `/etc/postfix` 설정 파일 복원
- Postfix 서비스 재시작 및 테스트

**Step 2: 메일 데이터 복구**

**2.1 EML ZIP 파일에서 메일 복구**

사원들이 제공한 최신 EML ZIP 백업 파일 활용:

```bash
# 1. EML ZIP 파일 압축 해제
cd /tmp/mail_restore
unzip employee1_20250101_mail_backup.zip -d employee1_eml/

# 2. EML 파일 확인
ls -la employee1_eml/
# 출력 예: 000001.eml, 000002.eml, ...

# 3. EML 파일을 메일 서버로 임포트 (선택사항)
# 또는 웹 메일 시스템으로 임포트
```

**2.2 PST 파일에서 메일 복구 (필요 시)**

기존 PST 파일이 있는 경우:

```bash
# 1. PST → EML 변환 (Linux 환경)
# readpst 도구 설치
sudo dnf install -y p7zip p7zip-plugins
# 또는 libpff 도구 사용

# 2. PST 파일 변환
readpst -o /tmp/pst_export employee1_archive.pst

# 3. 변환된 EML 파일 확인
ls -la /tmp/pst_export/
```

### 13.4 메일 업데이트 및 관리자 툴

**관리자 툴을 이용한 신규 메일 업데이트 방안:**

**웹 기반 관리자 툴 (그누보드 연동) - 권장 방식**

**위치 및 접근:**
- URL: `https://mekeng.com/home/plus/mailer/import/` (또는 `/home/plus/mail_import/`)
- 로그인: 관리자 계정 필요 (그누보드 로그인 연동)
- 저장 폴더: `/home/plus/mailer/upload/` 또는 `/home/plus/mail_upload/`

**주요 기능:**
1. **EML ZIP 파일 업로드**
   - 웹 인터페이스를 통한 파일 업로드
   - 또는 FTP/SFTP로 업로드된 파일 자동 감지 및 처리
   - 사원별 폴더에서 ZIP 파일 자동 검색

2. **자동 처리 프로세스**
   - ZIP 파일 자동 압축 해제
   - EML 파일 파싱 (Message-ID, From, To, Cc, Bcc, Subject, Date, Body 등)
   - 중복 메일 체크 (Message-ID 기반)
   - 중복되지 않는 메일만 DB 저장 (`mail_archive` 테이블)

3. **고객 메일 리스트 자동 업데이트**
   - 메일 저장 후 자동으로 고객 이메일 주소 추출
   - `g5_customer_mail_list` 테이블에 고객 정보 자동 업데이트
   - 최신 여부(`cml_is_latest`) 및 빈도 등급(`cml_frequency_level`) 자동 계산

4. **처리 로그 및 통계**
   - 처리 완료/실패 통계
   - 중복 메일 수, 신규 메일 수
   - 고객 리스트 업데이트 통계

**구현 예시:**

```
/var/www/html/mekeng.com/home/plus/mailer/import/
├─ index.php              (메일 임포트 관리자 페이지)
├─ upload.php             (ZIP 파일 업로드 처리)
├─ process.php            (업로드된 ZIP 파일 자동 처리)
├─ extract.php            (ZIP 압축 해제)
├─ parse_eml.php          (EML 파일 파싱)
├─ import_to_db.php       (DB 저장 처리)
├─ update_customer_list.php (고객 리스트 업데이트)
└─ logs.php               (처리 로그 조회)
```

**처리 프로세스 흐름:**

```
사원이 EML ZIP 파일 업로드
    ↓
/home/plus/mailer/upload/{사원명}/ 폴더에 저장
    ↓
관리자 툴에서 "처리 시작" 버튼 클릭 (또는 자동 감지)
    ↓
ZIP 파일 압축 해제
    ↓
EML 파일 파싱 (Message-ID, From, To, Subject, Date, Body 등)
    ↓
중복 체크 (Message-ID 기준, mail_archive 테이블 조회)
    ↓
중복되지 않는 메일만 mail_archive 테이블에 INSERT
    ↓
고객 이메일 주소 추출 (To, Cc, From 필드에서)
    ↓
g5_customer_mail_list 테이블 업데이트 (INSERT 또는 UPDATE)
    - 신규 고객: INSERT
    - 기존 고객: UPDATE (최종 교신일시, 교신 횟수 등)
    ↓
최신 여부 및 빈도 등급 자동 계산 및 업데이트
    ↓
처리 완료된 ZIP 파일을 processed/ 폴더로 이동
    ↓
처리 로그 기록 (성공/실패, 통계 등)
```

**옵션 2: 명령줄 스크립트**

**스크립트 위치:** `/usr/local/bin/mail_import.sh`

```bash
#!/bin/bash
# mail_import.sh - EML ZIP 파일 임포트 스크립트

MAIL_BACKUP_DIR="/mnt/e/backup/cafe24/mail/eml_backup"
PROCESSED_DIR="/mnt/e/backup/cafe24/mail/processed"
EXTRACT_DIR="/tmp/mail_extract"
LOG_FILE="/var/log/mail_import_$(date +%Y%m%d).log"

# 사원별 ZIP 파일 처리
for employee_dir in "$MAIL_BACKUP_DIR"/*/; do
    employee_name=$(basename "$employee_dir")
    echo "[INFO] Processing: $employee_name" >> "$LOG_FILE"
    
    for zip_file in "$employee_dir"*.zip; do
        if [ -f "$zip_file" ]; then
            echo "[INFO] Extracting: $zip_file" >> "$LOG_FILE"
            
            # ZIP 압축 해제
            unzip -q "$zip_file" -d "$EXTRACT_DIR/$employee_name/"
            
            # EML 파일 개수 확인
            eml_count=$(find "$EXTRACT_DIR/$employee_name" -name "*.eml" | wc -l)
            echo "[INFO] Found $eml_count EML files" >> "$LOG_FILE"
            
            # 여기서 DB 임포트 또는 파일 복사 처리
            # 예: PHP 스크립트 호출
            # php /var/www/html/admin/mail_import/import_eml.php "$EXTRACT_DIR/$employee_name"
            
            # 처리 완료 후 이동
            mkdir -p "$PROCESSED_DIR/$employee_name"
            mv "$zip_file" "$PROCESSED_DIR/$employee_name/"
        fi
    done
done

echo "[INFO] Mail import completed" >> "$LOG_FILE"
```

**옵션 3: PHP 기반 EML 파싱 및 DB 저장**

**스크립트:** `/var/www/html/admin/mail_import/import_eml.php`

```php
<?php
/**
 * EML 파일 임포트 스크립트
 * 사용법: php import_eml.php /path/to/eml/directory
 */

$eml_dir = $argv[1] ?? '/tmp/mail_extract';
$eml_files = glob("$eml_dir/*.eml");

foreach ($eml_files as $eml_file) {
    // EML 파일 파싱
    $message = mailparse_msg_parse_file($eml_file);
    
    // 헤더 추출
    $headers = mailparse_msg_get_part_data($message);
    
    // Message-ID 확인 (중복 체크)
    $message_id = $headers['headers']['message-id'] ?? '';
    
    // DB 조회 (중복 체크)
    // SELECT * FROM mail_archive WHERE message_id = ?
    
    // 중복이 아니면 DB 저장
    // INSERT INTO mail_archive (...)
    
    echo "Processed: $eml_file\n";
}
```

### 13.5 메일 복구 프로세스 (상세)

**사원별 메일 백업 파일 수집 및 처리:**

**1. 사원으로부터 백업 파일 수집**

- **다우 오피스 메일 백업:**
  1. 각 사원이 다우 오피스에서 메일 ZIP 백업 다운로드
  2. 파일 명명: `{사원명}_{날짜}_mail_backup.zip`
  3. 내부 PC E드라이브 특정 폴더에 저장
  4. 또는 네트워크 공유 폴더에 업로드

- **Outlook PST 파일 (기존):**
  1. 각 사원 PC에서 PST 파일 복사
  2. 중앙 백업 저장소에 아카이브
  3. 1회성 작업 (이미 수집된 경우 제외)

**2. 백업 파일 처리 및 저장**

```bash
# 사원별 백업 파일 정리
E:\backup\cafe24\mail\eml_backup\
├─ employee1\
│   ├─ 20250101_mail_backup.zip
│   ├─ 20250201_mail_backup.zip
│   └─ 20250301_mail_backup.zip
├─ employee2\
│   └─ ...
```

**3. 재해 발생 시 복구 절차**

```bash
# 1. 복구할 백업 파일 확인
ls -la /mnt/e/backup/cafe24/mail/eml_backup/employee1/

# 2. 최신 백업 파일 선택 (또는 특정 날짜 선택)
BACKUP_FILE="employee1_20250301_mail_backup.zip"

# 3. ZIP 파일 압축 해제
cd /tmp/mail_restore
unzip "/mnt/e/backup/cafe24/mail/eml_backup/employee1/$BACKUP_FILE" -d employee1_eml/

# 4. EML 파일 확인
ls -la employee1_eml/
find employee1_eml/ -name "*.eml" | wc -l  # EML 파일 개수 확인

# 5. 메일 복구 옵션:
# 옵션 A: 웹 메일 시스템으로 임포트 (관리자 툴 사용)
# 옵션 B: 메일 클라이언트로 직접 임포트 (Thunderbird, Outlook 등)
# 옵션 C: DB에 저장하여 웹에서 조회 (메일 아카이브 시스템)
```

**4. 관리자 툴을 이용한 메일 업데이트 (상세)**

**사용 방법:**

1. **파일 업로드**
   - 방법 A: 웹 관리자 페이지에서 직접 ZIP 파일 업로드
   - 방법 B: FTP/SFTP를 통해 `/home/plus/mailer/upload/{사원명}/` 폴더에 파일 업로드

2. **처리 실행**
   - 관리자 페이지에서 "메일 임포트 시작" 버튼 클릭
   - 또는 업로드된 파일 자동 감지 및 처리 (스케줄러 사용 시)

3. **처리 결과 확인**
   - 처리 로그 확인 (성공/실패 메일 수, 중복 메일 수 등)
   - 고객 리스트 업데이트 통계 확인
   - 처리 완료된 파일은 `processed/` 폴더로 자동 이동

**PHP 구현 예시 (import_to_db.php):**

```php
<?php
/**
 * EML 파일 임포트 및 고객 리스트 자동 업데이트
 */

// 1. EML ZIP 파일 처리
$upload_dir = G5_PATH . '/home/plus/mailer/upload/';
$processed_dir = G5_PATH . '/home/plus/mailer/processed/';

// 업로드된 ZIP 파일 검색
$zip_files = glob($upload_dir . '*/ *.zip');

foreach ($zip_files as $zip_file) {
    $employee_name = basename(dirname($zip_file));
    $extract_dir = sys_get_temp_dir() . '/mail_extract_' . uniqid();
    
    // ZIP 압축 해제
    $zip = new ZipArchive();
    if ($zip->open($zip_file) === TRUE) {
        $zip->extractTo($extract_dir);
        $zip->close();
    }
    
    // EML 파일 처리
    $eml_files = glob($extract_dir . '/*.eml');
    $success_count = 0;
    $duplicate_count = 0;
    $customer_emails = array();
    
    foreach ($eml_files as $eml_file) {
        // EML 파싱
        $message = mailparse_msg_parse_file($eml_file);
        $headers = mailparse_msg_get_part_data($message);
        
        $message_id = $headers['headers']['message-id'] ?? '';
        $from_email = $headers['headers']['from'] ?? '';
        $to_email = $headers['headers']['to'] ?? '';
        $subject = $headers['headers']['subject'] ?? '';
        $date = $headers['headers']['date'] ?? '';
        
        // 중복 체크
        $existing = sql_fetch("SELECT id FROM mail_archive WHERE message_id = '{$message_id}'");
        if ($existing) {
            $duplicate_count++;
            continue;
        }
        
        // mail_archive 테이블에 INSERT
        $sql = "INSERT INTO mail_archive (message_id, user_id, from_email, to_email, subject, sent_at, ...) 
                VALUES (...)";
        sql_query($sql);
        $success_count++;
        
        // 고객 이메일 주소 추출
        $to_emails = explode(',', $to_email);
        foreach ($to_emails as $email) {
            $email = trim($email);
            if (!empty($email) && !preg_match('/@mekeng\.com$/', $email)) {
                $customer_emails[] = $email;
            }
        }
    }
    
    // 고객 메일 리스트 업데이트
    foreach ($customer_emails as $email) {
        update_customer_mail_list($email, $date);
    }
    
    // 처리 완료된 파일 이동
    $processed_file = $processed_dir . $employee_name . '/' . basename($zip_file);
    mkdir(dirname($processed_file), 0755, true);
    rename($zip_file, $processed_file);
    
    // 로그 기록
    log_mail_import($employee_name, $success_count, $duplicate_count);
}

/**
 * 고객 메일 리스트 업데이트 함수
 */
function update_customer_mail_list($email, $contact_date) {
    $email = sql_escape_string($email);
    
    // 기존 고객 확인
    $existing = sql_fetch("SELECT cml_id, cml_contact_count, cml_last_contact_date 
                          FROM g5_customer_mail_list 
                          WHERE cml_email = '{$email}'");
    
    if ($existing) {
        // 업데이트
        $new_count = $existing['cml_contact_count'] + 1;
        $sql = "UPDATE g5_customer_mail_list 
                SET cml_contact_count = {$new_count},
                    cml_last_contact_date = '{$contact_date}',
                    cml_is_latest = CASE 
                        WHEN '{$contact_date}' >= DATE_SUB(NOW(), INTERVAL 6 MONTH) THEN 1 
                        ELSE 0 
                    END,
                    cml_frequency_level = CASE
                        WHEN {$new_count} >= 10 THEN 'high'
                        WHEN {$new_count} >= 3 THEN 'medium'
                        ELSE 'low'
                    END,
                    cml_updated_at = NOW()
                WHERE cml_email = '{$email}'";
        sql_query($sql);
    } else {
        // 신규 삽입
        $sql = "INSERT INTO g5_customer_mail_list 
                (cml_email, cml_contact_count, cml_last_contact_date, cml_first_contact_date,
                 cml_source, cml_status, cml_is_latest, cml_frequency_level, cml_created_at)
                VALUES ('{$email}', 1, '{$contact_date}', '{$contact_date}',
                        'mail_archive', 'active', 
                        CASE WHEN '{$contact_date}' >= DATE_SUB(NOW(), INTERVAL 6 MONTH) THEN 1 ELSE 0 END,
                        'low', NOW())";
        sql_query($sql);
    }
}
?>
```

### 13.6 MEK+ 메일러용 고객 메일 리스트 정리/확보/업데이트 방안

**목적:**
메일 아카이브 DB(`mail_archive`)에 저장된 메일 데이터에서 고객 이메일 주소를 추출하고, 최신 여부와 빈도 분석을 통해 MEK+ 메일러 발송 대상 고객 리스트를 체계적으로 관리합니다.

**13.6.1 고객 메일 리스트 DB 테이블 설계**

**고객 메일 리스트 테이블: `g5_customer_mail_list`**

```sql
CREATE TABLE `g5_customer_mail_list` (
  `cml_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '고유 ID',
  `cml_email` VARCHAR(255) NOT NULL COMMENT '고객 이메일 주소',
  `cml_name` VARCHAR(255) DEFAULT NULL COMMENT '고객 이름 (메일에서 추출)',
  `cml_company` VARCHAR(255) DEFAULT NULL COMMENT '회사명 (메일에서 추출)',
  `cml_last_contact_date` DATETIME DEFAULT NULL COMMENT '최종 교신일시',
  `cml_contact_count` INT(11) NOT NULL DEFAULT 0 COMMENT '교신 횟수 (빈도)',
  `cml_first_contact_date` DATETIME DEFAULT NULL COMMENT '최초 교신일시',
  `cml_last_sent_date` DATETIME DEFAULT NULL COMMENT '최종 발송일시 (MEK+ 메일러)',
  `cml_send_count` INT(11) NOT NULL DEFAULT 0 COMMENT '발송 횟수 (MEK+ 메일러)',
  `cml_status` VARCHAR(20) NOT NULL DEFAULT 'active' COMMENT '상태: active, inactive, unsubscribed',
  `cml_is_latest` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '최신 여부 (최근 N개월 내 교신)',
  `cml_frequency_level` VARCHAR(20) DEFAULT NULL COMMENT '빈도 등급: high, medium, low',
  `cml_source` VARCHAR(50) DEFAULT NULL COMMENT '출처: mail_archive, manual, csv',
  `cml_tags` TEXT DEFAULT NULL COMMENT '태그 (JSON 형식)',
  `cml_notes` TEXT DEFAULT NULL COMMENT '메모',
  `cml_created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '생성일시',
  `cml_updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일시',
  PRIMARY KEY (`cml_id`),
  UNIQUE KEY `idx_email` (`cml_email`),
  KEY `idx_status` (`cml_status`),
  KEY `idx_last_contact_date` (`cml_last_contact_date`),
  KEY `idx_is_latest` (`cml_is_latest`),
  KEY `idx_frequency_level` (`cml_frequency_level`),
  KEY `idx_created_at` (`cml_created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='MEK+ 메일러용 고객 메일 리스트';
```

**13.6.2 메일 아카이브 DB에서 고객 메일 추출**

**추출 대상:**
- `mail_archive` 테이블의 `to_email`, `cc_email`, `from_email` 필드
- 회사 도메인(`@mekeng.com`) 제외
- 내부 사원 이메일 제외
- 중복 제거 (이메일 주소 기준)

**SQL 쿼리 예시:**

```sql
-- 메일 아카이브에서 고객 이메일 추출 (수신자 기준)
SELECT DISTINCT 
    TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(ma.to_email, ',', n.n), ',', -1)) AS customer_email,
    COUNT(*) AS contact_count,
    MAX(ma.sent_at) AS last_contact_date,
    MIN(ma.sent_at) AS first_contact_date
FROM mail_archive ma
CROSS JOIN (
    SELECT 1 n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5
    UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10
) n
WHERE ma.to_email IS NOT NULL 
    AND TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(ma.to_email, ',', n.n), ',', -1)) != ''
    AND TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(ma.to_email, ',', n.n), ',', -1)) NOT LIKE '%@mekeng.com'
    AND TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(ma.to_email, ',', n.n), ',', -1)) NOT LIKE '%@webmail.mekeng.com'
    AND TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(ma.to_email, ',', n.n), ',', -1)) NOT LIKE '%@www.mekeng.com'
GROUP BY customer_email
HAVING customer_email REGEXP '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$';
```

**13.6.3 최신 여부 판단 기준**

**최신 여부 (`cml_is_latest`) 판단:**
- 최근 6개월(또는 12개월) 내 메일 교신이 있는 경우: `1`
- 그 외: `0`

**SQL 업데이트:**

```sql
-- 최신 여부 업데이트 (최근 6개월 기준)
UPDATE g5_customer_mail_list
SET cml_is_latest = CASE 
    WHEN cml_last_contact_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH) THEN 1
    ELSE 0
END;
```

**13.6.4 빈도 분석 및 등급 부여**

**빈도 등급 (`cml_frequency_level`) 기준:**

| 등급 | 기준 | 설명 |
|------|------|------|
| **high** | 10회 이상 | 활발한 교신 고객 |
| **medium** | 3~9회 | 일반 교신 고객 |
| **low** | 1~2회 | 드문 교신 고객 |

**SQL 업데이트:**

```sql
-- 빈도 등급 업데이트
UPDATE g5_customer_mail_list
SET cml_frequency_level = CASE
    WHEN cml_contact_count >= 10 THEN 'high'
    WHEN cml_contact_count >= 3 THEN 'medium'
    ELSE 'low'
END;
```

**13.6.5 고객 메일 리스트 업데이트 프로세스**

**Step 1: 메일 아카이브에서 고객 이메일 추출**

```php
<?php
/**
 * 메일 아카이브에서 고객 메일 리스트 추출 및 업데이트
 * 실행: php extract_customer_mail_list.php
 */

// 메일 아카이브에서 고객 이메일 추출
$sql = "SELECT DISTINCT 
    TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(ma.to_email, ',', n.n), ',', -1)) AS customer_email,
    COUNT(*) AS contact_count,
    MAX(ma.sent_at) AS last_contact_date,
    MIN(ma.sent_at) AS first_contact_date
FROM mail_archive ma
CROSS JOIN (
    SELECT 1 n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5
    UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10
) n
WHERE ma.to_email IS NOT NULL 
    AND TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(ma.to_email, ',', n.n), ',', -1)) != ''
    AND TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(ma.to_email, ',', n.n), ',', -1)) NOT LIKE '%@mekeng.com'
GROUP BY customer_email
HAVING customer_email REGEXP '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,}$'";

$result = sql_query($sql);
$customers = array();

while ($row = sql_fetch_array($result)) {
    $customers[] = $row;
}

// 고객 리스트 업데이트 또는 삽입
foreach ($customers as $customer) {
    $email = sql_escape_string($customer['customer_email']);
    
    // 기존 고객 확인
    $existing = sql_fetch("SELECT cml_id, cml_contact_count FROM g5_customer_mail_list WHERE cml_email = '{$email}'");
    
    if ($existing) {
        // 업데이트
        $sql_update = "UPDATE g5_customer_mail_list 
                       SET cml_contact_count = {$customer['contact_count']},
                           cml_last_contact_date = '{$customer['last_contact_date']}',
                           cml_first_contact_date = COALESCE(cml_first_contact_date, '{$customer['first_contact_date']}'),
                           cml_source = 'mail_archive',
                           cml_updated_at = NOW()
                       WHERE cml_email = '{$email}'";
        sql_query($sql_update);
    } else {
        // 신규 삽입
        $sql_insert = "INSERT INTO g5_customer_mail_list 
                       (cml_email, cml_contact_count, cml_last_contact_date, cml_first_contact_date, 
                        cml_source, cml_status, cml_created_at) 
                       VALUES ('{$email}', {$customer['contact_count']}, 
                               '{$customer['last_contact_date']}', '{$customer['first_contact_date']}',
                               'mail_archive', 'active', NOW())";
        sql_query($sql_insert);
    }
}

// 최신 여부 및 빈도 등급 업데이트
sql_query("UPDATE g5_customer_mail_list SET cml_is_latest = CASE 
    WHEN cml_last_contact_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH) THEN 1 ELSE 0 END");

sql_query("UPDATE g5_customer_mail_list SET cml_frequency_level = CASE
    WHEN cml_contact_count >= 10 THEN 'high'
    WHEN cml_contact_count >= 3 THEN 'medium'
    ELSE 'low' END");

echo "고객 메일 리스트 업데이트 완료\n";
?>
```

**Step 2: MEK+ 메일러와 연동**

**MEK+ 메일러에 고객 리스트 옵션 추가:**

```php
// mailer/index.php 수정 예시
case 'customer':
    // g5_customer_mail_list 테이블에서 고객 이메일 가져오기
    $filter_status = isset($_POST['customer_status']) ? $_POST['customer_status'] : 'active';
    $filter_latest = isset($_POST['customer_latest']) ? intval($_POST['customer_latest']) : -1;
    $filter_frequency = isset($_POST['customer_frequency']) ? $_POST['customer_frequency'] : '';
    
    $sql = "SELECT cml_email FROM g5_customer_mail_list WHERE cml_status = '{$filter_status}'";
    
    if ($filter_latest >= 0) {
        $sql .= " AND cml_is_latest = {$filter_latest}";
    }
    
    if (!empty($filter_frequency)) {
        $sql .= " AND cml_frequency_level = '{$filter_frequency}'";
    }
    
    $result = sql_query($sql);
    while ($row = sql_fetch_array($result)) {
        $emails[] = $row['cml_email'];
    }
    
    // 발송 후 발송 통계 업데이트
    if ($success) {
        sql_query("UPDATE g5_customer_mail_list 
                   SET cml_last_sent_date = NOW(), 
                       cml_send_count = cml_send_count + 1 
                   WHERE cml_email = '{$email}'");
    }
    break;
```

**13.6.6 고객 메일 리스트 관리 화면**

**관리자 페이지 기능:**

- **고객 리스트 조회 및 필터링:**
  - 상태별 (active, inactive, unsubscribed)
  - 최신 여부 (최근 6개월 내 교신 여부)
  - 빈도 등급 (high, medium, low)
  - 검색 (이메일, 이름, 회사명)

- **통계 및 분석:**
  - 총 고객 수
  - 등급별 고객 수
  - 최신 고객 수
  - 발송 통계 (발송 횟수, 최종 발송일)

- **리스트 내보내기:**
  - CSV 파일 다운로드
  - 필터링된 리스트만 내보내기
  - MEK+ 메일러 발송용 형식

**13.6.7 정기 업데이트 프로세스**

**자동 업데이트 스크립트 (cron):**

```bash
#!/bin/bash
# update_customer_mail_list.sh
# 실행 주기: 주 1회 (일요일 새벽)

cd /var/www/html/mekeng.com

# PHP 스크립트 실행
php extract_customer_mail_list.php >> /var/log/customer_mail_list_update.log 2>&1

# 로그 확인
echo "[$(date)] 고객 메일 리스트 업데이트 완료" >> /var/log/customer_mail_list_update.log
```

**cron 설정:**

```bash
# 주 1회 업데이트 (일요일 새벽 2시)
0 2 * * 0 /usr/local/bin/update_customer_mail_list.sh
```

**13.6.8 고객 메일 리스트 활용 방안**

**MEK+ 메일러 발송 시 활용:**

1. **전체 고객 발송:**
   - 모든 active 상태 고객
   - 신규 제품 안내, 이벤트 안내 등

2. **최신 고객 발송:**
   - `cml_is_latest = 1` 고객만
   - 최근 교신한 고객 대상 마케팅

3. **빈도 등급별 발송:**
   - High: VIP 고객 대상 특별 안내
   - Medium: 일반 고객 대상 일반 안내
   - Low: 재참여 유도 메일

4. **조합 발송:**
   - 최신 + High: 최근 활발히 교신한 핵심 고객
   - 최신 + Medium: 최근 교신한 일반 고객

### 13.7 리멤버 서비스 고객 정보 중앙 DB 업데이트 방안

**목적:**
리멤버(Remember) CRM 서비스에서 관리 중인 고객 정보를 중앙 고객 메일 리스트 DB(`g5_customer_mail_list`)에 통합하여 메일 아카이브 데이터와 함께 활용할 수 있도록 합니다.

**13.7.1 리멤버 서비스 개요**

**특성:**
- 회사에서 사용 중인 CRM 서비스
- 고객 연락처 정보 관리 (이메일, 전화번호, 회사명, 이름 등)
- 고객과의 교신 이력 관리
- 다양한 데이터 추출 방법 제공 (API, CSV 내보내기 등)

**13.7.2 데이터 추출 방법**

**옵션 1: CSV 내보내기 (권장 - 간단하고 안정적)**

**절차:**
1. 리멤버 서비스에서 고객 정보 CSV 파일 내보내기
2. CSV 파일 형식:
   ```
   이메일,이름,회사명,전화번호,최종연락일,메모,...
   customer@example.com,홍길동,ABC회사,010-1234-5678,2025-01-01,...
   ```

3. CSV 파일 업로드:
   - 웹 관리자 툴을 통해 CSV 파일 업로드
   - 또는 FTP/SFTP로 `/home/plus/mailer/upload/csv/` 폴더에 업로드

**옵션 2: API 연동 (자동화 가능)**

**요구사항:**
- 리멤버 API 키 및 인증 정보
- API 엔드포인트 확인
- 데이터 형식 확인 (JSON/XML)

**구현 방식:**
- PHP 스크립트로 API 호출
- 주기적 동기화 (cron 작업)
- 변경사항만 업데이트 (증분 동기화)

**옵션 3: 데이터베이스 직접 접근 (제한적)**

- 리멤버가 자체 호스팅 서비스인 경우에만 가능
- 외부 API를 통한 접근이 불가능한 경우 고려

**13.7.3 CSV 파일 기반 업데이트 프로세스**

**Step 1: CSV 파일 업로드**

- 경로: `/home/plus/mailer/upload/csv/remember_export_YYYYMMDD.csv`
- 파일 형식: UTF-8 인코딩, 쉼표 구분(CSV)
- 필수 컬럼: 이메일 주소 (다른 정보는 선택사항)

**Step 2: CSV 파싱 및 DB 업데이트**

**PHP 구현 예시:**

```php
<?php
/**
 * 리멤버 CSV 파일에서 고객 정보 추출 및 DB 업데이트
 * 실행: php import_remember_customer.php /path/to/remember_export.csv
 */

$csv_file = $argv[1] ?? G5_PATH . '/home/plus/mailer/upload/csv/remember_export.csv';

if (!file_exists($csv_file)) {
    die("CSV 파일이 존재하지 않습니다: $csv_file\n");
}

$handle = fopen($csv_file, 'r');
if ($handle === FALSE) {
    die("CSV 파일을 열 수 없습니다: $csv_file\n");
}

// 헤더 읽기 (첫 번째 줄)
$headers = fgetcsv($handle);
$email_col = array_search('이메일', $headers); // 또는 'email', 'Email' 등
$name_col = array_search('이름', $headers);
$company_col = array_search('회사명', $headers);
$phone_col = array_search('전화번호', $headers);
$last_contact_col = array_search('최종연락일', $headers);

if ($email_col === FALSE) {
    die("CSV 파일에 '이메일' 컬럼이 없습니다.\n");
}

$import_count = 0;
$update_count = 0;
$skip_count = 0;

// 데이터 라인 처리
while (($data = fgetcsv($handle)) !== FALSE) {
    if (count($data) < $email_col + 1) {
        continue; // 데이터가 부족한 라인 건너뛰기
    }
    
    $email = trim($data[$email_col]);
    
    // 이메일 주소 유효성 검사
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $skip_count++;
        continue;
    }
    
    // 회사 도메인 제외
    if (preg_match('/@mekeng\.com$/', $email)) {
        $skip_count++;
        continue;
    }
    
    $name = isset($name_col) && isset($data[$name_col]) ? trim($data[$name_col]) : NULL;
    $company = isset($company_col) && isset($data[$company_col]) ? trim($data[$company_col]) : NULL;
    $phone = isset($phone_col) && isset($data[$phone_col]) ? trim($data[$phone_col]) : NULL;
    $last_contact = isset($last_contact_col) && isset($data[$last_contact_col]) ? trim($data[$last_contact_col]) : NULL;
    
    $email_escaped = sql_escape_string($email);
    
    // 기존 고객 확인
    $existing = sql_fetch("SELECT cml_id, cml_contact_count, cml_last_contact_date 
                          FROM g5_customer_mail_list 
                          WHERE cml_email = '{$email_escaped}'");
    
    if ($existing) {
        // 업데이트
        $update_sql = "UPDATE g5_customer_mail_list SET ";
        $update_fields = array();
        
        if (!empty($name)) {
            $update_fields[] = "cml_name = '" . sql_escape_string($name) . "'";
        }
        if (!empty($company)) {
            $update_fields[] = "cml_company = '" . sql_escape_string($company) . "'";
        }
        if (!empty($last_contact)) {
            $update_fields[] = "cml_last_contact_date = '" . sql_escape_string($last_contact) . "'";
            $update_fields[] = "cml_is_latest = CASE 
                WHEN '" . sql_escape_string($last_contact) . "' >= DATE_SUB(NOW(), INTERVAL 6 MONTH) THEN 1 
                ELSE 0 
            END";
        }
        
        $update_fields[] = "cml_source = 'remember'";
        $update_fields[] = "cml_updated_at = NOW()";
        
        $update_sql .= implode(', ', $update_fields);
        $update_sql .= " WHERE cml_email = '{$email_escaped}'";
        
        sql_query($update_sql);
        $update_count++;
    } else {
        // 신규 삽입
        $last_contact_date = !empty($last_contact) ? "'" . sql_escape_string($last_contact) . "'" : "NULL";
        $is_latest = !empty($last_contact) && strtotime($last_contact) >= strtotime('-6 months') ? 1 : 0;
        
        $insert_sql = "INSERT INTO g5_customer_mail_list 
                      (cml_email, cml_name, cml_company, cml_last_contact_date, 
                       cml_contact_count, cml_first_contact_date, cml_source, 
                       cml_status, cml_is_latest, cml_frequency_level, cml_created_at)
                      VALUES (
                          '{$email_escaped}',
                          " . (!empty($name) ? "'" . sql_escape_string($name) . "'" : "NULL") . ",
                          " . (!empty($company) ? "'" . sql_escape_string($company) . "'" : "NULL") . ",
                          {$last_contact_date},
                          0,
                          {$last_contact_date},
                          'remember',
                          'active',
                          {$is_latest},
                          'low',
                          NOW()
                      )";
        
        sql_query($insert_sql);
        $import_count++;
    }
}

fclose($handle);

echo "리멤버 고객 정보 임포트 완료\n";
echo "- 신규 추가: {$import_count}건\n";
echo "- 업데이트: {$update_count}건\n";
echo "- 건너뜀: {$skip_count}건\n";
?>
```

**Step 3: 웹 관리자 툴 통합**

**관리자 페이지 기능:**
- CSV 파일 업로드 인터페이스
- 업로드된 CSV 파일 목록 표시
- "리멤버 고객 정보 임포트" 버튼
- 임포트 결과 통계 표시 (신규/업데이트/건너뜀 건수)
- 처리 로그 조회

**13.7.4 API 연동 방식 (선택사항)**

**구현 예시:**

```php
<?php
/**
 * 리멤버 API를 통한 고객 정보 동기화
 * 실행: php sync_remember_api.php
 */

// 리멤버 API 설정
$api_key = 'YOUR_REMEMBER_API_KEY';
$api_endpoint = 'https://api.remember.com/v1/customers';
$api_secret = 'YOUR_REMEMBER_API_SECRET';

// API 인증 및 데이터 가져오기
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer ' . $api_key,
    'Content-Type: application/json'
));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200) {
    die("API 호출 실패: HTTP {$http_code}\n");
}

$customers = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("JSON 파싱 오류: " . json_last_error_msg() . "\n");
}

// 각 고객 정보 DB 업데이트
foreach ($customers['data'] as $customer) {
    $email = $customer['email'] ?? '';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        continue;
    }
    
    // DB 업데이트 로직 (CSV 방식과 동일)
    update_customer_from_remember($customer);
}

echo "리멤버 API 동기화 완료\n";
?>
```

**13.7.5 정기 동기화 프로세스**

**자동화 스크립트 (cron):**

```bash
#!/bin/bash
# sync_remember_customer.sh
# 실행 주기: 주 1회 (일요일 새벽 3시)

cd /var/www/html/mekeng.com

# 옵션 1: CSV 파일 자동 처리
if [ -f "/home/plus/mailer/upload/csv/remember_export.csv" ]; then
    php import_remember_customer.php /home/plus/mailer/upload/csv/remember_export.csv \
        >> /var/log/remember_sync.log 2>&1
    
    # 처리 완료된 파일 이동
    mv /home/plus/mailer/upload/csv/remember_export.csv \
       /home/plus/mailer/processed/csv/remember_export_$(date +%Y%m%d).csv
fi

# 옵션 2: API 동기화 (API 연동 시)
# php sync_remember_api.php >> /var/log/remember_sync.log 2>&1

echo "[$(date)] 리멤버 고객 정보 동기화 완료" >> /var/log/remember_sync.log
```

**cron 설정:**

```bash
# 주 1회 동기화 (일요일 새벽 3시)
0 3 * * 0 /usr/local/bin/sync_remember_customer.sh
```

**13.7.6 데이터 통합 전략**

**출처(source) 구분:**
- `cml_source` 필드로 데이터 출처 구분:
  - `mail_archive`: 메일 아카이브에서 추출
  - `remember`: 리멤버 서비스에서 임포트
  - `manual`: 수동 입력
  - `csv`: CSV 파일 임포트

**중복 처리:**
- 동일 이메일 주소가 여러 출처에서 들어오는 경우:
  - 가장 최근 정보로 업데이트
  - `cml_source` 필드에 최신 출처 기록
  - 또는 여러 출처를 JSON 형식으로 저장

**데이터 우선순위:**
1. **메일 아카이브 데이터**: 교신 횟수(`cml_contact_count`) 정확
2. **리멤버 데이터**: 이름, 회사명 등 상세 정보 보완
3. **수동 입력 데이터**: 관리자가 직접 보정한 정보

**13.7.7 활용 방안**

**통합 고객 리스트 활용:**
- MEK+ 메일러에서 메일 아카이브와 리멤버 고객 정보를 통합하여 활용
- 출처별 필터링: `cml_source = 'remember'` 또는 `cml_source = 'mail_archive'`
- 통합 검색: 이름, 회사명, 이메일로 검색

**데이터 보완:**
- 메일 아카이브에는 이메일만 있는 경우, 리멤버에서 이름/회사명 보완
- 리멤버에는 이름만 있는 경우, 메일 아카이브에서 최신 교신일시 업데이트

### 13.8 메일 백업 및 복구 체크리스트

**정기 백업:**
- [ ] 사원별 EML ZIP 백업 파일 수집 (월 1회 또는 분기 1회)
- [ ] 백업 파일을 웹 서버 `/home/plus/mailer/upload/` 폴더에 업로드 (또는 로컬 백업 저장소에 저장)
- [ ] 백업 파일 무결성 확인 (ZIP 파일 손상 여부)
- [ ] 백업 파일 목록 기록 (사원별, 날짜별)

**재해 복구 시:**
- [ ] Postfix 메일 서버 설정 복원 (`/etc/postfix/`)
- [ ] 복구할 EML ZIP 백업 파일 선택
- [ ] ZIP 파일 압축 해제
- [ ] EML 파일 개수 및 상태 확인
- [ ] 메일 복구 방법 선택 (웹 임포트 / 클라이언트 임포트 / DB 저장)
- [ ] 관리자 툴을 이용한 메일 업데이트 (선택사항)
- [ ] 복구된 메일 검증

**관리자 툴 운영:**
- [ ] 웹 관리자 페이지 접근 확인 (`/home/plus/mailer/import/`)
- [ ] ZIP 파일 업로드 기능 테스트 (`/home/plus/mailer/upload/` 폴더)
- [ ] EML 파싱 및 DB 저장 프로세스 확인
- [ ] 중복 메일 체크 기능 확인
- [ ] 고객 메일 리스트 자동 업데이트 확인
- [ ] 처리 로그 확인

**리멤버 고객 정보 동기화:**
- [ ] 리멤버 CSV 파일 내보내기 (월 1회 또는 분기 1회)
- [ ] CSV 파일 업로드 (`/home/plus/mailer/upload/csv/` 폴더)
- [ ] 리멤버 고객 정보 임포트 실행
- [ ] 임포트 결과 확인 (신규/업데이트/건너뜀 건수)
- [ ] 정기 동기화 cron 작업 설정 (선택사항)

**고객 메일 리스트 관리:**
- [ ] 메일 아카이브 DB에서 고객 이메일 추출 스크립트 실행
- [ ] 고객 메일 리스트 DB 업데이트
- [ ] 최신 여부 및 빈도 등급 업데이트
- [ ] MEK+ 메일러와 연동 확인
- [ ] 정기 업데이트 cron 작업 설정
- [ ] 고객 리스트 관리 화면 테스트

---

## 부록 A: 참고 자료

- RFC 5322: Internet Message Format
- readpst: https://github.com/Hamza-Mohamed/readpst
- libpff: https://github.com/libyal/libpff
- 그누보드5 개발 가이드

---

**문서 버전**: 2.1  
**최종 수정일**: 2025-01-02  
**변경 사항**:
- 메일 백업 및 복구 전략 섹션 추가 (13장)
- MEK+ 메일러용 고객 메일 리스트 관리 방안 추가
- 서버 재해 복구 문서의 메일 관련 내용 통합
- 다우 메일 EML 백업 파일 업로드 방식 수정 (`/home/plus/mailer/` 폴더 기반)
- 관리자 툴을 통한 메일 업데이트 및 고객 리스트 자동 업데이트 프로세스 상세화
- 리멤버 서비스 고객 정보 중앙 DB 업데이트 방안 추가 (13.7장)

