<?php

class Pixafy_Registeration_Block_Adminhtml_Waitlist_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		    {
		        $form = new Varien_Data_Form();
		        $this->setForm($form);
		        $fieldset = $form->addFieldset('entity_id', array('legend'=>Mage::helper('registeration')->__('Customer information')));
		       
		        $fieldset->addField('cust_email', 'text', array(
		            'label'     => Mage::helper('registeration')->__('Email'),
		            'class'     => 'required-entry',
		            'required'  => true,
		            'name'      => 'cust_email',
		        ));
		        
		        $fieldset->addField('first_name', 'text', array(
		        		'label'     => Mage::helper('registeration')->__('First Name'),
		        		'class'     => 'required-entry',
		        		'required'  => true,
		        		'name'      => 'first_name',
		        ));
		        
		        $fieldset->addField('last_name', 'text', array(
		        		'label'     => Mage::helper('registeration')->__('Last Name'),
		        		'class'     => 'required-entry',
		        		'required'  => true,
		        		'name'      => 'last_name',
		        ));
		 
		        $fieldset->addField('actived', 'select', array(
		            'label'     => Mage::helper('registeration')->__('Status'),
		            'name'      => 'actived',
		            'values'    => array(
		                array(
		                    'value'     => 1,
		                    'label'     => Mage::helper('registeration')->__('Active'),
		                ),
		 
		                array(
		                    'value'     => 0,
		                    'label'     => Mage::helper('registeration')->__('Inactive'),
		                ),
		            ),
		        ));
		       
		        if ( Mage::getSingleton('adminhtml/session')->getRegisterationData() )
		        {
		            $form->setValues(Mage::getSingleton('adminhtml/session')->getRegisterationData());
		            Mage::getSingleton('adminhtml/session')->setRegisterationData(null);
		        } elseif ( Mage::registry('registeration_data') ) {
		            $form->setValues(Mage::registry('registeration_data')->getData());
		        }
		        return parent::_prepareForm();
		    }
}
