<?php
class Pixafy_Pixvote_Helper_Data extends Mage_Core_Helper_Abstract
{
	public $ENTRY_IMAGE_FOLDER = "catalog/design_entries/entry_";

	public $TEMP_IMAGE_SESSION_KEY = "TempImages";

	public $ENTRY_TEMP_IMAGE_FOLDER = "catalog/design_entries/temp_";

	public $CHALLENGE_IMAGE_FOLDER = "catalog/challenges/challenge_";

	public $CUSTOMER_IMAGE_FOLDER = "customer/customer_";

	public $CUSTOMER_STUDIO_IMAGE_FOLDER = "customer/studio_";

	public $DEFAULT_PROFILE_IMAGE_PATH = "pixvote/general/default_profile_image";

	public $DEFAULT_PROFILE_IMAGE_LAGE_PATH = "pixvote/general/default_profile_image_large";

	public $VOTE_UPDATE_FREQUENCY_PATH = "pixvote/general/vote_update_frequency";

	public $NAME_EXCLUDE_LIST_PATH = "pixvote/general/name_exclude_list";

	public $COLLECTION_CATEGORY_ID_PATH = "bevel/general/collection_category_id";

	public $IN_PRODUCTION_CATEGORY_ID_PATH = "bevel/general/in_production_category_id";

	public $IN_PRODUCTION_DEFAULT_QTY_PATH = "bevel/general/in_production_default_qty";

	public $IN_PRODUCTION_DISCOUNT_PATH = "bevel/general/in_production_discount";

	public $ENTRY_STATUS_IN_PENDING = 1;

	public $ENTRY_STATUS_IN_VOTING = 2;

	public $ENTRY_STATUS_IN_PRODUCTION = 3;

	public $ENTRY_STATUS_IN_LIVE = 4;

	public $ENTRY_STATUS_IN_REJECTED = 5;

	public $ENTRY_STATUS_IN_APPROVED = 6;

	public $ENTRY_STATUS_IN_EXPIRED = 7;

	public $VOTE_LIMIT = 2;

	public $VOTE_STORE_CREDIT_AMOUNT = 10;

	public $TIMESTAMP_FORMAT = 'Y-m-d  H:i:s';

	public $TIMESTAMP_GRID_FORMAT = 'M j, Y  g:i:s A';

	public $FRONT_NAME = "challenges";

	public $VOTE_FRONT_NAME = "vote";

	public $CHALLENGES_MODEL_PATH = "pixvote/challenges";

	public $STATUS_MODEL_PATH = "pixvote/status";

	public $DESIGNS_MODEL_PATH = "pixvote/designs";

	public $DESIGN_IMAGES_MODEL_PATH = "pixvote/images";

	public $DESIGN_COMMENTS_MODEL_PATH = "pixvote/comments";

	public $DESIGN_FOLLOW_MODEL_PATH = "pixvote/follow";

	public $DESIGN_VOTES_MODEL_PATH = "pixvote/votes";

	public $DESIGN_STUDIO_IMAGES_MODEL_PATH = "pixvote/studioimages";

	public $VOTES_MODEL_PATH = "pixvote/votes";

	public $REJECTIONS_MODEL_PATH = "pixvote/rejections";

	public $DESIGN_VIEWS_MODEL_PATH = "pixvote/designviews";

	public $DESIGN_SOURCES_MODEL_PATH = "pixvote/designsources";

	public $DESIGN_TIPS_MODEL_PATH = "pixvote/designtips";

	public $SUBMISSION_IMAGE_LIMIT = 4;

	public $ITEM_TYPES_MODEL_PATH = "pixvote/itemtypes";

	public $DESIGN_SHARES_MODEL_PATH = "pixvote/designshares";
	
	public $TRACKING_MODEL_PATH = "pixvote/tracking";

	public $VOTE_COUNT_PATH = "bevel/general/vote_count";

	public $CHALLENGE_SEPARATOR = "-";

	public $JUDGE_SEPARATOR = ",";

	public $REGISTRY_CATEGORY_KEY = "registry_category_";

