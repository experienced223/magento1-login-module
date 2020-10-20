<?php

class Noodle_Checkoutlogin_Model_Mysql4_Checkoutlogin extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the checkoutlogin_id refers to the key field in your database table.
        $this->_init('checkoutlogin/checkoutlogin', 'checkoutlogin_id');
    }
}