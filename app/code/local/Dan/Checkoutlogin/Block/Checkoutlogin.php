<?php
class Dan_Checkoutlogin_Block_Checkoutlogin extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getCheckoutlogin()     
     { 
        if (!$this->hasData('checkoutlogin')) {
            $this->setData('checkoutlogin', Mage::registry('checkoutlogin'));
        }
        return $this->getData('checkoutlogin');
        
    }
    public function getActionOfForm()
    {
        return $this->getUrl('checkoutlogin/index/login');
    }
    public function getData_tbl()
    {
        $customerData = Mage::getSingleton('customer/session')->getCustomer();
	    $customer_id = $customerData->getId();
		$collection = Mage::getModel('sales/quote_address')->getCollection()->addFilter('customer_id', $customer_id);
		$data = $collection->getData();
        return $data[count($data) - 1];
    }
}