	public $REGISTRY_DESIGNSOURCE_KEY = "designsource_data";

	public $REGISTRY_CHALLENGE_DATA = "challenge_data";

	public $CURRENT_CHALLENGE_KEY = "current_challenge";

	public $CURRENT_VOTE_CHALLENGE_KEY = "current_vote_challenge";

	public $CURRENT_SUBMISSION = "current_submission";

	public $REGISTRY_FB_SHARE_OBJECT = "current_share_object";

	public $REGISTRY_IN_PRODUCTION_CATEGORY = "in_production_category";

	public $DESIGN_IMAGE_WIDTH = 268;

	public $DESIGN_ITEM_IMAGE_WIDTH = 520;

	public $DESIGN_ITEM_IMAGE_HEIGHT = 390;

	public $DESIGN_ITEM_IMAGE_SMALL_WIDTH = 84;

	public $DESIGN_ITEM_IMAGE_SMALL_HEIGHT = 84;

	public $PROFILE_IMAGE_WIDTH = 300;

	public $PROFILE_IMAGE_HEIGHT = 300;

	public $DESIGN_PROFILE_IMAGE_WIDTH = 150;

	public $DESIGN_PROFILE_IMAGE_HEIGHT = 150;

	public $STUDIO_IMAGE_WIDTH = 370;

	public $STUDIO_IMAGE_HEIGHT = 200;

	public $TIP_EMAIL_IMAGE_WIDTH = 157;

	public $TIP_EMAIL_IMAGE_HEIGHT = 156;

	public $ERROR_LOGIN;

	public $ERROR_NO_DATA;

	public $IS_VISIBLE = 1;

	public $IS_DEFAULT = 1;

	public $VAL_TRUE = 1;

	public $VAL_FALSE = 0;

	public $ERROR_UPLOAD_IMAGE;

	public $VALID_IMAGE_TYPES = array('jpg', 'jpeg','png', 'bmp', 'gif');

	public $CURRENT_DESIGNER_KEY = "current_designer";

	public $STUDIO_IMAGE_LIMIT = 6;

	public $ITEMS_PER_SLIDER = 4;

	public $DEFAULT_MOODBOARD = 'http://pinterest.com/ShopBevel/nod-to-neon/';

	public $DEFAULT_COUNTRY = 'US';

	public $TEMPLATE_DESIGN_PROFILE = 'pixvote/designprofile.phtml';

	public $TEMPLATE_DESIGN_VOTE  = 'pixvote/designvote.phtml';

	public $APPEAR_IN_SUBMISSION_EMAIL = 1;

	public $SUBMISSION_EMAIL_IMAGE_WIDTH = 344;

	public $SUBMISSION_EMAIL_IMAGE_HEIGHT = 153;

	public $VOTE_EMAIL_IMAGE_WIDTH = 155;

	public $VOTE_EMAIL_IMAGE_HEIGHT = 100;

	public $FIRST_NAME_ATRRIBUTE_ID = 5;

	public $LAST_NAME_ATRRIBUTE_ID = 7;

	public $STATIC_BLOCK_POPUP_VOTE = "vote-popup-text";

	public $STATIC_BLOCK_POPUP_SUBMISSION = "submission-popup-text";

	public $STATIC_BLOCK_POPUP_GENERAL = "login-box-blurb";

	public $STATIC_BLOCK_SIGNUP_POPUP_VOTE = "vote-signup-popup-text";

	public $STATIC_BLOCK_SIGNUP_POPUP_SUBMISSION = "submission-signup-popup-text";

	public $STATIC_BLOCK_VOTE_SUBHEADER = "vote-sub-header";

	public $STATIC_BLOCK_VOTE_SHARE_MAIN = "share-vote-popup";

	public $STATIC_BLOCK_VOTE_SHARE_THANKS = "vote-share-thanks-popup";

	public $STATIC_BLOCK_VOTE_SHARE_NO_THANKS = "vote-share-no-thanks-popup";

	public $DESIGN_REWRITE_ID_PATH = "design/";

	public $CHALLENGE_COPY_TARGET = "design challenge";

