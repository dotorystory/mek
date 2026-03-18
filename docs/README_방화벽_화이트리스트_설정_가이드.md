# 🛡️ 방화벽 화이트리스트 설정 가이드

**작성일:** 2025년 12월 5일  
**서버:** mekeng.com (172.234.92.172)  
**적용 방식:** iptables 화이트리스트

---

## 📋 목차

1. [개요](#개요)
2. [현재 설정](#현재-설정)
3. [백업 및 복원](#백업-및-복원)
4. [적용된 규칙](#적용된-규칙)
5. [문제 해결](#문제-해결)
6. [추가 포트 허용](#추가-포트-허용)

---

## 개요

### 변경 전 상태
- **정책:** INPUT ACCEPT (모든 포트 열림)
- **보안 수준:** 낮음
- **문제:** 불필요한 포트(33011 등)가 외부에 노출

### 변경 후 상태
- **정책:** INPUT DROP (필요한 포트만 허용)
- **보안 수준:** 높음
- **장점:** 명시적으로 허용된 포트만 접근 가능

---

## 현재 설정

### 허용된 포트 목록

| 포트 | 프로토콜 | 용도 | 비고 |
|------|---------|------|------|
| 22 | TCP | SSH | 서버 관리 |
| 80 | TCP | HTTP | 웹 서비스 |
| 443 | TCP | HTTPS | 보안 웹 서비스 |
| 21 | TCP | FTP 제어 | FileZilla 등 |
| 20 | TCP | FTP 데이터 (Active) | FTP Active 모드 |
| 40000-42000 | TCP | FTP 데이터 (Passive) | FileZilla 기본 모드 |
| 25 | TCP | SMTP | 메일 전송 |
| 587 | TCP | SMTP Submission | 보안 메일 전송 |
| 3306 | TCP | MySQL/MariaDB | 데이터베이스 (로컬만) |
| - | ICMP | Ping | 서버 상태 확인 |

### 특별 규칙
- **Localhost (127.0.0.1):** 모든 포트 허용
- **ESTABLISHED, RELATED:** 기존 연결 유지
- **fail2ban:** SSH, Postfix 보호 규칙 유지

---

## 백업 및 복원

### 백업 파일 위치

```bash
/root/iptables_backup_20251205_131449.txt
```

적용 시 자동으로 추가 백업 생성:
```bash
/root/iptables_backup_before_whitelist_YYYYMMDD_HHMMSS.txt
```

### 복원 방법

#### 방법 1: 긴급 복원 스크립트 (가장 빠름)
```bash
sudo bash /root/reset_firewall.sh
```
→ 모든 방화벽을 ACCEPT 상태로 초기화

#### 방법 2: 백업 파일에서 복원
```bash
# 백업 파일 목록 확인
ls -lh /root/iptables_backup_*.txt

# 특정 백업으로 복원
sudo iptables-restore < /root/iptables_backup_20251205_131449.txt
```

#### 방법 3: 수동 초기화
```bash
sudo iptables -F                  # 모든 규칙 삭제
sudo iptables -X                  # 사용자 정의 체인 삭제
sudo iptables -P INPUT ACCEPT     # INPUT 정책을 ACCEPT로
sudo iptables -P FORWARD ACCEPT   # FORWARD 정책을 ACCEPT로
sudo iptables -P OUTPUT ACCEPT    # OUTPUT 정책을 ACCEPT로
```

#### 방법 4: SSH 연결이 끊겼을 경우
1. **Linode 대시보드** 접속
2. **Launch LISH Console** 클릭
3. 로그인 후 위의 방법 1 또는 3 실행

---

## 적용된 규칙

### 현재 iptables 규칙 확인
```bash
sudo iptables -L -n -v
```

### 규칙 상세 내역
```bash
# 1. 기존 연결 유지
iptables -A INPUT -m state --state ESTABLISHED,RELATED -j ACCEPT

# 2. Localhost 허용
iptables -A INPUT -i lo -j ACCEPT

# 3. SSH (필수)
iptables -A INPUT -p tcp --dport 22 -j ACCEPT

# 4. 웹 서비스
iptables -A INPUT -p tcp --dport 80 -j ACCEPT
iptables -A INPUT -p tcp --dport 443 -j ACCEPT

# 5. FTP
iptables -A INPUT -p tcp --dport 21 -j ACCEPT
iptables -A INPUT -p tcp --dport 20 -j ACCEPT
iptables -A INPUT -p tcp --dport 40000:42000 -j ACCEPT

# 6. 메일
iptables -A INPUT -p tcp --dport 25 -j ACCEPT
iptables -A INPUT -p tcp --dport 587 -j ACCEPT

# 7. ICMP (Ping)
iptables -A INPUT -p icmp -j ACCEPT

# 8. 기본 정책: 나머지 모두 차단
iptables -P INPUT DROP
```

### 설정 영구 저장
```bash
sudo iptables-save > /etc/iptables/rules.v4
```

---

## 문제 해결

### SSH 연결이 안 될 때

**증상:** 방화벽 적용 후 SSH 접속 불가

**해결:**
1. Linode LISH Console 접속
2. 복원 스크립트 실행:
   ```bash
   sudo bash /root/reset_firewall.sh
   ```
3. SSH 재연결 후 규칙 재검토

### 웹사이트가 안 열릴 때

**증상:** HTTP/HTTPS 접속 안 됨

**확인:**
```bash
# 80, 443 포트 규칙 확인
sudo iptables -L INPUT -n -v | grep -E "dpt:(80|443)"

# Apache/httpd 실행 확인
sudo systemctl status httpd
```

**해결:**
```bash
# 포트 규칙 추가 (없는 경우)
sudo iptables -I INPUT -p tcp --dport 80 -j ACCEPT
sudo iptables -I INPUT -p tcp --dport 443 -j ACCEPT
```

### FTP 연결이 안 될 때

**증상:** FileZilla로 파일 전송 실패

**확인:**
```bash
# FTP 포트 규칙 확인
sudo iptables -L INPUT -n -v | grep -E "dpt:(21|20|40000:42000)"

# vsftpd 실행 확인
sudo systemctl status vsftpd
```

**FileZilla 설정:**
- **전송 모드:** Passive (기본값)
- **포트:** 21

### 메일 전송이 안 될 때

**확인:**
```bash
# 메일 포트 규칙 확인
sudo iptables -L INPUT -n -v | grep -E "dpt:(25|587)"

# Postfix 실행 확인
sudo systemctl status postfix
```

---

## 추가 포트 허용

### 새 포트 추가 방법

#### 예: 8080 포트 허용
```bash
# 임시 추가 (재부팅 시 사라짐)
sudo iptables -I INPUT -p tcp --dport 8080 -j ACCEPT

# 영구 저장
sudo iptables-save > /etc/iptables/rules.v4
```

#### 예: MySQL 외부 접속 허용 (주의!)
```bash
# 특정 IP만 허용 (권장)
sudo iptables -I INPUT -p tcp -s 1.2.3.4 --dport 3306 -j ACCEPT

# 모든 IP 허용 (비권장)
sudo iptables -I INPUT -p tcp --dport 3306 -j ACCEPT

# 저장
sudo iptables-save > /etc/iptables/rules.v4
```

### 포트 차단
```bash
# 특정 포트 차단
sudo iptables -A INPUT -p tcp --dport 33011 -j DROP

# 저장
sudo iptables-save > /etc/iptables/rules.v4
```

---

## 유용한 명령어

### 방화벽 상태 확인
```bash
# 전체 규칙 확인
sudo iptables -L -n -v

# INPUT 체인만 확인
sudo iptables -L INPUT -n -v --line-numbers

# 기본 정책 확인
sudo iptables -L | grep policy
```

### 특정 규칙 삭제
```bash
# 규칙 번호 확인
sudo iptables -L INPUT -n -v --line-numbers

# 특정 번호 규칙 삭제
sudo iptables -D INPUT [번호]

# 예: 5번 규칙 삭제
sudo iptables -D INPUT 5
```

### 로그 확인
```bash
# 최근 방화벽 관련 로그
sudo tail -100 /var/log/messages | grep iptables

# SSH 로그
sudo tail -100 /var/log/secure
```

---

## 재부팅 후 자동 적용

### systemd 서비스 생성

```bash
cat << 'EOF' | sudo tee /etc/systemd/system/iptables-restore.service
[Unit]
Description=Restore iptables rules
Before=network-pre.target
Wants=network-pre.target

[Service]
Type=oneshot
ExecStart=/sbin/iptables-restore /etc/iptables/rules.v4
ExecReload=/sbin/iptables-restore /etc/iptables/rules.v4
RemainAfterExit=yes

[Install]
WantedBy=multi-user.target
EOF

# 서비스 활성화
sudo systemctl enable iptables-restore.service
sudo systemctl start iptables-restore.service
```

---

## 보안 권장 사항

### 1. SSH 포트 변경 (선택 사항)
```bash
# /etc/ssh/sshd_config 수정
Port 2222

# 방화벽 규칙 변경
sudo iptables -A INPUT -p tcp --dport 2222 -j ACCEPT
sudo iptables -D INPUT -p tcp --dport 22 -j ACCEPT

# SSH 재시작
sudo systemctl restart sshd
```

### 2. fail2ban 상태 확인
```bash
# fail2ban 실행 확인
sudo systemctl status fail2ban

# 차단된 IP 확인
sudo fail2ban-client status sshd
```

### 3. 정기적인 로그 점검
```bash
# 차단된 연결 시도 확인
sudo grep "DPT=" /var/log/messages | tail -50
```

### 4. 불필요한 서비스 중지
```bash
# 실행 중인 서비스 확인
sudo systemctl list-units --type=service --state=running

# 불필요한 서비스 중지
sudo systemctl stop [서비스명]
sudo systemctl disable [서비스명]
```

---

## 관련 파일 위치

| 파일 | 설명 |
|------|------|
| `/root/apply_whitelist_firewall.sh` | 화이트리스트 적용 스크립트 |
| `/root/reset_firewall.sh` | 긴급 복원 스크립트 |
| `/root/iptables_backup_*.txt` | 백업 파일들 |
| `/etc/iptables/rules.v4` | 현재 적용 중인 규칙 |
| `/etc/vsftpd/vsftpd.conf` | FTP 서버 설정 |
| `/etc/ssh/sshd_config` | SSH 서버 설정 |

---

## 변경 이력

| 날짜 | 작업 | 담당자 | 비고 |
|------|------|--------|------|
| 2025-12-05 | 화이트리스트 방식 적용 | root | 초기 설정 |
| 2025-12-05 | 백업 및 복원 스크립트 생성 | root | 안전장치 구축 |

---

## 참고 자료

- [iptables 공식 문서](https://netfilter.org/)
- [vsftpd 설정 가이드](https://security.appspot.com/vsftpd.html)
- [fail2ban 설정](https://www.fail2ban.org/)

---

**문의:** 시스템 관리자
**마지막 업데이트:** 2025년 12월 5일

