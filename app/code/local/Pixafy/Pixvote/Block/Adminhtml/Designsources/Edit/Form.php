<?php
class Pixafy_Pixvote_Block_Adminhtml_Designsources_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(array(
			'id'		=>	'edit_form',
			'action'	=>	$this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
			'method'	=>	'post',
			'enctype' => 'multipart/form-data',
		));
		$pixvoteHelper =  Mage::helper('pixvote');
		$source = Mage::registry($pixvoteHelper->REGISTRY_DESIGNSOURCE_KEY);
		$inputPrefix = "designsource";
		$form->setUseContainer(true);
		$this->setForm($form);	
		$dateTimeFormat = 'yyyy-MM-dd';
		$fieldset = $form->addFieldset('pixvote_form', array('legend' => $pixvoteHelper->__('Submission Source')));
		
		//Form fields
		$fieldset->addField('id', 'hidden', array(
			'name'	=> $inputPrefix.'[id]',
			'value'	=> $source->getId()
		));

		$fieldset->addField('value', 'text', array(
			'name'	=> $inputPrefix.'[value]',
			'label' => $pixvoteHelper->__('Source'),
			'value'	=> $source->getValue()
		));
		
		
		return parent::_prepareForm();
	}
}