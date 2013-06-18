<?php
 class Pixafy_Pixvote_Model_Customer extends Mage_Customer_Model_Customer
 {
	 function getLocation()
	 {
		$location = "";
		$city = trim($this->getDesignerCity());
		$stateId = trim($this->getDesignerState());
		$zip = trim($this->getDesignerZip());
		$countryId = trim($this->getDesignerCountry());
		$pixvoteHelper = Mage::helper("pixvote");
		$US_CODE = "US";
		if($city)
		{
			$location .= $city;
		}

		if($stateId)
		{
			$state = Mage::getModel('directory/region')->load($stateId);
			if($countryId !== $US_CODE)
			{
				$location .= ', '.$state->getName();
			}
			else
			{
				$location .= ', '.$state->getCode();
			}
		}

		/*if($zip)
		{
			$location .= ', '.$zip;
		}
		*/
		if($countryId && $countryId !== $US_CODE)
		{
			$country = Mage::getModel('directory/country')->load($countryId);
			$location .= ' '.$country->getName();
		}
		return $location;
	 }

	 function checkIfProfileIsCompleted()
	 {
		 return $this->getProfileImage()
				 && $this->getFirstname()
				 && $this->getLastname();
				/*&& $this->getLocation()
				&& $this->getDescription()
				&& $this->getSpecialties()
				&& $this->getWebsiteUrl()
				&& $this->getFbUrl()
				&& $this->getTwitterUrl()
				&& $this->getPinterestUrl();*/
	 }
 }
?>
