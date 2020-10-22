<?php
class Noodle_customorder_Block_customorder extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getcustomorder()     
     { 
        if (!$this->hasData('customorder')) {
            $this->setData('customorder', Mage::registry('customorder'));
        }
        return $this->getData('customorder');
        
    }
    public function getActionOfForm()
    {
        return $this->getUrl('customorder/index/login');
    }
    public function getData_tbl()
    {
        $customerData = Mage::getSingleton('customer/session')->getCustomer();
	    $customer_id = $customerData->getId();
		$collection = Mage::getModel('sales/quote_address')->getCollection()->addFilter('customer_id', $customer_id);
		$data = $collection->getData();
        //var_dump($data[count($data) - 1]);
        return $data[count($data) - 1];
    }
}