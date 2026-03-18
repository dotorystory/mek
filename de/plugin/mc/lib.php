<?php
/**
 * category library
 * @author eyesonlyz@nate.com
 * @update 2019-10-07
 * @version 2.0
 */

namespace {

    use mc\Board;
    use mc\Category;

    const MC = true;
    define('MC_VERSION', file_get_contents(dirname(__FILE__)."/version"));
    define('MC_DATA_PATH', G5_DATA_PATH . '/mc'); // 설정파일 저장 디렉토리 (고정)
    define('MC_PLUGIN_PATH', G5_PLUGIN_PATH . '/mc'); // 플로그인 업로드 경로 (고정)
    define('MC_PLUGIN_URL', G5_PLUGIN_URL . '/mc'); // 플로그인 업로드 경로 (고정)


    define('MC_USE_RANGE_SEARCH', false);


    const MC_SEARCH_DELIMITER = ',';
    const MC_CATEGORY_DELIMITER = ".";
    const MC_OR_DELIMITER = '|';
    const MC_CATEGORY_NAME_CHECK_REGEX = '/,|\|\"\'/';

    /**
     * 카테고리명에 사용할수 업는 문자 치환
     * @param string $name 카테고리명
     * @return string
     */
    function mc_category_name_input_filter($name)
    {
        $name = trim((string)$name);
        $name = str_replace(array('.', ','), array('.', ','), $name);
        return $name;
    }

    /**
     * 멀티카테고리 데이타셋 제출
     * @return Category|null
     */
    function mc()
    {
        static $obj = null;
        if (func_num_args() > 0) {
            return Category::get(func_get_arg(0));
        }
        return $obj ?: $obj = Category::root();
    }

    /**
     * @param string $bo_table
     * @return Board|null
     */
    function mc_board($bo_table)
    {
        global $g5;
        static $obj = array();

        if (empty($obj[$bo_table])) {
            if ($GLOBALS['bo_table'] === $bo_table && !empty($GLOBALS['board'])) {
                $board = $GLOBALS['board'];
            } else {
                $board = sql_fetch("SELECT * FROM {$g5['board_table']} WHERE bo_table = '$bo_table' ");
            }

            if (is_array($board) && !empty($board['bo_table'])) {
                $obj[$bo_table] = new Board($board);
            }
        }

        return isset($obj[$bo_table]) ? $obj[$bo_table] : null;
    }



    if (function_exists('mysqli_fetch_object') && G5_MYSQLI_USE) {
        function sql_fetch_obj($result, $class = 'stdClass')
        {
            return call_user_func_array("mysqli_fetch_object", func_get_args());
        }
    } else {
        function sql_fetch_obj()
        {
            return call_user_func_array("mysql_fetch_object", func_get_args());
        }
    }

    /**
     * @param string $str
     * @return array
     */
    function mc_checkbox_to_array($str)
    {
        $words = array();
        foreach (explode(MC_OR_DELIMITER, $str) as $word) {
            $word = trim($word);
            if ($word) {
                $words[] = $word;
            }
        }
        return $words;
    }

    /**
     * @param string $str
     * @return string
     */
    function mc_title($str)
    {
        return (string)str_replace('.', '', str_replace('..', ', ', $str));
    }

