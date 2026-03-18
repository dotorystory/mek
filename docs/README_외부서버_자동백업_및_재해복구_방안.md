# MEK 외부 서버 자동 백업 및 재해 복구 방안 보고서

## 📋 문서 정보
- **작성일**: 2024-12-30
- **프로젝트명**: 카페24 서버 자동 백업 및 재해 복구 시스템
- **목적**: 외부 서버(카페24)의 데이터를 회사 내부 PC에 자동 백업하고, 유사시 다른 외부 서버로 즉시 복구 가능하도록 구축

---

## 1. 현재 상황 분석

### 1.1 서버 환경

**외부 서버 (카페24):**
- **호스팅**: 카페24 서버호스팅
- **OS**: Linux
- **웹서버**: Apache (httpd, dnf)
- **PHP**: PHP 8.1
- **CMS**: 그누보드 다국어 웹사이트
- **주요 경로**: `/var/www/html/mekeng.com`
- **기능**: 미디어파일 업로더, 메일러, 품질검사프로그램 등
- **메일 서버**: Postfix 기반 (`@webmail.mekeng.com`, `@www.mekeng.com`)

**메일 시스템:**
- **다우 오피스 메일**: `@mekeng.com` (외부 서비스)
  - 저장 용량 제한
  - 자동 삭제 정책
  - 사용자 수동 백업 시 ZIP 파일 (EML 형식 포함)
- **Postfix 메일 서버**: `@webmail.mekeng.com`, `@www.mekeng.com` (카페24 서버)
  - 서버 백업 시 설정 파일 포함 (`/etc/postfix/`)

**내부 PC WSL 환경:**
- **OS**: Windows + WSL (Windows Subsystem for Linux)
- **Linux 배포판**: Rocky Linux 9
- **웹서버**: Apache (httpd, dnf)
- **PHP**: PHP 8.1
- **특징**: 외부 서버와 거의 동일한 환경 구성
- **위치**: C드라이브 WSL 설치
- **장점**: 
  - 외부 서버와 동일한 환경으로 백업/복구 스크립트 사전 테스트 가능
  - rsync 등 리눅스 도구 직접 사용 가능
  - 로컬에서 백업 검증 및 복구 프로세스 테스트 가능

### 1.2 현재 백업 현황
- ✅ 자동 백업 기능 구현 및 운용 중
- ✅ cron 자동화 설정 완료
- ✅ 수동 백업 기능 보유
- ✅ 회사 내부 윈도우 데스크탑 네트워크 서버 자동 백업/동기화 사용 중

### 1.3 백업 저장소 현황
- **위치**: 회사 내부 PC의 HDD E드라이브
- **여유 공간**: 1TB 이상
- **용도**: 회사 내부 윈도우 데스크탑 네트워크 서버 자동 백업/동기화 사용 중
- **목표**: 
  - 외부 서버(카페24) 백업 데이터 추가 저장
  - 메일 백업 데이터 저장 (PST 파일, EML ZIP 파일)

### 1.4 내부 PC 환경 구성
- **Windows**: 메인 OS
- **WSL (Rocky Linux 9)**: C드라이브에 설치
  - 외부 서버와 동일한 환경 (httpd, dnf, PHP 8.1)
  - 백업 스크립트 개발 및 테스트 환경
  - rsync, tar, gzip 등 리눅스 도구 활용 가능
  - 복구 프로세스 사전 검증 가능
- **E드라이브**: 백업 저장소 (1TB+ 여유 공간)

### 1.5 요구사항
1. **자동 백업**: 카페24 서버 → 내부 PC E드라이브 자동 백업
2. **즉시 복구**: 다른 외부 서버 호스팅으로 즉시 복구 가능
3. **안정성**: 서버 다운 상황에 대비한 완전한 백업 체계
4. **모니터링**: 백업 성공/실패 알림 및 상태 확인
5. **메일 백업**: 다우 오피스 메일(@mekeng.com) 및 Postfix 메일(@webmail.mekeng.com, @www.mekeng.com) 백업 및 복구

---

## 2. 백업 아키텍처 설계

### 2.1 전체 구조도

```
┌─────────────────────────────────────────────────────────────┐
│              [카페24 외부 서버 (Linux)]                      │
│                                                              │
│  /var/www/html/mekeng.com/                                  │
│  ├─ 웹사이트 파일                                            │
│  ├─ 업로드 파일 (미디어, 문서 등)                            │
│  ├─ 데이터베이스 (MariaDB)                                   │
│  └─ 설정 파일                                                │
│                                                              │
│  ┌──────────────────────────────────────────┐              │
│  │  백업 스크립트 (cron)                     │              │
│  │  - 파일 백업                              │              │
│  │  - DB 덤프                                │              │
│  │  - 압축 및 암호화                         │              │
│  └──────────────────────────────────────────┘              │
└──────────────────────┬─────────────────────────────────────┘
                       │
                       │ (SFTP/SSH/rsync)
                       │ 암호화 전송
                       ↓
┌─────────────────────────────────────────────────────────────┐
│         [회사 내부 PC]                                        │
│                                                              │
│  ┌──────────────────────────────────────────┐              │
│  │  WSL (Rocky Linux 9) - C드라이브          │              │
│  │  - 외부 서버와 동일한 환경                 │              │
│  │  - rsync, tar, gzip 등 리눅스 도구        │              │
│  │  - 백업 스크립트 실행 환경                 │              │
│  │  - 복구 프로세스 테스트 환경                │              │
│  └──────────────────────────────────────────┘              │
│                       │                                     │
│                       ↓                                     │
│  ┌──────────────────────────────────────────┐              │
│  │  백업 수신 스크립트 (WSL)                 │              │
│  │  - rsync 클라이언트 (SSH)                 │              │
│  │  - SFTP 클라이언트                       │              │
│  │  - 백업 검증                             │              │
│  │  - 알림 발송                             │              │
│  └──────────────────────────────────────────┘              │
│                       │                                     │
│                       ↓                                     │
│  E:\backup\cafe24\                                          │
│  ├─ daily\          (일일 백업)                             │
│  │   ├─ 20241230\                                           │
│  │   │   ├─ files.tar.gz                                    │
│  │   │   ├─ database.sql.gz                                 │
│  │   │   └─ metadata.json                                   │
│  │   └─ ...                                                 │
│  ├─ weekly\         (주간 백업)                             │
│  ├─ monthly\        (월간 백업)                             │
│  └─ restore\        (복구용 임시 폴더)                       │
└──────────────────────┬─────────────────────────────────────┘
                       │
                       │ (복구 시)
                       ↓
┌─────────────────────────────────────────────────────────────┐
│        [다른 외부 서버 호스팅 (복구 대상)]                   │
│                                                              │
│  ┌──────────────────────────────────────────┐              │
│  │  복구 스크립트                            │              │
│  │  - 백업 파일 다운로드                     │              │
│  │  - 파일 복원                              │              │
│  │  - DB 복원                                │              │
│  │  - 설정 적용                              │              │
│  └──────────────────────────────────────────┘              │
└─────────────────────────────────────────────────────────────┘
```

### 2.2 백업 구성 요소

**1. 파일 백업**
- 웹사이트 소스 코드
- 업로드된 미디어 파일
- 설정 파일 (.htaccess, config 등)
- 로그 파일

**2. 데이터베이스 백업**
- MariaDB 전체 덤프
- 다국어 사이트별 DB (mek_kr, mek_en, mek_jp, mek_cn, mek_de, mek_es)
- 사용자 데이터, 게시판, 플러그인 데이터

