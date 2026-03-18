<?php


namespace mc;


class CategorySelectbox
{
    /**
     * @var Category
     */
    protected $category;

    protected $value;
    public $data_column = 'mc';

    public $input_name = '';
    public $placeholder = '선택해주세요';

    protected $root;
    public function __construct(Category $category, $value = null, $root = null)
    {
        $this->category = $category;
        $this->value = $value;
        $this->root = $root? Category::get($root):Category::root();
    }

    public function render()
    {
        $category = $this->category;
        $value = $this->value;
        $max_depth = $category->getMaxDepth();
        $items = array($category->getChild());
        $data_column = $this->data_column;
        ?>
        <div class="mc-group" data-name="<?php echo $this->input_name; ?>">
            <input type="hidden" name="<?php echo $this->input_name; ?>" value="<?php echo $value; ?>">
            <?php for ($i = 0; $i < $max_depth; $i++):

                ?>
                <select data-name="<?php echo $this->input_name; ?>" onchange="mc_handle(this)">
                    <?php if ($items[$i]) {
                        printf('<option value="">%s</option>', $this->placeholder);
                        foreach ($items[$i] as $item): $selected = $value === $item->$data_column ? ' selected' : ''; ?>
                            <?php if ($selected) {
                                $items[$i + 1] = $item->getChild();
                            } ?>
                            <option value="<?php echo $item->$data_column; ?>" <?php echo $selected; ?>><?php echo $item->title; ?></option>
                        <?php endforeach;
                    } ?>
                </select>
            <?php

            endfor; ?>
        </div>
        <?php
    }



}