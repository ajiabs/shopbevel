<?php
class Pixafy_Bevel_Helper_Email extends Mage_Core_Helper_Abstract {
	
	public $EMAIL_TEMPLATE_SUBMISSION = 'Thanks for your submission.';
	public $EMAIL_TEMPLATE_VOTE = 'Thank You For Voting';
	public $EMAIL_TEMPLATE_DESIGN_APPROVED = 'You have been approved.';
	public $EMAIL_TEMPLATE_DESIGN_NOT_APPROVED = 'You were not approved.';
	public $EMAIL_TEMPLATE_HOW_IT_WORKS = "How It Works";
	public $EMAIL_TEMPLATE_GIFT_CERTIFICATE = "A little something to get started.";
	public $EMAIL_TEMPLATE_DAILY_STATS = "Daily Stats";
	public $EMAIL_TEMPLATE_VOTING_IN_SESSION = "Voting is now in session";
	public $EMAIL_TEMPLATE_PROFILE_NOT_DONE = "Your designer profile is not done.";
	public $EMAIL_TEMPLATE_PROFILE_DONE = "Thanks for filling out your profile.";
	public $EMAIL_TEMPLATE_PREPARE_TO_WIN = "Prepare To Win";
	public $EMAIL_TEMPLATE_WHILE_YOU_WAIT = "While You Wait";

	function sendEmail($templateId, $email, $vars = array())
	{
		$sender  = array(
			'name' => Mage::getStoreConfig('trans_email/ident_general/name', Mage::app()->getStore()->getId()),
			'email' => Mage::getStoreConfig('trans_email/ident_general/email', Mage::app()->getStore()->getId())
		);
		$emailTemplate = Mage::getModel('core/email_template')->loadByCode($templateId);
		$processedTemplate = $emailTemplate->getProcessedTemplate($vars);
		//echo $processedTemplate;
		//exit;
		try{
			$emailTemplate->setSenderEmail($sender['email']);
			$emailTemplate->setSenderName($sender['name']);
			$emailTemplate->send($email,$name, $vars, $sender['email']);
		}
		catch(Exception $e){
			echo $e->getMessage();
		}
	}
}
?>