<?php
class Noodle_Checkoutlogin_IndexController extends Mage_Core_Controller_Front_Action
{
	protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/checkoutlogin?id=15 
    	 *  or
    	 * http://site.com/checkoutlogin/id/15 	
    	 */
    	/* 
		$checkoutlogin_id = $this->getRequest()->getParam('id');

  		if($checkoutlogin_id != null && $checkoutlogin_id != '')	{
			$checkoutlogin = Mage::getModel('checkoutlogin/checkoutlogin')->load($checkoutlogin_id)->getData();
		} else {
			$checkoutlogin = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($checkoutlogin == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$checkoutloginTable = $resource->getTableName('checkoutlogin');
			
			$select = $read->select()
			   ->from($checkoutloginTable,array('checkoutlogin_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$checkoutlogin = $read->fetchRow($select);
		}
		Mage::register('checkoutlogin', $checkoutlogin);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
	}
	public function loginAction()
	{
		$session = $this->_getSession();
		if ($this->_getSession()->isLoggedIn()) {
			
			echo "Login already!";
        }else{
			$email = $this->getRequest()->getParam('email');
			$password = $this->getRequest()->getParam('password');
			if ($session->login($email, $password)) {
				echo "Login success!";
			}else{
				echo "Login failed!";
			}
		}
		$this->_redirect('checkoutlogin/index/index');
	}
}