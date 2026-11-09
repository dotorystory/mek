<?php

namespace mc;

class Column
{
    const DATASET_CATEGORY = 'category';
    const DATASET_STRING_SPLIT = 'string_split';
    const DATASET_DATE = 'date';
    const DATASET_NUMBER = 'number';
    const DATASET_TEXT = 'text';
    const DATASET_URL = 'url';
    const DATASET_TEL = 'tel';
    public $bo_table;
    /**
     * 컬럼명
     * @var string
     */
    public $name;
    /**
     * 입력값 또는 검색값 - array 는 검색범위
     * @var string|array
     */
    public $value;
    /**
     * @var mixed
     */
    public $data;
    public $category_depth;
    /**
     * 카테고리 컬럼 mc || path
     * @var string
     */
    public $category_column = 'path';
    public $dataset;
    public $data_type;
    public $data_depth;
    public $multiple;
    public $label;
    public $column;
    public $type;
    /**
     * input 이 textbox 인경우
     * @var string
     */
    public $placeholder = '';
    /**
     * 검색양식
     * @var
     */
    public $list_type;
    public $op = 'and';
    public $title;
    public $required;
    public $searchable;
    public $caption;
    public $control;
    public $search_skin;
    public $write_type = 'select';
    protected $mode;

    public function getDataset()
    {
    }

    public static function factory($attrs)
    {
        $column = new static;
        foreach ($attrs as $k => $v) {
            $column->$k = $v;
        }
        return $column;
    }

    public function toArray()
    {
        $arr = get_object_vars($this);
        return $arr;
    }

    public static function getDataTypes()
    {
        return array(
            self::DATASET_CATEGORY => '계층형카테고리',
            self::DATASET_STRING_SPLIT => '문자분할(|)',
            self::DATASET_DATE => '날짜',
            self::DATASET_TEXT => '문자',
            self::DATASET_NUMBER => '숫자',
            self::DATASET_TEL => '전화번호',
            self::DATASET_URL => 'URL'
        );
    }

    public static function getDataForms()
    {
        $forms = array('select', 'radio', 'checkbox', 'date', 'number');
        $arr = array();
        $arr[self::DATASET_CATEGORY] = array(
            'write' => array(
                'select',
                'radio'
            ),
            'list' => array()
        );
        return $arr;
    }

    /**
     * 검색양식스킨목록 제출
     * @param string $list_type select, checkbox, radio
     * @return array
     */
    public static function getSearchControlSkins($list_type)
    {
        $dir = MC_PLUGIN_PATH . '/list_skin/control/' . $list_type . '/';
        $arr = array();
        foreach (glob($dir . '*.php') as $file) {
            $arr[] = substr(basename($file), 0, -4);
        }
        return $arr;
    }

    public function getCaption()
    {
        return '선택해 주세요';
    }

    /**
     * 검색가능한 컬럼형식인지 여부 제출
     * @return bool
     */
    public function supportSearchable()
    {
        if (
            $this->data_type === Column::DATASET_CATEGORY
            || $this->data_type === Column::DATASET_STRING_SPLIT
            || $this->data_type === Column::DATASET_DATE
            || $this->data_type === Column::DATASET_NUMBER
        ) {
            return true;
        }
        return false;
    }

    public static function isInputTextType($type)
    {
        if (
            $type === Column::DATASET_TEXT
            || $type === Column::DATASET_NUMBER
            || $type === Column::DATASET_TEL
            || $type === Column::DATASET_URL) {
            return true;
        }
        return false;
    }

    public function render()
    {
        $mode = $this->mode;
        $form_type = $this->{$mode . '_type'};
        if ($mode === 'view') {
            echo '<div class="mc-controls">';
            $value = $this->value;
            switch ($this->data_type) {
                case self::DATASET_DATE:
                    if ($value === '1970-01-01') {
                        $value = '';
                    }
                    break;
                case self::DATASET_NUMBER:
                    $value = number_format((int)$value);
                    break;
                default:
                    if ($this->write_type === 'checkbox' || $this->multiple) {
                        $values = array();
                        foreach (explode(',', $value) as $v) {
                            $values[] = '<span class="mc-view-multipart-item">' . $this->formatItemLabel($v) . '</span>';
                        }
                        $value = join('', $values);
                    }
            }
            echo $value;
            echo '</div>';
            return;
        }
        $required = $this->mode === 'write' && $this->required ? ' required' : '';
        $attrs = '';
        $_attrs = array(
            'data-name' => $this->name,
            'data-type' => $this->data_type,
            'data-root' => $this->data,
            'data-multiple' => $this->multiple,
            'data-mode' => $mode,
            'data-input' => $form_type
        );
        if ($required) {
            $_attrs['data-required'] = 1;
        }
        foreach ($_attrs as $k => $v) {
            $attrs .= $k . '="' . $v . '" ';
        }
        echo '<div class="mc-controls" ' . $attrs . '>';
        if ($this->data_type === self::DATASET_CATEGORY) {
            $data_opt = json_encode(
                array(
                    'column' => $this->category_column
                )
            );
            echo '<script type="text/json">' . $data_opt . '</script>';
        }
        $render = 'render' . ucfirst($form_type);
        if ($form_type === 'select') {
            $render .= $this->multiple ? 'Multiple' : 'One';
            if ($this->data_type === self::DATASET_CATEGORY) {
                $render = 'renderMultiSelect';
            }
        }
        if (method_exists($this, $render)) {
            $this->$render();
        } else {
            echo $render;
        }
        echo '</div>';
    }

