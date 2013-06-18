<?php
 class Pixafy_Pixvote_Model_Rejections extends Mage_Core_Model_Abstract
 {
    public function _construct()
    {	
		parent::_construct();
        $this->_init('pixvote/rejections');
    }
 }
?>
