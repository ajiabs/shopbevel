<?php
 class Pixafy_Pixvote_Model_Challenges extends Mage_Core_Model_Abstract
 {
    public function _construct()
    {	
		parent::_construct();
        $this->_init('pixvote/challenges');
    }
 }
?>
