<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Pixafy_Pixvote_Block_Adminhtml_Challenges_Edit_Tab_Images extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		//New form Object
		//Prefix for form data
		$form = new Varien_Data_Form();
		$inputPrefix = "challenge-image";
		$entry = Mage::registry("challenge_data");
		$this->setForm($form);
		$fieldset = $form->addFieldset('pixvote_form', array('legend' => Mage::helper('pixvote')->__('Design Images')));
		$fieldset->addField('image', 'image', array(
          'label'     => Mage::helper('pixvote')->__('Image'),
          'required'  => false,
          'name'      => $inputPrefix.'[]',
		 ));
		return parent::_prepareForm();
	}
}
?>
