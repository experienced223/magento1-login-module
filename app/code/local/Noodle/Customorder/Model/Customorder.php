<?php

class Noodle_customorder_Model_customorder extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('customorder/customorder');
    }
}