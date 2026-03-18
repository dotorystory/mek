#!/bin/bash
# WSL 백업 환경 초기 설정 스크립트
# E드라이브 MEK_WEB 폴더 구조 생성 및 권한 설정
# 작성일: 2025-01-01

echo "======================================="
echo " WSL 백업 환경 초기 설정"
echo "======================================="

# ---------------------------
# 설정
# ---------------------------
BACKUP_ROOT="/mnt/e/MEK_WEB"
BACKUP_DIR="$BACKUP_ROOT/backups"
LOG_DIR="$BACKUP_ROOT/logs"
RESTORE_DIR="$BACKUP_ROOT/restore"

# 현재 사용자 확인
CURRENT_USER=$(whoami)
echo "[INFO] 현재 사용자: $CURRENT_USER"

# ---------------------------
# 디렉토리 구조 생성
# ---------------------------
echo "[INFO] 백업 디렉토리 구조 생성 중..."

# 백업 디렉토리 생성
mkdir -p "$BACKUP_DIR"
mkdir -p "$LOG_DIR"
mkdir -p "$RESTORE_DIR"

echo "[INFO] 디렉토리 구조 생성 완료:"
echo "  - 백업 디렉토리: $BACKUP_DIR"
echo "  - 로그 디렉토리: $LOG_DIR"
echo "  - 복구 디렉토리: $RESTORE_DIR"

# ---------------------------
# 디렉토리 구조 확인
# ---------------------------
if [ -d "$BACKUP_ROOT" ]; then
    echo "[INFO] 백업 루트 디렉토리 확인: $BACKUP_ROOT"
    ls -la "$BACKUP_ROOT"
else
    echo "[ERROR] 백업 루트 디렉토리를 찾을 수 없습니다: $BACKUP_ROOT"
    echo "[INFO] E드라이브가 마운트되어 있는지 확인하세요."
    exit 1
fi

# ---------------------------
# 필수 도구 확인
# ---------------------------
echo "[INFO] 필수 도구 확인 중..."

# rsync 확인
if command -v rsync >/dev/null 2>&1; then
    echo "[OK] rsync 설치됨: $(which rsync)"
else
    echo "[WARNING] rsync가 설치되어 있지 않습니다."
    echo "[INFO] 설치 명령: sudo dnf install -y rsync"
fi

# ssh 확인
if command -v ssh >/dev/null 2>&1; then
    echo "[OK] ssh 설치됨: $(which ssh)"
else
    echo "[WARNING] ssh가 설치되어 있지 않습니다."
    echo "[INFO] 설치 명령: sudo dnf install -y openssh-clients"
fi

# ---------------------------
# SSH 키 설정 확인
# ---------------------------
echo "[INFO] SSH 키 설정 확인 중..."
if [ -f ~/.ssh/id_rsa_backup ] || [ -f ~/.ssh/id_rsa ]; then
    echo "[OK] SSH 키 파일 존재"
    if [ -f ~/.ssh/id_rsa_backup ]; then
        echo "[INFO] 백업용 SSH 키: ~/.ssh/id_rsa_backup"
    fi
else
    echo "[WARNING] SSH 키 파일이 없습니다."
    echo "[INFO] SSH 키 생성 명령 (로컬 PC WSL 터미널에서 실행):"
    echo "  ssh-keygen -t rsa -b 4096 -f ~/.ssh/id_rsa_backup -N \"\""
    echo "  ssh-copy-id -i ~/.ssh/id_rsa_backup.pub root@172.234.92.172"
    echo ""
    echo "[INFO] 참고: SSH 키는 로컬 PC WSL에서 생성하고 외부 서버에 공개키를 등록합니다."
fi

# ---------------------------
# E드라이브 접근 권한 확인
# ---------------------------
echo "[INFO] E드라이브 접근 권한 확인 중..."
if [ -w "$BACKUP_ROOT" ]; then
    echo "[OK] E드라이브 쓰기 권한 있음"
else
    echo "[WARNING] E드라이브 쓰기 권한이 없습니다."
    echo "[INFO] Windows에서 폴더 속성 > 보안 설정을 확인하세요."
fi

# ---------------------------
# 테스트 파일 생성
# ---------------------------
TEST_FILE="$BACKUP_ROOT/test_write.txt"
if echo "테스트 파일" > "$TEST_FILE" 2>/dev/null; then
    echo "[OK] 파일 쓰기 테스트 성공"
    rm -f "$TEST_FILE"
else
    echo "[WARNING] 파일 쓰기 테스트 실패"
fi

# ---------------------------
# 완료
# ---------------------------
echo ""
echo "======================================="
echo " 초기 설정 완료"
echo "======================================="
echo "[INFO] 다음 단계:"
echo "  1. wsl_rsync_backup.sh 스크립트의 SERVER_HOST 설정 확인"
echo "     vi /var/www/html/mekeng.com/backup-scripts/wsl_rsync_backup.sh"
echo "     # SERVER_HOST 변수에 실제 서버 주소 설정 (예: root@172.234.92.172)"
echo ""
echo "  2. SSH 키 생성 및 서버에 공개키 복사 (로컬 PC WSL 터미널에서 실행)"
echo "     ssh-keygen -t rsa -b 4096 -f ~/.ssh/id_rsa_backup -N \"\""
echo "     ssh-copy-id -i ~/.ssh/id_rsa_backup.pub root@172.234.92.172"
echo "     # 참고: WSL에서 생성한 키를 외부 서버에 등록합니다"
echo ""
echo "  3. rsync 연결 테스트"
echo "     bash /var/www/html/mekeng.com/backup-scripts/wsl_rsync_backup.sh"
echo ""
echo "  4. WSL cron 자동화 설정"
echo "     crontab -e"
echo "     # 다음 라인 추가 (매주 월요일 오전 10:00 실행)"
echo "     0 10 * * 1 /var/www/html/mekeng.com/backup-scripts/wsl_rsync_backup.sh"
echo "     # 또는 요일 이름 사용 (일부 시스템):"
echo "     0 10 * * MON /var/www/html/mekeng.com/backup-scripts/wsl_rsync_backup.sh"

