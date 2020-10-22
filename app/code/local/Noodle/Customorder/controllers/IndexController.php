<?php
class Noodle_customorder_IndexController extends Mage_Core_Controller_Front_Action
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
    	 * http://site.com/customorder?id=15 
    	 *  or
    	 * http://site.com/customorder/id/15 	
    	 */
    	/* 
		$customorder_id = $this->getRequest()->getParam('id');

  		if($customorder_id != null && $customorder_id != '')	{
			$customorder = Mage::getModel('customorder/customorder')->load($customorder_id)->getData();
		} else {
			$customorder = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($customorder == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$customorderTable = $resource->getTableName('customorder');
			
			$select = $read->select()
			   ->from($customorderTable,array('customorder_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$customorder = $read->fetchRow($select);
		}
		Mage::register('customorder', $customorder);
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
		$this->_redirect('customorder/index/index');
	}

	public function setorderAction()
	{
		$email = $this->getRequest()->getParam('email');
		$firstname = $this->getRequest()->getParam('firstname');
		$lastname = $this->getRequest()->getParam('lastname');
		$product_id = $this->getRequest()->getParam('product_id');
		$product_qty = $this->getRequest()->getParam('product_qty');
		//echo $email."----------".$firstname."------------".$lastname;
		
		$store = Mage::app()->getStore();
		$website = Mage::app()->getWebsite();

		//Create sales quote object
		$quote = Mage::getModel('sales/quote')->setStoreId($store->getStoreId());
		//Customer information
		$customerEmail = $email;
		$customerFirstname = $firstname; 
		$customerLastname = $lastname;

		$billingAddress = array(
			'customer_address_id' => '',
			'prefix' => '',
			'firstname' => $customerFirstname,
			'middlename' => '',
			'lastname' => $customerLastname,
			'suffix' => '',
			'company' => '', 
			'street' => array(
				'0' => 'Your Street Address 1', // required
				'1' => 'Your Street Address 1' // optional
			),
			'city' => 'Your City',
			'country_id' => 'US', // country code
			'region' => 'Alaska',
			'region_id' => '2',
			'postcode' => '45263',
			'telephone' => '999-888-0000',
			'fax' => '',
			'save_in_address_book' => 1
		);

		$shippingAddress = array(
			'customer_address_id' => '',
			'prefix' => '',
			'firstname' => $customerFirstname,
			'middlename' => '',
			'lastname' => $customerLastname,
			'suffix' => '',
			'company' => '', 
			'street' => array(
				'0' => 'Your Street Address 1', // required
				'1' => 'Your Street Address 1' // optional
			),
			'city' => 'Your City',
			'country_id' => 'US', // country code
			'region' => 'Alaska',
			'region_id' => '2',
			'postcode' => '45263',
			'telephone' => '999-888-0000',
			'fax' => '',
			'save_in_address_book' => 1
		);

		//Check whether the customer already registered or not
		$customer = Mage::getModel('customer/customer')->setWebsiteId($website->getId())->loadByEmail($customerEmail);

		if (!$customer->getId()) {

			//Create the new customer account if not registered
			$customer = Mage::getModel('customer/customer'); 
			$customer->setWebsiteId($website->getId())
					->setStore($store)
					->setFirstname($customerFirstname)
					->setLastname($customerLastname)
					->setEmail($customerEmail);

			try {
				$password = $customer->generatePassword(); 
				$customer->setPassword($password);

				//Set the customer as confirmed
				$customer->setForceConfirmed(true);
				$customer->save();

				$customer->setConfirmation(null);
				$customer->save();

				//Set customer address
				$customerId = $customer->getId(); 
				$customAddress = Mage::getModel('customer/address'); 
				$customAddress->setData($billingAddress)
							->setCustomerId($customerId)
							->setIsDefaultBilling('1')
							->setIsDefaultShipping('1')
							->setSaveInAddressBook('1');

				//Save customer address
				$customAddress->save();

				//Send new account email to customer
				$storeId = $customer->getSendemailStoreId();
				$customer->sendNewAccountEmail('registered', '', $storeId);

				//Set password remainder email if the password is auto generated by magento
				$customer->sendPasswordReminderEmail();

			} catch (Exception $e) {
				Mage::logException($e);
			} 
		}

		//Assign the customer to quote
		$quote->assignCustomer($customer);

		//Set currency for the quote
		$quote->setCurrency(Mage::app()->getStore()->getBaseCurrencyCode());

		$productIds = array($product_id => $product_qty); //array('product_id' => 'qty')
		
		//Add products to quote
		foreach($productIds as $productId => $qty) {
			$product = Mage::getModel('catalog/product')->load($productId);
			$quote->addProduct($product, $qty);
		}

		//Add billing address to quote
		$billingAddressData = $quote->getBillingAddress()->addData($billingAddress);

		//Add shipping address to quote
		$shippingAddressData = $quote->getShippingAddress()->addData($shippingAddress);

		//Collect shipping rates on quote
		$shippingAddressData->setCollectShippingRates(true)->collectShippingRates();

		//Set shipping method and payment method on the quote
		$shippingAddressData->setShippingMethod('flatrate_flatrate')->setPaymentMethod('checkmo'); //Shipping is flatrate for this example

		//Set payment method for the quote
		$quote->getPayment()->importData(array('method' => 'checkmo')); //Payment is check and money order for this example

		try {
			//Collect totals & save quote
			$quote->collectTotals()->save();

			//Create order from quote
			$service = Mage::getModel('sales/service_quote', $quote);
			$service->submitAll();
			/*
			$increment_id = $service->getOrder()->getRealOrderId();
			
			echo 'Order Id: ' .$increment_id. ' has been successfully created.';
			*/
			
			$_order = $service->getOrder();
			print_r(json_encode($_order->getData()));

		} catch (Exception $e) {
			Mage::logException($e);
		}
		
	}
}