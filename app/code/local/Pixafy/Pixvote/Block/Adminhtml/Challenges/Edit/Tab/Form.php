<?php

class Pixafy_Pixvote_Block_Adminhtml_Challenges_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		//New form Object
		//Prefix for form data
		$form = new Varien_Data_Form();
		$inputPrefix = "challenge";
		$challenge = Mage::registry("challenge_data");
		$this->setForm($form);
		$dateTimeFormat = 'yyyy-MM-dd H:mm';
		$calImage = 'images/grid-cal.gif';
		$fieldset = $form->addFieldset('pixvote_form', array('legend' => Mage::helper('pixvote')->__('Challenge Information')));
		$defaultTime = '0000-00-00 00:00:00';
		$yesNoValues = array('1' => 'Yes','0' => 'No');
		//Form fields
		$fieldset->addField('challenge_id', 'hidden', array(
			'name'	=> $inputPrefix.'[id]',
			'value'	=> $challenge->getId()
		));

		
		$fieldset->addField('name', 'text', array(
			'name' => $inputPrefix.'[name]',
			'label' => Mage::helper('pixvote')->__('Challenge Name'),
			'class' => 'required-entry',
			'required' => true,
			'value'	=> $challenge->getName()
		));
		
		
		$fieldset->addField('image', 'image', array(
          'label'     => Mage::helper('pixvote')->__('Image'),
          'required'  => false,
          'name'      => 'challenge-image[]',
		  'class' => 'required-entry',
		  'required'  => true,
		  'value'     =>  $challenge->getImage() ? Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$challenge->getImage() : '',
			
		 ));    
		
		$fieldset->addField('description', 'textarea', array(
			'name' => $inputPrefix.'[description]',
			'label' => Mage::helper('pixvote')->__('About'),
			'class' => 'required-entry',
			'required' => true,
			'value'	=> $challenge->getDescription()
		));
		
		$fieldset->addField('rules', 'textarea', array(
			'name' => $inputPrefix.'[rules]',
			'label' => Mage::helper('pixvote')->__('Rules'),
			'class' => 'required-entry',
			'required' => true,
			'value'	=> $challenge->getRules()
		));
		
		
		$fieldset->addField('judges', 'textarea', array(
			'name' => $inputPrefix.'[judges]',
			'label' => Mage::helper('pixvote')->__('Judges'),
			//'label' => Mage::helper('pixvote')->__('Judges(comma-separated)'),
			'class' => 'required-entry',
			'required' => true,
			'value'=> $challenge->getJudges()
		));
		
		$fieldset->addField('prize', 'textarea', array(
			'name' => $inputPrefix.'[prize]',
			'label' => Mage::helper('pixvote')->__('Prize'),
			'class' => 'required-entry',
			'required' => true,
			'value'=> $challenge->getPrize()
		));
		
		$fieldset->addField('pintrest_url', 'text', array(
			'name' => $inputPrefix.'[pintrest_url]',
			'label' => Mage::helper('pixvote')->__('Pintrest Url'),
			'class' => 'required-entry',
			'required' => true,
			'value'=> $challenge->getPintrestUrl()
		));
	
		
		$fieldset->addField('submission_start_time', 'date', array(
			'name' => $inputPrefix.'[submission_start_time]',
			'label' => Mage::helper('pixvote')->__('Submission Start Time'),
			'value'	=> $challenge->getSubmissionStartTime() == $defaultTime ? '' : $challenge->getSubmissionStartTime(),
			'format'    => $dateTimeFormat,
			'time'      => true,
			'image'     => $this->getSkinUrl($calImage),
		));
		
		$fieldset->addField('submission_end_time', 'date', array(
			'name' => $inputPrefix.'[submission_end_time]',
			'label' => Mage::helper('pixvote')->__('Submission End Time'),
			'value'	=>  $challenge->getSubmissionEndTime() == $defaultTime ? '' : $challenge->getSubmissionEndTime(),
			'format'    => $dateTimeFormat,
			'time'      => true,
			'image'     => $this->getSkinUrl($calImage),
		));
		
		$fieldset->addField('vote_start_time', 'date', array(
			'name' => $inputPrefix.'[vote_start_time]',
			'label' => Mage::helper('pixvote')->__('Vote Start Time'),
			'value'	=> $challenge->getVoteStartTime() == $defaultTime ? '' : $challenge->getVoteStartTime(),
			'format'    => $dateTimeFormat,
			'time'      => true,
			'image'     => $this->getSkinUrl($calImage),
		));
		
		$fieldset->addField('vote_end_time', 'date', array(
			'name' => $inputPrefix.'[vote_end_time]',
			'label' => Mage::helper('pixvote')->__('Vote End Time'),
			'value'	=>  $challenge->getVoteEndTime() == $defaultTime ? '' : $challenge->getVoteEndTime(),
			'format'    => $dateTimeFormat,
			'time'      => true,
			'image'     => $this->getSkinUrl($calImage),
		));
		
		$fieldset->addField('is_visible', 'select', array(
          'label'     => Mage::helper('pixvote')->__('Is visible'),
          'name'      => $inputPrefix.'[is_visible]',
          'onclick' => "",
          'onchange' => "",
          'value'  =>  $challenge->getIsVisible(),
          'values' => $yesNoValues,
        ));
		
		$fieldset->addField('is_default', 'select', array(
          'label'     => Mage::helper('pixvote')->__('Is the default voiting challenge?'),
          'name'      => $inputPrefix.'[is_default]',
          'onclick' => "",
          'onchange' => "",
          'value'  =>  $challenge->getIsDefault(),
          'values' => $yesNoValues,
        ));
		
		$fieldset->addField('appear_in_submission_email', 'select', array(
          'label'     => Mage::helper('pixvote')->__('Appear in submission email'),
          'name'      => $inputPrefix.'[appear_in_submission_email]',
          'onclick' => "",
          'onchange' => "",
          'value'  =>  $challenge->getAppearInSubmissionEmail(),
          'values' => $yesNoValues,
        ));
		
		$fieldset->addField('require_school', 'select', array(
          'label'     => Mage::helper('pixvote')->__('Require school?'),
          'name'      => $inputPrefix.'[require_school]',
          'onclick' => "",
          'onchange' => "",
          'value'  =>  $challenge->getRequireSchool(),
          'values' => $yesNoValues,
        ));
		
		return parent::_prepareForm();
	}
}