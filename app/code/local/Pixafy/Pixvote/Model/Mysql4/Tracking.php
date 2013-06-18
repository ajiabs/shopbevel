<?php
 class Pixafy_Pixvote_Model_Mysql4_Tracking extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Initialize resource
     */
    public function _construct()
    {
        $this->_init('pixvote/tracking', 'id');
    }
}
?>