**3. 메타데이터**
- 백업 일시
- 파일 목록 및 크기
- DB 버전 정보
- 체크섬 (무결성 검증용)

---

## 3. 백업 전략

### 3.1 현재 백업 시스템 (backup.sh 기반)

현재 운영 중인 백업 스크립트 (`/usr/local/bin/backup.sh`) 기준:

#### 3.1.1 백업 정책
- **백업 위치**: `/var/www/.bak/backups` (서버 로컬)
- **보관 정책**: 
  - 1년 이상된 백업: 자동 삭제
  - 1년 이내 백업: 최신 5개만 보관 (나머지 자동 삭제)
- **백업 방식**: 전체 백업 (Full Backup)

#### 3.1.2 백업 대상
1. **시스템 설정 파일**
   - `/etc/httpd` (Apache 설정)
   - `/etc/php*` (PHP 설정)

2. **웹사이트 파일**
   - `/var/www/html` (웹사이트 소스 코드 및 업로드 파일)

3. **데이터베이스**
   - MariaDB 전체 데이터베이스 (`mysqldump --all-databases`)

#### 3.1.3 백업 파일 구조 (서버 로컬)

```
/var/www/.bak/backups/
├─ 20251028_170530/
│   ├─ etc_httpd.tar.gz
│   ├─ etc_php.tar.gz
│   ├─ var_www_html.tar.gz
│   └─ db_backup.sql
├─ 20251103_144701/
├─ 20251110_080650/
├─ 20251126_135513/
└─ 20251211_154126/          (최신 5개만 보관)
```

#### 3.1.4 백업 스크립트 개별 기능

**backup_html.sh** (`/usr/local/bin/backup_html.sh`):
- HTML 파일 전용 백업
- 저장 위치: `/var/www/.bak/html/`
- 보관 기간: 5일 (5일 이상된 백업 자동 삭제)
- 로그: `/var/www/.bak/log/backup_YYYY-MM-DD.log`

**backup_mysql.sh** (`/usr/local/bin/backup_mysql.sh`):
- MySQL 개별 데이터베이스 백업
- 저장 위치: `/var/www/.bak/db/`
- 전체 DB 백업: `all_db_YYYY-MM-DD.sql`
- 개별 DB 백업: `{DB명}_YYYY-MM-DD.sql`
- 보관 기간: 7일

### 3.2 외부 백업 저장소 (내부 PC E드라이브) 구조

서버 로컬 백업을 내부 PC로 복사/동기화하는 구조:

**실제 폴더 구조 (E드라이브):**
```
E:\MEK_WEB\
├─ backups\                  (서버 백업 파일 동기화)
│   ├─ 20251028_170530\
│   │   ├─ etc_httpd.tar.gz
│   │   ├─ etc_php.tar.gz
│   │   ├─ var_www_html.tar.gz
│   │   └─ db_backup.sql
│   ├─ 20251103_144701\
│   └─ ... (최신 5개)
│
├─ logs\                     (동기화 로그)
│   ├─ sync_20250101_030000.log
│   └─ ...
│
└─ restore\                  (복구 작업 시 임시 파일)
```

**WSL에서 접근 경로:**
```
/mnt/e/MEK_WEB/
├─ backups/
├─ logs/
└─ restore/
```

### 3.3 백업 전송 방법

**서버 → 내부 PC 전송:**

**⚠️ 중요: 백업 작업 실행 위치**

백업 시스템은 **2단계 구조**로 동작합니다:

1. **1단계: 서버 측 (외부 웹서버 - 카페24)**
   - 서버에서 `backup.sh` 스크립트가 cron으로 자동 실행
   - 서버 로컬에 백업 파일 생성 (`/var/www/.bak/backups/`)
   - 백업 파일: `etc_httpd.tar.gz`, `etc_php.tar.gz`, `var_www_html.tar.gz`, `db_backup.sql`

2. **2단계: 클라이언트 측 (로컬 PC WSL)**
   - **로컬 PC의 WSL 환경에서 rsync 클라이언트 실행**
   - WSL에서 서버의 백업 폴더를 E드라이브로 동기화
   - 서버에서 데이터를 "가져오는" 방식 (Pull 방식)

**전송 방법:**
1. **rsync (SSH)**: **WSL 환경에서** 서버 백업 폴더를 E드라이브로 동기화
2. **SFTP**: WinSCP 등으로 수동/자동 다운로드 (Windows 또는 WSL)
3. **스크립트 자동화**: **WSL cron** + rsync로 자동 동기화

---

---

## 4. 백업 구현 방안

### 4.1 방법 1: SFTP 기반 백업 (권장) ⭐⭐⭐⭐⭐

#### 4.1.1 특징
- **보안**: SSH 암호화 통신
- **안정성**: 네트워크 오류 시 재시도 가능
- **호환성**: Windows/Linux 모두 지원
- **구현 난이도**: 중간

#### 4.1.2 동작 방식

**서버 측 (카페24):**
1. cron으로 매일 백업 스크립트 실행
2. 파일 압축 및 DB 덤프 생성
3. SFTP로 내부 PC에 전송
4. 전송 완료 후 서버 측 임시 파일 삭제

**클라이언트 측 (내부 PC):**
1. SFTP 서버 모드로 대기 (또는 풀링 방식)
2. 백업 파일 수신
3. 무결성 검증 (체크섬 확인)
4. 알림 발송 (성공/실패)

#### 4.1.3 장점
- 보안성 높음 (SSH 암호화)
- 네트워크 방화벽 설정 용이
- Windows 환경에서 구현 가능
- 재시도 로직 구현 가능

#### 4.1.4 단점
- 초기 설정 복잡 (SSH 키 관리)
- 포트 개방 필요 (22번 또는 커스텀 포트)

### 4.2 방법 2: rsync 기반 백업 (WSL 활용) ⭐⭐⭐⭐⭐

#### 4.2.1 특징
- **효율성**: 증분 동기화로 변경된 파일만 전송
- **속도**: 빠른 동기화
- **압축**: 전송 시 자동 압축
- **구현 난이도**: 쉬움 (WSL 환경 활용)
- **환경 일치**: WSL이 외부 서버와 동일한 환경

#### 4.2.2 동작 방식

**⚠️ 실행 위치: 로컬 PC WSL 환경**

1. **서버 측 (카페24 외부 서버)**:
   - `backup.sh` 스크립트가 cron으로 실행되어 서버 로컬에 백업 생성
   - 백업 위치: `/var/www/.bak/backups/{날짜}/`
   - 서버는 백업 파일을 생성만 하고, 전송은 하지 않음

2. **클라이언트 측 (로컬 PC WSL)**:
   - **WSL 환경에서 rsync 클라이언트 실행** (SSH를 통해 서버 접속)
   - 서버의 `/var/www/.bak/backups/` 폴더를 E드라이브로 동기화
   - 명령어 예시:
     ```bash
     # WSL에서 실행
     rsync -avz --delete \
       user@cafe24-server.com:/var/www/.bak/backups/ \
       /mnt/e/backup/cafe24/backups/
     ```
   - 변경된 파일만 동기화 (증분 백업)
   - E드라이브 마운트 경로(`/mnt/e/`)로 직접 저장

**요약:**
- 서버: 백업 파일 생성 (backup.sh)
- **WSL: 백업 파일 가져오기 (rsync 클라이언트)**

#### 4.2.3 장점
- 네트워크 대역폭 절약
- 빠른 동기화 속도
- 증분 백업에 최적화
- **WSL 환경 활용으로 Windows에서도 리눅스 도구 직접 사용**
- **외부 서버와 동일한 환경으로 스크립트 호환성 보장**
- **로컬에서 백업/복구 프로세스 사전 테스트 가능**

