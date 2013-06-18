<?php
class Pixafy_RememberMe_Model_Observer extends Varien_Object
{
	public $_name = "remember_me";

	function loginCheck($observer)
	{
		$login = Mage::app()->getFrontController()->getRequest()->getParam("login");
		
		//REMOVE
		//$login['remember_me'] = 1;

		Mage::getModel('core/cookie')->delete($this->_name);

		if(!empty($login['remember_me']))
		{
			$value = $login['remember_me'];
			Mage::getModel('core/cookie')->set($this->_name, $value, true);
		}
	}
	
	function removeRemember()
	{
		Mage::getModel('core/cookie')->delete($this->_name);
	}
}