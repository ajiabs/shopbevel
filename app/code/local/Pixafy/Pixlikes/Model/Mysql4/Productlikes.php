<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 class Pixafy_Pixlikes_Model_Mysql4_Productlikes extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Initialize resource
     */
    public function _construct()
    {
        $this->_init('pixlikes/productlikes', 'id');
    }
}
?>