#### 4.2.4 단점
- 초기 전체 백업 시 시간 소요
- WSL 네트워크 설정 필요 (Windows 방화벽)

#### 4.2.5 WSL 활용 시 추가 장점
- **백업 스크립트 사전 검증**: WSL에서 백업 스크립트 테스트 후 서버에 배포
- **복구 프로세스 연습**: WSL 환경에서 복구 시나리오 반복 테스트
- **환경 일관성**: 외부 서버와 동일한 Rocky Linux 9 환경
- **도구 활용**: rsync, tar, gzip, mysqldump 등 리눅스 도구 직접 사용

### 4.3 방법 3: FTP/SFTP 클라이언트 기반 (Windows) ⭐⭐⭐

#### 4.3.1 특징
- **구현 난이도**: 쉬움
- **Windows 친화적**: WinSCP, FileZilla 등 GUI 도구 활용
- **자동화**: 스크립트로 자동 실행

#### 4.3.2 동작 방식
- Windows 작업 스케줄러로 스크립트 실행
- WinSCP 스크립트로 서버에서 파일 다운로드
- 백업 검증 및 알림

#### 4.3.3 장점
- Windows 환경에 최적화
- GUI 도구 활용 가능
- 구현이 간단

#### 4.3.4 단점
- 보안성 상대적으로 낮음 (FTP 사용 시)
- 대용량 파일 처리 시 성능 제한

### 4.4 방법 4: 클라우드 스토리지 중계 (백업 중계소) ⭐⭐⭐⭐

#### 4.4.1 특징
- **중계 방식**: 서버 → 클라우드 → 내부 PC
- **안정성**: 클라우드 스토리지의 안정성 활용
- **구현 난이도**: 중간

#### 4.4.2 동작 방식
1. 서버에서 클라우드 스토리지에 백업 업로드 (AWS S3, Google Drive, OneDrive 등)
2. 내부 PC에서 클라우드 스토리지에서 다운로드
3. 로컬 저장

#### 4.4.3 장점
- 네트워크 문제 시에도 클라우드에 백업 보관
- 여러 위치에서 백업 접근 가능
- 클라우드 스토리지의 내구성 활용

#### 4.4.4 단점
- 클라우드 스토리지 비용 발생
- 데이터 암호화 필수
- 다운로드 속도 제한 가능

### 4.5 최종 권장안

**1차 권장: rsync 기반 백업 (WSL 활용) ⭐⭐⭐⭐⭐**
- WSL 환경 활용으로 외부 서버와 동일한 환경
- rsync의 증분 동기화로 효율적 백업
- 리눅스 도구 직접 사용 가능
- 백업/복구 스크립트 사전 테스트 가능
- Windows에서도 리눅스 환경 활용

**2차 권장: rsync + SFTP 하이브리드**
- 일일 증분: rsync via WSL (빠른 동기화)
- 주간 전체: SFTP (안정적 전송)
- WSL에서 rsync, Windows에서 SFTP 클라이언트 병행 사용

**3차 권장: SFTP 기반 백업 (WSL 없이)**
- 보안성과 안정성의 균형
- Windows 환경에서만 구현
- 네트워크 방화벽 설정 용이

---

## 5. 재해 복구 전략

### 5.1 복구 시나리오

#### 시나리오 1: 서버 완전 다운 (카페24 서버 장애)
1. 다른 호스팅 업체에 신규 서버 구축
2. 초기 서버 환경 설정 (setup.sh 실행)
3. 백업 파일을 새 서버로 전송
4. 백업 파일 직접 복원 (1차 복원)
5. DNS 변경 (도메인 연결)
6. 서비스 재개
7. 이후 변경사항 rsync 동기화 (2차 동기화)

#### 시나리오 2: 데이터 손실 (파일 삭제/손상)
1. 최신 백업 파일 확인
2. 특정 파일/폴더만 선택적 복구
3. DB 특정 테이블만 복구 (필요 시)

#### 시나리오 3: 해킹/보안 사고
1. 감염 전 백업으로 롤백
2. 보안 패치 적용
3. 취약점 점검

### 5.2 복구 프로세스 (상세)

#### Step 1: 백업 파일 확인 및 준비
- 복구할 백업 날짜 선택 (최신 백업 권장)
- 백업 파일 무결성 검증
- 내부 PC에서 백업 파일 준비:
  - `/var/www/.bak/backups/{날짜}/` 폴더 전체
  - 또는 E드라이브 백업 복사본

#### Step 2: 신규 서버 초기 환경 구축 (setup.sh 활용)

**2.1 기본 서버 환경 설정**

setup.sh 스크립트 실행 (`/usr/local/bin/setup.sh`):

```bash
# Rocky Linux 9 서버에서 실행
sudo bash /usr/local/bin/setup.sh

# 선택 옵션:
# 1) 물리 서버 설치
# 2) PHP 버전 선택 (PHP 8.1 권장)
```

**setup.sh가 자동으로 설치/설정하는 항목:**
- 시스템 업데이트 (dnf update)
- 필수 패키지 설치 (epel-release, dnf-utils, wget, curl, git, vim, unzip, tar)
- Apache (httpd) 설치 및 서비스 시작
- MariaDB 설치 및 서비스 시작
- PHP 8.0 또는 8.1 설치 (선택)
- PHP 확장 모듈 설치:
  - 기본: php, php-cli, php-fpm, php-common, php-mbstring, php-mysqlnd, php-xml, php-pdo, php-json, php-opcache, php-zip, php-gd, php-curl
  - 추가: php-intl, php-exif, php-imagick, php-gmp, php-imap, php-sockets, php-amqp, php-redis, php-xmlwriter, php-bcmath, php-fileinfo
- 웹루트 디렉토리 생성 및 권한 설정:
  - `/var/www/html` (웹루트)
  - `/var/www/tmp` (업로드 폴더)
  - 소유권: apache:user
  - 권한: 디렉토리 775, 파일 664
  - ACL 설정: user 계정 rwx 권한

**2.2 추가 패키지/라이브러리 재설치**

setup.sh 이후 추가로 설치된 패키지가 있는 경우:

```bash
# 설치된 패키지 목록 확인 (백업본에서)
# /etc/httpd, /etc/php* 백업본의 설정 파일 참고

# 예시: ImageMagick, Redis 등 추가 라이브러리
sudo dnf install -y ImageMagick ImageMagick-devel
sudo dnf install -y redis

# PHP 모듈 확인 및 재설치
php -m  # 설치된 모듈 확인
# 누락된 모듈이 있으면 dnf install로 재설치
```

**2.3 Postfix 메일 서버 설치 및 설정**

```bash
# Postfix 설치
sudo dnf install -y postfix

# Postfix 설정 파일 복원 (백업본에서)
# /etc/postfix/main.cf, /etc/postfix/master.cf 복원

# 또는 기본 설정으로 시작
sudo systemctl enable postfix
sudo systemctl start postfix

# Postfix 설정 확인
sudo postfix check
sudo postfix status
```

**Postfix 설정 파일 복원:**
- 백업본의 `/etc/postfix/main.cf` 복원
- 백업본의 `/etc/postfix/master.cf` 복원 (있는 경우)
- 주요 설정 항목 확인:
  - `myhostname`: 서버 호스트명
  - `mydomain`: 도메인명
  - `myorigin`: 발신 도메인
  - `inet_interfaces`: 수신 인터페이스
  - `mydestination`: 로컬 도메인
  - `relayhost`: 릴레이 서버 (필요 시)

