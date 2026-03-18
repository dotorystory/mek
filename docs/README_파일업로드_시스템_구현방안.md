# 📁 파일 업로드 시스템 구현 방안

## 📋 목차
1. [요구사항 분석](#요구사항-분석)
2. [시스템 아키텍처](#시스템-아키텍처)
3. [구현 방안](#구현-방안)
4. [보안 고려사항](#보안-고려사항)
5. [자동 동기화 시스템](#자동-동기화-시스템)
6. [구현 단계](#구현-단계)
7. [예상 비용 및 시간](#예상-비용-및-시간)

---

## 📌 요구사항 분석

### 1. 기능 요구사항

#### ✅ 필수 기능
- **관리자 인증**: 그누보드 회원 로그인 후 업로드 페이지 접근
- **파일 업로드**: 휴대폰 등에서 사진/영상/문서 업로드
- **저장 위치**: `/var/www/html/mekeng.com/upload` 또는 `/var/www/html/mekeng.com/home/data`
- **윈도우 탐색기 UI**: 파일을 편리하게 관리할 수 있는 웹 인터페이스
- **자동 동기화**: 회사 내부 특정 폴더로 자동 업로드

#### 📂 저장 폴더 비교

| 항목 | `/var/www/html/mekeng.com/upload` | `/var/www/html/mekeng.com/home/data` |
|------|-----------------------------------|--------------------------------------|
| **장점** | - 그누보드와 독립적<br>- 관리가 명확<br>- 백업이 용이 | - 그누보드 권한 시스템 활용<br>- 기존 구조 통합 |
| **단점** | - 별도 보안 설정 필요 | - 그누보드 의존성<br>- data 폴더 비대화 |
| **권장** | ✅ **추천** (독립성, 확장성) | ⚠️ 그누보드 긴밀 통합 시 |

**결론**: `/var/www/html/mekeng.com/upload` 사용 권장

---

## 🏗️ 시스템 아키텍처

```
┌─────────────────────────────────────────────────────────────┐
│                     외부 사용자 (휴대폰 등)                    │
└────────────────────────┬────────────────────────────────────┘
                         │ HTTPS
                         ↓
┌─────────────────────────────────────────────────────────────┐
│              웹 서버 (mekeng.com)                            │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ 그누보드 인증 시스템 (/home/plus/upload_manager.php)  │    │
│  └──────────────────────┬──────────────────────────────┘    │
│                         │ 로그인 확인                         │
│                         ↓                                    │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ 파일 업로드 인터페이스 (elFinder 또는 Dropzone.js)     │    │
│  └──────────────────────┬──────────────────────────────┘    │
│                         │ 파일 저장                           │
│                         ↓                                    │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ /var/www/html/mekeng.com/upload/{회원ID}/           │    │
│  │  ├─ documents/                                      │    │
│  │  ├─ images/                                         │    │
│  │  └─ videos/                                         │    │
│  └──────────────────────┬──────────────────────────────┘    │
└─────────────────────────┼───────────────────────────────────┘
                         │ rsync/scp/samba
                         ↓
┌─────────────────────────────────────────────────────────────┐
│            회사 내부 서버 (Network Share)                     │
│  ├─ \\company-server\uploads\{날짜}\{회원ID}\              │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔧 구현 방안

### 방안 1: elFinder 기반 (권장 ⭐⭐⭐⭐⭐)

#### 📦 elFinder 특징
- **윈도우 탐색기와 유사한 UI**
- **드래그 앤 드롭** 지원
- **다중 파일 업로드**
- **이미지 미리보기** 및 편집
- **폴더 생성/삭제/이동**
- **파일 검색** 기능
- **모바일 최적화**
- **PHP 기반** (그누보드와 호환)

#### 장점
- ✅ 완성도 높은 파일 관리 UI
- ✅ 대용량 파일 지원
- ✅ 썸네일 자동 생성
- ✅ 파일 타입 제한 가능
- ✅ 한국어 지원

#### 단점
- ⚠️ 초기 설정이 복잡
- ⚠️ 라이센스 확인 필요 (BSD 라이센스)

#### 구현 복잡도: ⭐⭐⭐ (중급)

---

### 방안 2: Dropzone.js + 커스텀 파일 관리자

#### 📦 Dropzone.js 특징
- **드래그 앤 드롭** 업로드
- **진행률 표시**
- **이미지 미리보기**
- **경량화** (100KB 미만)
- **MIT 라이센스** (무료)

#### 추가 개발 필요
- 파일 목록 표시
- 폴더 구조 관리
- 파일 삭제/다운로드 기능

#### 장점
- ✅ 가볍고 빠름
- ✅ 커스터마이징 용이
- ✅ 모던한 디자인

#### 단점
- ⚠️ 파일 관리 기능 직접 구현 필요
- ⚠️ 개발 시간 증가

#### 구현 복잡도: ⭐⭐⭐⭐ (중상급)

---

### 방안 3: 그누보드 자체 업로드 확장

#### 그누보드 파일 업로드 시스템 활용
- 기존 게시판 업로드 로직 재사용
- `home/bbs/download.php` 참고

#### 장점
- ✅ 그누보드 권한 시스템 완벽 통합
- ✅ 기존 코드 재사용

#### 단점
- ⚠️ UI가 단순함
- ⚠️ 윈도우 탐색기 같은 UX 구현 어려움
- ⚠️ 모바일 최적화 부족

#### 구현 복잡도: ⭐⭐ (하급)

---

## 💡 최종 권장 방안: elFinder + 그누보드 인증

### 이유
1. **윈도우 탐색기 같은 UX** 요구사항 충족
2. **모바일 최적화** (휴대폰 업로드)
3. **보안**: 그누보드 인증 시스템 활용
4. **유지보수**: 검증된 오픈소스

---

## 🔐 보안 고려사항

### 1. 인증 및 권한

#### 그누보드 회원 레벨 활용
```php
// /home/plus/upload_manager.php
<?php
include_once('../common.php');

// 로그인 확인
if (!$is_member) {
    alert('회원만 이용할 수 있습니다.', G5_BBS_URL.'/login.php?url='.urlencode($_SERVER['REQUEST_URI']));
}

// 회원 레벨 3 이상만 허용
if ($member['mb_level'] < 3 && $is_admin != 'super') {
    alert('파일 업로드는 회원 레벨 3 이상만 이용 가능합니다.\\n\\n레벨 업그레이드는 관리자에게 문의하세요.', G5_URL);
}

// 접근 로그 기록
log_file_access($member['mb_id'], 'ACCESS', '파일 관리자 접속', $_SERVER['REMOTE_ADDR']);
?>
```

### 2. 파일 업로드 보안

#### 허용 파일 타입
```php
$allowed_extensions = [
    'images'    => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'heic'],
    'videos'    => ['mp4', 'mov', 'avi', 'mkv', 'webm'],
    'documents' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'hwp']
];
```

#### 파일 크기 제한
```php
// php.ini 설정
upload_max_filesize = 500M
post_max_size = 500M
max_execution_time = 300
memory_limit = 512M
```

#### 파일명 보안 처리
```php
// 한글 파일명 안전하게 처리
function secure_filename($filename) {
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $name = pathinfo($filename, PATHINFO_FILENAME);
    
    // 시간 + 랜덤 + 원본파일명(해시)
    $secure_name = date('YmdHis') . '_' . substr(md5($name), 0, 8) . '.' . strtolower($ext);
    
    return $secure_name;
}
```

### 3. 디렉터리 보안

#### .htaccess 설정
```apache
# /var/www/html/mekeng.com/upload/.htaccess
<Files ~ "\.php$">
    Order allow,deny
    Deny from all
</Files>

# 직접 접근 차단 (다운로드는 PHP 스크립트를 통해서만)
Options -Indexes

# 특정 확장자만 허용
<FilesMatch "\.(jpg|jpeg|png|gif|webp|pdf|doc|docx|xls|xlsx|mp4|mov)$">
    Order allow,deny
    Allow from all
</FilesMatch>
```

#### 폴더 권한 설정
```bash
# upload 폴더 생성 및 권한
mkdir -p /var/www/html/mekeng.com/upload
chown -R apache:user /var/www/html/mekeng.com/upload
chmod 755 /var/www/html/mekeng.com/upload

# 하위 폴더는 770 (웹서버와 관리자만)
find /var/www/html/mekeng.com/upload -type d -exec chmod 770 {} \;
find /var/www/html/mekeng.com/upload -type f -exec chmod 660 {} \;
```

### 4. 회원별 폴더 분리

```
/var/www/html/mekeng.com/upload/
├── admin/              (최고관리자)
│   ├── documents/
│   ├── images/
│   └── videos/
├── user01/             (일반 회원)
│   ├── documents/
│   └── images/
└── .htaccess
```

### 5. 업로드/다운로드 로그 시스템

#### 데이터베이스 테이블 설계

```sql
-- 파일 업로드/다운로드 로그 테이블
CREATE TABLE IF NOT EXISTS `g5_file_log` (
  `fl_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '로그 ID',
  `fl_mb_id` varchar(20) NOT NULL COMMENT '회원 아이디',
  `fl_type` enum('UPLOAD','DOWNLOAD','DELETE','ACCESS') NOT NULL COMMENT '작업 타입',
  `fl_filename` varchar(255) NOT NULL COMMENT '파일명',
  `fl_filepath` varchar(500) DEFAULT NULL COMMENT '파일 경로',
  `fl_filesize` bigint(20) DEFAULT 0 COMMENT '파일 크기(bytes)',
  `fl_ip` varchar(50) NOT NULL COMMENT 'IP 주소',
  `fl_datetime` datetime NOT NULL COMMENT '작업 일시',
  `fl_note` varchar(500) DEFAULT NULL COMMENT '비고',
  PRIMARY KEY (`fl_id`),
  KEY `fl_mb_id` (`fl_mb_id`),
  KEY `fl_type` (`fl_type`),
  KEY `fl_datetime` (`fl_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='파일 업로드/다운로드 로그';

-- 회원별 파일 통계 테이블
CREATE TABLE IF NOT EXISTS `g5_file_stats` (
  `fs_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '통계 ID',
  `fs_mb_id` varchar(20) NOT NULL COMMENT '회원 아이디',
  `fs_total_files` int(11) DEFAULT 0 COMMENT '총 파일 개수',
  `fs_total_size` bigint(20) DEFAULT 0 COMMENT '총 용량(bytes)',
  `fs_upload_count` int(11) DEFAULT 0 COMMENT '업로드 횟수',
  `fs_download_count` int(11) DEFAULT 0 COMMENT '다운로드 횟수',
  `fs_last_upload` datetime DEFAULT NULL COMMENT '마지막 업로드',
  `fs_last_download` datetime DEFAULT NULL COMMENT '마지막 다운로드',
  `fs_update_datetime` datetime NOT NULL COMMENT '업데이트 일시',
  PRIMARY KEY (`fs_id`),
  UNIQUE KEY `fs_mb_id` (`fs_mb_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='회원별 파일 통계';
```

#### 로그 기록 함수

```php
<?php
// /home/lib/file_log.lib.php

/**
 * 파일 작업 로그 기록
 */
function log_file_access($mb_id, $type, $filename, $ip, $filepath = '', $filesize = 0, $note = '') {
    global $g5;
    
    $mb_id = sql_escape_string($mb_id);
    $type = sql_escape_string($type);
    $filename = sql_escape_string($filename);
    $filepath = sql_escape_string($filepath);
    $ip = sql_escape_string($ip);
    $note = sql_escape_string($note);
    
    $sql = "INSERT INTO g5_file_log 
            (fl_mb_id, fl_type, fl_filename, fl_filepath, fl_filesize, fl_ip, fl_datetime, fl_note) 
            VALUES 
            ('{$mb_id}', '{$type}', '{$filename}', '{$filepath}', {$filesize}, '{$ip}', NOW(), '{$note}')";
    
    sql_query($sql);
    
    // 통계 업데이트
    update_file_stats($mb_id, $type, $filesize);
}

/**
 * 파일 업로드 로그
 */
function log_file_upload($mb_id, $filename, $filepath, $filesize, $ip) {
    log_file_access($mb_id, 'UPLOAD', $filename, $ip, $filepath, $filesize, '파일 업로드');
}

/**
 * 파일 다운로드 로그
 */
function log_file_download($mb_id, $filename, $filepath, $filesize, $ip) {
    log_file_access($mb_id, 'DOWNLOAD', $filename, $ip, $filepath, $filesize, '파일 다운로드');
}

/**
 * 파일 삭제 로그
 */
function log_file_delete($mb_id, $filename, $filepath, $filesize, $ip) {
    log_file_access($mb_id, 'DELETE', $filename, $ip, $filepath, $filesize, '파일 삭제');
}

/**
 * 회원별 파일 통계 업데이트
 */
function update_file_stats($mb_id, $type, $filesize = 0) {
    global $g5;
    
    $mb_id = sql_escape_string($mb_id);
    
    // 통계 레코드 존재 확인
    $stats = sql_fetch("SELECT * FROM g5_file_stats WHERE fs_mb_id = '{$mb_id}'");
    
    if (!$stats) {
        // 신규 생성
        $sql = "INSERT INTO g5_file_stats 
                (fs_mb_id, fs_total_files, fs_total_size, fs_upload_count, fs_download_count, 
                 fs_last_upload, fs_last_download, fs_update_datetime) 
                VALUES 
                ('{$mb_id}', 0, 0, 0, 0, NULL, NULL, NOW())";
        sql_query($sql);
        $stats = sql_fetch("SELECT * FROM g5_file_stats WHERE fs_mb_id = '{$mb_id}'");
    }
    
    // 통계 업데이트
    if ($type == 'UPLOAD') {
        $sql = "UPDATE g5_file_stats SET 
                fs_upload_count = fs_upload_count + 1,
                fs_last_upload = NOW(),
                fs_update_datetime = NOW()
                WHERE fs_mb_id = '{$mb_id}'";
    } elseif ($type == 'DOWNLOAD') {
        $sql = "UPDATE g5_file_stats SET 
                fs_download_count = fs_download_count + 1,
                fs_last_download = NOW(),
                fs_update_datetime = NOW()
                WHERE fs_mb_id = '{$mb_id}'";
    } elseif ($type == 'DELETE') {
        $sql = "UPDATE g5_file_stats SET 
                fs_update_datetime = NOW()
                WHERE fs_mb_id = '{$mb_id}'";
    }
    
    if (isset($sql)) {
        sql_query($sql);
    }
    
    // 실제 파일 개수 및 용량 재계산
    recalculate_file_stats($mb_id);
}

/**
 * 실제 파일 개수 및 용량 재계산
 */
function recalculate_file_stats($mb_id) {
    global $g5;
    
    $upload_path = "/var/www/html/mekeng.com/upload/{$mb_id}";
    
    if (!is_dir($upload_path)) {
        return;
    }
    
    $total_size = 0;
    $file_count = 0;
    
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($upload_path, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    
    foreach ($files as $file) {
        if ($file->isFile() && !preg_match('/\.tmb/', $file->getPathname())) {
            $total_size += $file->getSize();
            $file_count++;
        }
    }
    
    $mb_id = sql_escape_string($mb_id);
    $sql = "UPDATE g5_file_stats SET 
            fs_total_files = {$file_count},
            fs_total_size = {$total_size},
            fs_update_datetime = NOW()
            WHERE fs_mb_id = '{$mb_id}'";
    
    sql_query($sql);
}

/**
 * 회원별 파일 통계 조회
 */
function get_file_stats($mb_id) {
    global $g5;
    
    $mb_id = sql_escape_string($mb_id);
    $stats = sql_fetch("SELECT * FROM g5_file_stats WHERE fs_mb_id = '{$mb_id}'");
    
    if (!$stats) {
        return [
            'total_files' => 0,
            'total_size' => 0,
            'total_size_mb' => 0,
            'upload_count' => 0,
            'download_count' => 0,
            'last_upload' => null,
            'last_download' => null
        ];
    }
    
    return [
        'total_files' => $stats['fs_total_files'],
        'total_size' => $stats['fs_total_size'],
        'total_size_mb' => round($stats['fs_total_size'] / 1024 / 1024, 2),
        'upload_count' => $stats['fs_upload_count'],
        'download_count' => $stats['fs_download_count'],
        'last_upload' => $stats['fs_last_upload'],
        'last_download' => $stats['fs_last_download']
    ];
}

/**
 * 회원별 최근 로그 조회
 */
function get_recent_file_logs($mb_id, $limit = 20) {
    global $g5;
    
    $mb_id = sql_escape_string($mb_id);
    $limit = (int)$limit;
    
    $sql = "SELECT * FROM g5_file_log 
            WHERE fl_mb_id = '{$mb_id}' 
            ORDER BY fl_datetime DESC 
            LIMIT {$limit}";
    
    $result = sql_query($sql);
    $logs = [];
    
    while ($row = sql_fetch_array($result)) {
        $logs[] = $row;
    }
    
    return $logs;
}
?>
```

---

## 🔄 자동 동기화 시스템

### 방법 1: rsync (리눅스 → 리눅스) ⭐⭐⭐⭐⭐

#### 특징
- **증분 동기화** (변경된 파일만 전송)
- **압축 전송** (대역폭 절약)
- **SSH 암호화**
- **빠르고 안정적**

#### 구현 예시

```bash
#!/bin/bash
# /var/www/html/mekeng.com/scripts/sync_uploads.sh

# 설정
SOURCE_DIR="/var/www/html/mekeng.com/upload/"
DEST_SERVER="company-server.local"
DEST_USER="syncuser"
DEST_DIR="/mnt/company-share/website-uploads/"
LOG_FILE="/var/log/upload_sync.log"
DATE_FOLDER=$(date +%Y%m%d)

# rsync 실행
echo "[$(date '+%Y-%m-%d %H:%M:%S')] 동기화 시작" >> $LOG_FILE

rsync -avz --delete \
    --exclude='.htaccess' \
    --exclude='*.tmp' \
    -e "ssh -p 22 -i /home/syncuser/.ssh/id_rsa" \
    "$SOURCE_DIR" \
    "${DEST_USER}@${DEST_SERVER}:${DEST_DIR}${DATE_FOLDER}/" \
    >> $LOG_FILE 2>&1

if [ $? -eq 0 ]; then
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] ✓ 동기화 완료" >> $LOG_FILE
else
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] ✗ 동기화 실패" >> $LOG_FILE
fi
```

#### cron 설정
```bash
# 매일 새벽 2시, 오후 6시 자동 동기화
0 2,18 * * * /var/www/html/mekeng.com/scripts/sync_uploads.sh
```

#### SSH 키 설정 (비밀번호 없이 자동 실행)
```bash
# 1. SSH 키 생성
ssh-keygen -t rsa -b 4096 -f /home/syncuser/.ssh/id_rsa -N ""

# 2. 공개키를 대상 서버에 복사
ssh-copy-id -i /home/syncuser/.ssh/id_rsa.pub syncuser@company-server.local

# 3. 권한 설정
chmod 700 /home/syncuser/.ssh
chmod 600 /home/syncuser/.ssh/id_rsa
```

---

### 방법 2: Samba (리눅스 → Windows 공유폴더) ⭐⭐⭐⭐

#### 특징
- **Windows 네트워크 공유** 지원
- **도메인 인증** 가능
- **설정이 간단**

#### 구현 예시

```bash
#!/bin/bash
# /var/www/html/mekeng.com/scripts/sync_to_samba.sh

# 설정
SOURCE_DIR="/var/www/html/mekeng.com/upload/"
SAMBA_SERVER="//192.168.1.100/uploads"
SAMBA_USER="domain\\username"
SAMBA_PASS="password"
MOUNT_POINT="/mnt/company-share"
DATE_FOLDER=$(date +%Y%m%d)
LOG_FILE="/var/log/upload_sync.log"

# Samba 마운트
if ! mountpoint -q "$MOUNT_POINT"; then
    mount -t cifs "$SAMBA_SERVER" "$MOUNT_POINT" \
        -o username="$SAMBA_USER",password="$SAMBA_PASS",vers=3.0
    
    if [ $? -ne 0 ]; then
        echo "[$(date)] ✗ Samba 마운트 실패" >> $LOG_FILE
        exit 1
    fi
fi

# 파일 복사
echo "[$(date)] 동기화 시작" >> $LOG_FILE
rsync -av --delete "$SOURCE_DIR" "$MOUNT_POINT/$DATE_FOLDER/" >> $LOG_FILE 2>&1

if [ $? -eq 0 ]; then
    echo "[$(date)] ✓ 동기화 완료" >> $LOG_FILE
else
    echo "[$(date)] ✗ 동기화 실패" >> $LOG_FILE
fi

# 언마운트
umount "$MOUNT_POINT"
```

#### 보안 강화: 비밀번호 파일 분리
```bash
# /root/.smbcredentials (권한 600)
username=domain\username
password=SecurePassword123!
domain=COMPANY

# 스크립트에서 사용
mount -t cifs "$SAMBA_SERVER" "$MOUNT_POINT" \
    -o credentials=/root/.smbcredentials,vers=3.0
```

---

### 방법 3: FTP/SFTP (범용) ⭐⭐⭐

#### lftp를 이용한 자동 업로드

```bash
#!/bin/bash
# /var/www/html/mekeng.com/scripts/sync_via_ftp.sh

SOURCE_DIR="/var/www/html/mekeng.com/upload/"
FTP_HOST="ftp.company-server.local"
FTP_USER="uploaduser"
FTP_PASS="password"
FTP_DIR="/website-uploads/$(date +%Y%m%d)"
LOG_FILE="/var/log/upload_sync.log"

lftp -c "
    set ftp:ssl-allow no
    set net:timeout 30
    set net:max-retries 3
    open -u $FTP_USER,$FTP_PASS $FTP_HOST
    mirror --reverse --delete --verbose \
        $SOURCE_DIR $FTP_DIR
    bye
" >> $LOG_FILE 2>&1

if [ $? -eq 0 ]; then
    echo "[$(date)] ✓ FTP 동기화 완료" >> $LOG_FILE
else
    echo "[$(date)] ✗ FTP 동기화 실패" >> $LOG_FILE
fi
```

---

### 방법 4: 실시간 동기화 (inotify-tools) ⭐⭐⭐⭐⭐

#### 파일이 업로드되면 즉시 동기화

```bash
#!/bin/bash
# /var/www/html/mekeng.com/scripts/realtime_sync.sh

SOURCE_DIR="/var/www/html/mekeng.com/upload/"
DEST_SERVER="company-server.local"
DEST_USER="syncuser"
DEST_DIR="/mnt/company-share/website-uploads/"

# inotifywait로 파일 생성/수정 감지
inotifywait -m -r -e close_write,create,delete,move "$SOURCE_DIR" | while read path action file; do
    echo "[$(date)] 감지: $path$file ($action)"
    
    # 즉시 rsync
    rsync -az \
        -e "ssh -i /home/syncuser/.ssh/id_rsa" \
        "$SOURCE_DIR" \
        "${DEST_USER}@${DEST_SERVER}:${DEST_DIR}"
    
    echo "[$(date)] ✓ 동기화 완료"
done
```

#### systemd 서비스로 등록
```ini
# /etc/systemd/system/upload-sync.service
[Unit]
Description=Real-time Upload Sync Service
After=network.target

[Service]
Type=simple
User=apache
ExecStart=/var/www/html/mekeng.com/scripts/realtime_sync.sh
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
```

```bash
# 서비스 활성화
sudo systemctl enable upload-sync.service
sudo systemctl start upload-sync.service
sudo systemctl status upload-sync.service
```

---

### 동기화 방법 비교

| 방법 | 속도 | 보안 | 복잡도 | 실시간 | 추천도 |
|------|------|------|--------|--------|--------|
| **rsync (SSH)** | ⚡⚡⚡⚡ | 🔒🔒🔒🔒🔒 | ⭐⭐⭐ | ❌ | ⭐⭐⭐⭐⭐ |
| **Samba (CIFS)** | ⚡⚡⚡ | 🔒🔒🔒 | ⭐⭐ | ❌ | ⭐⭐⭐⭐ |
| **FTP/SFTP** | ⚡⚡ | 🔒🔒🔒 | ⭐⭐ | ❌ | ⭐⭐⭐ |
| **inotify + rsync** | ⚡⚡⚡⚡⚡ | 🔒🔒🔒🔒🔒 | ⭐⭐⭐⭐ | ✅ | ⭐⭐⭐⭐⭐ |

**최종 권장**: 
- **정기 동기화**: rsync + cron
- **실시간 필요 시**: inotify-tools + rsync

---

## 📋 구현 단계

### Phase 1: 기본 구조 및 데이터베이스 설정 (1-2시간)

#### 1-1. 데이터베이스 테이블 생성

```bash
# MySQL 접속
mysql -u root -p

# 또는 phpMyAdmin에서 SQL 실행
```

```sql
-- mek_kr 데이터베이스 사용
USE mek_kr;

-- 파일 로그 테이블
CREATE TABLE IF NOT EXISTS `g5_file_log` (
  `fl_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '로그 ID',
  `fl_mb_id` varchar(20) NOT NULL COMMENT '회원 아이디',
  `fl_type` enum('UPLOAD','DOWNLOAD','DELETE','ACCESS') NOT NULL COMMENT '작업 타입',
  `fl_filename` varchar(255) NOT NULL COMMENT '파일명',
  `fl_filepath` varchar(500) DEFAULT NULL COMMENT '파일 경로',
  `fl_filesize` bigint(20) DEFAULT 0 COMMENT '파일 크기(bytes)',
  `fl_ip` varchar(50) NOT NULL COMMENT 'IP 주소',
  `fl_datetime` datetime NOT NULL COMMENT '작업 일시',
  `fl_note` varchar(500) DEFAULT NULL COMMENT '비고',
  PRIMARY KEY (`fl_id`),
  KEY `fl_mb_id` (`fl_mb_id`),
  KEY `fl_type` (`fl_type`),
  KEY `fl_datetime` (`fl_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='파일 업로드/다운로드 로그';

-- 파일 통계 테이블
CREATE TABLE IF NOT EXISTS `g5_file_stats` (
  `fs_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '통계 ID',
  `fs_mb_id` varchar(20) NOT NULL COMMENT '회원 아이디',
  `fs_total_files` int(11) DEFAULT 0 COMMENT '총 파일 개수',
  `fs_total_size` bigint(20) DEFAULT 0 COMMENT '총 용량(bytes)',
  `fs_upload_count` int(11) DEFAULT 0 COMMENT '업로드 횟수',
  `fs_download_count` int(11) DEFAULT 0 COMMENT '다운로드 횟수',
  `fs_last_upload` datetime DEFAULT NULL COMMENT '마지막 업로드',
  `fs_last_download` datetime DEFAULT NULL COMMENT '마지막 다운로드',
  `fs_update_datetime` datetime NOT NULL COMMENT '업데이트 일시',
  PRIMARY KEY (`fs_id`),
  UNIQUE KEY `fs_mb_id` (`fs_mb_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='회원별 파일 통계';

-- 다국어 사이트도 동일하게 생성
-- USE mek_en;
-- (위 CREATE TABLE 문 반복)
```

#### 1-2. 폴더 생성 및 권한 설정

```bash
# 1. upload 폴더 생성
mkdir -p /var/www/html/mekeng.com/upload
mkdir -p /var/www/html/mekeng.com/scripts
mkdir -p /var/log/mekeng

# 2. 권한 설정
chown -R apache:user /var/www/html/mekeng.com/upload
chmod 755 /var/www/html/mekeng.com/upload

# 3. .htaccess 생성
cat > /var/www/html/mekeng.com/upload/.htaccess << 'EOF'
<Files ~ "\.php$">
    Order allow,deny
    Deny from all
</Files>
Options -Indexes
EOF

# 4. index.php (빈 파일로 디렉터리 목록 방지)
touch /var/www/html/mekeng.com/upload/index.php
```

#### 1-3. 로그 라이브러리 생성

```bash
# file_log.lib.php 파일 생성
nano /var/www/html/mekeng.com/home/lib/file_log.lib.php
```

위의 "5. 업로드/다운로드 로그 시스템" 섹션의 `file_log.lib.php` 코드를 복사하여 붙여넣기

---

### Phase 2: elFinder 설치 및 설정 (2-3시간)

#### 1. elFinder 다운로드
```bash
cd /var/www/html/mekeng.com/home/plus
wget https://github.com/Studio-42/elFinder/archive/refs/tags/2.1.62.tar.gz
tar -xzf 2.1.62.tar.gz
mv elFinder-2.1.62 elfinder
rm 2.1.62.tar.gz
```

#### 2. 커넥터 설정
```php
<?php
// /home/plus/elfinder/php/connector.minimal.php
include_once('../../common.php');

// 로그인 확인
if (!$is_member) {
    http_response_code(403);
    exit('로그인이 필요합니다.');
}

// 권한 확인 (레벨 3 이상 또는 관리자)
if ($member['mb_level'] < 3 && $is_admin != 'super') {
    http_response_code(403);
    exit('파일 업로드는 회원 레벨 3 이상만 이용 가능합니다.');
}

// 로그 라이브러리 로드
include_once(G5_LIB_PATH.'/file_log.lib.php');

// elFinder 라이브러리 로드
require './elFinderConnector.class.php';
require './elFinder.class.php';
require './elFinderVolumeDriver.class.php';
require './elFinderVolumeLocalFileSystem.class.php';

// 회원별 폴더 경로
$upload_path = '/var/www/html/mekeng.com/upload/' . $member['mb_id'];
if (!is_dir($upload_path)) {
    mkdir($upload_path, 0770, true);
}

// elFinder 설정
$opts = array(
    'roots' => array(
        array(
            'driver'        => 'LocalFileSystem',
            'path'          => $upload_path,
            'URL'           => '/upload/' . $member['mb_id'] . '/',
            'accessControl' => 'access',
            'uploadDeny'    => array('all'),
            'uploadAllow'   => array('image', 'video', 'application/pdf', 'application/msword'),
            'uploadOrder'   => array('deny', 'allow'),
            'uploadMaxSize' => '500M',
            'tmbPath'       => $upload_path . '/.tmb',
            'attributes' => array(
                array(
                    'pattern' => '/\.tmb/',
                    'read'    => false,
                    'write'   => false,
                    'locked'  => true,
                    'hidden'  => true
                )
            )
        )
    )
);

// 실행
$connector = new elFinderConnector(new elFinder($opts));
$connector->run();
```

#### 3. 업로드 관리자 페이지
```php
<?php
// /home/plus/upload_manager.php
include_once('../common.php');

// 로그인 확인
if (!$is_member) {
    alert('회원만 이용할 수 있습니다.', G5_BBS_URL.'/login.php?url='.urlencode($_SERVER['REQUEST_URI']));
}

// 권한 확인 (레벨 3 이상)
if ($member['mb_level'] < 3 && $is_admin != 'super') {
    alert('파일 업로드는 회원 레벨 3 이상만 이용 가능합니다.\\n\\n레벨 업그레이드는 관리자에게 문의하세요.', G5_URL);
}

// 로그 라이브러리 로드
include_once(G5_LIB_PATH.'/file_log.lib.php');

// 접근 로그 기록
log_file_access($member['mb_id'], 'ACCESS', '파일 관리자', $_SERVER['REMOTE_ADDR']);

// 파일 통계 조회
$file_stats = get_file_stats($member['mb_id']);

$g5['title'] = '파일 업로드 관리자';
include_once(G5_PATH.'/head.sub.php');
?>

<style>
    #elfinder { height: 70vh; }
    .file-stats {
        background: #f5f5f5;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
        display: flex;
        justify-content: space-around;
        flex-wrap: wrap;
    }
    .stat-item {
        text-align: center;
        padding: 10px 20px;
    }
    .stat-value {
        font-size: 24px;
        font-weight: bold;
        color: #1a4691;
    }
    .stat-label {
        font-size: 12px;
        color: #666;
        margin-top: 5px;
    }
</style>

<!-- 파일 통계 표시 -->
<div class="file-stats">
    <div class="stat-item">
        <div class="stat-value"><?php echo number_format($file_stats['total_files']); ?></div>
        <div class="stat-label">총 파일 개수</div>
    </div>
    <div class="stat-item">
        <div class="stat-value"><?php echo $file_stats['total_size_mb']; ?> MB</div>
        <div class="stat-label">총 용량</div>
    </div>
    <div class="stat-item">
        <div class="stat-value"><?php echo number_format($file_stats['upload_count']); ?></div>
        <div class="stat-label">업로드 횟수</div>
    </div>
    <div class="stat-item">
        <div class="stat-value"><?php echo number_format($file_stats['download_count']); ?></div>
        <div class="stat-label">다운로드 횟수</div>
    </div>
    <?php if ($file_stats['last_upload']) { ?>
    <div class="stat-item">
        <div class="stat-value" style="font-size: 14px;"><?php echo date('Y-m-d H:i', strtotime($file_stats['last_upload'])); ?></div>
        <div class="stat-label">마지막 업로드</div>
    </div>
    <?php } ?>
</div>

<div id="elfinder"></div>

<!-- elFinder CSS -->
<link rel="stylesheet" href="./elfinder/css/elfinder.min.css">
<link rel="stylesheet" href="./elfinder/css/theme.css">

<!-- jQuery (그누보드에 이미 있음) -->
<script src="<?php echo G5_JS_URL; ?>/jquery-1.12.4.min.js"></script>

<!-- jQuery UI (elFinder 필수) -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="//code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<!-- elFinder JS -->
<script src="./elfinder/js/elfinder.min.js"></script>
<script src="./elfinder/js/i18n/elfinder.ko.js"></script>

<script>
$(function() {
    var elf = $('#elfinder').elfinder({
        url: './elfinder/php/connector.minimal.php',
        lang: 'ko',
        height: '70vh',
        handlers: {
            // 업로드 완료 시 로그 기록
            upload: function(event, instance) {
                if (event.data && event.data.added) {
                    console.log('파일 업로드 완료:', event.data.added);
                    // 페이지 새로고침으로 통계 업데이트
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                }
            },
            // 다운로드 시 로그 기록
            download: function(event, instance) {
                console.log('파일 다운로드:', event.data);
            }
        },
        uiOptions: {
            toolbar: [
                ['back', 'forward'],
                ['reload'],
                ['home', 'up'],
                ['mkdir', 'mkfile', 'upload'],
                ['open', 'download', 'getfile'],
                ['info'],
                ['quicklook'],
                ['copy', 'cut', 'paste'],
                ['rm'],
                ['duplicate', 'rename', 'edit', 'resize'],
                ['extract', 'archive'],
                ['search'],
                ['view'],
                ['help']
            ]
        },
        contextmenu: {
            files: ['getfile', '|', 'open', 'quicklook', '|', 'download', '|', 'copy', 'cut', 'paste', 'duplicate', '|', 'rm', '|', 'edit', 'rename', 'resize', '|', 'archive', 'extract', '|', 'info']
        }
    });
});
</script>

<?php
include_once(G5_PATH.'/tail.sub.php');
?>
```

---

### Phase 3: 자동 동기화 설정 (2시간)

#### 1. rsync 스크립트 생성
```bash
# 위의 "방법 1: rsync" 스크립트 참고
nano /var/www/html/mekeng.com/scripts/sync_uploads.sh
chmod +x /var/www/html/mekeng.com/scripts/sync_uploads.sh
```

#### 2. SSH 키 설정
```bash
# 위의 "SSH 키 설정" 참고
```

#### 3. cron 등록
```bash
crontab -e

# 매일 새벽 2시, 오후 6시 동기화
0 2,18 * * * /var/www/html/mekeng.com/scripts/sync_uploads.sh
```

#### 4. 동기화 테스트
```bash
# 수동 실행 테스트
/var/www/html/mekeng.com/scripts/sync_uploads.sh

# 로그 확인
tail -f /var/log/upload_sync.log
```

---

### Phase 4: 모바일 최적화 및 테스트 (1-2시간)

#### 1. 반응형 CSS 추가
```css
/* 모바일 최적화 */
@media (max-width: 768px) {
    #elfinder {
        height: 90vh !important;
    }
    
    .elfinder-toolbar {
        overflow-x: auto;
    }
}
```

#### 2. 대용량 파일 업로드 설정
```bash
# /etc/php.ini 수정
sudo nano /etc/php.ini

# 다음 값 수정
upload_max_filesize = 500M
post_max_size = 500M
max_execution_time = 300
memory_limit = 512M

# Apache 설정
sudo nano /etc/httpd/conf/httpd.conf

# 추가
LimitRequestBody 524288000

# 재시작
sudo systemctl restart httpd
```

#### 3. 휴대폰 테스트
- iOS Safari에서 사진 업로드
- Android Chrome에서 동영상 업로드
- 파일 크기 제한 확인

---

## 💰 예상 비용 및 시간

### 개발 시간 (내부 개발 기준)

| 단계 | 작업 내용 | 예상 시간 |
|------|-----------|-----------|
| Phase 1 | DB 테이블 생성, 폴더 생성, 로그 라이브러리 작성 | 2시간 |
| Phase 2 | elFinder 설치 및 커스터마이징, 로그 연동 | 3-4시간 |
| Phase 3 | 관리자 로그/통계 페이지 작성 | 2-3시간 |
| Phase 4 | 자동 동기화 스크립트 작성 | 2시간 |
| Phase 5 | 모바일 최적화 및 테스트 | 2시간 |
| **합계** | | **11-13시간** |

### 외주 개발 비용 (참고)

| 항목 | 비용 (원) |
|------|-----------|
| 기본 파일 업로드 시스템 | 1,500,000 ~ 2,000,000 |
| elFinder 통합 및 커스터마이징 | 1,000,000 ~ 1,500,000 |
| 자동 동기화 시스템 | 800,000 ~ 1,200,000 |
| 모바일 최적화 | 500,000 ~ 800,000 |
| **총 예상 비용** | **3,800,000 ~ 5,500,000** |

### 운영 비용

| 항목 | 월 비용 (원) |
|------|-------------|
| 추가 스토리지 (500GB) | 20,000 ~ 50,000 |
| 백업 스토리지 (1TB) | 30,000 ~ 80,000 |
| 대역폭 (동기화 트래픽) | 10,000 ~ 30,000 |
| **월 합계** | **60,000 ~ 160,000** |

---

## 🔍 추가 고려사항

### 1. 백업 전략

```bash
#!/bin/bash
# 매일 자동 백업
tar -czf /backup/upload_$(date +%Y%m%d).tar.gz /var/www/html/mekeng.com/upload/

# 30일 이상 된 백업 삭제
find /backup/ -name "upload_*.tar.gz" -mtime +30 -delete
```

### 2. 디스크 용량 모니터링

```bash
#!/bin/bash
# 디스크 사용량 80% 이상 시 알림
USAGE=$(df -h /var/www/html/mekeng.com/upload | awk 'NR==2 {print $5}' | sed 's/%//')
if [ $USAGE -gt 80 ]; then
    echo "경고: 업로드 폴더 디스크 사용량 ${USAGE}%" | mail -s "디스크 경고" admin@mekeng.com
fi
```

### 3. 관리자 로그 조회 페이지

```php
<?php
// /home/plus/upload_log.php
include_once('../common.php');

// 관리자만 접근 가능
if ($is_admin != 'super') {
    alert('관리자만 접근 가능합니다.', G5_URL);
}

include_once(G5_LIB_PATH.'/file_log.lib.php');

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 50;
$offset = ($page - 1) * $per_page;

// 검색 조건
$where = " WHERE 1=1 ";
$search_mb_id = isset($_GET['search_mb_id']) ? trim($_GET['search_mb_id']) : '';
$search_type = isset($_GET['search_type']) ? trim($_GET['search_type']) : '';
$search_date_from = isset($_GET['search_date_from']) ? trim($_GET['search_date_from']) : '';
$search_date_to = isset($_GET['search_date_to']) ? trim($_GET['search_date_to']) : '';

if ($search_mb_id) {
    $where .= " AND fl_mb_id LIKE '%".sql_escape_string($search_mb_id)."%' ";
}
if ($search_type) {
    $where .= " AND fl_type = '".sql_escape_string($search_type)."' ";
}
if ($search_date_from) {
    $where .= " AND fl_datetime >= '{$search_date_from} 00:00:00' ";
}
if ($search_date_to) {
    $where .= " AND fl_datetime <= '{$search_date_to} 23:59:59' ";
}

// 전체 개수
$total_count = sql_fetch("SELECT COUNT(*) as cnt FROM g5_file_log {$where}");
$total_count = $total_count['cnt'];
$total_page = ceil($total_count / $per_page);

// 로그 조회
$sql = "SELECT l.*, m.mb_name 
        FROM g5_file_log l 
        LEFT JOIN {$g5['member_table']} m ON l.fl_mb_id = m.mb_id 
        {$where} 
        ORDER BY l.fl_datetime DESC 
        LIMIT {$offset}, {$per_page}";
$result = sql_query($sql);

$g5['title'] = '파일 업로드/다운로드 로그';
include_once(G5_PATH.'/head.sub.php');
?>

<style>
.log-search {
    background: #f5f5f5;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 5px;
}
.log-search input, .log-search select {
    padding: 8px;
    margin: 5px;
    border: 1px solid #ddd;
    border-radius: 3px;
}
.log-table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
}
.log-table th {
    background: #1a4691;
    color: #fff;
    padding: 12px;
    text-align: left;
}
.log-table td {
    padding: 10px;
    border-bottom: 1px solid #eee;
}
.log-table tr:hover {
    background: #f9f9f9;
}
.type-badge {
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: bold;
}
.type-upload { background: #4CAF50; color: #fff; }
.type-download { background: #2196F3; color: #fff; }
.type-delete { background: #f44336; color: #fff; }
.type-access { background: #9E9E9E; color: #fff; }
.pagination {
    text-align: center;
    margin-top: 20px;
}
.pagination a {
    padding: 8px 12px;
    margin: 0 2px;
    border: 1px solid #ddd;
    text-decoration: none;
    color: #333;
}
.pagination a.active {
    background: #1a4691;
    color: #fff;
    border-color: #1a4691;
}
</style>

<h2>파일 업로드/다운로드 로그</h2>

<form method="get" class="log-search">
    <input type="text" name="search_mb_id" placeholder="회원 아이디" value="<?php echo $search_mb_id; ?>">
    <select name="search_type">
        <option value="">전체 타입</option>
        <option value="UPLOAD" <?php echo $search_type == 'UPLOAD' ? 'selected' : ''; ?>>업로드</option>
        <option value="DOWNLOAD" <?php echo $search_type == 'DOWNLOAD' ? 'selected' : ''; ?>>다운로드</option>
        <option value="DELETE" <?php echo $search_type == 'DELETE' ? 'selected' : ''; ?>>삭제</option>
        <option value="ACCESS" <?php echo $search_type == 'ACCESS' ? 'selected' : ''; ?>>접속</option>
    </select>
    <input type="date" name="search_date_from" value="<?php echo $search_date_from; ?>">
    ~
    <input type="date" name="search_date_to" value="<?php echo $search_date_to; ?>">
    <button type="submit">검색</button>
    <a href="?">초기화</a>
    <span style="float: right;">전체 <?php echo number_format($total_count); ?>건</span>
</form>

<table class="log-table">
    <thead>
        <tr>
            <th>번호</th>
            <th>회원</th>
            <th>타입</th>
            <th>파일명</th>
            <th>파일 크기</th>
            <th>IP</th>
            <th>일시</th>
            <th>비고</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (sql_num_rows($result) > 0) {
            $num = $total_count - $offset;
            while ($row = sql_fetch_array($result)) {
                $type_class = 'type-' . strtolower($row['fl_type']);
                $filesize_mb = $row['fl_filesize'] > 0 ? round($row['fl_filesize'] / 1024 / 1024, 2) . ' MB' : '-';
        ?>
        <tr>
            <td><?php echo $num--; ?></td>
            <td>
                <?php echo $row['mb_name'] ? $row['mb_name'] : $row['fl_mb_id']; ?>
                <br><small style="color:#999;"><?php echo $row['fl_mb_id']; ?></small>
            </td>
            <td><span class="type-badge <?php echo $type_class; ?>"><?php echo $row['fl_type']; ?></span></td>
            <td><?php echo htmlspecialchars($row['fl_filename']); ?></td>
            <td><?php echo $filesize_mb; ?></td>
            <td><?php echo $row['fl_ip']; ?></td>
            <td><?php echo $row['fl_datetime']; ?></td>
            <td><?php echo htmlspecialchars($row['fl_note']); ?></td>
        </tr>
        <?php
            }
        } else {
        ?>
        <tr>
            <td colspan="8" style="text-align: center; padding: 50px;">로그가 없습니다.</td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<!-- 페이징 -->
<div class="pagination">
    <?php
    $page_range = 10;
    $start_page = max(1, $page - floor($page_range / 2));
    $end_page = min($total_page, $start_page + $page_range - 1);
    
    if ($page > 1) {
        echo '<a href="?page=1">처음</a> ';
        echo '<a href="?page='.($page-1).'">이전</a> ';
    }
    
    for ($i = $start_page; $i <= $end_page; $i++) {
        $active = $i == $page ? 'active' : '';
        echo '<a href="?page='.$i.'" class="'.$active.'">'.$i.'</a> ';
    }
    
    if ($page < $total_page) {
        echo '<a href="?page='.($page+1).'">다음</a> ';
        echo '<a href="?page='.$total_page.'">마지막</a>';
    }
    ?>
</div>

<?php
include_once(G5_PATH.'/tail.sub.php');
?>
```

### 4. 통계 대시보드

```php
<?php
// /home/plus/upload_stats.php
include_once('../common.php');

// 관리자만 접근 가능
if ($is_admin != 'super') {
    alert('관리자만 접근 가능합니다.', G5_URL);
}

include_once(G5_LIB_PATH.'/file_log.lib.php');

// 전체 통계
$total_stats = sql_fetch("
    SELECT 
        COUNT(DISTINCT fs_mb_id) as total_users,
        SUM(fs_total_files) as total_files,
        SUM(fs_total_size) as total_size,
        SUM(fs_upload_count) as total_uploads,
        SUM(fs_download_count) as total_downloads
    FROM g5_file_stats
");

// 최근 30일 활동
$recent_activity = sql_query("
    SELECT 
        DATE(fl_datetime) as date,
        fl_type,
        COUNT(*) as count
    FROM g5_file_log
    WHERE fl_datetime >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY DATE(fl_datetime), fl_type
    ORDER BY date DESC, fl_type
");

// 회원별 통계 (TOP 10)
$top_users = sql_query("
    SELECT 
        fs.*,
        m.mb_name,
        m.mb_level
    FROM g5_file_stats fs
    LEFT JOIN {$g5['member_table']} m ON fs.fs_mb_id = m.mb_id
    ORDER BY fs.fs_total_size DESC
    LIMIT 10
");

$g5['title'] = '파일 업로드 통계';
include_once(G5_PATH.'/head.sub.php');
?>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}
.stat-card {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    text-align: center;
}
.stat-card-value {
    font-size: 36px;
    font-weight: bold;
    color: #1a4691;
}
.stat-card-label {
    color: #666;
    margin-top: 10px;
}
.chart-container {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}
</style>

<h2>파일 업로드 시스템 통계</h2>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-value"><?php echo number_format($total_stats['total_users']); ?></div>
        <div class="stat-card-label">사용 회원 수</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-value"><?php echo number_format($total_stats['total_files']); ?></div>
        <div class="stat-card-label">총 파일 개수</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-value"><?php echo round($total_stats['total_size'] / 1024 / 1024 / 1024, 2); ?> GB</div>
        <div class="stat-card-label">총 용량</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-value"><?php echo number_format($total_stats['total_uploads']); ?></div>
        <div class="stat-card-label">총 업로드 횟수</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-value"><?php echo number_format($total_stats['total_downloads']); ?></div>
        <div class="stat-card-label">총 다운로드 횟수</div>
    </div>
</div>

<div class="chart-container">
    <h3>상위 10명 회원 (용량 기준)</h3>
    <table class="log-table">
        <thead>
            <tr>
                <th>순위</th>
                <th>회원</th>
                <th>레벨</th>
                <th>파일 개수</th>
                <th>총 용량</th>
                <th>업로드 횟수</th>
                <th>다운로드 횟수</th>
                <th>마지막 업로드</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $rank = 1;
            while ($row = sql_fetch_array($top_users)) {
            ?>
            <tr>
                <td><?php echo $rank++; ?></td>
                <td>
                    <?php echo $row['mb_name']; ?>
                    <br><small style="color:#999;"><?php echo $row['fs_mb_id']; ?></small>
                </td>
                <td><?php echo $row['mb_level']; ?></td>
                <td><?php echo number_format($row['fs_total_files']); ?></td>
                <td><?php echo round($row['fs_total_size'] / 1024 / 1024, 2); ?> MB</td>
                <td><?php echo number_format($row['fs_upload_count']); ?></td>
                <td><?php echo number_format($row['fs_download_count']); ?></td>
                <td><?php echo $row['fs_last_upload'] ? date('Y-m-d H:i', strtotime($row['fs_last_upload'])) : '-'; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php
include_once(G5_PATH.'/tail.sub.php');
?>
```

---

## 📞 지원 및 문의

### elFinder 공식 문서
- 홈페이지: https://studio-42.github.io/elFinder/
- GitHub: https://github.com/Studio-42/elFinder
- 데모: https://studio-42.github.io/elFinder/demo/2.1/

### 기술 스택
- **백엔드**: PHP 8.1, 그누보드 5
- **프론트엔드**: jQuery, jQuery UI, elFinder
- **동기화**: rsync, SSH, cron
- **보안**: .htaccess, 그누보드 인증 시스템

---

## ✅ 체크리스트

### 구현 전 확인사항
- [ ] PHP 버전 8.0 이상
- [ ] 디스크 여유 공간 충분 (최소 100GB)
- [ ] 회사 내부 서버 접근 권한 확보
- [ ] SSH 또는 Samba 연결 테스트 완료
- [ ] 방화벽 규칙 확인 (rsync, ssh, samba 포트)

### 구현 중 확인사항
- [ ] upload 폴더 생성 및 권한 설정
- [ ] 데이터베이스 테이블 생성 (g5_file_log, g5_file_stats)
- [ ] file_log.lib.php 라이브러리 작성
- [ ] elFinder 설치 및 그누보드 통합
- [ ] 회원 레벨 3 권한 확인 로직 적용
- [ ] 파일 업로드 테스트 (이미지, 영상, 문서)
- [ ] 로그 기록 테스트 (업로드/다운로드)
- [ ] 통계 조회 테스트
- [ ] 모바일 업로드 테스트
- [ ] 자동 동기화 스크립트 작성
- [ ] cron 등록 및 테스트

### 구현 후 확인사항
- [ ] 다양한 브라우저 테스트 (Chrome, Safari, Firefox)
- [ ] 휴대폰 테스트 (iOS, Android)
- [ ] 대용량 파일 업로드 테스트 (100MB+)
- [ ] 동기화 로그 확인
- [ ] 백업 설정 확인
- [ ] 보안 점검 (.htaccess, 권한)

---

## 📝 결론

### 최종 권장 구성
```
✅ 업로드 인터페이스: elFinder (윈도우 탐색기 UX)
✅ 인증 시스템: 그누보드 회원 레벨 3 이상
✅ 저장 위치: /var/www/html/mekeng.com/upload/{회원ID}/
✅ 동기화 방법: rsync + cron (정기) 또는 inotify (실시간)
✅ 보안: .htaccess, 파일 타입 제한, 회원별 폴더 분리
✅ 로그 시스템: 업로드/다운로드/삭제/접근 로그 DB 기록
✅ 통계 기능: 회원별 파일 통계 및 관리자 대시보드
```

### 장점
1. ✅ **윈도우 탐색기 같은 UX** → 사용 편의성 극대화
2. ✅ **모바일 최적화** → 휴대폰에서 편리한 업로드
3. ✅ **보안** → 그원 레벨 3 이상 권한 제한
4. ✅ **로그 시스템** → 모든 업로드/다운로드 기록 추적
5. ✅ **통계 기능** → 회원별 사용량 및 전체 통계 조회
6. ✅ **자동 동기화** → 회사 내부 서버로 자동 백업
7. ✅ **확장성** → 추가 기능 (썸네일, 검색, 공유) 쉽게 구현 가능

### 예상 효과
- 🚀 업로드 속도: 파일당 평균 5초 (100MB 기준)
- 📱 모바일 호환성: iOS/Android 모두 지원
- 🔒 보안: 회원 인증 + 파일 타입 제한 + 디렉터리 보호
- 🔄 자동화: cron으로 일 2회 자동 동기화

---

**작성일**: 2025-12-05  
**최종 수정일**: 2025-12-05  
**작성자**: AI Assistant (Cursor)  
**문서 버전**: 1.1

---

## 📝 변경 이력

### v1.1 (2025-12-05)
- ✅ 회원 레벨 요구사항 변경: 레벨 8 → 레벨 3 이상
- ✅ 데이터베이스 로그 시스템 추가
  - g5_file_log 테이블: 업로드/다운로드/삭제/접근 로그
  - g5_file_stats 테이블: 회원별 파일 통계
- ✅ file_log.lib.php 라이브러리 추가
  - log_file_upload(): 업로드 로그 기록
  - log_file_download(): 다운로드 로그 기록
  - log_file_delete(): 삭제 로그 기록
  - get_file_stats(): 회원별 통계 조회
- ✅ 관리자 로그 조회 페이지 추가 (upload_log.php)
- ✅ 관리자 통계 대시보드 추가 (upload_stats.php)
- ✅ 파일 업로드 페이지에 실시간 통계 표시
- ✅ 구현 체크리스트 업데이트

### v1.0 (2025-12-05)
- 초기 문서 작성

