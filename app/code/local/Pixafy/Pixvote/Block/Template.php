<?php
	class Pixafy_Pixvote_Block_Template extends Mage_Core_Block_Template
	{
		protected function _loadDesignData($designs, $loadChallenges = true)
		{
			$this->design_id_filter = "design_id";
			$images = array();
			$commentsCount = array();
			$comments = array();
			$designers = array();
			$authors = array();
			$votes = array();
			$designIds = array();
			$challenges = array();
			foreach($designs as $design)
			{
				$designIds[] = $design->getId();
				$images[$design->getId()] = Mage::getModel("pixvote/images")->getCollection()
				->addFilter($this->design_id_filter, $design->getId());
				if(!isset($designers[$design->getCustomerId()]))
				{
					$designers[$design->getCustomerId()] = Mage::getModel("customer/customer")->load($design->getCustomerId());
				}
				$commentsCount[$design->getId()] = Mage::getModel("pixvote/comments")->getCollection()->addFieldToFilter($this->design_id_filter, $design->getId())->count();
				$votes[$design->getId()] = $design->getVotes() ? '': Mage::getModel("pixvote/votes")->getCollection()->addFieldToFilter($this->design_id_filter, $design->getId())->count();

				$commentInfo = $this->_loadComments($design->getId(),$authors, 1, array("field" => "created_on", "order" => "DESC" ));
				$comments[$design->getId()] = $commentInfo['comments'];
				$authors = $commentInfo['authors'];
				if($loadChallenges)
				{
					if(!isset($challenges[$design->getChallengeId()]))
					{
						$challenges[$design->getChallengeId()] = Mage::getModel($this->helper("pixvote")->CHALLENGES_MODEL_PATH)->load($design->getChallengeId());
					}
				}
			}
			$this->setUserVotes($this->helper("pixvote")->getUserVotes($designIds));
			$this->setDesigns($designs);
			$this->setDesigners($designers);
			$this->setCommentCount($commentsCount);
			$this->setComments($comments);
			$this->setAuthors($authors);
			$this->setDesignIds($designIds);
			$this->setImages($images);
			$this->setVotes($votes);
			$this->setEntryImagePath($this->helper("pixvote")->ENTRY_IMAGE_FOLDER);
			//$this->loadFeaturedDesign();
			$this->setChallenges($challenges);
		}
		
		protected function  _loadComments($designId, $authors = array(), $limit = false, $sort = false)
		{
				//$comments = Mage::getModel("pixvote/comments")->getCollection()->setOrder('created_on');
				$comments = Mage::getModel("pixvote/comments")->getCollection();
				if($sort)
				{
					$comments = Mage::getModel("pixvote/comments")->getCollection()->setOrder($sort['field'],$sort['order']);
				}
				if($limit)
				{
					$comments->setPageSize($limit);
				}
				$comments->addFieldToFilter($this->design_id_filter, $designId);

				//Get users who made the comments
				foreach($comments as $comment)
				{
					if(!isset($authors[$comment->getCustomerId()]))
					{
						$authors[$comment->getCustomerId()] = Mage::getModel("customer/customer")->load($comment->getCustomerId());
					}
				}

				return array(
							"comments" => $comments,
							"authors" => $authors,
				);
		}
	}
?>