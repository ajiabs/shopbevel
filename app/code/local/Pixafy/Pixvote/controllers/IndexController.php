<?php
class Pixafy_Pixvote_IndexController extends Mage_Core_Controller_Front_Action
{
	function indexAction()
	{
		$pixvoteHelper = Mage::helper("pixvote");
		$id = $this->getRequest()->getParam("id");
		$now = Mage::getModel('core/date')->timestamp(time());
		$dateFormat = 'Y-m-d  H:i:s';
		$todayTime = strtotime(date($dateFormat, $now));
		if($id)
		{
			$challenge = $this->_loadChallenge();
			$voteStartSeconds = strtotime($challenge->getVoteStartTime());
			if(!$challenge->getId() || !$challenge->getIsVisible())
			{
				Mage::getSingleton('customer/session')->addError(Mage::helper('customer')->__("Challenge not found"));
				return $this->_redirect('');
			}
			else if(($todayTime - $voteStartSeconds) < 0)
			{
				Mage::getSingleton('customer/session')->addError(Mage::helper('customer')->__("This challenge is not available for voting"));
				return $this->_redirect('');
			}
			else
			{
				Mage::register($pixvoteHelper->CURRENT_VOTE_CHALLENGE_KEY, $challenge);
				$this->loadLayout();
				$this->_initLayoutMessages('customer/session');
				$this->renderLayout();
			}
		}
		else
		{
			//Get all challenges
			$challenges = $pixvoteHelper->getAvailableChallenges();
			$challenges = $challenges->getItems();
			Mage::register($pixvoteHelper->CURRENT_VOTE_CHALLENGE_KEY, $challenges);
			$this->loadLayout();
			$this->_initLayoutMessages('customer/session');
			$this->renderLayout();
		}
	}

	function addVoteAction()
	{
		$ERROR_NOT_LOGGED_IN = $this->__("Please login to vote");
		$ERROR_NO_DATA = $this->__("No data has been passed");
		$ERROR_ADD_VOTE = $this->__("An error has occurred while adding the vote. Please try again");
		$ERROR_ALREADY_VOTED = $this->__("You have voted for this product already");
		$id = $this->getRequest()->getParam("id");
		$customer = Mage::helper("customer")->getCustomer();
		$customerId = $customer->getId();
		$json = array();
		$pixvoteHelper = Mage::helper("pixvote");
		if(!Mage::helper("customer")->isLoggedIn())
		{
			$json['error'] = $ERROR_NOT_LOGGED_IN;
		}
		else if(!$id)
		{
			$json['error'] = $ERROR_NO_DATA;
		}
		else
		{
			$existing = Mage::getModel($pixvoteHelper->DESIGN_VOTES_MODEL_PATH)->getCollection()
				->addFieldToFilter("customer_id", $customerId)
				->addFieldToFilter("design_id", $id)
				->count();
			if($existing == $pixvoteHelper->VOTE_LIMIT)
			{
				$json['error'] = $ERROR_ALREADY_VOTED;
			}
			else
			{
				//Check if user has ever voted
				$allVotes = $pixvoteHelper->getUserVotes();
				$isFirstVote = empty($allVotes);
				
				//prepareData
				$data = array(
					"customer_id" => $customerId,
					"design_id" => $id
				);
				$voteRec = Mage::getModel($pixvoteHelper->DESIGN_VOTES_MODEL_PATH)->setData($data);
				if($voteRec->save())
				{
					//Update vote count
					$voteCountModel = Mage::getModel("core/config_data")->load($pixvoteHelper->VOTE_COUNT_PATH,"path");
					$count = $voteCountModel->getValue();
					$count++;
					$voteCountModel->setValue($count)->save();
					
					//reinit config
					//Mage::getConfig()->reinit();
					
					//Send vote email if user has not recieved one before
					if(!$customer->getVoteEmailSent())
					{
						$pixvoteHelper->sendVoteEmail($customer->getEmail());
						$customer->setVoteEmailSent($pixvoteHelper->VAL_TRUE)->save();
					}
					
					//Update vote count
					$design = Mage::getModel($pixvoteHelper->DESIGNS_MODEL_PATH)->load($id);
					$voteCnt = $design->getVotes();
					if($voteCnt)
					{
						$voteCnt++;
					}
					else
					{
						$voteCnt = Mage::getModel("pixvote/votes")->getCollection()->addFieldToFilter("design_id", $id)->count();
					}
					$design->setVotes($voteCnt)->save();
					
					//Prepare output
					$json['id'] = $id;
					$json['vote_count'] = $voteCnt;
					$json['total_vote_count'] = $count;
					
					//Add store credit if it was first vote and they did not get gift certificate email
					if($isFirstVote && !$customer->getReceivedVoteCredit() && !$customer->getGiftEmailSent())
					{
							$data = array();
							$data['amount_delta'] = $pixvoteHelper->VOTE_STORE_CREDIT_AMOUNT;
							$data['comment'] = "First vote store credit";
							$customer->setCustomerBalanceData($data);
							$observer = new Varien_Object();
							$observer->setCustomer($customer);
							Mage::getModel("enterprise_customerbalance/observer")->customerSaveAfter($observer);
							$customer->setReceivedVoteCredit($pixvoteHelper->VAL_TRUE);
							$customer->setGiftEmailSent($pixvoteHelper->VAL_TRUE);
							$customer->save();
							$json['store_credit'] = $pixvoteHelper->getStoreCredit($customer->getId());
					}
					
					//Call share to store share if passed in
					$this->_storeShare($data);
				}
				else
				{
					$json['error'] = $ERROR_ADD_VOTE;
				}
			}
		}
		echo json_encode($json);
	}

