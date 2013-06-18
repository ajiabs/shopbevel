<?php

class Pixafy_Pixvote_Block_Adminhtml_Entry_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		//New form Object
		//Prefix for form data
		$form = new Varien_Data_Form();
		$inputPrefix = "entry";
		$entry = Mage::registry("entry_data");
		$this->setForm($form);
		$helper = Mage::helper("pixvote");
		$fieldset = $form->addFieldset('pixvote_form', array('legend' => Mage::helper('pixvote')->__('Entry Information')));
		
		//Retrieve design statuses
		$statuses = Mage::getModel($helper->STATUS_MODEL_PATH)->getCollection()->setOrder("sort_order","ASC");
		$statusValues = array();
		foreach($statuses as $status)
		{
			$statusValues[] = array(
				'label' => $status->getValue(),
				'value' => $status->getId(),
				
			);
		}
		
		
		//Retrieve challenges
		$challenges = Mage::getModel($helper->CHALLENGES_MODEL_PATH)->getCollection();
		$challegeValues = array();
		foreach($challenges as $challenge)
		{
			$challegeValues[] = array(
				'label' => $challenge->getName(),
				'value' => $challenge->getId(),
				
			);
		}
		
		//Retrieve rejection types
		$rejections = Mage::getModel($helper->REJECTIONS_MODEL_PATH)->getCollection();
		$rejectionValues = array();
		foreach($rejections as $rejection)
		{
			$rejectionValues[] = array(
				'label' => $rejection->getReason(),
				'value' => $rejection->getId(),
				
			);
		}
		
		//Retrieve Items Types
		$itemTypes = Mage::getModel($helper->ITEM_TYPES_MODEL_PATH)->getCollection();
		$itemTypeValues = array();
		foreach($itemTypes as $itemType)
		{
			$itemTypeValues[] = array(
				'label' => $itemType->getName(),
				'value' => $itemType->getId(),
				
			);
		}
		
		//Retrieve colors
		$colors = $helper->getProductColors();
		$colors[] = array("label" => "N/A", "value" => 0);
		
		
		//Form fields
		$fieldset->addField('entry_id', 'hidden', array(
			'name'	=> $inputPrefix.'[id]',
			'value'	=> $entry->getId()
		));
		
		//Form fields
		$fieldset->addField('customer_id', 'text', array(
			'name'	=> $inputPrefix.'[customer_id]',
			'value'	=> $entry->getCustomerId(),
			'label' => 'Customer ID',
			'class' => 'required-entry',
			'required' => true,
		));
                
//                $fieldset->addField('firstname', 'text', array(
//			'name'	=> $inputPrefix.'[firstname]',
//			'value'	=> $entry->getFirstname(),
//			'label' => 'First Name',
//			'class' => 'required-entry',
//			'required' => true,
//		));
//                
//                $fieldset->addField('lastname', 'text', array(
//			'name'	=> $inputPrefix.'[lastname]',
//			'value'	=> $entry->getLastname(),
//			'label' => 'First Name',
//			'class' => 'required-entry',
//			'required' => true,
//		));
		
		$fieldset->addField('name', 'text', array(
			'name' => $inputPrefix.'[name]',
			'label' => 'Title',
			'class' => 'required-entry',
			'required' => true,
			'value'	=> $entry->getName()
		));
		
		$fieldset->addField('description', 'textarea', array(
			'name' => $inputPrefix.'[description]',
			'label' => 'Design story',
			'class' => 'required-entry',
			'required' => true,
			'value'	=> $entry->getDescription()
		));
		$fieldset->addField('materials', 'textarea', array(
			'name' => $inputPrefix.'[materials]',
			'label' => 'Materials',
			'class' => 'required-entry',
			'required' => true,
			'value'	=> $entry->getMaterials()
		));
		
		$willEmailImage = $entry->getWillEmailImages() ? "Yes" : "No";
		$fieldset->addField('will_email_images', 'note', array(
           'label' => $helper->__('Will email images?'),
           'text'  => $helper->__($willEmailImage),
			));
		
		$designSource = Mage::getModel($helper->DESIGN_SOURCES_MODEL_PATH)->load($entry->getSourceId());
		$fieldset->addField('design_source', 'note', array(
           'label' => $helper->__('How did you find us?'),
           'text'  => $helper->__($designSource->getValue()),
			));
		
		$fieldset->addField('staff_pick', 'checkbox', array(
			'name' => $inputPrefix.'[is_staff_pick]',
			'label' => 'Staff Pick',
			'value'	=> 1,
			'checked'=> $entry->getIsStaffPick() ?'':0
		));
		
		$fieldset->addField('is_featured', 'checkbox', array(
			'name' => $inputPrefix.'[is_featured]',
			'label' => 'Featured',
			'value'	=> 1,
			'checked'=> $entry->getIsFeatured() ?'':0
		));
		
		$fieldset->addField('school', 'textarea', array(
			'name' => $inputPrefix.'[school]',
			'label' => 'School',
			'value'	=> $entry->getSchool()
		));
		
		$fieldset->addField('price', 'text', array(
			'name' => $inputPrefix.'[price]',
			'label' => 'Suggested price',
			'class' => 'required-entry',
			'required' => true,
			'value'	=> $entry->getPrice()
		));
		
		$fieldset->addField('bevel_price', 'text', array(
			'name' => $inputPrefix.'[bevel_price]',
			'label' => 'Bevel price',
			//'class' => 'required-entry',
			//'required' => true,
			'value'	=> $entry->getBevelPrice()
		));
		
		$fieldset->addField('item_type_id', 'select', array(
			'name' => $inputPrefix.'[item_type_id]',
			'label'     => 'Item Type',
			'class' => 'required-entry',
			'required' => true,
			'values'    => $itemTypeValues,
			'value' => $entry->getItemTypeId()
		));
		$fieldset->addField('color', 'select', array(
			'name' => $inputPrefix.'[color]',
			'label'     => 'Color',
			'class' => 'required-entry',
			'required' => true,
			'values'    => $colors,
			'value' => $entry->getColor()
		));
		
		$fieldset->addField('challenge_id', 'select', array(
			'name' => $inputPrefix.'[challenge_id]',
			'label'     => 'Challenge',
			'class' => 'required-entry',
			'required' => true,
			'values'    => $challegeValues,
			'value' => $entry->getChallengeId()
		));
		
		$fieldset->addField('status_id', 'select', array(
			'name' => $inputPrefix.'[status_id]',
			'label'     => 'Design Status',
			'class' => 'required-entry',
			'required' => true,
			'values'    => $statusValues,
			'value' => $entry->getStatusId()
		));
		
		$fieldset->addField('rejection_reason', 'select', array(
			'name' => 'rejection_reason',
			'label'     => 'Rejection Reason',
		//	'style' => 'display:none',
			'values'    => $rejectionValues,
		));
		
		$fieldset->addField('votes', 'text', array(
			'name' => $inputPrefix.'[votes]',
			'label'     => 'Votes',
		//	'style' => 'display:none',
			'value'    => $entry->getVotes(),
		));
		return parent::_prepareForm();
	}
}