<?php

namespace mc;

class Category implements \Countable
{
    /**
     * 카테고리명
     * @var string
     */
    public $title = '';

    /**
     * depth
     * @var int
     */
    public $depth;

    /**
     * 기본키
     * @var int
     */
    public $mc;

    /**
     * @var string 경로
     */
    public $path;

    public $path_id;

    public $parent_id;

    /**
     * 기본키 제출
     * @return int
     */
    public function getPk()
    {
        return $this->mc;
    }

    /**
     * 카테고리 검색 제출.
     * @param array|int $where
     * @return Category|null
     */
    public static function get($where)
    {
        if (!is_array($where)) {
            $where = array('mc' => (int)$where);
        }
        $sql = "SELECT mc, parent_id, title, depth, path, path_id FROM " . TABLE_NAME . " WHERE ";
        $wheres = array();
        foreach ($where as $k => $v) {
            $wheres[] = "`$k`='$v'";
        }
        $sql .= join(' AND ', $wheres);
        $result = sql_query($sql, DEBUG);
        return sql_fetch_obj($result, __CLASS__);
    }

    /**
     * ROOT 제출.
     * @return Category
     */
    public static function root()
    {
        return self::get(array('mc' => 1));
    }

    /**
     * 카테고리 추가.
     * @param string $title
     * @return Category|null
     * @throws Exception
     */
    public function add($title)
    {
        $title = mc_category_name_input_filter($title);
        if (empty($title)) {// 0 포함
            throw new Exception('카테고리명에을 입력해주세요.');
        }
        if (preg_match(MC_CATEGORY_NAME_CHECK_REGEX, $title)) {
            throw new Exception('카테고리명에 사용할 수 없는 문자가 있습니다.');
        }

        if (!$this->mc || !sql_query("SELECT @rgt := rgt FROM " . TABLE_NAME . " WHERE mc=" . $this->mc)) {
            throw new Exception('카테고리가 존재하지 않습니다.');
        }
        $sql = "SELECT COUNT(0) FROM total FROM " . TABLE_NAME . " WHERE parent_id=" . $this->mc . " AND title='$title'";
        $row = sql_fetch($sql);
        if ($row['total'] > 0) {
            throw new Exception($title . ' 카테고리명이 중복됩니다.'); // 같은 깊이에 카테고리명 중복
        }

        $sql = "UPDATE " . TABLE_NAME . " SET 
            lft = CASE WHEN lft >= @rgt THEN lft+2 ELSE lft END,
            rgt = rgt + 2
            WHERE rgt >= @rgt";
        sql_query($sql, DEBUG);
        $depth = $this->depth + 1;
        $path = $this->path ? $this->path . MC_CATEGORY_DELIMITER . $title : $title;// . MC_CATEGORY_DELIMITER;
        $sql = "INSERT INTO " . TABLE_NAME . " (title, lft, rgt, depth, parent_id, path) 
            VALUES ('$title', @rgt, @rgt+1, $depth, $this->mc, '$path')";
        sql_query($sql);
        if ($id = sql_insert_id()) {
            $path_id = $this->path_id ? $this->path_id . MC_CATEGORY_DELIMITER . $id : $id;// . MC_CATEGORY_DELIMITER;
            $sql = "UPDATE " . TABLE_NAME . " SET path_id='$path_id' WHERE mc=$id";
            sql_query($sql);
            return self::get($id);
        }
        return null;
    }

    /**
     * 위로이동.
     */
    public function moveUp()
    {
        $sql = "SELECT * FROM " . TABLE_NAME . " WHERE mc=$this->mc";
        $current = sql_fetch($sql);
        $current_count = $current['rgt'] - $current['lft'] + 1;
        $sql = "SELECT * FROM " . TABLE_NAME . " AS A WHERE rgt=" . ($current['lft'] - 1);
        $prev = sql_fetch($sql);
        if (!empty($prev)) {
            $prev_count = $prev['rgt'] - $prev['lft'] + 1;
            $sql = "UPDATE " . TABLE_NAME . " SET lft=lft-$prev_count, rgt=rgt-$prev_count WHERE path_id LIKE '{$this->path_id}%'";
            sql_fetch($sql, DEBUG);
            $sql = "UPDATE " . TABLE_NAME . " SET lft=lft+$current_count, rgt=rgt+$current_count WHERE path_id LIKE '{$prev['path_id']}%'";
            sql_fetch($sql, DEBUG);
        }
    }

