<?php
if (!defined('_GNUBOARD_')) exit;

/**
 * CSV 파일 파싱 함수
 */

/**
 * CSV 파일 파싱 및 템플릿 데이터 추출
 */
function parse_quality_csv($file_path) {
    if (!file_exists($file_path)) {
        return array('error' => '파일을 찾을 수 없습니다.');
    }
    
    // 파일 인코딩 감지 및 변환
    $content = file_get_contents($file_path);
    $encoding = mb_detect_encoding($content, array('UTF-8', 'EUC-KR', 'CP949'), true);
    
    if ($encoding != 'UTF-8') {
        $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        // 임시 파일에 UTF-8로 변환된 내용 저장
        $temp_file = tempnam(sys_get_temp_dir(), 'csv_');
        file_put_contents($temp_file, $content);
        $file_path = $temp_file;
    }
    
    $handle = fopen($file_path, 'r');
    if (!$handle) {
        return array('error' => '파일을 열 수 없습니다.');
    }
    
    $data = array();
    $row_num = 0;
    
    // CSV 파일 읽기
    while (($row = fgetcsv($handle)) !== FALSE) {
        $row_num++;
        
        // 빈 행 건너뛰기 (모든 셀이 비어있거나 공백만 있는 경우)
        $row_filtered = array_filter($row, function($cell) {
            return trim($cell) !== '';
        });
        if (empty($row_filtered)) {
            continue;
        }
        
        // 첫 번째 행: (문서ID), 검사표제목, 제목값, 문서위치, 위치값, 메모, 메모값
        // 샘플: (문서ID),검사표제목,A-측정장비 제작품질 검수서(X.B.L),문서위치,/서버/제조기술/두께측정기/문서자료,메모,(메모를 입력하세요.)
        if ($row_num == 1) {
            // row[0] = "(문서ID)" (라벨)
            // row[1] = "검사표제목" (라벨)
            // row[2] = "A-측정장비 제작품질 검수서(X.B.L)" (실제 제목)
            // row[3] = "문서위치" (라벨)
            // row[4] = "/서버/제조기술/두께측정기/문서자료" (실제 위치)
            // row[5] = "메모" (라벨)
            // row[6] = "(메모를 입력하세요.)" (실제 메모)
            
            $data['doc_id'] = 'TMP_' . date('YmdHis') . '_' . uniqid(); // 문서ID는 자동 생성
            $data['title'] = isset($row[2]) ? trim($row[2]) : '';  // 3번째 열 (인덱스 2) - 실제 제목
            $data['location'] = isset($row[4]) ? trim($row[4]) : '';  // 5번째 열 (인덱스 4) - 실제 위치
            $data['memo'] = isset($row[6]) ? trim($row[6]) : '';  // 7번째 열 (인덱스 6) - 실제 메모
            
            // 메모에서 괄호 제거
            if (!empty($data['memo']) && preg_match('/^\((.+)\)$/', $data['memo'], $matches)) {
                $data['memo'] = trim($matches[1]);
            }
        }
        // 두 번째 행: 관리자, 검사자, 검수자, 최종결제자
        elseif ($row_num == 2) {
            $data['admin'] = isset($row[0]) ? trim($row[0]) : '';
            $data['inspector'] = isset($row[1]) ? trim($row[1]) : '';
            $data['inspector_name'] = isset($row[2]) ? trim($row[2]) : '';
            $data['reviewer'] = isset($row[3]) ? trim($row[3]) : '';
            $data['reviewer_name'] = isset($row[4]) ? trim($row[4]) : '';
            $data['final_approver'] = isset($row[5]) ? trim($row[5]) : '';
            $data['final_approver_name'] = isset($row[6]) ? trim($row[6]) : '';
        }
        // 세 번째 행: 일시 정보 (현재는 저장하지 않음, 검사 시 입력)
        elseif ($row_num == 3) {
            // 검사일, 검수일, 최종결제일은 검사 시 입력받음
        }
        // 네 번째 행: 컬럼 헤더 (검증용)
        elseif ($row_num == 4) {
            $data['headers'] = $row;
        }
        // 다섯 번째 행부터: 검사 항목 데이터
        elseif ($row_num >= 5) {
            if (!isset($data['items'])) {
                $data['items'] = array();
            }
            
            // ID, 검사절차명, 검사항목, 검사방법, 품질기준, 단위, 검사결과, 첨부파일, 비고
            $item = array(
                'item_id' => isset($row[0]) ? trim($row[0]) : '',
                'procedure' => isset($row[1]) ? trim($row[1]) : '',
                'item' => isset($row[2]) ? trim($row[2]) : '',
                'method' => isset($row[3]) ? trim($row[3]) : '',
                'standard' => isset($row[4]) ? trim($row[4]) : '',
                'unit' => isset($row[5]) ? trim($row[5]) : '',
                'order' => count($data['items']) + 1
            );
            
            // 빈 항목은 제외
            if (!empty($item['item_id']) && !empty($item['item'])) {
                $data['items'][] = $item;
            }
        }
    }
    
    fclose($handle);
    
    // 임시 파일 삭제
    if (isset($temp_file) && file_exists($temp_file)) {
        @unlink($temp_file);
    }
    
    return $data;
}