    /**
     * 그누보드 사용자 컬럼 제출
     * @param string $bo_table
     * @return array
     */
    function get_mc_board_ext_columns($bo_table)
    {
        static $columns = array();
        static $exclude_columns = array('wr_id', 'wr_num', 'wr_reply', 'wr_parent', 'wr_is_comment', 'wr_comment', 'wr_comment_reply', 'ca_name', 'wr_option', 'wr_subject', 'wr_content', 'wr_link1', 'wr_link2', 'wr_link1_hit', 'wr_link2_hit', 'wr_hit', 'wr_good', 'wr_nogood', 'mb_id', 'wr_password', 'wr_name', 'wr_email', 'wr_homepage', 'wr_datetime', 'wr_file', 'wr_last', 'wr_ip', 'wr_facebook_user', 'wr_twitter_user');

        if (!isset($columns[$bo_table])) {

            $sql = "SELECT `COLUMN_NAME`
			FROM `INFORMATION_SCHEMA`.`COLUMNS` 
			WHERE `TABLE_SCHEMA`='" . G5_MYSQL_DB . "' 
			AND `TABLE_NAME`='" . G5_TABLE_PREFIX . "write_" . $bo_table . "'
			AND ORDINAL_POSITION >= (
				SELECT ORDINAL_POSITION FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='" . G5_MYSQL_DB . "' AND `TABLE_NAME`='" . G5_TABLE_PREFIX . "write_" . $bo_table . "'
				AND COLUMN_NAME = 'wr_1'
			)
			ORDER BY ORDINAL_POSITION;";
            $result = sql_query($sql);
            $_columns = array();
            while ($row = sql_fetch_obj($result)) {
                $_columns[] = $row->COLUMN_NAME;
            }
            $columns[$bo_table] = array_diff($_columns, $exclude_columns);
        }
        return $columns[$bo_table];
    }


    function mc_get_sql_search($search_ca_name, $search_field, $search_text, $search_operator = 'and')
    {
        global $g5, $mc_search;

        $str = "";
        if ($search_ca_name)
            $str = " ca_name = '$search_ca_name' ";


        if ($mc_search) {
            if ($str) $str .= ' AND ';
            $str .= $mc_search;
        }

        $search_text = strip_tags(($search_text));
        $search_text = trim(stripslashes($search_text));

        if (!$search_text && $search_text !== '0') {
            if ($search_ca_name || $mc_search) {
                return $str;
            } else {
                return '0';
            }
        }

        if ($str)
            $str .= " and ";

        // 쿼리의 속도를 높이기 위하여 ( ) 는 최소화 한다.
        $op1 = "";

        // 검색어를 구분자로 나눈다. 여기서는 공백
        $s = array();
        $s = explode(" ", $search_text);

        // 검색필드를 구분자로 나눈다. 여기서는 +
        $tmp = array();
        $tmp = explode(",", trim($search_field));
        $field = explode("||", $tmp[0]);
        $not_comment = "";
        if (!empty($tmp[1]))
            $not_comment = $tmp[1];

        $str .= "(";
        for ($i = 0; $i < count($s); $i++) {
            // 검색어
            $search_str = trim($s[$i]);
            if ($search_str == "") continue;

            // 인기검색어
            insert_popular($field, $search_str);

            $str .= $op1;
            $str .= "(";

            $op2 = "";
            for ($k = 0; $k < count($field); $k++) { // 필드의 수만큼 다중 필드 검색 가능 (필드1+필드2...)

                // SQL Injection 방지
                // 필드값에 a-z A-Z 0-9 _ , | 이외의 값이 있다면 검색필드를 wr_subject 로 설정한다.
                $field[$k] = preg_match("/^[\w\,\|]+$/", $field[$k]) ? strtolower($field[$k]) : "wr_subject";

                $str .= $op2;
                switch ($field[$k]) {
                    case "mb_id" :
                    case "wr_name" :
                        $str .= " $field[$k] = '$s[$i]' ";
                        break;
                    case "wr_hit" :
                    case "wr_good" :
                    case "wr_nogood" :
                        $str .= " $field[$k] >= '$s[$i]' ";
                        break;
                    // 번호는 해당 검색어에 -1 을 곱함
                    case "wr_num" :
                        $str .= "$field[$k] = " . ((-1) * $s[$i]);
                        break;
                    case "wr_ip" :
                    case "wr_password" :
                        $str .= "1=0"; // 항상 거짓
                        break;
                    // LIKE 보다 INSTR 속도가 빠름
                    default :
                        if (preg_match("/[a-zA-Z]/", $search_str))
                            $str .= "INSTR(LOWER($field[$k]), LOWER('$search_str'))";
                        else
                            $str .= "INSTR($field[$k], '$search_str')";
                        break;
                }
                $op2 = " or ";
            }
            $str .= ")";

            $op1 = " $search_operator ";
        }
        $str .= " ) ";
        if ($not_comment)
            $str .= " and wr_is_comment = '0' ";

        return $str;
    }


