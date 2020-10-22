<?php

class Noodle_customorder_Model_Mysql4_customorder_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('customorder/customorder');
    }
}