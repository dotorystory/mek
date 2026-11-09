-- 품질 검사표 시스템 데이터베이스 테이블 생성
-- 데이터베이스: mek_kr
-- 테이블 접두사: g5_

-- 1. 검사표 템플릿 테이블
CREATE TABLE IF NOT EXISTS `g5_quality_template` (
  `qt_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '템플릿 ID',
  `qt_doc_id` varchar(100) NOT NULL COMMENT '문서 ID (고유 식별자)',
  `qt_title` varchar(255) NOT NULL COMMENT '검사표 제목 (측정장비명)',
  `qt_location` varchar(255) DEFAULT NULL COMMENT '문서 위치',
  `qt_memo` text DEFAULT NULL COMMENT '메모',
  `qt_filepath` varchar(500) DEFAULT NULL COMMENT '템플릿 파일 경로',
  `qt_filename` varchar(255) DEFAULT NULL COMMENT '템플릿 파일명',
  `qt_created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '생성 일시',
  `qt_updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '수정 일시',
  `qt_status` enum('active','inactive') NOT NULL DEFAULT 'active' COMMENT '상태',
  PRIMARY KEY (`qt_id`),
  UNIQUE KEY `qt_doc_id` (`qt_doc_id`),
  KEY `qt_status` (`qt_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='검사표 템플릿';

-- 2. 검사 항목 테이블
CREATE TABLE IF NOT EXISTS `g5_quality_item` (
  `qi_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '항목 ID',
  `qt_id` int(11) NOT NULL COMMENT '템플릿 ID (외래키)',
  `qi_item_id` varchar(50) DEFAULT NULL COMMENT '항목 ID (템플릿 내 순서)',
  `qi_procedure` varchar(255) DEFAULT NULL COMMENT '검사절차명',
  `qi_item` varchar(255) DEFAULT NULL COMMENT '검사항목',
  `qi_method` text DEFAULT NULL COMMENT '검사방법',
  `qi_standard` text DEFAULT NULL COMMENT '품질기준',
  `qi_unit` varchar(50) DEFAULT NULL COMMENT '단위',
  `qi_order` int(11) DEFAULT 0 COMMENT '정렬 순서',
  PRIMARY KEY (`qi_id`),
  KEY `qt_id` (`qt_id`),
  KEY `qi_order` (`qi_order`),
  CONSTRAINT `g5_quality_item_ibfk_1` FOREIGN KEY (`qt_id`) REFERENCES `g5_quality_template` (`qt_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='검사 항목';

-- 3. 검사 결과 테이블
CREATE TABLE IF NOT EXISTS `g5_quality_result` (
  `qr_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '결과 ID',
  `qt_id` int(11) NOT NULL COMMENT '템플릿 ID (외래키)',
  `qr_doc_id` varchar(100) NOT NULL COMMENT '문서 ID',
  `qr_title` varchar(255) NOT NULL COMMENT '검사표 제목',
  `qr_location` varchar(255) DEFAULT NULL COMMENT '문서 위치',
  `qr_memo` text DEFAULT NULL COMMENT '메모',
  `qr_inspection_date` datetime DEFAULT NULL COMMENT '검사일시 (사용자 입력)',
  `qr_review_date` datetime DEFAULT NULL COMMENT '검수일시 (사용자 입력)',
  `qr_approval_date` datetime DEFAULT NULL COMMENT '최종결제일시 (사용자 입력)',
  `qr_inspector` varchar(50) DEFAULT NULL COMMENT '검사자 (회원 ID)',
  `qr_inspector_name` varchar(100) DEFAULT NULL COMMENT '검사자 이름',
  `qr_reviewer` varchar(50) DEFAULT NULL COMMENT '검수자 (회원 ID)',
  `qr_reviewer_name` varchar(100) DEFAULT NULL COMMENT '검수자 이름',
  `qr_final_approver` varchar(50) DEFAULT NULL COMMENT '최종결제자 (회원 ID)',
  `qr_final_approver_name` varchar(100) DEFAULT NULL COMMENT '최종결제자 이름',
  `qr_status` enum('draft','inspected','reviewed','approved','rejected') NOT NULL DEFAULT 'draft' COMMENT '상태',
  `qr_created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '생성 일시',
  `qr_updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '수정 일시',
  PRIMARY KEY (`qr_id`),
  KEY `qt_id` (`qt_id`),
  KEY `qr_doc_id` (`qr_doc_id`),
  KEY `qr_status` (`qr_status`),
  KEY `qr_inspector` (`qr_inspector`),
  KEY `qr_inspection_date` (`qr_inspection_date`),
  KEY `qr_review_date` (`qr_review_date`),
  KEY `qr_approval_date` (`qr_approval_date`),
  CONSTRAINT `g5_quality_result_ibfk_1` FOREIGN KEY (`qt_id`) REFERENCES `g5_quality_template` (`qt_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='검사 결과';

-- 4. 검사 결과 상세 테이블
CREATE TABLE IF NOT EXISTS `g5_quality_result_detail` (
  `qrd_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '상세 ID',
  `qr_id` int(11) NOT NULL COMMENT '결과 ID (외래키)',
  `qi_id` int(11) NOT NULL COMMENT '항목 ID (외래키)',
  `qrd_result` text DEFAULT NULL COMMENT '검사결과 (값)',
  `qrd_reviewer_check` enum('Y','N') DEFAULT 'N' COMMENT '검수자확인',
  `qrd_note` text DEFAULT NULL COMMENT '비고',
  `qrd_order` int(11) DEFAULT 0 COMMENT '정렬 순서',
  PRIMARY KEY (`qrd_id`),
  KEY `qr_id` (`qr_id`),
  KEY `qi_id` (`qi_id`),
  KEY `qrd_order` (`qrd_order`),
  CONSTRAINT `g5_quality_result_detail_ibfk_1` FOREIGN KEY (`qr_id`) REFERENCES `g5_quality_result` (`qr_id`) ON DELETE CASCADE,
  CONSTRAINT `g5_quality_result_detail_ibfk_2` FOREIGN KEY (`qi_id`) REFERENCES `g5_quality_item` (`qi_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='검사 결과 상세';

-- 5. 검사 사진 테이블
CREATE TABLE IF NOT EXISTS `g5_quality_photo` (
  `qp_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '사진 ID',
  `qrd_id` int(11) DEFAULT NULL COMMENT '결과 상세 ID (외래키)',
  `qr_id` int(11) NOT NULL COMMENT '결과 ID (외래키)',
  `qi_id` int(11) NOT NULL COMMENT '항목 ID (외래키)',
  `qp_filename` varchar(255) NOT NULL COMMENT '원본 파일명',
  `qp_filepath` varchar(500) NOT NULL COMMENT '저장 경로',
  `qp_filesize` int(11) DEFAULT 0 COMMENT '파일 크기 (bytes)',
  `qp_width` int(11) DEFAULT 0 COMMENT '이미지 너비 (px)',
  `qp_height` int(11) DEFAULT 0 COMMENT '이미지 높이 (px)',
  `qp_thumbnail` varchar(500) DEFAULT NULL COMMENT '썸네일 경로',
  `qp_uploaded_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '업로드 일시',
  `qp_order` int(11) DEFAULT 0 COMMENT '정렬 순서',
  PRIMARY KEY (`qp_id`),
  KEY `qrd_id` (`qrd_id`),
  KEY `qr_id` (`qr_id`),
  KEY `qi_id` (`qi_id`),
  KEY `qp_order` (`qp_order`),
  CONSTRAINT `g5_quality_photo_ibfk_1` FOREIGN KEY (`qrd_id`) REFERENCES `g5_quality_result_detail` (`qrd_id`) ON DELETE CASCADE,
  CONSTRAINT `g5_quality_photo_ibfk_2` FOREIGN KEY (`qr_id`) REFERENCES `g5_quality_result` (`qr_id`) ON DELETE CASCADE,
  CONSTRAINT `g5_quality_photo_ibfk_3` FOREIGN KEY (`qi_id`) REFERENCES `g5_quality_item` (`qi_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='검사 사진';

-- 6. 결제 이력 테이블
CREATE TABLE IF NOT EXISTS `g5_quality_approval` (
  `qa_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '결제 ID',
  `qr_id` int(11) NOT NULL COMMENT '결과 ID (외래키)',
  `qa_step` enum('inspector','reviewer','final') NOT NULL COMMENT '결제 단계',
  `qa_approver` varchar(50) NOT NULL COMMENT '결제자 (회원 ID)',
  `qa_approver_name` varchar(100) NOT NULL COMMENT '결제자 이름',
  `qa_status` enum('approved','rejected') NOT NULL COMMENT '상태',
  `qa_comment` text DEFAULT NULL COMMENT '결제 의견',
  `qa_approved_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '결제 일시',
  PRIMARY KEY (`qa_id`),
  KEY `qr_id` (`qr_id`),
  KEY `qa_step` (`qa_step`),
  KEY `qa_status` (`qa_status`),
  CONSTRAINT `g5_quality_approval_ibfk_1` FOREIGN KEY (`qr_id`) REFERENCES `g5_quality_result` (`qr_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='결제 이력';