	public $SHARE_TYPES = array("fb");
	
	public $SUBJECT_POPUP_SHARE = "Popup Share";
	
	public $SUBJECT_POPUP_THANKS = "Popup thanks";
	
	public $SUBJECT_POPUP_NO_THANKS = "Popup no thanks";
	
	public $TESTING_VARIANTS = array("a", "b");

	public $FIRST_VOTE_MODAL_BLOCK_NAME = "firstVoteModal";
	
	function __construct()
	{
		$this->ERROR_LOGIN = $this->__('Please login to continue');
		$this->ERROR_UPLOAD_IMAGE = $this->__('Please upload an image before submitting a design');
		$this->ERROR_NO_DATA = $this->__('No data has been passed');
	}
	function getProfileImage($customer, $isProfile = false)
	{
		if(!$customer->getProfileImage())
		{
			if($isProfile)
			{
				$imageUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'pixvote'.DS.Mage::getStoreConfig($this->DEFAULT_PROFILE_IMAGE_LAGE_PATH);
			}
			else
			{
				$imageUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'pixvote'.DS.Mage::getStoreConfig($this->DEFAULT_PROFILE_IMAGE_PATH);
			}
		}
		else
		{
			if($isProfile)
			{
				//$imageUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'customer'.$customer->getProfileImage();
				$imageUrl = $this->getResizedImage($customer, 'getProfileImage', $this->PROFILE_IMAGE_WIDTH,  $this->PROFILE_IMAGE_HEIGHT);
			}
			else
			{
				$imageUrl = $this->getResizedImage($customer, 'getProfileImage', $this->DESIGN_PROFILE_IMAGE_WIDTH,  $this->DESIGN_PROFILE_IMAGE_WIDTH);

			}
		}
		return $imageUrl;
	}

	function getResizedImage($object, $imageFunc, $width, $height = null, $dir ="", $quality = 100)
	{
		//$objImage = str_replace("/","\\", $object->$imageFunc());
		//$dir = str_replace("/","\\", $dir);
		$objImage = $object->$imageFunc();
		if (!$objImage)
		{
		echo $objImage;
			return "";
		}
		$fileName = pathinfo($objImage, PATHINFO_BASENAME );
		$imageFileName = $object->getId().'_'.pathinfo($objImage, PATHINFO_BASENAME );

		//remove quotes spaces
		$imageFileName = str_replace('"', "", $imageFileName);
		$imageFileName = str_replace("'", "", $imageFileName);
		$imageFileName = str_replace(" ", "-", $imageFileName);
		$resizePrefix = $width.(is_null($height) ? "x_" : "x".$height.'_');
		if(stripos($objImage, "media") !== 0)
		{
			$imageUrl = Mage::getBaseDir ( 'media' ) . DS . $dir . $objImage;
		}
		else
		{
			$imageUrl = Mage::getBaseDir() . DS . $objImage;
		}
		if (!is_file($imageUrl))
		{
			return "";
		}
		if(!$dir)
		{
			$dir = pathinfo($objImage, PATHINFO_DIRNAME  );
		}

		$imageResized = Mage::getBaseDir ( 'media' ) . DS . $dir . DS . "resized" . DS . $resizePrefix . $imageFileName;
		///$imageResized = Mage::getBaseDir ( 'media' ) . DS . "catalog" . DS . "product" . DS . "cache" . DS . "resized" . DS . $resizePrefix . $imageFileName;
		// Because clean Image cache function works in this folder only
		if (! file_exists ( $imageResized ) && file_exists ( $imageUrl ) || file_exists($imageUrl) && filemtime($imageUrl) > filemtime($imageResized)) :
		$imageObj = new Varien_Image ( $imageUrl );
		$imageObj->constrainOnly ( true );
		$imageObj->keepAspectRatio ( true );
		$imageObj->keepFrame ( false );
		$imageObj->quality ( $quality );
		$imageObj->resize ( $width, $height );
		$imageObj->save ( $imageResized );
		endif;

		if(file_exists($imageResized))
		{

			return Mage::getBaseUrl ( 'media' ) .$dir."/resized/" . $resizePrefix . $imageFileName;
		}
		else
		{
			return $objImage;;
		}

	}

