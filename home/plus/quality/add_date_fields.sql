-- 검수일시 및 최종결제일시 필드 추가
-- 기존 테이블에 필드를 추가하는 마이그레이션 스크립트

ALTER TABLE `g5_quality_result` 
ADD COLUMN `qr_review_date` datetime DEFAULT NULL COMMENT '검수일시 (사용자 입력)' AFTER `qr_inspection_date`,
ADD COLUMN `qr_approval_date` datetime DEFAULT NULL COMMENT '최종결제일시 (사용자 입력)' AFTER `qr_review_date`;

-- 인덱스 추가 (선택사항)
ALTER TABLE `g5_quality_result` 
ADD INDEX `qr_review_date` (`qr_review_date`),
ADD INDEX `qr_approval_date` (`qr_approval_date`);

