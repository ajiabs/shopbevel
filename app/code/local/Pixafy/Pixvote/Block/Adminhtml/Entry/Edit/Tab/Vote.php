<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Pixafy_Pixvote_Block_Adminhtml_Entry_Edit_Tab_Vote extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		//New form Object
		//Prefix for form data
		$form = new Varien_Data_Form();
		$inputPrefix = "entry";
		$entry = Mage::registry("entry_data");
		$this->setForm($form);
		$fieldset = $form->addFieldset('pixvote_form', array('legend' => Mage::helper('pixvote')->__('Entry Vote Time')));
		$dateTimeFormat = 'yyyy-MM-dd H:mm';
		$calImage = 'images/grid-cal.gif';
		//Form fields
		$default = '0000-00-00 00:00:00';
		$fieldset->addField('vote_start_time', 'date', array(
			'name' => $inputPrefix.'[vote_start_time]',
			'label' => 'Start Time',
			'value'	=> $entry->getVoteStartTime() == $default ? '' : $entry->getVoteStartTime(),
                        'format'    => $dateTimeFormat,
                        'time'      => true,
                        'image'     => $this->getSkinUrl($calImage),
		));
		
		$fieldset->addField('vote_end_time', 'date', array(
			'name' => $inputPrefix.'[vote_end_time]',
			'label' => 'End Time',
			'value'	=>  $entry->getVoteEndTime() == $default ? '' : $entry->getVoteEndTime(),
                        'format'    => $dateTimeFormat,
                        'time'      => true,
                        'image'     => $this->getSkinUrl($calImage),
		));
		
		return parent::_prepareForm();
	}
}
?>
