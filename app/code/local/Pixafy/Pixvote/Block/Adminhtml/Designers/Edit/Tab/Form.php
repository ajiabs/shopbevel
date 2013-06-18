<?php

class Pixafy_Pixvote_Block_Adminhtml_Designers_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		//New form Object
		//Prefix for form data
		$form = new Varien_Data_Form();
		$inputPrefix = "designer";
		$designer = Mage::registry("designer_data");
		$this->setForm($form);
		$dateTimeFormat = 'yyyy-MM-dd H:mm';
		$calImage = 'images/grid-cal.gif';
		$fieldset = $form->addFieldset('pixvote_form', array('legend' => Mage::helper('pixvote')->__('Designer Information')));
		$defaultTime = '0000-00-00 00:00:00';
		//Form fields
		$fieldset->addField('entity_id', 'hidden', array(
			'name'	=> $inputPrefix.'[entity_id]',
			'value'	=> $challenge->getId()
		));

		
		$fieldset->addField('fb_url', 'text', array(
			'name' => $inputPrefix.'[fb_url]',
			'label' => Mage::helper('pixvote')->__('Facebook Url'),
//			'class' => 'required-entry',
//			'required' => true,
			'value'	=> $challenge->getFbUrls()
		));

		return parent::_prepareForm();
	}
}