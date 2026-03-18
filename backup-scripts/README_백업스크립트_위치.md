# WSL 백업 스크립트 위치 가이드

## 📍 스크립트 위치 옵션

### 권장 위치: `/usr/local/bin` ⭐⭐⭐⭐⭐

**장점**:
- PATH에 기본 포함되어 어디서든 실행 가능
- 시스템 유틸리티를 위한 표준 위치
- 웹사이트 프로젝트와 독립적 관리
- cron 설정 간단 (`wsl_rsync_backup.sh`만으로 실행 가능)

**이동 방법**:
```bash
sudo cp /var/www/html/mekeng.com/backup-scripts/wsl_rsync_*.sh /usr/local/bin/
sudo chmod +x /usr/local/bin/wsl_rsync_*.sh
```

**상세 가이드**: `스크립트_위치_권장사항.md` 파일 참조

---

### 현재 위치: `/var/www/html/mekeng.com/backup-scripts/`

웹사이트 프로젝트와 함께 관리되며, 프로젝트와 연계된 백업 스크립트로 사용 가능합니다.

---

## 🔧 설정 가이드

### 1. 스크립트 확인
```bash
# 스크립트 위치 확인
ls -la /var/www/html/mekeng.com/backup-scripts/

# 실행 권한 확인
chmod +x /var/www/html/mekeng.com/backup-scripts/*.sh
```

### 2. 초기 설정 실행
```bash
cd /var/www/html/mekeng.com/backup-scripts/
bash wsl_rsync_setup.sh
```

### 3. SSH 키 설정
```bash
# SSH 키 생성
ssh-keygen -t rsa -b 4096 -f ~/.ssh/id_rsa_backup -N ""

# 서버에 공개키 복사
ssh-copy-id -i ~/.ssh/id_rsa_backup.pub root@172.234.92.172
```

### 4. 수동 테스트
```bash
bash /var/www/html/mekeng.com/backup-scripts/wsl_rsync_backup.sh
```

### 5. cron 자동화 설정
```bash
crontab -e
```

다음 라인 추가:
```bash
# 매주 월요일 오전 10시 백업 동기화
0 10 * * 1 /var/www/html/mekeng.com/backup-scripts/wsl_rsync_backup.sh
```

또는 요일 이름 사용 (일부 시스템):
```bash
0 10 * * MON /var/www/html/mekeng.com/backup-scripts/wsl_rsync_backup.sh
```

---

## 📝 스크립트 파일

1. **wsl_rsync_setup.sh**: WSL 백업 환경 초기 설정
2. **wsl_rsync_backup.sh**: rsync 백업 동기화 실행

---

## 📝 참고사항

1. **스크립트 경로**: 모든 cron 설정에서 절대 경로 사용
2. **로그 확인**: 
   - 스크립트 내부 로그: `/mnt/e/MEK_WEB/logs/sync_YYYYMMDD_HHMMSS.log`
   - cron 실행 결과 확인: `grep CRON /var/log/syslog`
3. **프로젝트 폴더**: 웹사이트 프로젝트(`/var/www/html/mekeng.com/`)가 존재해야 스크립트 실행 가능
4. **Git 관리**: 필요시 `.gitignore`에 로그 파일 제외 설정 권장

---

**작성일**: 2025-01-01

