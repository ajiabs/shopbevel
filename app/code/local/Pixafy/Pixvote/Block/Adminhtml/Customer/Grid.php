<?php
class Pixafy_Pixvote_Block_Adminhtml_Customer_Grid extends Mage_Adminhtml_Block_Customer_Grid
{
	protected function _prepareMassaction()
    {
		 parent::_prepareMassaction();
        $this->getMassactionBlock()->addItem('export_stats', array(
				 'label'    => Mage::helper('customer')->__('Export shopbevel stats'),
				 'url'      => Mage::helper('adminhtml')->getUrl('*/designers/exportBevelStats')
		));
        return $this;
    }
}