	function ajaxDesignAction()
	{
		$id = $this->getRequest()->getParam("id");
		$pixvoteHelper = Mage::helper("pixvote");
		if($id)
		{
			$challenge = $this->_loadChallenge();
			Mage::register($pixvoteHelper->CURRENT_VOTE_CHALLENGE_KEY, $challenge);
		}
		else
		{
			//Get all challenges
//			$now = Mage::getModel('core/date')->timestamp(time());
//			$dateFormat = 'Y-m-d  H:i:s';
//			$today = date($dateFormat, $now);
//			$challenges = Mage::getModel($pixvoteHelper->CHALLENGES_MODEL_PATH)->getCollection()
//			->addFieldToFilter("is_visible", $pixvoteHelper->IS_VISIBLE)
//			->addFieldToFilter('vote_start_time', array('datetime' => true, 'to' => $today))
//			->addFieldToFilter('vote_end_time', array('datetime' => true, 'from' => $today));
//			$challenges = $challenges->getItems();
			$challenges = $pixvoteHelper->getAvailableChallenges();
			$challenges = $challenges->getItems();
			Mage::register($pixvoteHelper->CURRENT_VOTE_CHALLENGE_KEY, $challenges);
		}
		$this->loadLayout();
		$this->renderLayout();
	}

	function ajaxfeaturedAction()
	{
		$pixvoteHelper = Mage::helper("pixvote");
		$challenge = $this->_loadChallenge();
		Mage::register(Mage::helper("pixvote")->CURRENT_VOTE_CHALLENGE_KEY, $challenge);
		$this->loadLayout();
		$this->renderLayout();
	}

	function viewAction()
	{
		$id = $this->getRequest()->getParam("id");
		$pixvoteHelper = Mage::helper("pixvote");
		$design = Mage::getModel("pixvote/designs")->getCollection()
		->addFieldToFilter("id", $id)
		->addFieldToFilter("status_id", array( "nin" => array($pixvoteHelper->ENTRY_STATUS_IN_REJECTED, $pixvoteHelper->ENTRY_STATUS_IN_PENDING)))
		->getFirstItem();
		
		if(!$id || !$design->getId())
		{
			Mage::getSingleton('customer/session')->addError($pixvoteHelper->__("Design not found"));
			return $this->_redirect('vote');
		}
		else
		{
			//Update views if necessary
			$now = $pixvoteHelper->now();
			$todayDateTime = new DateTime($now);
			$yesterdayDateTime = clone $todayDateTime;
			$yesterdayDateTime->modify('-1 day');
			$dateFormat = 'Y-m-d  H:i:s';
			$ipAddress = Mage::helper('core/http')->getRemoteAddr();
			$visited = Mage::getModel($pixvoteHelper->DESIGN_VIEWS_MODEL_PATH)->getCollection()
			->addFieldToFilter('design_id',$design->getId())
			->addFieldToFilter('created_on', array('datetime' => true, 'from' => $yesterdayDateTime->format($dateFormat)))
			->addFieldToFilter('created_on', array('datetime' => true, 'to' => $todayDateTime->format($dateFormat)));
			
			//If logged in, look for customer id
			//otherwise, look for ip address
			if(Mage::helper("customer")->isLoggedIn())
			{
				$customer = Mage::helper("customer")->getCustomer();
				$visited->addFieldToFilter("customer_id",$customer->getId());
			}
			else
			{
				$visited->addFieldToFilter("ip_address", $ipAddress);
			}
			
			if(!$visited->count())
			{
				$visitRec = Mage::getModel($pixvoteHelper->DESIGN_VIEWS_MODEL_PATH);
				if(isset($customer))
				{
					$visitRec->setCustomerId($customer->getId());
				}
				$visitRec->setDesignId($design->getId());
				$visitRec->setIpAddress($ipAddress);
				$visitRec->save();
				
			}
			
			$challenge = Mage::getModel($pixvoteHelper->CHALLENGES_MODEL_PATH)->load($design->getChallengeId());
			Mage::register($pixvoteHelper->CURRENT_SUBMISSION, $design);
			Mage::register($pixvoteHelper->CURRENT_VOTE_CHALLENGE_KEY, $challenge);
			$this->loadLayout();
			$this->renderLayout();
		}
	}
	
