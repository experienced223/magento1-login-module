<?php

class Noodle_customorder_Model_Mysql4_customorder extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the customorder_id refers to the key field in your database table.
        $this->_init('customorder/customorder', 'customorder_id');
    }
}