    /**
     * 카테고리
     * Class mc
     */
    class mc
    {
        /**
         * 설치여부 제출
         * @param bool $cached
         * @return bool
         */
        public static function isInstalled($cached = false)
        {
            static $installed = null;
            if ($cached && $installed !== null) {
                return $installed;
            }
            return ($installed = sql_fetch("SHOW TABLES LIKE '" . mc\TABLE_NAME . "'")) ? true : false;
        }

        /**
         * 인스톨.
         * @return bool
         */
        public static function install()
        {
            if (!self::isInstalled()) {
                $sql = "CREATE TABLE IF NOT EXISTS " . mc\TABLE_NAME . " (
                    `mc`  INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
                    `parent_id`  INT(11) UNSIGNED NULL DEFAULT NULL ,
                    `lft`  INT(11) UNSIGNED NOT NULL ,
                    `rgt`  INT(11) UNSIGNED NOT NULL ,
                    `depth`  TINYINT(3) UNSIGNED NOT NULL ,
                    `title`  VARCHAR(32) NOT NULL ,
                    `path`  VARCHAR(255) NOT NULL ,
                    `path_id`  VARCHAR(255) ,
                    PRIMARY KEY (`mc`),
                    INDEX `lft` (`lft`) ,
                    INDEX `rgt` (`rgt`) ,
                    INDEX `depth` (`depth`) , 
                    UNIQUE INDEX `path` (`path`) , 
                    UNIQUE INDEX `path_id` (`path_id`)  
                ) DEFAULT CHARACTER SET=utf8";
                sql_query($sql);
                mc::insert(mc\TABLE_NAME, array('title' => '카테고리', 'lft' => 1, 'rgt' => 2, 'depth' => 0)); // root 카테고리 입력
                if (!is_dir(MC_DATA_PATH)) {
                    mkdir(MC_DATA_PATH, G5_DIR_PERMISSION);
                }
            }
            return self::isInstalled();
        }

        /**
         * 언인스톨
         * @param bool $data_delete 디렉토리 데이타 삭제여부.
         * @return bool
         */
        public static function uninstall($data_delete = false)
        {
            if (self::isInstalled()) {
                sql_query("DROP TABLE IF EXISTS " . mc\TABLE_NAME);
                if ($data_delete && is_dir(MC_DATA_PATH)) {
                    foreach (glob(MC_DATA_PATH . '/*.js') as $file) {
                        unlink($file);
                    }
                    rmdir(MC_DATA_PATH);
                }
            }
            return !self::isInstalled();
        }

        public static function insert($table, $data)
        {
            $data = array_map('sql_escape_string', $data);
            $sql = "INSERT INTO $table ";
            $columns = array();
            foreach ($data as $k => $v) {
                $columns[] = "`$k`";
                $values[] = $v;
            }
            if (!empty($values)) {
                $sql .= "(" . join(',', $columns) . ") VALUES ('" . join("','", $values) . "')";
                sql_query($sql, true);
            }
        }

        public static function update($table, $data, $where)
        {
            $data = array_map('sql_escape_string', $data);
            $sql = "UPDATE $table SET ";
            $columns = array();
            foreach ($data as $k => $v) {
                if($v === null){
                    $columns[] = "`$k`=NULL";
                }else{
                    $columns[] = "`$k`='$v'";
                }
            }
            $sql .= join(',', $columns);
            $wheres = array();
            foreach ($where as $k => $v) {
                $wheres[] = "`$k`='$v'";
            }
            $sql .= " WHERE " . join(' AND ', $wheres);
            sql_query($sql, true);
        }
    }
}

namespace mc {

    use \Exception;

    /**
     * 설정테이블.
     */
    const TABLE_NAME = 'mc_category';

    /**
     * sql 디버깅 사용여부.
     */
    const DEBUG = false;


    function arrayToAttrString(array $attrs)
    {
        $arr = array();
        foreach ($attrs as $k => $v) {
            if (is_int($k)) {
                $arr[] = htmlspecialchars($v);
            } else {
                $arr[] = $k . '="' . htmlspecialchars($v) . '"';
            }
        }
        return join(' ', $arr);
    }

}
