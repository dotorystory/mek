<?php


namespace mc;


class CategorySelectboxAdmin extends CategorySelectbox
{
    public $placeholder = '선택';

    public function renderAsButton(){
        $category = $this->category;
        $data_column = $this->data_column;
        $value = $this->value;
        $selected = Category::get($value);
        ?>
        <div class="mc-group">
            <button type="button"
                    onclick="mc_handle_btn(this)"
                    data-input-name="<?php echo $this->input_name;?>"
                    data-root="<?php echo $this->root->mc;?>"
                    data-name="<?php echo $data_column;?>"
                    data-value="<?php echo $value;?>"><?php echo $selected->title;?></button>
        </div>
        <?php
    }

    public function render()
    {
        $category = $this->category;
        $value = $this->value;
        $max_depth = $category->getMaxDepth();
        $items = array($category->getChild());

        $data_column = $this->data_column === 'mc'? 'mc':'path';

        ?>
        <div class="mc-group" data-name="<?php echo $this->input_name; ?>" data-type="<?php echo Column::DATASET_CATEGORY;?>" data-root="<?php echo $category->mc;?>" data-category-column="<?php echo $data_column;?>">
            <input type="hidden" name="<?php echo $this->input_name; ?>" value="<?php echo $value; ?>">
            <?php for ($i = 0; $i < $max_depth; $i++):
                $disabled = $items[$i]? '':' disabled';
                ?>
                <select data-name="<?php echo $this->input_name; ?>" onchange="mc_handle(this)"<?php echo $disabled;?>>
                    <?php printf('<option value="">%s</option>', $this->placeholder);?>
                    <?php if ($items[$i]) {

                        foreach ($items[$i] as $item): $selected = $value === $item->$data_column ? ' selected' : ''; ?>
                            <?php if ($selected) {
                                $items[$i + 1] = $item->getChild();
                            }
                            $total = count($item);
                            $disabled = $total ===0? ' disabled':'';
                            ?>
                            <option value="<?php echo $item->$data_column; ?>" <?php echo $selected; ?><?php echo $disabled;?>><?php echo $item->title; ?>(<?php echo $total;?>)</option>
                        <?php endforeach;
                    } ?>
                </select>
            <?php

            endfor; ?>
        </div>
        <?php
    }
}