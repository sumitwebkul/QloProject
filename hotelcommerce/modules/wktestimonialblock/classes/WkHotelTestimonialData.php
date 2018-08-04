<?php
class WkHotelTestimonialData extends ObjectModel
{
    public $name;
    public $designation;
    public $testimonial_content;
    public $testimonial_image;
    public $active;
    public $position;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_testimonials_block_data',
        'primary' => 'id_testimonial_block',
        'fields' => array(
            'name' => array('type' => self::TYPE_STRING),
            'designation' => array('type' => self::TYPE_STRING),
            'testimonial_content' => array('type' => self::TYPE_STRING),
            'testimonial_image' => array('type' => self::TYPE_STRING),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
    ));

    public function getTestimonialData($active = 2)
    {
        $sql = 'SELECT `name`, `designation`, `testimonial_content`, `testimonial_image`, `active`, `position`
                FROM `'._DB_PREFIX_.'htl_testimonials_block_data` WHERE 1';
        if ($active != 2) {
            $sql .= ' AND `active` = '.(int) $active;
        }
        $sql .= ' ORDER BY `position`';

        $result = Db::getInstance()->executeS($sql);
        return $result;
    }

    public function delete()
    {
        // delete image of the block
        $imgPath = _PS_MODULE_DIR_.'wktestimonialblock/views/img/hotels_testimonials_img/'.$this->id.'.jpg';
        if (file_exists($imgPath)) {
            unlink($imgPath);
        }
        $return = parent::delete();
        /* Reinitializing position */
        $this->cleanPositions();
        return $return;
    }

    public static function getHigherPosition()
    {
        $position = DB::getInstance()->getValue(
            'SELECT MAX(`position`) FROM `'._DB_PREFIX_.'htl_testimonials_block_data`'
        );
        $result = (is_numeric($position)) ? $position : 0;
        return $result + 1;
    }

    public function updatePosition($way, $position)
    {
        if (!$result = Db::getInstance()->executeS(
            'SELECT htb.`id_testimonial_block`, htb.`position` FROM `'._DB_PREFIX_.'htl_testimonials_block_data` htb
            WHERE htb.`id_testimonial_block` = '.(int) $this->id.' ORDER BY `position` ASC'
        )
        ) {
            return false;
        }

        $movedBlock = false;
        foreach ($result as $block) {
            if ((int)$block['id_testimonial_block'] == (int)$this->id) {
                $movedBlock = $block;
            }
        }

        if ($movedBlock === false) {
            return false;
        }
        return (Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'htl_testimonials_block_data` SET `position`= `position` '.($way ? '- 1' : '+ 1').
            ' WHERE `position`'.($way ? '> '.
            (int)$movedBlock['position'].' AND `position` <= '.(int)$position : '< '
            .(int)$movedBlock['position'].' AND `position` >= '.(int)$position)
        ) && Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'htl_testimonials_block_data`
            SET `position` = '.(int)$position.'
            WHERE `id_testimonial_block`='.(int)$movedBlock['id_testimonial_block']
        ));
    }

    /**
     * Reorder blocks position
     * Call it after deleting a blocks.
     * @return bool $return
     */
    public static function cleanPositions()
    {
        Db::getInstance()->execute('SET @i = -1', false);
        $sql = 'UPDATE `'._DB_PREFIX_.'htl_testimonials_block_data` SET `position` = @i:=@i+1 ORDER BY `position` ASC';
        return (bool) Db::getInstance()->execute($sql);
    }
}
