<?php
 class Pixafy_Pixvote_Model_Follow extends Mage_Core_Model_Abstract
 {
    public function _construct()
    {	
		parent::_construct();
        $this->_init('pixvote/follow');
    }
 }
?>
