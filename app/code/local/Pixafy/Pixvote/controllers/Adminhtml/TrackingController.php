<?php
	class Pixafy_Pixvote_Adminhtml_TrackingController extends Mage_Adminhtml_Controller_Action
	{
		function indexAction()
		{
			$this->loadLayout();
			$this->_setActiveMenu('pixvotemenutracking/view_tracking');
			$this->renderLayout();
		}
		
		function gridAction()
		{
			$this->loadLayout();
			$this->getResponse()->setBody(
				$this->getLayout()->createBlock('pixvote/adminhtml_tracking_grid')->toHtml()
			);
		}
		
		/**
		 * Export grid to CSV format
		 */
		public function exportCsvAction()
		{
			$pixvoteHelper = Mage::helper("pixvote");
			$now = str_replace(" ","_", $pixvoteHelper->now());
			$fileName   = 'tracking_'.$now.'.csv';
			$content    = $this->getLayout()->createBlock('pixvote/adminhtml_tracking_grid')->getCsvFile();
			$this->_prepareDownloadResponse($fileName, $content);
		}
	}
?>