	function getUserVotes($designIds = array())
	{
		$designArr = array();

		if(Mage::helper("customer")->isLoggedIn())
		{
			$votedDesigns = Mage::getModel("pixvote/votes")->getCollection()
			->addFieldToFilter("customer_id", Mage::helper("customer")->getCustomer()->getId());
			if(!empty($designIds))
			{
				$votedDesigns->addFieldToFilter("design_id", array("in" => $designIds));
			}
			foreach($votedDesigns as $design)
			{
				if(isset($designArr[$design->getDesignId()]))
				{
					$designArr[$design->getDesignId()]++;
				}
				else
				{
					$designArr[$design->getDesignId()] = 1;
				}
			}
		}
		return $designArr;
	}

	function getDesignersImFollowing($designerIds = array())
	{
		$followingArr = array();

		if(Mage::helper("customer")->isLoggedIn())
		{
			$followings = Mage::getModel($this->DESIGN_FOLLOW_MODEL_PATH)->getCollection()
			->addFieldToFilter("customer_id", Mage::helper("customer")->getCustomer()->getId());
			if(!empty($designerIds))
			{
				$followings->addFieldToFilter("designer_id", array("in" => $designerIds));
			}
			foreach($followings as $following)
			{
				$followingArr[$following->getDesignId()] = $following->getDesignerId();
			}
		}
		return $followingArr;
	}

	//Get prev and next products for pagination
	public function getPrevNextDesign($challengeId, $currentDesignId, $designCollection = array())
	{
		if(empty($designCollection))
		{
			$designCollection = Mage::getModel("pixvote/designs")->getCollection()
			->addFieldToFilter('challenge_id',$challengeId)
			//->addFieldToFilter("status_id", array( "nin" => array($this->ENTRY_STATUS_IN_EXPIRED, $this->ENTRY_STATUS_IN_REJECTED, $this->ENTRY_STATUS_IN_PENDING)));
			->addFieldToFilter("status_id", array( "nin" => array($this->ENTRY_STATUS_IN_REJECTED, $this->ENTRY_STATUS_IN_PENDING)));
			//convert to index-based array

		}
		foreach($designCollection as $design)
		{
			$designs[] = $design;
		}
		//Get previous and next products in collection
		$count = 0;
		$prevNextArr = array('_prevDesign' => '', '_nextDesign' => '');
		foreach($designs as $key => $design)
		{
			if($design->getId() == $currentDesignId)
			{
				if($count)
				{
					$prevNextArr['_prevDesign'] = $designs[$count - 1];
				}
				if(($count+1) != count($designs))
				{
					$prevNextArr['_nextDesign'] = $designs[$count + 1];
				}
				break;
			}
			$count++;
		}
		return $prevNextArr;
	}

	function uploadImage($i, $input_name, $media_folder)
	{
		$file_errors = $_FILES[$input_name]['error'][$i];
		if ($file_errors == UPLOAD_ERR_OK)
		{
			$file_name = $_FILES[$input_name]['name'][$i];
			$file_info = pathinfo($file_name);
			if(!in_array(strtolower($file_info['extension']),$this->VALID_IMAGE_TYPES))
			{
				return false;
			}
			$file_uploaded = time();

			$server_name = basename($file_name,'.'.$file_info['extension']).'-'.$file_uploaded.'.'.$file_info['extension'];
			$tmp_name = $_FILES[$input_name]['tmp_name'][$i];
			move_uploaded_file($tmp_name, "$media_folder".DS."$server_name");
			return array(
				"file_name" => $file_name,
				"server_name" => $server_name,
			);
		}
		return false;
	}

	//Get moodBoard
	function getMoodBoard($url)
	{
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$html = curl_exec($ch);
		if($html)
		{
			return trim(Mage::helper("cim")->substring_between($html,'<body class="board">','</body>'));
		}
		else
		{
			return '';
		}
	}

