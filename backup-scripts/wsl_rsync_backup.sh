#!/bin/bash
# WSL rsync 백업 동기화 스크립트
# 서버의 백업 파일을 E드라이브 MEK_WEB로 동기화
# 작성일: 2025-01-01

# ---------------------------
# 설정
# ---------------------------
# 서버 연결 정보
SERVER_HOST="root@172.234.92.172"  # 실제 서버 주소로 변경 필요
SERVER_BACKUP_PATH="/var/www/.bak/backups"

# 로컬 백업 경로 (E드라이브 MEK_WEB)
LOCAL_BACKUP_ROOT="/mnt/e/MEK_WEB/backups"
LOG_DIR="/mnt/e/MEK_WEB/logs"
LOG_FILE="$LOG_DIR/sync_$(date +%Y%m%d_%H%M%S).log"

# SSH 키 경로 (필요시)
SSH_KEY="$HOME/.ssh/id_rsa_backup"
# SSH 옵션 (SSH 키가 있는 경우)
SSH_OPTS=""
if [ -f "$SSH_KEY" ]; then
    SSH_OPTS="-i $SSH_KEY"
fi

# 디렉토리 생성
mkdir -p "$LOCAL_BACKUP_ROOT"
mkdir -p "$LOG_DIR"

# 로깅 함수
log_message() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

log_message "======================================="
log_message " WSL rsync 백업 동기화 시작"
log_message "======================================="
log_message "[INFO] 서버: $SERVER_HOST"
log_message "[INFO] 소스 경로: $SERVER_BACKUP_PATH"
log_message "[INFO] 대상 경로: $LOCAL_BACKUP_ROOT"

# ---------------------------
# SSH 연결 테스트
# ---------------------------
log_message "[INFO] SSH 연결 테스트 중..."
if ssh $SSH_OPTS -o ConnectTimeout=10 -o BatchMode=yes -o StrictHostKeyChecking=no "$SERVER_HOST" "echo 'Connection successful'" >> "$LOG_FILE" 2>&1; then
    log_message "[INFO] SSH 연결 성공"
else
    log_message "[ERROR] SSH 연결 실패. 서버 접속을 확인하세요."
    log_message "[INFO] SSH 키 설정 확인: $SSH_KEY"
    log_message "[INFO] 서버 주소 확인: $SERVER_HOST"
    exit 1
fi

# ---------------------------
# rsync 동기화 실행
# ---------------------------
log_message "[INFO] rsync 동기화 시작..."

# rsync 옵션 설명:
# -a: 아카이브 모드 (권한, 시간 등 보존)
# -v: 상세 출력
# -z: 압축 전송
# --progress: 진행 상황 표시
# --exclude: 제외할 파일/폴더
# --delete: 주의! 소스에 없는 파일 삭제 (백업 삭제 정책에 따라 선택사항)
#           현재는 사용하지 않음 (서버에서 삭제된 백업이 로컬에 유지됨)

if rsync -avz --progress $SSH_OPTS \
    --exclude='*.log' \
    "$SERVER_HOST:$SERVER_BACKUP_PATH/" \
    "$LOCAL_BACKUP_ROOT/" \
    >> "$LOG_FILE" 2>&1; then
    
    log_message "[INFO] rsync 동기화 완료"
    
    # 동기화된 백업 개수 확인
    BACKUP_COUNT=$(ls -1 "$LOCAL_BACKUP_ROOT" 2>/dev/null | wc -l)
    log_message "[INFO] 동기화된 백업 개수: $BACKUP_COUNT"
    
    # 최신 백업 정보
    LATEST_BACKUP=$(ls -1t "$LOCAL_BACKUP_ROOT" 2>/dev/null | head -1)
    if [ -n "$LATEST_BACKUP" ]; then
        log_message "[INFO] 최신 백업: $LATEST_BACKUP"
        BACKUP_SIZE=$(du -sh "$LOCAL_BACKUP_ROOT/$LATEST_BACKUP" 2>/dev/null | cut -f1)
        log_message "[INFO] 최신 백업 크기: $BACKUP_SIZE"
    fi
else
    log_message "[ERROR] rsync 동기화 실패"
    log_message "[INFO] 로그 파일 확인: $LOG_FILE"
    log_message "[INFO] 서버 경로 확인: $SERVER_HOST:$SERVER_BACKUP_PATH"
    log_message "[INFO] 로컬 경로 확인: $LOCAL_BACKUP_ROOT"
    exit 1
fi

# ---------------------------
# 디스크 사용량 확인
# ---------------------------
DISK_USAGE=$(df -h /mnt/e 2>/dev/null | tail -1 | awk '{print $5}')
log_message "[INFO] E드라이브 사용량: $DISK_USAGE"

# 90% 이상 사용 시 경고
DISK_USAGE_NUM=$(echo "$DISK_USAGE" | sed 's/%//')
if [ "$DISK_USAGE_NUM" -ge 90 ]; then
    log_message "[WARNING] E드라이브 사용량이 90% 이상입니다. 디스크 정리가 필요합니다."
fi

log_message "[INFO] 백업 동기화 작업 완료!"
log_message "======================================="

# ---------------------------
# 로그 파일 정리
# ---------------------------
log_message "[INFO] 30일 이상된 로그 파일 정리 중..."
DELETED_LOGS=$(find "$LOG_DIR" -type f -name "sync_*.log" -mtime +30 -delete -print 2>/dev/null | wc -l)
if [ "$DELETED_LOGS" -gt 0 ]; then
    log_message "[INFO] 삭제된 로그 파일: $DELETED_LOGS개"
fi