    public function moveDown()
    {
        $sql = "SELECT * FROM " . TABLE_NAME . " WHERE mc=$this->mc";
        $current = sql_fetch($sql);
        $current_count = $current['rgt'] - $current['lft'] + 1;
        $sql = "SELECT * FROM " . TABLE_NAME . " AS A WHERE lft=" . ($current['rgt'] + 1);
        $next = sql_fetch($sql);
        if (!empty($next)) {
            $next_count = $next['rgt'] - $next['lft'] + 1;
            $sql = "UPDATE " . TABLE_NAME . " SET lft=lft+$next_count, rgt=rgt+$next_count WHERE path_id LIKE '{$this->path_id}%'";
            sql_fetch($sql, DEBUG);
            $sql = "UPDATE " . TABLE_NAME . " SET lft=lft-$current_count, rgt=rgt-$current_count WHERE path_id LIKE '{$next['path_id']}%'";
            sql_fetch($sql, DEBUG);
            return true;
        }
        return false;
    }

    /**
     * 부모 카테고리 제출.
     * @return Category|null
     */
    public function parent()
    {
        return $this->parent_id ? self::get(array('mc' => $this->parent_id)) : null;
    }

    public function tree($callback, $tag = 'ul')
    {
        $rows = $this->getChild(-1);
        $depth = 0;
        foreach ($rows as $row) {
            if ($row->depth > $depth) {
                echo "<$tag>";
            } else {
                if ($row->depth < $depth) {
                    echo str_repeat("</$tag>", $depth - $row->depth);
                }
            }
            $callback($row);
            $depth = $row->depth;
        }
        if ($this->depth < $depth) {
            echo str_repeat("</$tag>", $depth - $this->depth);
        }
    }

    /**
     * 카테고리 멀티 추가.
     * @param string $context
     * @param string $indent_char
     * @return int 입력성공한 갯수.
     */
    public function addMulti($context, $indent_char = "\t")
    {
        $title = str_replace($indent_char, "    ", $context);
        $titles = array_map('rtrim', explode("\n", $title));

       // print_r($titles);

        $array = array();
        $array[-1] = $this;
        $i = 0;
        $matches = array();
        foreach ($titles as $title) {
            $depth = 0;
            if (preg_match('/^(\s+)/', $title, $matches)) {
                $depth = strlen($matches[1]) / 4;
            }
            $title = ltrim($title);
            if (!empty($title)) {
                $array[$depth] = $array[$depth - 1]->add($title);
                ++$i;
            }
        }
        return $i;
    }

    /**
     * 자식 카테고리 제출.
     * @param int $offset
     * @param string $class_name
     * @return Category[]
     */
    public function getChild($offset = 0, $class_name = __CLASS__)
    {
        setType($offset, 'int');
        $id = (int)$this->mc;
        if ($offset === 0) {
            $sql = "SELECT * FROM " . TABLE_NAME . " WHERE parent_id={$this->mc} ORDER BY lft ASC";
        } else {
            $sql = "SELECT B.mc, B.title, B.depth, B.path, B.path_id
                FROM " . TABLE_NAME . " AS A LEFT JOIN " . TABLE_NAME . " AS B";
            $sql .= " ON B.lft BETWEEN A.lft + 1 AND A.rgt";
            $sql .= " WHERE A.mc=$id";
            if ($offset > -1) {
                $depth = $this->depth + $offset + 1;
                $sql .= " AND B.depth <= $depth";
            }
            $sql .= " ORDER BY B.lft ASC";
        }
        $rows = array();
        $result = sql_query($sql, DEBUG);
        if ($result) {
            while ($row = sql_fetch_obj($result, $class_name)) {
                if ($row->mc) {
                    $rows[] = $row;
                }
            }
        }

        return $rows;
    }

