<?php

class Noodle_Checkoutlogin_Model_Checkoutlogin extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('checkoutlogin/checkoutlogin');
    }
}