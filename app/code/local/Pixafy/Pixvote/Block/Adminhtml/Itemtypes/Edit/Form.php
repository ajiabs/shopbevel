<?php
class Pixafy_Pixvote_Block_Adminhtml_Itemtypes_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(array(
			'id'		=>	'edit_form',
			'action'	=>	$this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
			'method'	=>	'post',
			'enctype' => 'multipart/form-data',
		));
		$itemType = Mage::registry("item_type_data");
		$inputPrefix = "itemType";
		$form->setUseContainer(true);
		$this->setForm($form);	
		$fieldset = $form->addFieldset('pixvote_form', array('legend' => Mage::helper('pixvote')->__('Item Type Information')));
		
		//Form fields
		$fieldset->addField('itemtype_id', 'hidden', array(
			'name'	=> $inputPrefix.'[id]',
			'value'	=> $itemType->getId()
		));
		
		$fieldset->addField('name', 'text', array(
			'name' => $inputPrefix.'[name]',
			'label' => 'Item Type Name',
			'class' => 'required-entry',
			'required' => true,
			'value'	=> $itemType->getName()
		));
		
		return parent::_prepareForm();
	}
}