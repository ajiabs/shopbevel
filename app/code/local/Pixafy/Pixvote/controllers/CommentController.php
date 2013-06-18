<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Pixafy_Pixvote_CommentController extends Mage_Core_Controller_Front_Action 
{	
	
	//Loads layout for ajax call
	function getCommentsAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	function addCommentAction()
	{
		$ERROR_NOT_LOGGED_IN = $this->__("Please login to post a comment");
		$ERROR_NO_DATA = $this->__("No data has been passed");
		$ERROR_ADD_COMMENT = $this->__("An error has occurred while adding the comment. Please try again");
		//Add comment only if user is logged in
		$json = array();
		if(Mage::helper("customer")->isLoggedIn())
		{
			$commentData = $this->getRequest()->getParam("comment");
			if(is_array($commentData))
			{
				$commentData['customer_id'] = Mage::helper("customer")->getCustomer()->getId();
				$commentRec = Mage::getModel("pixvote/comments")->setData($commentData);
				$json['id'] = $commentData['design_id'];
				if($commentRec->save())
				{
					//Success msg here
					$json['comment'] = $commentData['comment'];
					$json['count'] = Mage::getModel("pixvote/comments")->getCollection()->addFieldToFilter("design_id", $commentData['design_id'])->count();
					$json['author'] = Mage::helper("customer")->getCustomer()->getName();
					$json['image'] = Mage::helper("pixvote")->getProfileImage(Mage::helper("customer")->getCustomer());
				}
				else
				{
					//Error msg here
					$json['error'] = $ERROR_ADD_COMMENT;
				}
			}
			else
			{
				//Error msg here
				$json['error'] = $ERROR_NO_DATA;
			}
		}
		else
		{
			//Error msg here
			$json['error'] = $ERROR_NOT_LOGGED_IN;
		}
		echo json_encode($json);
	}
}
?>
