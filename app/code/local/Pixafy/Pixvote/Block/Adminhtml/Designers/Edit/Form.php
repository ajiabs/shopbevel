<?php
class Pixafy_Pixvote_Block_Adminhtml_Designers_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(array(
			'id'		=>	'edit_form',
			'action'	=>	$this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
			'method'	=>	'post',
			'enctype' => 'multipart/form-data',
		));
		$designer = Mage::registry("designer_data");
		$inputPrefix = "designer";
		$form->setUseContainer(true);
		$this->setForm($form);	
		$pixvoteHelper =  Mage::helper('pixvote');
		$fieldset = $form->addFieldset('pixvote_form', array('legend' => $pixvoteHelper->__('Designer Information')));
		
		//Country and Region
		$countries = Mage::getModel('directory/country_api')->items();
		$countryValues = array();
		foreach($countries as $country)
		{
			$countryValues[] = array(
				'label' => $country['name'],
				'value' => $country['country_id'],
				
			);
		}
		
		
		$currentCountry = $designer->getDesignerCountry() ?'': $pixvoteHelper->DEFAULT_COUNTRY;
		$regions = Mage::getModel('directory/region_api')->items($currentCountry);
		$regionValues = array();
		foreach($regions as $region)
		{
			$regionValues[] = array(
				'label' => $region['name'],
				'value' => $region['region_id'],
				
			);
		}
		
		if(empty($regionValues))
		{
			$regionValues[] = array(
				'label' => $pixvoteHelper->__("No states found"),
				'value' => '',
				
			);
		}
		
		//Form fields
		$fieldset->addField('entity_id', 'hidden', array(
			'name'	=> $inputPrefix.'[entity_id]',
			'value'	=> $designer->getId()
		));
		
//		if($designer->getProfileImage())
//        {
//			$imageUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$designer->getProfileImage();
//			$imageHtml='<a target="_blank" href="'.$imageUrl.'"><img src="'.$imageUrl.'" height="100" width="auto" /></a>';
//			$fieldset->addField('image', 'note', array(
//            'label' => $pixvoteHelper->__('Current profile image'),
//            'text'  => $pixvoteHelper->__($imageHtml),
//			));
//		}
		
		$fieldset->addField('profile_image', 'image', array(
			'name'	=> 'profile-picture[]',
			'label' => $pixvoteHelper->__('Profile image'),
			'value'	=> $designer->getProfileImage()
		));
		
		$fieldset->addField('firstname', 'text', array(
			'name'	=> $inputPrefix.'[firstname]',
			'label' => $pixvoteHelper->__('First name'),
			'value'	=> $designer->getFirstname()
		));
		
		$fieldset->addField('lastname', 'text', array(
			'name'	=> $inputPrefix.'[lastname]',
			'label' => $pixvoteHelper->__('Last name'),
			'value'	=> $designer->getLastname()
		));

//		$fieldset->addField('location', 'text', array(
//			'name' => $inputPrefix.'[location]',
//			'label' => $pixvoteHelper->__('Location'),
////			'class' => 'required-entry',
////			'required' => true,
//			'value'	=> $designer->getLocation()
//		));
		
		$fieldset->addField('description', 'textarea', array(
			'name' => $inputPrefix.'[description]',
			'label' => $pixvoteHelper->__('About Me'),
//			'class' => 'required-entry',
//			'required' => true,
			'value'	=> $designer->getDescription()
		));
		
		$fieldset->addField('designer_city', 'text', array(
			'name' => $inputPrefix.'[designer_city]',
			'label' => $pixvoteHelper->__('Designer city'),
			'value'	=> $designer->getDesignerCity()
		));
		
		$fieldset->addField('profile-state', 'select', array(
			'name' => $inputPrefix.'[designer_state]',
			'label' => $pixvoteHelper->__('Designer state'),
			'values' => $regionValues,
			'value'	=> $designer->getDesignerState()
		));
		
		$fieldset->addField('profile-country', 'select', array(
			'name' => $inputPrefix.'[designer_country]',
			'label' => $pixvoteHelper->__('Designer country'),
			'values' => $countryValues,
			'value'	=> $currentCountry
		));
		
		$fieldset->addField('fb_url', 'text', array(
			'name' => $inputPrefix.'[fb_url]',
			'label' => $pixvoteHelper->__('Facebook Url'),
//			'class' => 'required-entry',
//			'required' => true,
			'value'	=> $designer->getFbUrl()
		));
		$fieldset->addField('twitter_url', 'text', array(
			'name' => $inputPrefix.'[twitter_url]',
			'label' => $pixvoteHelper->__('Twitter Url'),
//			'class' => 'required-entry',
//			'required' => true,
			'value'	=> $designer->getTwitterUrl()
		));
		$fieldset->addField('pinterest_url', 'text', array(
			'name' => $inputPrefix.'[pinterest_url]',
			'label' => $pixvoteHelper->__('Pinterest Url'),
//			'class' => 'required-entry',
//			'required' => true,
			'value'	=> $designer->getPinterestUrl()
		));
		$fieldset->addField('website_url', 'text', array(
			'name' => $inputPrefix.'[website_url]',
			'label' => $pixvoteHelper->__('Website Url'),
//			'class' => 'required-entry',
//			'required' => true,
			'value'	=> $designer->getWebsiteUrl()
		));
		$fieldset->addField('specialties', 'text', array(
			'name' => $inputPrefix.'[specialties]',
			'label' => $pixvoteHelper->__('Specialties'),
//			'class' => 'required-entry',
//			'required' => true,
			'value'	=> $designer->getSpecialties()
		));
		
		$fieldset->addField('moodboard_url', 'text', array(
			'name' => $inputPrefix.'[moodboard_url]',
			'label' => $pixvoteHelper->__('Moodboard url'),
//			'class' => 'required-entry',
//			'required' => true,
			'value'	=> $designer->getMoodboardUrl()
		));
		return parent::_prepareForm();
	}
}