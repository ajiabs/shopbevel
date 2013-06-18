<?php
class Pixafy_Pixvote_Block_Designs extends Mage_Core_Block_Template
{

	const TAG_RECENT = "recent";
	const TAG_ENDING = "ending";
	const TAG_TRENDING = "trending";
	const TAG_POPULAR = "popular";
	const TAG_STAFF_PICKS = "staff_pick";
	const TAG_FEATURED = "featured";
	const IS_STAFF_PICK = 1;
	const IS_FEATURED = 1;

	public $design_id_filter;
	public $pixvote_helper;
	public $json;
	public $designs_per_page;
	public $featured_designs_per_page;
	public $view_page_design_limit;
	function __construct()
	{
		parent::__construct();
		$this->design_id_filter = "design_id";
		$this->front_name = "vote";
		$this->json = "";
		$this->designs_per_page = 8;
		$this->featured_designs_per_page = 1;
		$this->view_page_design_limit = 4;

	}

	function getUserDesigns()
	{
		//Get designs That are currently avaiable for voting
		$now = Mage::getModel('core/date')->timestamp(time());
		$dateFormat = 'Y-m-d  H:i:s';
		$today = date($dateFormat, $now);
		$curPage = $this->getRequest()->getParam("page");
		$helper = $this->helper("pixvote");
		$type = $this->getRequest()->getParam("type");
		$challenge = Mage::registry($helper->CURRENT_VOTE_CHALLENGE_KEY);
		$designs = Mage::getModel("pixvote/designs")->getCollection()
		->addFieldToFilter("status_id", $helper->ENTRY_STATUS_IN_VOTING);
		if(is_array($challenge))
		{
			$designs->addFieldToFilter("challenge_id", array("in" => array_keys($challenge)));
		}
		else
		{
			$designs->addFieldToFilter("challenge_id", $challenge->getId());
		}
		$designs->setPageSize($this->designs_per_page);
		switch($type)
		{
			case self::TAG_RECENT:
				//$designs->getSelect()->setOrder(new Zend_Db_Expr('RAND()'));
				$designs->setOrder("vote_start_time","DESC");
			break;
			case self::TAG_ENDING:
				$designs->setOrder("vote_end_time","ASC");
			break;
			case self::TAG_TRENDING:
				$todayDateTime = new DateTime($today);
				$yesterdayDateTime = clone $todayDateTime;
				$yesterdayDateTime->modify('-1 day');
				$period = array(
					"from" =>$yesterdayDateTime->format($dateFormat),
					"to" => $todayDateTime->format($dateFormat)
				);
				$designs = $this->getPopularDesigns($designs, $period);
			break;
			case self::TAG_POPULAR:
				$designs = $this->getPopularDesigns($designs);
			break;
			case self::TAG_STAFF_PICKS:
				$designs->addFieldToFilter("is_staff_pick", self::IS_STAFF_PICK);
			break;
			default:
				//$designs->getSelect()->order(new Zend_Db_Expr('RAND()'));
				$designs->setOrder("vote_start_time","DESC");
				$type = self::TAG_RECENT;
			break;
		}

		if(!$curPage)
		{
			//Get count of pages to be used during pagination


			$this->setPageTotal($designs->getLastPageNumber());
			$paginationUrl = $helper->FRONT_NAME.'/index/ajaxdesign';
			if(!is_array($challenge))
			{
				$paginationUrl .= '/id/'.$challenge->getId();
			}
			$this->setPaginationUrl(Mage::getUrl($paginationUrl.'?type='.$type));
		}
		else
		{
			$designs->setCurPage($curPage);
		}

		//->setCurPage(3)
		//->getSelect()->limit(0,1);
		//die((string)$designs->getSelect());

		$images = array();
		$commentsCount = array();
		$comments = array();
		$designers = array();
		$authors = array();
		$votes = array();
		$designIds = array();
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
			$votes[$design->getId()] = $design->getVotes() ? '' : Mage::getModel("pixvote/votes")->getCollection()->addFieldToFilter($this->design_id_filter, $design->getId())->count();

			$commentInfo = $this->_loadComments($design->getId(),$authors, 1, array("field" => "created_on", "order" => "DESC" ));
			$comments[$design->getId()] = $commentInfo['comments'];
			$authors = $commentInfo['authors'];
		}
		$designs = $designs->getItems();
		shuffle($designs);
		$userVotes = $this->helper("pixvote")->getUserVotes($designIds);
		if(!empty($userVotes))
		{
			$isFirstVote = false;
		}
		else
		{
			//Check if user has ever voted
			$allVotes = $this->helper("pixvote")->getUserVotes();
			$isFirstVote = empty($allVotes);
		}
		$this->setUserVotes($userVotes);
		$this->setDesigns($designs);
		$this->setDesigners($designers);
		$this->setCommentCount($commentsCount);
		$this->setComments($comments);
		$this->setAuthors($authors);
		$this->setDesignIds($designIds);
		$this->setImages($images);
		$this->setVotes($votes);
		$this->setEntryImagePath($helper->ENTRY_IMAGE_FOLDER);
		$this->setIsFirstVote($isFirstVote);
		$this->setChallenge($challenge);
	}


	function loadFeaturedDesign()
	{
		$now = Mage::getModel('core/date')->timestamp(time());
		$dateFormat = 'Y-m-d  H:i:s';
		$today = date($dateFormat, $now);
		$curPage = $this->getRequest()->getParam("page");
		$helper = $this->helper("pixvote");

		$challenge = Mage::registry($helper->CURRENT_VOTE_CHALLENGE_KEY);
		$featuredDesign = Mage::getModel("pixvote/designs")->getCollection()
		->addFieldToFilter("status_id", $helper->ENTRY_STATUS_IN_VOTING)
		->addFieldToFilter("challenge_id", $challenge->getId())
		->setPageSize($this->designs_per_page);

		$featuredDesign->setPageSize($this->featured_designs_per_page);

		//Get count of pages to be used during pagination
		$this->setFeaturedPageTotal($featuredDesign->getLastPageNumber());
		$curPage = $this->getRequest()->getParam("page");
		if(!$curPage)
		{
			$this->setFeaturedPaginationUrl(Mage::getUrl($helper->FRONT_NAME.'/index/ajaxfeatured/id/'.$challenge->getId()));
		}
		else
		{
			$featuredDesign->setCurPage($curPage);
		}
		$this->setCurrentPage(intval($curPage));
		$images = $this->getImages();
		$commentCount = $this->getCommentCount();
		$comments = $this->getComments();
		$authors = $this->getAuthors();
		$designers = $this->getDesigners();
		$designs = $this->getDesigns();
		$votes = $this->getVotes();
		$design = $featuredDesign->getFirstItem();


		//Load featured design info if not already loaded
		$designIds[] = $design->getId();

		$images[$design->getId()] = Mage::getModel("pixvote/images")->getCollection()
		->addFilter($this->design_id_filter, $design->getId());
		if(!isset($designers[$design->getCustomerId()]))
		{
			$designers[$design->getCustomerId()] = Mage::getModel("customer/customer")->load($design->getCustomerId());
		}

		if(!isset($commentsCount[$design->getId()]))
		{
			$commentsCount[$design->getId()] = Mage::getModel("pixvote/comments")->getCollection()->addFieldToFilter($this->design_id_filter, $design->getId())->count();
		}

		if(!isset($votes[$design->getId()]))
		{
			$votes[$design->getId()] = Mage::getModel("pixvote/votes")->getCollection()->addFieldToFilter($this->design_id_filter, $design->getId())->count();
		}

		if(!isset($comments[$design->getId()]))
		{
			$commentInfo = $this->_loadComments($design->getId(),$authors, 1, array("field" => "created_on", "order" => "DESC" ));
			$comments[$design->getId()] = $commentInfo['comments'];
			$authors = $commentInfo['authors'];
		}


		//Update variables
		$this->setFeaturedUserVotes($this->helper("pixvote")->getUserVotes($designIds));
		$this->setFeaturedDesign($design);
		$this->setFeaturedDesigns($designs);
		$this->setFeaturedDesigners($designers);
		$this->setFeaturedCommentCount($commentsCount);
		$this->setFeaturedComments($comments);
		$this->setFeaturedAuthors($authors);
		$this->setFeaturedDesignIds($designIds);
		$this->setFeaturedImages($images);
		$this->setFeaturedVotes($votes);
		$this->setIsAjax($this->getRequest()->isXmlHttpRequest() ? true : false);
		$this->setChallenge($challenge);
	}

	function getDesignComments()
	{
		$designId = $this->getRequest()->getParam("id");
		if($designId)
		{
			$commentInfo = $this->_loadComments($designId, array());
			$json = array();

			$json['id'] = $designId;
			$json['comments'] = array();
			$commentData = array();
			foreach($commentInfo['comments'] as $comment)
			{
				$commentData['author'] =  $commentInfo['authors'][$comment->getCustomerId()]->getName();
				$commentData['image'] =  $this->helper("pixvote")->getProfileImage($commentInfo['authors'][$comment->getCustomerId()]);
				$commentData['comment'] = $comment->getComment();
				$json['comments'][] = $commentData;
			}
			$this->json = $json;
		}
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

	protected function _toHtml()
	{
		$html = parent::_toHtml();
		if ($this->getRequest()->isXmlHttpRequest())
		{
			if($this->json)
			{
				return json_encode($this->json);
			}
			else
			{
				return json_encode(array("html" => $html));
			}
		}
		else
		{
			return $html;
		}
	}

	function getPopularDesigns($designs, $period = false)
	{
		$joinCond = 'main_table.id = design_votes.design_id';

		//get count of votes during a certain period of time
		if($period)
		{
			$joinCond .= " and design_votes.created_on >='".$period['from']."' and design_votes.created_on <='".$period['to']."'";
		}
		$resource = Mage::getSingleton('core/resource');
		$designs->getSelect()
		->joinLeft( array(
		'design_votes'=> $resource->getTableName('pixvote/votes')), $joinCond, array('count(design_votes.id) as votes'))
		->group(array('main_table.id'));
		$designs->setOrder('votes', 'DESC');
		//die((string)$designs->getSelect());
		return $designs;
	}

	function loadDesign()
	{
		$now = Mage::getModel('core/date')->timestamp(time());
		$dateFormat = 'Y-m-d  H:i:s';
		$today = date($dateFormat, $now);
		$curPage = $this->getRequest()->getParam("page");
		$helper = $this->helper("pixvote");
		$challenge = Mage::registry($helper->CURRENT_VOTE_CHALLENGE_KEY);
		$design = Mage::registry($helper->CURRENT_SUBMISSION);
//
//		$featuredDesign->setPageSize($this->featured_designs_per_page);
//
//		//Get count of pages to be used during pagination
//		$this->setFeaturedPageTotal($featuredDesign->getLastPageNumber());
//		$curPage = $this->getRequest()->getParam("page");
//		if(!$curPage)
//		{
//			$this->setFeaturedPaginationUrl(Mage::getUrl('vote/index/ajaxfeatured'));
//		}
//		else
//		{
//			$featuredDesign->setCurPage($curPage);
//		}
//		$this->setCurrentPage(intval($curPage));

		$images = array();
		$commentCount = array();
		$comments = array();
		$authors = array();
		$designers = array();
		$designs = array();
		$votes = array();

		$images[$design->getId()] = Mage::getModel("pixvote/images")->getCollection()
		->addFilter($this->design_id_filter, $design->getId());
		$designIds[$design->getId()] = $design->getId();
		$images[$design->getId()] = Mage::getModel("pixvote/images")->getCollection()
		->addFilter($this->design_id_filter, $design->getId());
		if(!isset($designers[$design->getCustomerId()]))
		{
			$designers[$design->getCustomerId()] = Mage::getModel("customer/customer")->load($design->getCustomerId());
		}
		$commentsCount[$design->getId()] = Mage::getModel("pixvote/comments")->getCollection()->addFieldToFilter($this->design_id_filter, $design->getId())->count();
		$votes[$design->getId()] = $design->getVotes() ? '' : Mage::getModel("pixvote/votes")->getCollection()->addFieldToFilter($this->design_id_filter, $design->getId())->count();

		$commentInfo = $this->_loadComments($design->getId(),$authors, 1, array("field" => "created_on", "order" => "DESC" ));
		$comments[$design->getId()] = $commentInfo['comments'];
		$authors = $commentInfo['authors'];

		//Get 3 random designs to display on the bottom
		$otherDesigns = Mage::getModel("pixvote/designs")->getCollection()
		//->setPageSize($this->view_page_design_limit)
		->addFieldToFilter("challenge_id", $challenge->getId())
		//->addFieldToFilter("id", array("neq" => $this->getRequest()->getParam("id")))
		//->addFieldToFilter("status_id", array("nin" => array($helper->ENTRY_STATUS_IN_EXPIRED, $helper->ENTRY_STATUS_IN_REJECTED, $helper->ENTRY_STATUS_IN_PENDING)));
		->addFieldToFilter("status_id", array("nin" => array($helper->ENTRY_STATUS_IN_REJECTED, $helper->ENTRY_STATUS_IN_PENDING)));

		foreach($otherDesigns as $otherDesign)
		{
			$designIds[$otherDesign->getId()] = $otherDesign->getId();
			$images[$otherDesign->getId()] = Mage::getModel("pixvote/images")->getCollection()
			->addFilter($this->design_id_filter, $otherDesign->getId());
			if(!isset($designers[$otherDesign->getCustomerId()]))
			{
				$designers[$otherDesign->getCustomerId()] = Mage::getModel("customer/customer")->load($otherDesign->getCustomerId());
			}
			$commentsCount[$otherDesign->getId()] = Mage::getModel("pixvote/comments")->getCollection()->addFieldToFilter($this->design_id_filter, $otherDesign->getId())->count();
			$votes[$otherDesign->getId()] = $otherDesign->getVotes() ?'': Mage::getModel("pixvote/votes")->getCollection()->addFieldToFilter($this->design_id_filter, $otherDesign->getId())->count();

			$commentInfo = $this->_loadComments($otherDesign->getId(),$authors, 1, array("field" => "created_on", "order" => "DESC" ));
			$comments[$otherDesign->getId()] = $commentInfo['comments'];
			$authors = $commentInfo['authors'];
		}

		$userVotes = $this->helper("pixvote")->getUserVotes($designIds);
		if(!empty($userVotes))
		{
			$isFirstVote = false;
		}
		else
		{
			//Check if user has ever voted
			$allVotes = $this->helper("pixvote")->getUserVotes();
			$isFirstVote = empty($allVotes);
		}
		$this->setUserVotes($userVotes);
		$this->setSelectedDesign($design);
		$this->setOtherDesigns($otherDesigns);
		$this->setDesigners($designers);
		$this->setCommentCount($commentsCount);
		$this->setComments($comments);
		$this->setAuthors($authors);
		$this->setDesignIds($designIds);
		$this->setImages($images);
		$this->setVotes($votes);
		$this->setEntryImagePath($helper->ENTRY_IMAGE_FOLDER);
		$this->setChallenge($challenge);
		$this->setViewPageDesignLimit($this->view_page_design_limit);
		$this->setIsFirstVote($isFirstVote);

		//Register share object
//		$shareObj = new Varien_Object();
//		$shareObj->setName($design->getName);
//		$shareImage = Mage::getBaseDir ( 'media' ) . DS .$images[$otherDesign->getId()]->getFirstItem()->getUrl();
//		$shareObj->setImage($shareImage);
//		Mage::register($helper->REGISTER_FB_SHARE_OBJECT,$shareObj);

	}
}
?>