    protected function getSeparator()
    {
        return $this->mode === 'list' ? '|' : ',';
    }

    public function getItems()
    {
        $items = array();
        switch ($this->data_type) {
            case self::DATASET_STRING_SPLIT;
                $items = explode('|', $this->data);
                break;
            case self::DATASET_CATEGORY;
                $root = Category::get($this->data);
                $items = $root->getChild();
                break;
        }
        return $items;
    }

    /**
     * 체크박스 랜더링
     * @param string[] $items
     */
    protected function renderCheckbox($items = null)
    {
        $items = $this->getItems();
        $separator = $this->getSeparator();
        $values = explode($separator, $this->value);
        printf(
            '<input type="hidden" name="%s" value="%s" title="%s" %s>',
            $this->name,
            join($separator, $values),
            $this->title,
            $this->mode === 'write' && $this->required ? ' class="required"' : ''
        );
        foreach ($items as $item) {
            $checked = in_array($item, $values) ? ' checked' : '';
            printf(
                '<label class="mc-control-radio"><input type="checkbox" data-name="%s" value="%s"%s onchange="mc.handle(this)">%s</label>',
                $this->name,
                $item,
                $checked,
                $this->formatItemLabel($item)
            );
        }
    }

    /**
     * @todo
     */
    protected function renderRange()
    {
        $values = $this->value ? explode('~', $this->value) : array();
        $values = array_map(
            function ($v) {
                return (int)$v;
            },
            $values
        );
        if (empty($values[0]) && $values[0] === 0) {
            $values[0] = 0;
        }
        if (empty($values[1]) && $values[1] > $values[0]) {
            $values[1] = 0;
        }
        printf('<input type="hidden" name="%s" value="%s">', $this->name, $this->value ? join('~', $values) : '');
        printf('<input type="number" data-name="%s" value="%s" onblur="mc_betweenHandle(this)">', $this->name, $values[0]);
        printf('~<input type="number" data-name="%s" value="%s" onblur="mc_betweenHandle(this)">', $this->name, $values[1]);
        return;
        $id = $this->name . '-slider';
        printf('<input type="hidden" name="%s" value="%s">', $this->name, $this->value ? join('~', $values) : '');
        printf('<div id="%s" class="mc-range-slider"></div>', $id);
        echo "<script>(function(){
    var input = $('input[name=\"{$this->name}\"]');
    var slider = document.getElementById('{$id}');
noUiSlider.create(slider, {
    start: [{$values[0]}, {$values[1]}],
    connect: true,
    step:10000,
    pipe: true,
    range: {
        'min': 0,
        'max': 400000
    },
    format: {
        to:function(v){
            return parseInt(v);
        },
        from:function(v){
            return parseInt(v);
        }
    }
});
slider.noUiSlider.updateOptions({
        tooltips: true,
        pips: {
            mode: 'count',
            values: 2,
            density: 4
        }
    });

slider.noUiSlider.on('end', function (values) {
    input.val(values.join('~'));
    input.trigger('change');
});
})();
</script>";
    }

    protected function renderRadio($items = null)
    {
        $items = $this->getItems();
        $value = (string)$this->value;
        foreach ($items as $i => $item) {
            $label = $item;
            if ($item instanceof Category) {
                $label = $item->title;
            }
            $checked = $label === $value ? ' checked' : '';
            $required = $i === 0 && $this->mode === 'write' && $this->required ? ' required' : '';
            printf(
                '<label class="mc-control-radio"><input %s type="radio" name="%s" value="%s"%s>%s</label>',
                $required,
                $this->name,
                $label,
                $checked,
                $this->formatItemLabel($label)
            );
        }
    }

    protected function renderBetween()
    {
        $values = explode('~', $this->value);
        printf('<input type="hidden" name="%s" value="%s">', $this->name, join('~', $values));
        printf('<input type="date" value="%s" data-name="%s" onchange="mc_betweenHandle(this)"/> ~ ', $values[0], $this->name);
        printf('<input type="date" value="%s" data-name="%s" onchange="mc_betweenHandle(this)"/>', $values[1], $this->name);
    }

    /**
     * @param string[] $items
     */
    protected function renderSelectOne($items = null)
    {
        $items = $this->getItems();
        printf('<select name="%s" autocomplete="off">', $this->name);
        echo '<option value="">' . $this->getCaption() . '</option>';
        foreach ($items as $item) {
            $selected = $item === $this->value ? ' selected' : '';
            printf('<option value="%s"%s>%s</option>', $item, $selected, $this->formatItemLabel($item));
        }
        printf('</select>');
    }

    protected function formatItemLabel($item)
    {
        switch ($this->data_type) {
            case self::DATASET_STRING_SPLIT:
                if ((string)(int)$item === $item) {
                    $item = number_format($item);
                }
                break;
            case self::DATASET_NUMBER:
                $item = number_format($item);
        }
        return $item;
    }

    protected function renderSelectMultiple()
    {
        $separator = $this->getSeparator();
        $values = explode($separator, $this->value);
        $items = $this->getItems();
        $total = count($items);
        $size = $total > 6 ? 6 : $total;
        printf('<input type="hidden" name="%s" value="%s">', $this->name, $this->value);
        printf('<select data-name="%s" autocomplete="off" multiple size="%s" onchange="mc.handle(this);">', $this->name, $size);
        foreach ($items as $item) {
            $selected = in_array($item, $values) ? ' selected' : '';
            printf('<option value="%s"%s>%s</option>', $item, $selected, $this->formatItemLabel($item));
        }
        printf('</select>');
    }

    protected function renderInput()
    {
        $name = $this->name;
        $value = (string)$this->value;
        $type = $this->data_type;
        $pattern = '';
        $placeholder = (string)$this->placeholeder;
        $require_cls = '';
        switch ($this->data_type) {
            case self::DATASET_DATE:
                $type = 'date';
                break;
            case self::DATASET_URL:
                $type = 'url';
                break;
            case self::DATASET_NUMBER:
                $require_cls = 'numeric';
                break;
            case self::DATASET_TEL:
                $require_cls = 'telnum';
                $pattern = '([0-9]{2,4}-)?[0-9]{3,4}-[0-9]{4}';
                if (!$placeholder) {
                    $placeholder = '12-1234-5678';
                }
        }
        printf(
            '<input type="%s" name="%s" value="%s" data-name="%s" %s %s %s>',
            $type,
            $name,
            $value,
            $name,
            $pattern ? ' pattern="' . $pattern . '"' : '',
            $placeholder ? ' placeholder="' . $placeholder . '"' : '',
            //$this->mode === 'write' && $this->required ? ' class="required '.$require_cls.'"' : ''
            $this->mode === 'write' && $this->required ? ' required' : ''
        );
    }

    protected function renderMultiSelect()
    {
        $root = Category::get($this->data);
        if (!$root) {
            //print_r($this);
            echo '카테고리 데이타가 존재하지 않습니다 관리자 설정을 확인하세요';
            return;
        }
        $root_length = strlen($root->path) + 1;
        $max_depth = $root->getMaxDepth() - $root->depth;
        if ($this->data_depth && $this->data_depth < $max_depth) {
            $max_depth = $this->data_depth;
        }
        $values = explode('.', $this->value);
        $dataset = $root;
        $next_exists = false;
        $mode = $this->mode;
        if ($mode === 'write' && $this->multiple) {
            echo '<div class="mc-control-multiple">';
            if ($this->value) {
                foreach (explode(',', $this->value) as $v) {
                    echo '<span  onclick="mc.multipleDelete(this)" data-value="', $v, '">', $v, '<em>✗</em></span>';
                }
            }
            echo '</div>';
        }
        $value_count = count(explode(MC_CATEGORY_DELIMITER, $this->value));
        printf(
            '<input type="hidden" name="%s" autocomplete="off" value="%s" title="%s" %s>',
            $this->name,
            (string)$this->value,
            $this->title,
            $this->mode === 'write' && $this->required ? ' required' : ''
        );
        for ($i = 0; $i < $max_depth; $i++) {
            if ($dataset) {
                $rows = $dataset->getChild();
            } else {
                $rows = array();
            }
            printf('<select onchange="mc.handle(this)" autocomplete="off" data-name="%s" %s>', $this->name, !$rows && $value_count <= $i ? ' disabled' : '');
            printf('<option value="">%s</option>', $this->getCaption());
            foreach ($rows as $row) {
                $value = substr($row->path, $root_length);
                $selected = '';
                if ((!$this->multiple || $mode === 'list') && $values && join('.', array_slice($values, 0, $i + 1)) === $value) {
                    $selected = ' selected';
                    $next_exists = true;
                    $dataset = $row;
                }
                printf('<option value="%s"%s>%s</option>', $value, $selected, $row->title);
            }
            echo '</select>';
            if (!$next_exists) {
                $dataset = null;
            }
            if ($this->category_depth && $this->category_depth - 1 <= $i) {
                break;
            }
        }
        if ($mode === 'write' && $this->multiple) {
            printf('<button type="button" onclick="mc.handleMultiple(this)">추가</button>');
        }
    }
}