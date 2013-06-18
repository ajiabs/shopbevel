<?php
	class Pixafy_Pixvote_Block_Adminhtml_Challenges_Grid_Renderer_Time extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
	{
		public function render(Varien_Object $row)
		{
			$value =  $row->getData($this->getColumn()->getIndex());
			$now = Mage::getModel('core/date')->timestamp(time());
			$time = strtotime($value, $now);
			return date(Mage::helper("pixvote")->TIMESTAMP_GRID_FORMAT, $time);
			//return $value;
		}
	}
?>