	public function getVotesAction()
	{
		$pixvoteHelper = Mage::helper("pixvote");
		$voteCount = Mage::getModel("core/config_data")->load($pixvoteHelper->VOTE_COUNT_PATH,"path")->getValue();
		$json = array('total_vote_count' => $voteCount);
		echo json_encode($json);
	}

//	public function addCreditAction()
//	{
//		$customer = Mage::helper("customer")->getCustomer();
//		if(!$customer->getReceivedVoteCredit())
//		{
//			$data = array();
//			$data['amount_delta'] = 10;
//			$data['comment'] = "store credit";
//			$customer->setCustomerBalanceData($data);
//			$observer = new Varien_Object();
//			$observer->setCustomer($customer);
//			Mage::getModel("enterprise_customerbalance/observer")->customerSaveAfter($observer);
//			//var_dump();
//		}
//	}
	
	//Add design share to DB if design exists
	function shareAction()
	{
		$id = $this->getRequest()->getParam("id");
		$pixvoteHelper = Mage::helper("pixvote");
		$design = Mage::getModel("pixvote/designs")->getCollection()
		->addFieldToFilter("id", $id)
		->addFieldToFilter("status_id", array( "nin" => array($pixvoteHelper->ENTRY_STATUS_IN_REJECTED, $pixvoteHelper->ENTRY_STATUS_IN_PENDING)))
		->getFirstItem();
		$json = array();
		if(!$id || !$design->getId())
		{
			$json['error'] = $pixvoteHelper->__("Design not found");
		}
		else
		{
			//success
			$data = array("design_id" => $id);
			if(Mage::helper("customer")->isLoggedIn())
			{
				$customer = Mage::helper("customer")->getCustomer();
				$data["customer_id"] = $customer->getId();
			}
			$this->_storeShare($data);
		}
		echo json_encode($json);
	}
	
	
	//Create tracking record for popup
	function trackAction()
	{
		if(Mage::helper("customer")->isLoggedIn())
		{
			$post = $this->getRequest()->getPost();
			if(isset($post['subject']) && isset($post['version']))
			{
				$pixvoteHelper = Mage::helper("pixvote");
				$customer = Mage::helper("customer")->getCustomer();
				$trackingRec = Mage::getModel($pixvoteHelper->TRACKING_MODEL_PATH);
				$trackingRec->setSubject($post['subject']);
				$trackingRec->setVersion($post['version']);
				$trackingRec->setCustomerId($customer->getId());
				if($trackingRec->save())
				{
					//add id to session in case we need to update
					$cookie = Mage::getModel('core/cookie');
					$trackingKey = str_replace(" ", "_",$post['subject']);
					$cookie->set($trackingKey."_id", Mage::getSingleton("core/encryption")->encrypt($trackingRec->getId()), true);
					$cookie->set($trackingKey."_subject", $trackingRec->getSubject());
					
				}
			}
		}
	}
	
	function trackSuccessAction()
	{
		if(Mage::helper("customer")->isLoggedIn())
		{
			$post = $this->getRequest()->getPost();
			if(isset($post['subject']))
			{
				$trackingKey = str_replace(" ", "_",$post['subject']);
				$cookie = Mage::getModel('core/cookie');
				$id = Mage::getSingleton("core/encryption")->decrypt($cookie->get($trackingKey."_id"));
				if($id)
				{
					$pixvoteHelper = Mage::helper("pixvote");
					$trackingRec = Mage::getModel($pixvoteHelper->TRACKING_MODEL_PATH)->load($id);
					$trackingRec->setOutcome($pixvoteHelper->VAL_TRUE);
					$trackingRec->setUpdatedOn($pixvoteHelper->now());
					if($trackingRec->save())
					{
						$cookie->delete($trackingKey."_id");
						$cookie->delete($trackingKey."_subject");
					}
				}
			}
		}
	}
	
	function flagAction()
	{
		$pixvoteHelper = Mage::helper("pixvote");
		$shopBevelHelper = Mage::helper("shopbevel");
	}
	protected function _loadChallenge()
	{
		$pixvoteHelper = Mage::helper("pixvote");
		$id = $this->getRequest()->getParam("id");
		if(!$id)
		{
			//Load Default challenge
			$challenge = Mage::getModel($pixvoteHelper->CHALLENGES_MODEL_PATH)->load($pixvoteHelper->IS_DEFAULT, 'is_default');
		}
		else
		{
			$challenge = Mage::getModel($pixvoteHelper->CHALLENGES_MODEL_PATH)->load($id);
			//->addFieldToFilter('vote_start_time', array('datetime' => true, 'to' => $today))
			//->addFieldToFilter('vote_end_time', array('datetime' => true, 'from' => $today))
		}

		return $challenge;
	}
	
	
	protected function _storeShare($data)
	{
		//Check for share was due to share
		$pixvoteHelper = Mage::helper("pixvote");
		$shareType = $this->getRequest()->getParam("shareType");
		if(in_array($shareType, $pixvoteHelper->SHARE_TYPES))
		{
			$data['share_type'] = $shareType;
			$data['ip_address'] = Mage::helper('core/http')->getRemoteAddr();
			$shareRec = Mage::getModel($pixvoteHelper->DESIGN_SHARES_MODEL_PATH)->setData($data)->save();
		}
		else
		{
			return false;
		}
	}
}
?>
