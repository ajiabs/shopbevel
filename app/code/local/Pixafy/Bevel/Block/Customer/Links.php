<?php
/**
* Magento Enterprise Edition
*
* NOTICE OF LICENSE
*
* This source file is subject to the Magento Enterprise Edition License
* that is bundled with this package in the file LICENSE_EE.txt.
* It is also available through the world-wide-web at this URL:
* http://www.magentocommerce.com/license/enterprise-edition
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@magentocommerce.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade Magento to newer
* versions in the future. If you wish to customize Magento for your
* needs please refer to http://www.magentocommerce.com for more information.
*
* @category    Mage
* @package     Mage_Wishlist
* @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
* @license     http://www.magentocommerce.com/license/enterprise-edition
*/


class Pixafy_Bevel_Block_Customer_Links extends Mage_Page_Block_Template_Links_Block
{
    /**
     * Position in link list
     * @var int
     */
    protected $_position = 2;

    /**
     * Set link title, label and url
     */
    public function __construct()
    {
        parent::__construct();
        $this->initLinkProperties();
    }

    /**
     * Define label, title and url for link
     */
    public function initLinkProperties()
    {
        $customer          =             Mage::getSingleton('customer/session')->getCustomer();
        $this->_label      =             $customer->getFirstname();
        $this->_title      =             $this->_label;
        $this->_url        =             Mage::helper('customer')->getAccountUrl();
    }
}