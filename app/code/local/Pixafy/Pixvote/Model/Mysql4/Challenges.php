<?php
class Pixafy_Pixvote_Model_Mysql4_Challenges extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Initialize resource
     */
    public function _construct()
    {
        $this->_init('pixvote/challenges', 'id');
    }
}
?>
