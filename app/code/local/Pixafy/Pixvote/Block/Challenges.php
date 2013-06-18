<?php
	class Pixafy_Pixvote_Block_Challenges extends Mage_Core_Block_Template
	{
		public $IS_VISIBLE = 1;
		function __construct()
		{
			
		}
		
		//Load information for the specified challenge
		function loadChallenge()
		{
			$challenge = Mage::registry($this->helper("pixvote")->CURRENT_CHALLENGE_KEY);
			$designLimit = 8;
			$helper = $this->helper("pixvote");
			$this->setChallenge($challenge);
			$now = Mage::getModel('core/date')->timestamp(time());
			$time = strtotime(date("Y-m-d H:i:s",$now));
			$voteEndTime = strtotime($challenge->getVoteEndTime());
			$submissionHeader = ($voteEndTime - $time > 0) ? 'Recent' : 'Past';
			$designs = Mage::getModel("pixvote/designs")->getCollection()
			->addFieldToFilter("status_id", array( "nin" => array($helper->ENTRY_STATUS_IN_EXPIRED, $helper->ENTRY_STATUS_IN_REJECTED, $helper->ENTRY_STATUS_IN_PENDING)))
			->addFieldToFilter("challenge_id", $challenge->getId())
			->setPageSize($designLimit);
			$designs->setOrder("created_on","DESC");
			
			//Get recent designs
			$images = array();
			$designers = array();
			foreach($designs as $design)
			{
				$images[$design->getId()] = Mage::getModel("pixvote/images")->getCollection()
				->addFilter('design_id', $design->getId());
				if(!isset($designers[$design->getCustomerId()]))
				{
					$designers[$design->getCustomerId()] = Mage::getModel("customer/customer")->load($design->getCustomerId());
				}
			}
			//$images = array();
			//$designers = array();
			
			$this->setDesigns($designs);
			$this->setDesigners($designers);
			$this->setImages($images);
			$this->setEntryImagePath($helper->ENTRY_IMAGE_FOLDER);
			$this->setSubmissionHeader($submissionHeader);
		}
		//Load information for submit page
		function loadSubmissionInfo()
		{
			//clear temp images if any
			$dataHelper = $this->helper("pixvote");
			$unsetKey = 'uns'.$dataHelper->TEMP_IMAGE_SESSION_KEY;
			Mage::getSingleton('core/session')->$unsetKey();
			$itemTypes = Mage::getModel($dataHelper->ITEM_TYPES_MODEL_PATH)->getCollection();
			$now = Mage::getModel('core/date')->timestamp(time());
			$dateFormat = 'Y-m-d  H:i:s';
			$today = date($dateFormat, $now);
			$challenges = Mage::getModel($dataHelper->CHALLENGES_MODEL_PATH)->getCollection()
			->addFieldToFilter("is_visible", $this->IS_VISIBLE)
			->addFieldToFilter('submission_start_time', array('datetime' => true, 'to' => $today))
			->addFieldToFilter('submission_end_time', array('datetime' => true, 'from' => $today));
			$colors = $dataHelper->getProductColors();
			$sources = Mage::getModel($dataHelper->DESIGN_SOURCES_MODEL_PATH)->getCollection();
			$this->setSources($sources);
			$this->setColors($colors);
			$this->setItemTypes($itemTypes);
			$this->setChallenges($challenges);
		}
	}
?>