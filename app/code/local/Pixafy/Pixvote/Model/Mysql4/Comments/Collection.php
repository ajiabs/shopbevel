<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Pixafy_Pixvote_Model_Mysql4_Comments_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract 
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('pixvote/comments');
    }
}
?>
