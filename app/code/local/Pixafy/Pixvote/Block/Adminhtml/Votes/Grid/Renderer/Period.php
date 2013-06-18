<?php
	class Pixafy_Pixvote_Block_Adminhtml_Votes_Grid_Renderer_Period extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
	{
		public function render(Varien_Object $row)
		{
			$value =  $row->getData($this->getColumn()->getIndex());
			$now = Mage::getModel('core/date')->timestamp(time());
			return '';
			//return $value;
		}
	}
?>