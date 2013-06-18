<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 class Pixafy_Pixvote_Model_Tracking extends Mage_Core_Model_Abstract
 {
    public function _construct()
    {	
		parent::_construct();
        $this->_init('pixvote/tracking');
    }
 }
?>