**Postfix 재시작 및 테스트:**
```bash
sudo systemctl restart postfix
sudo systemctl status postfix

# 메일 발송 테스트
echo "Test email" | mail -s "Test Subject" test@example.com

# 메일 큐 확인
sudo mailq
```

#### Step 3: 백업 파일 복원 (1차 직접 복원)

**3.1 백업 파일을 새 서버로 전송**

```bash
# 내부 PC (WSL)에서 새 서버로 전송
rsync -avz /mnt/e/backup/cafe24/backups/20251211_154126/ user@new-server.com:/tmp/restore/
# 또는 scp 사용
scp -r /mnt/e/backup/cafe24/backups/20251211_154126/ user@new-server.com:/tmp/restore/
```

**3.2 시스템 설정 파일 복원**

```bash
# 새 서버에서 실행
cd /tmp/restore/

# Apache 설정 복원
sudo tar -xzf etc_httpd.tar.gz -C /

# PHP 설정 복원
sudo tar -xzf etc_php.tar.gz -C /

# Apache 재시작
sudo systemctl restart httpd
sudo systemctl status httpd
```

**3.3 웹사이트 파일 복원**

```bash
# 웹사이트 파일 복원
sudo tar -xzf var_www_html.tar.gz -C /

# 권한 설정 (setup.sh에서 설정한 권한과 동일)
sudo chown -R apache:user /var/www/html
sudo find /var/www/html -type d -exec chmod 775 {} \;
sudo find /var/www/html -type f -exec chmod 664 {} \;

# ACL 설정
sudo setfacl -R -m u:user:rwx /var/www/html
sudo setfacl -R -d -m u:user:rwx /var/www/html

# 업로드 폴더 권한도 확인
sudo chown -R apache:user /var/www/tmp
sudo find /var/www/tmp -type d -exec chmod 775 {} \;
sudo find /var/www/tmp -type f -exec chmod 664 {} \;
sudo setfacl -R -m u:user:rwx /var/www/tmp
sudo setfacl -R -d -m u:user:rwx /var/www/tmp
```

**3.4 데이터베이스 복원**

```bash
# MariaDB 서비스 확인
sudo systemctl status mariadb

# 데이터베이스 복원
sudo mysql < db_backup.sql

# 또는 MySQL 클라이언트로 직접 복원
mysql -u root -p < db_backup.sql

# 복원 확인
mysql -u root -p -e "SHOW DATABASES;"

# 데이터베이스별 복원 확인 (mek_kr, mek_en, mek_jp, mek_cn, mek_de, mek_es)
mysql -u root -p -e "USE mek_kr; SHOW TABLES;"
```

**3.5 데이터베이스 사용자 권한 설정**

```bash
# 백업본의 사용자 권한 확인 후 재설정
mysql -u root -p

# MySQL/MariaDB 콘솔에서:
SHOW GRANTS FOR 'user'@'localhost';
# 필요시 GRANT 명령어로 권한 재설정
```

#### Step 4: 서비스 재시작 및 확인

```bash
# 모든 서비스 재시작
sudo systemctl restart httpd
sudo systemctl restart mariadb
sudo systemctl restart postfix
sudo systemctl restart php-fpm

# 서비스 상태 확인
sudo systemctl status httpd
sudo systemctl status mariadb
sudo systemctl status postfix
sudo systemctl status php-fpm
```

#### Step 5: 변경사항 rsync 동기화 (2차 동기화)

**백업 복원 이후 변경된 파일만 동기화:**

```bash
# 기존 서버가 살아있는 경우 (선택적 복구 시나리오)
# 또는 다른 백업 소스와 동기화

# 예시: 내부 PC 백업과 새 서버 동기화
rsync -avz --delete \
  --exclude='.bak' \
  --exclude='cache' \
  /mnt/e/backup/cafe24/backups/latest/var_www_html/ \
  user@new-server.com:/var/www/html/

# 또는 기존 서버와 동기화 (서버가 살아있는 경우)
rsync -avz --delete \
  user@old-server.com:/var/www/html/ \
  user@new-server.com:/var/www/html/
```

**rsync 동기화 옵션:**
- `-a`: 아카이브 모드 (권한, 시간 등 보존)
- `-v`: 상세 출력
- `-z`: 압축 전송
- `--delete`: 소스에 없는 파일 삭제 (선택사항)
- `--exclude`: 제외할 파일/폴더 패턴

#### Step 6: DNS 변경 및 검증

**6.1 DNS 레코드 변경**
- 도메인 관리자 페이지에서 A 레코드 변경
- 새 서버 IP로 변경
- TTL 고려하여 전파 시간 확인

**6.2 웹사이트 접속 테스트**
```bash
# 새 서버에서 로컬 테스트
curl http://localhost
curl -I http://localhost

# 외부에서 도메인 테스트 (DNS 전파 후)
curl http://mekeng.com
```

**6.3 주요 기능 테스트**
- 웹사이트 접속 확인
- 로그인 기능 테스트
- 파일 업로드 기능 테스트
- 메일 발송 기능 테스트 (Postfix)
- 데이터베이스 연결 확인
- 다국어 사이트 접속 확인 (kr, en, jp, cn, de, es)

### 5.3 복구 시간 목표 (RTO: Recovery Time Objective)

| 복구 단계 | 예상 시간 | 비고 |
|----------|-----------|------|
| **서버 구축** | 1~2시간 | 호스팅 업체에 서버 요청 및 접근 권한 획득 |
| **초기 환경 설정** | 30분~1시간 | setup.sh 실행, 추가 패키지 설치 |
| **Postfix 설정** | 15~30분 | Postfix 설치 및 설정 파일 복원 |
| **백업 파일 전송** | 30분~2시간 | 백업 파일 크기에 따라 상이 |
| **파일 복원** | 30분~1시간 | 압축 해제 및 권한 설정 |
| **DB 복원** | 15분~1시간 | DB 크기에 따라 상이 |
| **검증 및 테스트** | 30분~1시간 | 기능 테스트 및 문제 수정 |
| **DNS 변경 및 전파** | 10분~2시간 | DNS TTL에 따라 상이 |
| **rsync 동기화** | 10분~30분 | 변경사항만 동기화 |
| **전체 복구 (합계)** | **4~10시간** | 새 서버 구축 포함 |

### 5.4 복구 스크립트 자동화 (예시)

**복구 스크립트 예시 구조:**

```bash
#!/bin/bash
# restore.sh - 재해 복구 스크립트

BACKUP_DATE="20251211_154126"
BACKUP_PATH="/tmp/restore"
NEW_SERVER_USER="user"
NEW_SERVER_HOST="new-server.com"

# 1. 백업 파일 전송
rsync -avz /mnt/e/backup/cafe24/backups/$BACKUP_DATE/ $NEW_SERVER_USER@$NEW_SERVER_HOST:/tmp/restore/

# 2. 원격 서버에서 복원 실행
ssh $NEW_SERVER_USER@$NEW_SERVER_HOST << 'EOF'
cd /tmp/restore

# 설정 파일 복원
sudo tar -xzf etc_httpd.tar.gz -C /
sudo tar -xzf etc_php.tar.gz -C /

# 웹사이트 파일 복원
sudo tar -xzf var_www_html.tar.gz -C /
sudo chown -R apache:user /var/www/html
sudo find /var/www/html -type d -exec chmod 775 {} \;
sudo find /var/www/html -type f -exec chmod 664 {} \;

# DB 복원
sudo mysql < db_backup.sql

# 서비스 재시작
sudo systemctl restart httpd mariadb postfix php-fpm
EOF
```