	function getProductColors()
	{
		return Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'color')->getSource()->getAllOptions(false);
	}

	function getDesignUrl($design, $challenge = null)
	{
		if(is_null($challenge))
		{
			$challenge = Mage::getModel($this->CHALLENGES_MODEL_PATH)->load($design->getChallengeId());
		}
		$challengeName = strtolower(str_replace(" ",Mage::helper("pixvote")->CHALLENGE_SEPARATOR, $challenge->getName()));
		$designName = $design->getId().'-'.strtolower(str_replace(" ",Mage::helper("pixvote")->CHALLENGE_SEPARATOR, $design->getName()));
		
		//remove quotes
		$designName = str_replace('"', "", $designName);
		$designName = str_replace("'", "", $designName);
		
		return $this->VOTE_FRONT_NAME.'/'.$challengeName.'/'.$designName;
	}

	//Get challenges that are in voting or have been voted on
	function getAvailableChallenges()
	{
		$now = Mage::getModel('core/date')->timestamp(time());
		$dateFormat = 'Y-m-d  H:i:s';
		$today = date($dateFormat, $now);
		$challenges = Mage::getModel($this->CHALLENGES_MODEL_PATH)->getCollection()
		->addFieldToFilter('is_visible', $this->IS_VISIBLE)
		->addFieldToFilter('vote_start_time', array('datetime' => true, 'to' => $today))
		->addFieldToFilter('vote_end_time', array('datetime' => true, 'from' => $today));
		//die((string)$challenges->getSelect());
		return $challenges;
	}

	function getChallengeVoteUrl($challengeName)
	{
		$challengeName = strtolower(str_replace(" ",$this->CHALLENGE_SEPARATOR, $challengeName));
		return Mage::getUrl($this->VOTE_FRONT_NAME.'/'.$challengeName);
	}

	function getChallengeAboutUrl($challenge)
	{
		$challengeName = strtolower(str_replace(" ",$this->CHALLENGE_SEPARATOR, $challenge->getName()));
		return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).$this->FRONT_NAME.'/'.$challengeName;
	}
	function formatName($name)
	{
		$exceptions = explode(',', Mage::getStoreConfig($this->NAME_EXCLUDE_LIST_PATH));
		if(!in_array($name,$exceptions))
		{
			$nameArr = explode(" ", $name);
			$firstName = strtoupper($nameArr[0]{0});
			return $firstName.'. '.end($nameArr);
		}
		else
		{
			return $name;
		}
	}

	function formatUrl($url)
	{
		if($url)
		{
			if(stripos($url, 'http') === false)
			{
				$url = "http://".$url;
			}
		}
		return $url;
	}

	function loadComments($designId, $authors = array(), $limit = false, $sort = false)
	{
		//$comments = Mage::getModel("pixvote/comments")->getCollection()->setOrder('created_on');
		$comments = Mage::getModel($this->DESIGN_COMMENTS_MODEL_PATH)->getCollection();
		if($sort)
		{
			$comments = Mage::getModel($this->DESIGN_COMMENTS_MODEL_PATH)->getCollection()->setOrder($sort['field'],$sort['order']);
		}
		if($limit)
		{
			$comments->setPageSize($limit);
		}
		$comments->addFieldToFilter('design_id', $designId);

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

	//function to remove resized images
	function deleteResizedImages($filePath)
	{
		$files = glob($filePath);
		foreach($files as $file)
		{
			if(is_file($file) && in_array(strtolower(pathinfo($file,PATHINFO_EXTENSION)),$this->VALID_IMAGE_TYPES))
			{
				unlink($file);
			}
		}
	}

	function getTodayDateTime()
	{
		$now = Mage::getModel('core/date')->timestamp(time());
		$time = date("Y-m-d H:i:s",$now);
		$today = new DateTime($time);
		return $today;
	}


	function checkIfExpired($expirationTime)
	{
		$now = Mage::getModel('core/date')->timestamp(time());
		$currentTime = strtotime(date("Y-m-d H:i:s",$now));
		$expirationTime = strtotime($expirationTime);
		return ($expirationTime - $currentTime) < 0;
	}

	function getProfileUrl($designer)
	{
		return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).$this->FRONT_NAME.'/designers/index/id/'.$designer->getId();
	}

	function now()
	{
		$now = Mage::getModel('core/date')->timestamp(time());
		return date("Y-m-d H:i:s",$now);
	}

	function sendVoteEmail($email)
	{
		//latest_item_image_1
		//Load data to place in Email
		$vars = array();
		$shopBevelHelper = Mage::helper("bevel/shopbevel");
		$collection = Mage::getModel("catalog/category")->getCollection()->addAttributeToFilter("appear_in_vote_email", $this->VAL_TRUE)->getFirstItem();
		$collectionProducts = $collection->getProductCollection()->addAttributeToSelect("image")->getData();
		shuffle($collectionProducts);
		$PRODUCT_LIMT = 4;
		$count = 1;
		foreach($collectionProducts as $collectionProduct)
		{
			$collectionProduct = Mage::getModel("catalog/product")->load($collectionProduct['entity_id']);
			$designerCategory = $shopBevelHelper->getProductDesigner($collectionProduct);
			$vars['latest_item_link_'.$count] = $collectionProduct->getProductUrl();
			$vars['latest_item_profile_'.$count] = $shopBevelHelper->getCategoryThumbnail($designerCategory);
			$vars['latest_item_price_'.$count] = $this->getItemPrice($collectionProduct);
			$vars['latest_item_name_'.$count] = $designerCategory->getName();
			$vars['latest_item_location_'.$count] = $designerCategory->getLocation();
			$vars['latest_item_image_'.$count] = (string)Mage::helper('catalog/image')->init($collectionProduct, 'small_image')->resize($this->VOTE_EMAIL_IMAGE_WIDTH, $this->VOTE_EMAIL_IMAGE_HEIGHT);

			$count++;
			if($count > $PRODUCT_LIMT)
			{
				break;
			}
		}

		//Get lastest we're making this products
		$productAttrs = array('price', 'special_price','small_image');
		$lastestMakingProducts = Mage::getModel("catalog/product")->getCollection()
		->addAttributeToSelect($productAttrs)
		->setOrder("entity_id", "DESC")
		//->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
		->addAttributeToFilter('is_made_by_bevel', $this->VAL_TRUE)
		->setPageSize($PRODUCT_LIMT);

		$count = 1;
		foreach($lastestMakingProducts as $product)
		{
			$designerCategory = $shopBevelHelper->getProductDesigner($product);
			$vars['making_item_link_'.$count] = $product->getProductUrl();
			$vars['making_item_profile_'.$count] = $shopBevelHelper->getCategoryThumbnail($designerCategory);
			$vars['making_item_price_'.$count] = $this->getItemPrice($product);
			$vars['making_item_name_'.$count] = $designerCategory->getName();
			$vars['making_item_location_'.$count] = $designerCategory->getLocation();
			$vars['making_item_image_'.$count] = (string)Mage::helper('catalog/image')->init($product, 'small_image')->resize($this->VOTE_EMAIL_IMAGE_WIDTH, $this->VOTE_EMAIL_IMAGE_HEIGHT);
			$count++;
		}

		//send vote email
		$emailHelper = Mage::helper("bevel/email");
		$emailHelper->sendEmail($emailHelper->EMAIL_TEMPLATE_VOTE, $email, $vars);
	}

	function getItemPrice($product)
	{
		$mainprice = $product->getPrice();
		$specialprice = $product->getSpecialPrice();
		$price = $specialprice ? '': $mainprice;
		return Mage::helper('core')->currency($price);
	}

	function checkForDesignerProfile($designerCategory)
	{
		if($designerCategory->getDesignerId())
		{
			$designer = new Varien_Object();
			$designer->setId($designerCategory->getDesignerId());
			$profileUrl = $this->getProfileUrl($designer);
		}
		else
		{
			$profileUrl = '';
		}
		return $profileUrl;
	}

	function getDesignRewriteUrl($designId)
	{
		$rewriteUrl = "";
		$idPath = $this->DESIGN_REWRITE_ID_PATH.$designId;
		$rewriteModel = Mage::getModel('core/url_rewrite')->loadByIdPath($idPath);
		if($rewriteModel->getId())
		{
			$rewriteUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).$rewriteModel->getRequestPath();
		}
		return $rewriteUrl;
	}

	//Function to get designs stats between dates
	function getDesignStats($id, $dateFrom, $dateTo, $stats = array())
	{
		$designStats = new Varien_Object();
		//echo 'From '. $dateFrom .'</br></br>';
		//echo 'To '. $dateTo .'</br></br>';

		if(empty($stats))
		{
			$designStats->setCommentCount($this->getCommentCount($id, $dateFrom, $dateTo));
			$designStats->setViewCount($this->getViewCount($id, $dateFrom, $dateTo));
			$designStats->setVoteCount($this->getVoteCount($id, $dateFrom, $dateTo));
		}
		else
		{
			foreach($stats as $stat)
			{
				switch ($stat)
				{
					case 'comments':
						$designStats->setCommentCount($this->getCommentCount($id, $dateFrom, $dateTo));
					break;
					case 'views':
						$designStats->setViewCount($this->getViewCount($id, $dateFrom, $dateTo));
					break;
					case 'vote':
						$designStats->setVoteCount($this->getVoteCount($id, $dateFrom, $dateTo));
					break;
				}
			}
		}

		return $designStats;
	}

	private function getCommentCount($id, $dateFrom, $dateTo)
	{
		$comments = Mage::getModel($this->DESIGN_COMMENTS_MODEL_PATH)->getCollection()->addFieldToFilter('design_id', $id);
		if($dateFrom)
		{
			$comments->addFieldToFilter('created_on', array('datetime' => true, 'from' => $dateFrom));
		}
		if($dateTo)
		{
			$comments->addFieldToFilter('created_on', array('datetime' => true, 'to' => $dateTo));
		}
		return $comments->count();
	}

	private function getViewCount($id, $dateFrom, $dateTo)
	{
		$views = Mage::getModel($this->DESIGN_VIEWS_MODEL_PATH)->getCollection()->addFieldToFilter('design_id', $id);
		if($dateFrom)
		{
			$views->addFieldToFilter('created_on', array('datetime' => true, 'from' => $dateFrom));
		}
		if($dateTo)
		{
			$views->addFieldToFilter('created_on', array('datetime' => true, 'to' => $dateTo));
		}
		return $views->count();
	}

	private function getVoteCount($id, $dateFrom, $dateTo)
	{
		$votes = Mage::getModel($this->DESIGN_VOTES_MODEL_PATH)->getCollection()->addFieldToFilter('design_id', $id);
		if($dateFrom)
		{
			$votes->addFieldToFilter('created_on', array('datetime' => true, 'from' => $dateFrom));
		}
		if($dateTo)
		{
			$votes->addFieldToFilter('created_on', array('datetime' => true, 'to' => $dateTo));
		}
		return $votes->count();
	}

	public function checkForChallenge($product)
	{
		if($product->getChallengeId())
		{
			$challenge = Mage::getModel($this->CHALLENGES_MODEL_PATH)->load($product->getChallengeId());
			return $challenge;
		}
		else if($product->getDesignId())
		{
			//Fallback for designs converted before addition of challenge_id attribute
			$design = Mage::getModel($this->DESIGNS_MODEL_PATH)->load($product->getDesignId());
			$challenge = Mage::getModel($this->CHALLENGES_MODEL_PATH)->load($design->getChallengeId());
			$product->setChallengeId($challenge->getId())->save();
			return $challenge;
		}
		else
		{
			return false;
		}
	}

	public function getStoreCredit($customerId = 0)
	{
		if(Mage::helper("customer")->isLoggedIn())
		{
			if(!$customerId)
			{
				$customerId = Mage::helper("customer")->getCustomer()->getId();
			}
			$balance = Mage::getModel('enterprise_customerbalance/balance')->load($customerId, 'customer_id');
			$amt = $balance->getAmount() ? '' : 0;
		}
		else
		{
			$amt = 0;
		}
		return $amt;
	}
}
?>
