-- 회사명과 구분 필드 추가
ALTER TABLE `g5_quality_result` 
ADD COLUMN `qr_company` varchar(255) DEFAULT NULL COMMENT '회사명' AFTER `qr_location`,
ADD COLUMN `qr_division` varchar(255) DEFAULT NULL COMMENT '구분(제품구분)' AFTER `qr_company`;