### 5.6 메일 백업 및 복구 (참고)

> **참고**: 메일 백업 및 복구 전략, MEK+ 메일러용 고객 메일 리스트 관리 방안 등 상세한 메일 관련 기획 내용은 **"README_MEK_고객메일_중앙통합관리_기획서.md"** 문서의 13장 "메일 백업 및 복구 전략" 섹션을 참조하세요.

**요약:**
- 다우 오피스 메일(@mekeng.com) 및 Postfix 메일(@webmail.mekeng.com, @www.mekeng.com) 백업
- PST 파일 및 EML ZIP 파일 백업 및 복구
- 메일 아카이브 DB에서 고객 메일 리스트 추출 및 MEK+ 메일러 연동
- 상세 내용: README_MEK_고객메일_중앙통합관리_기획서.md 문서 참조

---

## 6. 보안 및 암호화

### 6.1 전송 암호화
- **SFTP/SSH**: 모든 데이터 전송 시 암호화
- **TLS/SSL**: FTP 사용 시 필수
- **VPN**: 추가 보안 레이어 (선택사항)

### 6.2 저장 암호화
- **백업 파일 암호화**: AES-256 암호화
- **암호화 키 관리**: 안전한 키 저장소
- **DB 덤프 암호화**: 민감한 데이터 보호

### 6.3 접근 제어
- **SSH 키 기반 인증**: 비밀번호 대신 키 사용
- **IP 화이트리스트**: 특정 IP에서만 접근 허용
- **방화벽 설정**: 필요한 포트만 개방

### 6.4 백업 파일 보안
- **권한 설정**: 백업 파일 읽기 전용
- **백업 로그**: 접근 이력 기록
- **물리적 보안**: 내부 PC 물리적 보호

---

## 7. 모니터링 및 알림

### 7.1 모니터링 항목

**백업 상태:**
- 백업 성공/실패 여부
- 백업 파일 크기
- 백업 소요 시간
- 네트워크 전송 속도

**저장소 상태:**
- E드라이브 사용량
- 백업 파일 개수
- 오래된 백업 파일 정리 필요 여부

**서버 상태:**
- 서버 응답 시간
- 디스크 사용량
- DB 연결 상태

### 7.2 알림 방식

**이메일 알림:**
- 백업 성공 시: 일일 요약 리포트
- 백업 실패 시: 즉시 알림
- 저장소 부족 시: 경고 알림

**SMS 알림 (선택사항):**
- 백업 실패 시 긴급 알림
- 저장소 부족 시 경고

**대시보드:**
- 웹 기반 백업 상태 대시보드
- 그누보드 관리자 페이지 연동

### 7.3 로그 관리

**백업 로그:**
- 백업 실행 일시
- 성공/실패 기록
- 오류 메시지
- 전송 속도 및 용량

**로그 보관:**
- 최근 90일 로그 보관
- 월별 로그 아카이브

---

## 8. 네트워크 및 방화벽 설정

### 8.1 포트 개방

**필요한 포트:**
- **SSH/SFTP**: 22번 포트 (또는 커스텀 포트)
- **FTP (선택)**: 21번 포트
- **rsync (선택)**: 873번 포트

### 8.2 방화벽 설정

**서버 측 (카페24):**
- SSH 포트 개방 (특정 IP만 허용 권장)
- 방화벽 규칙 설정

**클라이언트 측 (내부 PC):**
- Windows 방화벽 설정
- 회사 네트워크 방화벽 설정 (필요 시)

### 8.3 VPN 활용 (선택사항)

**장점:**
- 추가 보안 레이어
- 네트워크 트래픽 암호화
- IP 제한 없이 접근 가능

**단점:**
- VPN 서버 구축 필요
- 추가 비용 발생 가능

---

## 9. 용량 관리 및 정리

### 9.1 백업 파일 보관 정책

| 백업 유형 | 보관 기간 | 자동 삭제 |
|----------|-----------|-----------|
| 일일 증분 | 7일 | 7일 경과 시 자동 삭제 |
| 주간 전체 | 4주 | 4주 경과 시 자동 삭제 |
| 월간 전체 | 12개월 | 12개월 경과 시 자동 삭제 |

### 9.2 용량 모니터링

**경고 기준:**
- E드라이브 사용량 80% 이상: 경고 알림
- E드라이브 사용량 90% 이상: 긴급 알림
- 백업 실패 시: 즉시 알림

**자동 정리:**
- 오래된 백업 파일 자동 삭제
- 중복 파일 제거 (선택사항)
- 압축률 높은 백업 유지

### 9.3 백업 파일 검증

**정기 검증:**
- 주 1회 백업 파일 무결성 검증
- 압축 해제 테스트
- DB 덤프 복원 테스트 (선택사항)

---

## 10. 구현 단계별 계획

### Phase 1: 기반 구축 (1주)
- [ ] 내부 PC E드라이브 백업 폴더 구조 생성
- [ ] WSL 환경 확인 및 설정
  - [ ] rsync, tar, gzip 등 도구 설치 확인
  - [ ] E드라이브 마운트 설정 (WSL에서 접근)
  - [ ] SSH 클라이언트 설정
- [ ] SFTP 클라이언트/서버 설정
- [ ] SSH 키 생성 및 교환 (WSL 환경에서)
- [ ] 네트워크 연결 테스트 (WSL에서 서버 접속 테스트)

### Phase 2: 백업 스크립트 개발 (1~2주)
- [ ] **서버 측 백업 스크립트 작성** (외부 웹서버 - 카페24)
  - 파일 압축 (`backup.sh` 기반)
  - DB 덤프 생성 (`mysqldump`)
  - 메타데이터 생성
  - 서버 로컬에 백업 저장 (`/var/www/.bak/backups/`)
- [ ] **클라이언트 측 수신 스크립트 작성** (로컬 PC WSL 환경)
  - **rsync 클라이언트 (SSH)** - WSL에서 실행
  - SFTP 다운로드 (대안)
  - 무결성 검증
  - 알림 발송
  - E드라이브로 백업 파일 저장
- [ ] WSL에서 백업 스크립트 사전 테스트
  - 로컬 환경에서 스크립트 검증
  - 외부 서버와 동일한 환경으로 호환성 확인
  - rsync 연결 테스트

### Phase 3: 자동화 설정 (3일)
- [ ] **서버 측 cron 등록** (외부 웹서버)
  - `backup.sh` 스크립트 자동 실행 (매일 새벽)
  - 서버 로컬에 백업 생성
- [ ] **클라이언트 측 자동화 설정** (로컬 PC WSL)
  - [ ] **WSL cron 설정** (systemd 또는 cron)
    - rsync 클라이언트 스크립트 자동 실행
    - 서버 백업 폴더를 E드라이브로 동기화
  - [ ] Windows 작업 스케줄러로 WSL 스크립트 실행 (대안)
  - [ ] WSL 백그라운드 서비스 설정
- [ ] 자동 정리 스크립트 설정 (WSL 환경)
  - E드라이브 백업 파일 정리 (오래된 파일 삭제)

### Phase 4: 모니터링 및 알림 (3일)
- [ ] 알림 시스템 구축
- [ ] 로그 수집 및 분석
- [ ] 대시보드 개발 (선택사항)

### Phase 5: 복구 시스템 구축 (1주)
- [ ] 복구 스크립트 작성 (WSL 환경)
- [ ] 복구 프로세스 문서화
- [ ] WSL 환경에서 복구 프로세스 사전 테스트
  - 로컬 백업 파일로 복구 시뮬레이션
  - 외부 서버와 동일한 환경으로 검증
