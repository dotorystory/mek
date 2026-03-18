-- 템플릿 테이블에 파일 경로 필드 추가
ALTER TABLE `g5_quality_template` 
ADD COLUMN `qt_filepath` varchar(500) DEFAULT NULL COMMENT '템플릿 파일 경로' AFTER `qt_memo`,
ADD COLUMN `qt_filename` varchar(255) DEFAULT NULL COMMENT '템플릿 파일명' AFTER `qt_filepath`;

