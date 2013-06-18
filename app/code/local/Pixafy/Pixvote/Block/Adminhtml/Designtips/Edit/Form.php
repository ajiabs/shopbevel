<?php
class Pixafy_Pixvote_Block_Adminhtml_Designtips_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(array(
			'id'		=>	'edit_form',
			'action'	=>	$this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
			'method'	=>	'post',
			'enctype' => 'multipart/form-data',
		));
		$tip = Mage::registry("designtip_data");
		$inputPrefix = "designtip";
		$form->setUseContainer(true);
		$this->setForm($form);	
		$pixvoteHelper =  Mage::helper('pixvote');
		$dateTimeFormat = 'yyyy-MM-dd';
		$default = '0000-00-00';
		$fieldset = $form->addFieldset('pixvote_form', array('legend' => $pixvoteHelper->__('Tip of the day')));
		
		//Form fields
		$fieldset->addField('id', 'hidden', array(
			'name'	=> $inputPrefix.'[id]',
			'value'	=> $tip->getId()
		));
		
		$fieldset->addField('customer_id', 'text', array(
			'name'	=> $inputPrefix.'[customer_id]',
			'label' => $pixvoteHelper->__('Designer ID'),
			'value'	=> $tip->getCustomerId()
		));
		
		$fieldset->addField('send_date', 'hidden', array(
			'name'	=> $inputPrefix.'[send_date]',
			'label' => $pixvoteHelper->__('Date To Use Tip'),
			//'format'    => $dateTimeFormat,
			//'image' => $this->getSkinUrl('images/grid-cal.gif'),
			'value'	=> $tip->getSendDate() != $default ? $tip->getSendDate() : ''
		));

		$fieldset->addField('text', 'textarea', array(
			'name'	=> $inputPrefix.'[text]',
			'label' => $pixvoteHelper->__('Text'),
			'value'	=> $tip->getText()
		));
		
		
		return parent::_prepareForm();
	}
}