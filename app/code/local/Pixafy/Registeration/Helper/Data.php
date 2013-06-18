<?php
	class Pixafy_Registeration_Helper_Data extends Mage_Core_Helper_Abstract {
		
		public function createRank(){
			$model = Mage::getModel('registeration/waitlist');
			//echo("aaa");
			//exit;
			$collection = $model->getCollection()->setPageSize(1);
			$collection->getSelect()->order("wait_rank desc");
			foreach($collection as $c){
				$maxRank = $c->getWaitRank();
				break;
			}			
			//$maxRank = $collection->getWaitRank();
			$rank = $maxRank+1;
			return $rank;	
		}
		
		public function createUniqueLink($email){
			
			$urlkey = md5($email.time());	
			$link = Mage::getUrl('registeration/link/process').'?invite='.$urlkey;

			try{
				$profile = Mage::getModel('registeration/waitlist')->loadByEmail($email);
				$profile->setCustKey($urlkey);
				$profile->save();
			}catch(Exception $e){
				echo $e->getMessage();
			}
			
			return $link;
		}
		
		
		public function createCustomer($profileId){
			$profile = Mage::getModel('registeration/waitlist')->load($profileId);
			$customerEntityType = Mage::getModel('eav/entity')->setType('customer')->getTypeId();
			$website = Mage::getModel('core/website')->load( 'default', 'code');
			$websiteID = $website->getId();
		    $customer = Mage::getModel('customer/customer');
		    $password = $profile->getPassword();
		    $email = $profile->getCustEmail();
		    $customer->setWebsiteId(1);
		    //$customer->setWebsiteCode ( 'default' );
		    $customer->loadByEmail($email);
		    if(!$customer->getId()) {
		        $groups = Mage::getResourceModel('customer/group_collection')->getData();
		        $groupID = '1';
		
		        $customer->setData( 'group_id', $groupID );
		        $customer->setEmail($email);
		        $customer->setFirstname($profile->getFirstName());
		        $customer->setLastname($profile->getLastName());
		        $customer->setPasswordHash(md5($password));
		
		        $customer->setConfirmation(null);
		        
		        $customer->setWebsiteId(1);
		        $customer->save();	
		        return $customer->getId();
		    }
		}
		
		public function verifyEmail($email){
			
			$profile = Mage::getModel('registeration/waitlist')->loadByEmail($email);
			if(!!$profile && !!$profile->getId()){
				return false;
			}
			return true;
		}
		
}