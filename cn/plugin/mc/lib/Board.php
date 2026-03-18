<?php


namespace mc;

class Board
{
    /**
     * 그누보드 board 설정값
     * @var array
     */
    protected $board;
    protected $config = array(
        'list_skin' => 'basic', // 리스트 스킨
        'write_skin' => 'basic', // 글쓰기 스킨
        'view_skin' => 'basic', // 내용보기 스킨
        'columns' => array(), // 여분플드 설정
        'list_selector_use' => '1',
        'list_selector' => '#bo_list',
        'write_selector_use' => '1',
        'write_selector' => '#fwrite',
        'view_selector_use' => '1',
        'view_selector' => '#bo_v_atc',
        'required' => 0
    );
    public static $default_column_config = array(
        'root' => '',
        'name' => '', // input name
        'column' => 'mc', // 테이타 컬럼
        'type' => 'select', // 출력방식
        'list_type' => 'select',
        'op' => 'or',
        'title' => '', //
        'label' => '',
        'required' => 0, //필수 입력
        'searchable' => 1,
    );

    protected $file;

    protected $mode;

    /**
     * @var array
     */
    public $values = array();


    public $list_selector;
    public $list_selector_use;

    public function __construct(array $board)
    {
        $this->board = $board;
        $this->file = MC_DATA_PATH . '/' . $board['bo_table'] . '.js';
        if (is_file($this->file)) {
            if ($config = json_decode(file_get_contents($this->file), true)) {
                if (json_last_error() === JSON_ERROR_NONE) {
                    $this->config = $config;
                }
            }
        }
    }

    public function setMode($mode)
    {
        $this->mode = $mode;
        return $this;
    }

    public function setValues($values)
    {
        $this->values = $values;
        return $this;
    }

    /**
     * 리스트 스킨셋
     * @param string $type ist,write
     * @return array
     */
    public static function getSkins($type = 'write')
    {
        $skins = array();
        if (self::isValidSkinType($type)) {
            $dir = MC_PLUGIN_PATH . '/skins/' . $type;
            foreach (glob($dir . '/*.php') as $name) {
                $name = pathinfo($name, PATHINFO_FILENAME);
                $skins[$name] = $name;
            }
        }
        return $skins;
    }

    /**
     * 스킨허용타입확인
     * @param $type
     * @return string|false
     */
    public static function isValidSkinType($type)
    {
        if (in_array($type, array('list', 'write', 'view'))) {
            return $type;
        } else {
            return false;
        }
    }