- [ ] 복구 테스트 수행 (실제 서버 환경)

### Phase 6: 테스트 및 검증 (1주)
- [ ] WSL 환경에서 백업 스크립트 로컬 테스트
- [ ] 백업 테스트 (전체/증분)
  - WSL에서 rsync 테스트
  - 실제 서버와의 연결 테스트
- [ ] 복구 테스트 (전체/선택적)
  - WSL 환경에서 복구 프로세스 시뮬레이션
  - 실제 서버 환경에서 복구 테스트
- [ ] 성능 테스트
- [ ] 장애 시나리오 테스트

### Phase 7: 운영 전환 (3일)
- [ ] 운영 환경 적용
- [ ] 모니터링 시작
- [ ] 사용자 교육 (필요 시)

---

## 11. 예상 비용 및 리소스

### 11.1 하드웨어 비용

| 항목 | 비용 | 비고 |
|------|------|------|
| 내부 PC E드라이브 | 0원 | 기존 사용 중 |
| 추가 저장소 (필요 시) | 100,000~200,000원 | 2TB HDD |

### 11.2 소프트웨어 비용

| 항목 | 비용 | 비고 |
|------|------|------|
| SFTP 클라이언트 | 0원 | WinSCP (무료) |
| 백업 스크립트 개발 | 내부 개발 | 또는 외주 500,000~1,000,000원 |
| 모니터링 도구 | 0원 | 오픈소스 활용 |

### 11.3 네트워크 비용

| 항목 | 비용 | 비고 |
|------|------|------|
| 인터넷 대역폭 | 0원 | 기존 회선 활용 |
| VPN (선택사항) | 월 10,000~50,000원 | 필요 시 |

### 11.4 운영 비용

| 항목 | 월 비용 | 비고 |
|------|---------|------|
| 전기료 (PC 운영) | 5,000~10,000원 | 24시간 운영 시 |
| 모니터링/알림 | 0원 | 이메일 무료 |

### 11.5 총 예상 비용

**초기 구축:**
- 내부 개발: **무료** (기존 인력 활용)
- 외주 개발: **500,000~1,000,000원**

**월 운영:**
- **5,000~10,000원** (전기료)

---

## 12. 리스크 및 대응 방안

### 12.1 기술적 리스크

| 리스크 | 영향도 | 대응 방안 |
|--------|--------|-----------|
| 네트워크 단절 | 높음 | 재시도 로직, 오프라인 백업 (USB 등) |
| 내부 PC 장애 | 높음 | 백업 파일을 클라우드에도 저장 (이중화) |
| 백업 파일 손상 | 중간 | 체크섬 검증, 다중 백업 보관 |
| 용량 부족 | 중간 | 자동 정리, 용량 모니터링 |
| 복구 실패 | 높음 | 정기적인 복구 테스트, 문서화 |

### 12.2 운영 리스크

| 리스크 | 영향도 | 대응 방안 |
|--------|--------|-----------|
| 백업 누락 | 높음 | 모니터링 및 알림, 정기 점검 |
| 복구 지연 | 중간 | 복구 프로세스 문서화, 교육 |
| 보안 침해 | 높음 | 암호화, 접근 제어, 로그 기록 |

### 12.3 비즈니스 리스크

| 리스크 | 영향도 | 대응 방안 |
|--------|--------|-----------|
| 서비스 중단 | 높음 | 빠른 복구 프로세스, RTO 목표 달성 |
| 데이터 손실 | 매우 높음 | 다중 백업, 정기 검증 |

---

## 13. 향후 개선 계획

### 13.1 단기 개선 (3~6개월)
- 실시간 백업 (파일 변경 시 즉시 백업)
- 클라우드 스토리지 이중화
- 자동 복구 테스트 (월 1회)
- **WSL 환경을 활용한 자동화된 복구 테스트**
  - WSL에서 주기적으로 백업 파일 검증
  - 로컬 복구 프로세스 자동 실행 및 검증

### 13.2 중기 개선 (6~12개월)
- 다른 지역 백업 서버 구축 (재해 복구)
- 백업 압축률 개선
- 복구 시간 단축 (RTO 2시간 이하)

### 13.3 장기 개선 (1년 이상)
- 자동화된 재해 복구 (Disaster Recovery Automation)
- 멀티 클라우드 백업
- AI 기반 이상 탐지

---

## 14. 결론 및 권장사항

### 14.1 핵심 권장사항

1. **WSL 환경을 활용한 rsync 기반 백업 시스템 구축** ⭐
   - 외부 서버와 동일한 Rocky Linux 9 환경 활용
   - rsync의 증분 동기화로 효율적 백업
   - 리눅스 도구 직접 사용으로 구현 간소화
   - 백업/복구 스크립트 사전 테스트 가능

2. **3단계 백업 전략**
   - 일일 증분 + 주간 전체 + 월간 전체
   - 다양한 복구 시나리오 대응
   - WSL 환경에서 자동화

3. **자동화 및 모니터링**
   - WSL cron 또는 Windows 작업 스케줄러 활용
   - 완전 자동화로 인력 부담 최소화
   - 실시간 모니터링 및 알림

4. **정기적인 복구 테스트**
   - WSL 환경에서 분기별 복구 프로세스 테스트
   - 로컬 환경에서 복구 시나리오 반복 연습
   - 복구 프로세스 문서화 및 개선

5. **이중화 백업**
   - 내부 PC (E드라이브) + 클라우드 스토리지 (선택사항)
   - 단일 장애점 제거
   - WSL 환경에서 다중 백업 관리

### 14.2 다음 단계

1. **프로젝트 승인 및 예산 확보**
2. **Phase 1 시작**: 기반 구축
3. **네트워크 및 보안 설정**
4. **백업 스크립트 개발**
5. **테스트 및 검증**
6. **운영 전환**

---

## 부록 A: WSL 환경 설정 가이드

### A.1 WSL 환경 확인

**WSL 버전 확인:**
```bash
wsl --version
wsl --list --verbose
```

**Rocky Linux 9 배포판 확인:**
```bash
cat /etc/os-release
```

### A.2 필수 도구 설치

**rsync, tar, gzip 설치:**
```bash
sudo dnf install -y rsync tar gzip
```

**SSH 클라이언트 확인:**
```bash
which ssh
which rsync
```

### A.3 E드라이브 마운트 설정

**Windows 드라이브 접근:**
- WSL에서 Windows 드라이브는 `/mnt/e/` 경로로 접근
- E드라이브 백업 폴더: `/mnt/e/backup/cafe24/`

**마운트 확인:**
```bash
ls -la /mnt/e/backup/cafe24/
```

**권한 설정:**
```bash
sudo chmod 755 /mnt/e/backup/cafe24/
```

### A.4 SSH 키 생성 및 설정

**WSL에서 SSH 키 생성:**
```bash
ssh-keygen -t rsa -b 4096 -f ~/.ssh/id_rsa_backup -N ""
```

**공개키를 서버에 복사:**
```bash
ssh-copy-id -i ~/.ssh/id_rsa_backup.pub user@cafe24-server.com
```

**SSH 설정 파일:**
```bash
# ~/.ssh/config
Host cafe24-backup
    HostName cafe24-server.com
    User backup_user
    IdentityFile ~/.ssh/id_rsa_backup
    Port 22
```

### A.5 WSL 자동화 설정

**방법 1: WSL systemd (WSL 2)**
```bash
# systemd 활성화
sudo systemctl enable cron
sudo systemctl start cron
```

