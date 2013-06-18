<?php
	class Pixafy_Pixvote_Block_Designers extends Pixafy_Pixvote_Block_Template
	{
		function loadDesigner()
		{
			$pixvoteHelper = Mage::helper("pixvote");
			$designer = Mage::registry($pixvoteHelper->CURRENT_DESIGNER_KEY);
			if(Mage::helper("customer")->isLoggedIn())
			{
				$usersImFollowing =	$pixvoteHelper->getDesignersImFollowing(array($designer->getId()));
				if(count($usersImFollowing))
				{
					$isFollowingUser = true;
				}
				else
				{
					$isFollowingUser = false;
				}
			}
			else
			{
				$isFollowingUser = false;
			}
			$studioImages = Mage::getModel($pixvoteHelper->DESIGN_STUDIO_IMAGES_MODEL_PATH)->getCollection()->addFieldToFilter("designer_id", $designer->getId());
			$designs = Mage::getModel($pixvoteHelper->DESIGNS_MODEL_PATH)->getCollection()
			->addFieldToFilter("customer_id", $designer->getId())
			->addFieldToFilter("status_id", array("nin" => array($pixvoteHelper->ENTRY_STATUS_IN_PENDING, $pixvoteHelper->ENTRY_STATUS_IN_REJECTED)));
//			$images = array();
//			$comments = array();
//			$authors = array();
//			foreach($designs as $design)
//			{
//				$designIds[] = $design->getId();
//				$images[$design->getId()] = Mage::getModel($pixvoteHelper->DESIGN_IMAGES_MODEL_PATH)->getCollection()
//				->addFilter("design_id", $design->getId());
//				$commentInfo = $pixvoteHelper->loadComments($design->getId(),$authors, 1, array("field" => "created_on", "order" => "DESC" ));
//				$comments[$design->getId()] = $commentInfo['comments'];
//				$authors = $commentInfo['authors'];
//			}
//			
			$this->_loadDesignData($designs, true);
			$userVotes = $this->getUserVotes($userVotes);
			if(!empty($userVotes))
			{
				$isFirstVote = false;
			}
			else
			{
				//Check if user has ever voted
				$allVotes = $pixvoteHelper->getUserVotes();
				$isFirstVote = empty($allVotes);
			}
			
			$isDesigner = $designer->getId() == Mage::helper("customer")->getCustomer()->getId() ? true : false;
			if($isDesigner)
			{
				$countries = Mage::getModel('directory/country_api')->items();
				$this->setCountries($countries);
				$currentCountry = $designer->getDesignerCountry() ? '': $pixvoteHelper->DEFAULT_COUNTRY;
				$regions = Mage::getModel('directory/region_api')->items($currentCountry);
				$this->setRegions($regions);
				
				//check if is first view
				$HAS_VIEWED_PROFILE = 1;
				$HAS_NOT_VIEWED_PROFILE = 0;
				$isFirstView = $designer->getHasViewedProfile() ? $HAS_NOT_VIEWED_PROFILE : $HAS_VIEWED_PROFILE;
				if($isFirstView)
				{
					$designer->setHasViewedProfile($pixvoteHelper->now())->save();
				}
				$this->setIsFirstView($isFirstView);
				$PROFILE_COMPLETED = 1;
				$PROFILE_NOT_COMPLETED = 0;
				//Check for profile completion
				$profileComplete = $designer->checkIfProfileIsCompleted() ? $PROFILE_COMPLETED : $PROFILE_NOT_COMPLETED;
				$this->setIsProfileComplete($profileComplete);
			}
			
			$this->setDesigner($designer);
			$this->setDesigns($designs);
			//$this->setImages($images);
			//$this->setComments($comments);	
			//$this->setAuthors($authors);
			$this->setIsFollowing($isFollowingUser);
			$this->setStudioImages($studioImages);
			$this->setIsDesigner($isDesigner);
			$this->setIsFirstVote($isFirstVote);
		}
	}
?>