    /**
     * @param array|null $params
     * @return string
     */
    public function getSearchSql(array $params = null)
    {
        if ($params === null) {
            $params = $_GET;
        }
        $mc_search = array();
        foreach ($this->config['columns'] as $k => $v) {
            if (!empty($v['searchable']) && (isset($params[$k]) || array_key_exists($k, $params))) {
                $params[$k] = trim($params[$k]);
                if ($params[$k] === '') continue;
                $value = $params[$k];

                if ($v['list_type'] === 'radio') {
                    $mc_search[] = "FIND_IN_SET('{$value}', `$k`) > 0";
                } elseif ($v['list_type'] === 'checkbox' || $v['write_type']==='checkbox') {// 멀티
                    $words = explode('|', $params[$k]);
                    $_word_sql = array();
                    foreach ($words as $word) {
                        if (!empty($word) && $word !== MC_CATEGORY_DELIMITER && $word != MC_SEARCH_DELIMITER) {
                            $_word_sql[] = "FIND_IN_SET('{$word}', `$k`) > 0";
                        }
                    }
                    if ($_word_sql) {
                        $op = strtolower($v['op']) === 'or' ? 'OR' : 'AND';
                        $mc_search[] = '(' . join(" $op ", $_word_sql) . ')';
                    }
                } else if ($v['list_type'] === 'between') {
                    $words = explode('~', $params[$k]);

                    if (!empty($words[0]) && !empty($words[1])) {
                        $mc_search[] = "$k BETWEEN '{$words[0]}' AND '{$words[1]}'";
                    } elseif (!empty($words[0])) {
                        $mc_search[] = "$k>='{$words[0]}'";
                    } elseif (!empty($words[1])) {
                        $mc_search[] = "$k<='{$words[1]}'";
                    }
                }else if($v['list_type'] === 'range'){
                    $words = explode('~', $params[$k]);

                    if (!empty($words[0]) && !empty($words[1])) {
                        $words[0] = (int) $words[0];
                        $words[1] = (int) $words[1];
                        $mc_search[] = "$k BETWEEN '{$words[0]}' AND '{$words[1]}'";
                    } elseif (!empty($words[0])) {
                        $words[0] = (int) $words[0];
                        $mc_search[] = "$k>='{$words[0]}'";
                    } elseif (!empty($words[1])) {
                        $words[1] = (int) $words[1];
                        $mc_search[] = "$k<='{$words[1]}'";
                    }
                } else { // list_type select 깊이

                    switch ($v['data_type']) {
                        case Column::DATASET_STRING_SPLIT:
                            $mc_search[] = "`$k`='{$params[$k]}'";
                            break;
                        case Column::DATASET_CATEGORY:
                            if ($v['column'] === 'mc' || $v['column'] === 'path') {
                                $mc_search[] = "(
                                    `$k`='{$params[$k]}' OR
                                    `$k` LIKE '{$params[$k]},%' OR
                                    `$k` LIKE '{$params[$k]}.%' OR
                                    `$k` LIKE '%,{$params[$k]}' OR
                                    `$k` LIKE '%,{$params[$k]},%' OR
                                    `$k` LIKE '%,{$params[$k]}.%'
                                )";
                            }
                            break;
                    }


                }
            }
        }
        if (!empty($mc_search)) {
            return '(' . join(' AND ', $mc_search) . ')';
        }
        return '';
    }

    public function getAddQueryString(array $params = null)
    {
        if ($params === null) {
            $params = $_GET;
        }
        $query = array();
        foreach ($this->config['columns'] as $k => $v) {
            if (!empty($params[$k]) && !empty($v['searchable'])) {
                $query[$k] = $params[$k];
            }
        }
        return $query ? http_build_query($query, null, '&amp;', PHP_QUERY_RFC1738) : null;
    }

    /**
     * @return \stdClass
     */
    public function &getConfig()
    {
        return $this->config;
    }

    /**
     * 리스트 스킨 파일 제출.
     * @param string $type
     * @return null|string
     */
    public function getSkinFile($type)
    {
        $file = MC_PLUGIN_PATH . '/' . $type . '_skin/' . $this->config[$type . '_skin'] . '.php';
        if (is_file($file)) {
            return $file;
        }
        return null;
    }

    public function getSelectbox($type, $name = null, $value = null, $before_add_html = '')
    {
        $name = $name === null ? $type . '_skin' : $name;
        $current_value = $value === null ? $this->config[$type . '_skin'] : '';
        $html = '<select name="' . $name . '">' . $before_add_html;
        foreach (self::getSkins($type) as $v) {
            $selected = $v === $current_value ? ' selected' : '';
            $html .= '<option value="' . $v . '"' . $selected . '>' . $v . '</option>';
        }
        $html .= '</select>';
        return $html;
    }

    /**
     * 컬럼 설정 추가
     * @param $column
     * @param $config
     * @return $this
     */
    public function addColumn($column, array $config)
    {
        $config = array_merge(self::$default_column_config, $config);
        $this->config['columns'][$column] = $config;
        return $this;
    }

    /**
     * 환결설정 저장
     */
    public function save()
    {
        ksort($this->config);
        $options = defined('JSON_UNESCAPED_UNICODE')? JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT:0;
        $json = json_encode($this->config, $options); // since php 5.4 options
        if($json) {
            file_put_contents($this->file, $json);
        }
    }

    /**
     * 컬럼설정삭제.
     * @param string $column
     * @return $this
     */
    public function removeColumn($column)
    {
        if (isset($this->config['columns'][$column])) {
            unset($this->config['columns'][$column]);
        }
        return $this;
    }

    /**
     * @return Column[]
     */
    public function getColumns()
    {
        $array = array();
        foreach ($this->config['columns'] as $name => $attrs) {
            if ($this->mode === 'list' && (/*$attrs['data_type'] === Column::DATASET_NUMBER ||*/ $attrs['data_type'] === Column::DATASET_TEXT)) {
                continue;
            }
            $attrs['name'] = $name;
            $attrs['mode'] = $this->mode;
            if (isset($this->values[$name]) || array_key_exists($name, $this->values)) {
                $attrs['value'] = $this->values[$name];
            }
            $array[$name] = Column::factory($attrs);
        }
        return $array;
    }

    /**
     * 목록에서 검색시 입력된 데이타셋
     * @return array
     */
    public function getExistValues()
    {
        $values = $this->values;
        $array = array();
        foreach ($this->config['columns'] as $name => $attrs) {
            if (isset($values[$name]) || array_key_exists($name, $values)) {
                $value = $values[$name];
                if ($value === '') continue;
                if ($attrs['list_type'] === 'checkbox') {
                    foreach (explode('|', $value) as $v) {
                        $array[] = array('name' => $name, 'value' => $v);
                    }
                } else {
                    $array[] = array('name' => $name, 'value' => $values[$name]);
                }
            }
        }
        return $array;
    }


    /**
     * 데이타 유효성 체크
     * @param array $params $_GET or $_POST ...
     * @throws \Exception
     */
    public function validateParams(array $params)
    {
        foreach ($this->config['columns'] as $name => $attrs) {
            if ($attrs['required'] && empty($params[$name])) {
                throw new \Exception($attrs['title'] . '를 선택해 주세요.');
            }
            if (!$root = \mc($attrs['root'])) {
                throw new \Exception($attrs['title'] . ' 값이 올바르지 않습니다.');
            }
            $value = $ori_value = $params[$name]; //유호한 테이터인지 확인.


            $_params = array('mc' => $value);
            if ($attrs['column'] === 'path') {
                if ($attrs['type'] === 'checkbox') {
                    if (true === $root->inContains(mc_checkbox_to_array($value), $attrs['column'], true)) {
                        return;
                    }
                } else {
                    $_params = array('path' => $root->path . MC_CATEGORY_DELIMITER . $value);
                }
            }

            $mc = \mc($_params);

            if (!$mc) {
                throw new \Exception($attrs['title'] . ' 값이 존재하지 않습니다.');


            }
            if (strncmp($mc->path_id, $root->path_id, strlen($root->path_id))) {

                throw new \Exception($attrs['title'] . ' 값이 올바르지 않습니다.');
            }
        }
    }


    public function render()
    {
        $mode = $this->mode;
        $skin = $this->config[$mode . '_skin'];
        $file = MC_PLUGIN_PATH . '/skins/' . $this->mode . '/' . $skin . '.php';
      
        echo '<link rel="stylesheet" href="', MC_PLUGIN_URL, '/skins/', $mode, '/', $skin, '.css">';

        if ($mode === 'list') {
            echo '<form id="mc-search-form" onsubmit="return mc_search(this);" autocomplete="off">';
        }
        echo '<div class="mc mc-', $mode, ' mc-', $mode, '-', $this->board['bo_table'], '">';
        if (is_file($file)) {
            $bo_table = $this->board['bo_table'];
            $board = $this;
            include $file;
        }
        echo '</div>';
        if ($mode === 'list') {
            echo '</form>';
        }
    }
}