**방법 2: Windows 작업 스케줄러**
- Windows 작업 스케줄러에서 WSL 명령 실행
- 예: `wsl -d RockyLinux9 -e bash /path/to/backup-script.sh`

**방법 3: WSL cron 직접 사용**
```bash
# crontab 편집
crontab -e

# 매일 새벽 3시 백업 실행
0 3 * * * /home/user/backup-scripts/daily-backup.sh
```

### A.6 네트워크 연결 테스트

**⚠️ WSL 환경에서 실행 (로컬 PC)**

**서버 연결 테스트:**
```bash
# WSL에서 실행
# SSH 연결 테스트
ssh cafe24-backup "echo 'Connection successful'"

# rsync 연결 테스트 (서버 → WSL E드라이브)
# 서버의 백업 폴더를 E드라이브로 동기화하는 시뮬레이션
rsync -avzn --dry-run \
  user@cafe24-server.com:/var/www/.bak/backups/ \
  /mnt/e/backup/cafe24/backups/

# 실제 동기화 (시뮬레이션 확인 후)
rsync -avz \
  user@cafe24-server.com:/var/www/.bak/backups/ \
  /mnt/e/backup/cafe24/backups/
```

### A.7 백업 스크립트 실행 환경

**스크립트 위치:**
- WSL 홈 디렉토리: `~/backup-scripts/`
- Windows에서 접근: `\\wsl$\RockyLinux9\home\user\backup-scripts\`

**환경 변수 설정:**
```bash
# ~/.bashrc 또는 ~/.bash_profile
export BACKUP_DIR="/mnt/e/backup/cafe24"
export SERVER_HOST="cafe24-backup"
export LOG_DIR="/mnt/e/backup/cafe24/logs"
```

### A.8 WSL 활용 장점 요약

1. **환경 일치**: 외부 서버와 동일한 Rocky Linux 9 환경
2. **도구 활용**: rsync, tar, gzip 등 리눅스 도구 직접 사용
3. **스크립트 호환성**: 서버 스크립트와 동일한 환경에서 실행
4. **사전 테스트**: 로컬에서 백업/복구 프로세스 검증 가능
5. **개발 편의성**: Windows와 리눅스 환경 동시 활용

---

## 부록 B: 백업 파일 메타데이터 구조

```json
{
  "backup_date": "2024-12-30",
  "backup_type": "daily|weekly|monthly",
  "backup_time": "2024-12-30 03:00:00",
  "server_info": {
    "hostname": "cafe24-server",
    "php_version": "8.1",
    "mysql_version": "10.x"
  },
  "files": {
    "total_count": 15234,
    "total_size": 10737418240,
    "compressed_size": 2147483648,
    "file_list": "file_list.txt"
  },
  "database": {
    "databases": ["mek_kr", "mek_en", "mek_jp", "mek_cn", "mek_de", "mek_es"],
    "dump_size": 536870912,
    "compressed_size": 134217728
  },
  "checksum": {
    "files": "sha256:abc123...",
    "database": "sha256:def456...",
    "metadata": "sha256:ghi789..."
  },
  "backup_duration": 1800
}
```

## 부록 C: 복구 체크리스트

### 복구 전 확인사항
- [ ] 복구할 백업 날짜 확인 (최신 백업 권장)
- [ ] 백업 파일 무결성 검증 (파일 존재, 크기 확인)
- [ ] 새 서버 접근 권한 확인 (SSH 접속, sudo 권한)
- [ ] 새 서버 사양 확인 (Rocky Linux 9, PHP 8.1, MariaDB)
- [ ] DNS 변경 계획 수립 (A 레코드 변경, TTL 고려)
- [ ] 복구 시간 예상 (RTO 4~10시간)
- [ ] 백업 파일 전송 경로 확인 (내부 PC → 새 서버)

### 초기 서버 환경 구축 (Step 2)
- [ ] setup.sh 스크립트 실행
  - [ ] 환경 선택 (물리 서버)
  - [ ] PHP 버전 선택 (PHP 8.1)
- [ ] 시스템 업데이트 완료 확인
- [ ] Apache 설치 및 서비스 시작 확인
- [ ] MariaDB 설치 및 서비스 시작 확인
- [ ] PHP 설치 및 확장 모듈 확인 (php -m)
- [ ] 웹루트 디렉토리 생성 확인 (/var/www/html, /var/www/tmp)
- [ ] 권한 설정 확인 (apache:user, 775/664)
- [ ] 추가 패키지/라이브러리 재설치
  - [ ] ImageMagick (php-imagick)
  - [ ] Redis (php-redis)
  - [ ] 기타 추가 설치된 패키지
- [ ] Postfix 설치 및 설정
  - [ ] Postfix 설치 (dnf install postfix)
  - [ ] /etc/postfix/main.cf 복원 (백업본에서)
  - [ ] Postfix 서비스 시작 및 상태 확인
  - [ ] 메일 발송 테스트

### 백업 파일 복원 (Step 3)
- [ ] 백업 파일 전송 (내부 PC → 새 서버)
  - [ ] etc_httpd.tar.gz
  - [ ] etc_php.tar.gz
  - [ ] var_www_html.tar.gz
  - [ ] db_backup.sql
- [ ] 시스템 설정 파일 복원
  - [ ] Apache 설정 복원 (/etc/httpd)
  - [ ] PHP 설정 복원 (/etc/php*)
  - [ ] Apache 재시작 및 상태 확인
- [ ] 웹사이트 파일 복원
  - [ ] /var/www/html 압축 해제
  - [ ] 권한 설정 (chown, chmod)
  - [ ] ACL 설정 (setfacl)
  - [ ] /var/www/tmp 권한 설정
- [ ] 데이터베이스 복원
  - [ ] DB 백업 파일 복원 (mysql < db_backup.sql)
  - [ ] 데이터베이스 목록 확인 (SHOW DATABASES)
  - [ ] 다국어 DB 확인 (mek_kr, mek_en, mek_jp, mek_cn, mek_de, mek_es)
  - [ ] 데이터베이스 사용자 권한 확인 및 재설정

### 서비스 재시작 및 확인 (Step 4)
- [ ] Apache 재시작 및 상태 확인
- [ ] MariaDB 재시작 및 상태 확인
- [ ] Postfix 재시작 및 상태 확인
- [ ] PHP-FPM 재시작 및 상태 확인
- [ ] 서비스 로그 확인 (journalctl, tail -f)

### 변경사항 동기화 (Step 5)
- [ ] 백업 복원 이후 변경사항 확인
- [ ] rsync 동기화 (필요 시)
  - [ ] 동기화 소스 확인 (기존 서버 또는 백업)
  - [ ] rsync 명령어 실행
  - [ ] 동기화 결과 확인
- [ ] 동기화 후 서비스 재시작 (필요 시)

### DNS 변경 및 검증 (Step 6)
- [ ] DNS A 레코드 변경 (도메인 관리 페이지)
- [ ] DNS 전파 대기 (TTL 고려)
- [ ] 웹사이트 접속 테스트
  - [ ] 로컬 접속 테스트 (curl http://localhost)
  - [ ] 도메인 접속 테스트 (curl http://mekeng.com)
  - [ ] HTTPS 접속 테스트 (SSL 인증서 설정 확인)
- [ ] 로그인 기능 테스트
- [ ] 파일 업로드 기능 테스트
- [ ] 메일 발송 기능 테스트 (Postfix)
- [ ] 데이터베이스 연결 확인
- [ ] 다국어 사이트 접속 확인
  - [ ] mekeng.com (홈)
  - [ ] mekeng.com/kr
  - [ ] mekeng.com/en
  - [ ] mekeng.com/jp
  - [ ] mekeng.com/cn
  - [ ] mekeng.com/de
  - [ ] mekeng.com/es

### 복구 후 검증 및 모니터링
- [ ] 웹사이트 주요 기능 테스트
  - [ ] 게시판 기능
  - [ ] 파일 업로드/다운로드
  - [ ] 메일 발송 (Postfix)
  - [ ] 관리자 페이지 접속
- [ ] 데이터 무결성 확인
  - [ ] 데이터베이스 데이터 확인
  - [ ] 업로드 파일 확인
  - [ ] 설정 파일 확인
- [ ] 성능 모니터링
  - [ ] 서버 부하 확인 (top, htop)
  - [ ] 디스크 사용량 확인 (df -h)
  - [ ] 메모리 사용량 확인 (free -h)
  - [ ] 네트워크 상태 확인
- [ ] 로그 확인
  - [ ] Apache 에러 로그
  - [ ] PHP 에러 로그
  - [ ] MariaDB 에러 로그
  - [ ] Postfix 메일 로그
- [ ] 백업 시스템 재구축 (복구 완료 후)
  - [ ] backup.sh 스크립트 설치
  - [ ] cron 자동화 설정
  - [ ] 백업 테스트 실행

---

## 부록 D: 복구 후 rsync 증분 동기화 가이드

### D.1 rsync 동기화 개요

백업본을 직접 복원한 이후, 추가로 변경된 파일이나 누락된 파일을 동기화하는 절차입니다.

**사용 시나리오:**
1. 백업 복원 후 추가 변경사항이 있는 경우
2. 기존 서버가 살아있어 최신 데이터와 동기화가 필요한 경우
3. 다른 백업 소스와 동기화가 필요한 경우

### D.2 rsync 기본 명령어

**기본 형식:**
```bash
rsync [옵션] 소스/ 대상/
```

**주요 옵션:**
- `-a` (--archive): 아카이브 모드 (권한, 시간, 심볼릭 링크 보존)
- `-v` (--verbose): 상세 출력
- `-z` (--compress): 전송 시 압축
- `--delete`: 소스에 없는 파일 삭제 (주의: 신중하게 사용)
- `--exclude=PATTERN`: 제외할 파일/폴더 패턴
- `--dry-run`: 실제 전송 없이 시뮬레이션만 실행
- `-n`: --dry-run과 동일

### D.3 복구 후 동기화 절차

#### D.3.1 기존 서버와 동기화 (서버가 살아있는 경우)

```bash
# 1. 시뮬레이션 실행 (안전 확인)
rsync -avzn --exclude='.bak' \
  user@old-server.com:/var/www/html/ \
  /var/www/html/