    /**
     * 하위 카테고리인지 여부 체크.
     * @param $category
     * @return bool
     */
    public function contains(Category $category)
    {
        $sql = "SELECT COUNT(0) AS total
            FROM " . TABLE_NAME . " AS A LEFT JOIN " . TABLE_NAME . " AS B";
        $sql .= " ON B.lft BETWEEN A.lft AND A.rgt";
        $sql .= " WHERE A.mc=$this->mc AND B.mc=$category->mc";
        $row = sql_fetch($sql);
        return !empty($row['total']);
    }

    public function inContains($values, $type = 'mc', $as_root = false)
    {
        $where = array();
        $sql = "SELECT COUNT(0) AS total FROM " . TABLE_NAME . " WHERE ";
        foreach ($values as $v) {
            if ($type === 'path' && $as_root) {
                $v = ($type === 'path' ? $this->path : $this->path_id) . MC_CATEGORY_DELIMITER . $v;
            }
            $where[] = $type . "='$v'";
        }
        $sql .= join(' OR ', $where);
        $row = sql_fetch($sql);
        $total = (int)$row['total'];
        if ($total === count($values)) {
            return true;
        } else {
            return $total;
        }
    }

    /**
     * 카테고리 삭제.
     */
    public function remove()
    {
        sql_query("SELECT @lft := lft, @rgt := rgt, @width := rgt - lft + 1 FROM " . TABLE_NAME . " WHERE mc = " . $this->mc);
        sql_query("DELETE FROM " . TABLE_NAME . " WHERE lft BETWEEN @lft AND @rgt", DEBUG);
        sql_query("UPDATE " . TABLE_NAME . " SET rgt = rgt - @width WHERE rgt > @rgt", DEBUG);
        sql_query("UPDATE " . TABLE_NAME . " SET lft = lft - @width WHERE lft > @rgt", DEBUG);

    }

    /**
     * 부모카테고리 배열셋 제출.
     * @param int $from_depth
     * @return Category[]
     */
    public function getParents($from_depth = 0)
    {
        settype($from_depth, 'int');
        $sql = "SELECT B.* FROM " . TABLE_NAME . " AS A, " . TABLE_NAME . " AS B 
		    WHERE A.lft BETWEEN B.lft AND B.rgt AND A.mc='$this->mc' AND B.depth > $from_depth
		    ORDER BY A.lft ASC, B.lft ASC";
        $result = sql_query($sql, DEBUG);
        $rows = array();
        if ($result) {
            while ($row = sql_fetch_obj($result, __CLASS__)) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    /**
     * @param bool $from_root root 로부터의 depth 또는 현제로부터의 depth.
     * @return int
     */
    public function getMaxDepth($from_root = true)
    {
        $depth = 0;
        $sql = "SELECT MAX(B.depth) AS depth FROM " . TABLE_NAME . " AS A LEFT JOIN " . TABLE_NAME . " AS B ON B.lft BETWEEN A.lft AND A.rgt WHERE A.mc=" . $this->mc;
        $row = sql_fetch($sql);
        if ($row) {
            $depth = (int)$row['depth'];
            if (!$from_root) {
                $depth = $depth - $this->depth;
            }
        }
        return $depth;
    }


    public function __toString()
    {
        return $this->title;
    }


    public function getPath($offset_depth = 0)
    {
        if ($offset_depth) {
            return join('.', array_slice(explode(MC_CATEGORY_DELIMITER, $this->path), $offset_depth));
        }
        return $this->path;
    }


    public static function isValidValidDataType($name)
    {
        return in_array($name, array('mc', 'path'));
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return count($this->getChild(0));
    }
}