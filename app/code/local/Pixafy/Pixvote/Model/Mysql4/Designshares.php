<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 class Pixafy_Pixvote_Model_Mysql4_Designshares extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Initialize resource
     */
    public function _construct()
    {
        $this->_init('pixvote/designshares', 'id');
    }
}
?>