# 2. 실제 동기화 실행
rsync -avz --exclude='.bak' \
  --exclude='cache' \
  --exclude='tmp/cache' \
  user@old-server.com:/var/www/html/ \
  /var/www/html/
```

#### D.3.2 내부 PC 백업과 동기화

```bash
# WSL 환경에서 실행
rsync -avz --exclude='.bak' \
  /mnt/e/backup/cafe24/backups/latest/var_www_html/ \
  user@new-server.com:/var/www/html/
```

#### D.3.3 특정 디렉토리만 동기화

```bash
# 업로드 파일만 동기화
rsync -avz \
  user@old-server.com:/var/www/html/data/ \
  /var/www/html/data/

# 특정 사이트만 동기화 (예: home 디렉토리)
rsync -avz \
  user@old-server.com:/var/www/html/home/ \
  /var/www/html/home/
```

### D.4 동기화 예외 항목

**제외해야 할 파일/폴더:**
- `.bak/`: 백업 파일
- `cache/`: 캐시 파일
- `tmp/cache/`: 임시 캐시
- `*.log`: 로그 파일 (선택사항)
- `.git/`: Git 저장소 (있는 경우)
- `node_modules/`: Node.js 모듈 (있는 경우)

**예시:**
```bash
rsync -avz \
  --exclude='.bak' \
  --exclude='cache' \
  --exclude='tmp/cache' \
  --exclude='*.log' \
  --exclude='.git' \
  source/ destination/
```

### D.5 동기화 스크립트 예시

```bash
#!/bin/bash
# sync-after-restore.sh - 복구 후 동기화 스크립트

OLD_SERVER="user@old-server.com"
NEW_SERVER="user@new-server.com"
WEB_ROOT="/var/www/html"

echo "=== 복구 후 동기화 시작 ==="

# 1. 시뮬레이션 실행
echo "[INFO] 동기화 시뮬레이션 실행..."
rsync -avzn \
  --exclude='.bak' \
  --exclude='cache' \
  --exclude='tmp/cache' \
  $OLD_SERVER:$WEB_ROOT/ \
  $WEB_ROOT/

read -p "동기화를 진행하시겠습니까? (y/n): " confirm
if [ "$confirm" != "y" ]; then
    echo "[INFO] 동기화 취소"
    exit 0
fi

# 2. 실제 동기화 실행
echo "[INFO] 동기화 실행 중..."
rsync -avz \
  --exclude='.bak' \
  --exclude='cache' \
  --exclude='tmp/cache' \
  $OLD_SERVER:$WEB_ROOT/ \
  $WEB_ROOT/

# 3. 권한 설정
echo "[INFO] 권한 설정..."
sudo chown -R apache:user $WEB_ROOT
sudo find $WEB_ROOT -type d -exec chmod 775 {} \;
sudo find $WEB_ROOT -type f -exec chmod 664 {} \;

# 4. 서비스 재시작 (필요 시)
echo "[INFO] Apache 재시작..."
sudo systemctl restart httpd

echo "[INFO] 동기화 완료!"
```

### D.6 동기화 후 검증

```bash
# 1. 파일 개수 확인
find /var/www/html -type f | wc -l

# 2. 디렉토리 크기 확인
du -sh /var/www/html

# 3. 최근 변경된 파일 확인
find /var/www/html -type f -mtime -1 -ls

# 4. 웹사이트 접속 테스트
curl -I http://localhost
```

### D.7 주의사항

1. **--delete 옵션 주의**: 소스에 없는 파일을 삭제하므로 신중하게 사용
2. **백업 복원 후 즉시 동기화하지 말 것**: 먼저 복원된 시스템이 정상 작동하는지 확인
3. **시뮬레이션 먼저 실행**: `--dry-run` 또는 `-n` 옵션으로 먼저 확인
4. **권한 확인**: 동기화 후 파일 권한이 올바른지 확인
5. **서비스 재시작**: 필요시 Apache, PHP-FPM 등 서비스 재시작

---

**문서 버전**: 2.3  
**최종 수정일**: 2025-01-01  
**작성자**: MEK 개발팀  
**변경 사항**:
- backup.sh 및 setup.sh 기준으로 백업 정책 업데이트
- 재해 복구 프로세스 상세화 (초기 서버 세팅, Postfix 설정, DB 복원, 웹서버 복원)
- 백업본 직접 복원 후 rsync 증분 동기화 절차 추가
- 복구 체크리스트 상세화
- **메일 관련 내용 통합 (v2.3)**:
  - 메일 백업 및 복구 전략, MEK+ 메일러용 고객 메일 리스트 관리 방안 등을 "README_MEK_고객메일_중앙통합관리_기획서.md" 문서로 이동
  - 본 문서에서는 간단한 참조만 유지 (5.6 섹션)

