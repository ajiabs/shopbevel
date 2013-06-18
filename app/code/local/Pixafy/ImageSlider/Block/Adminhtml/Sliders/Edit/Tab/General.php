<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product inventory data
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Pixafy_ImageSlider_Block_Adminhtml_Sliders_Edit_Tab_General extends Mage_Adminhtml_Block_Widget
{
	protected $_form;
	protected $_slider;
    public function __construct()
    {
        parent::__construct();
        $this->getFields();
        $this->setTemplate('imageslider/sliders/tab/general.phtml');
    }
    public function getFields(){
		$this->_slider	=	Mage::registry('pixafy_imageslider');
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $yesNo		=	array($this->__('No') => 0, $this->__('Yes') => 1);
        $fieldset = $form->addFieldset('slider', array(
            'legend'=>Mage::helper('imageslider')->__('General Information')
        ));
        $fieldset->addField('is_active', 'select', array(
            'name'      => 'slider[is_active]',
            'label'     => 	Mage::helper('imageslider')->__('Enabled'),
            'note'      => 	Mage::helper('imageslider')->__('Flag to hide or show the slider'),
            'value'		=>	$yesNo[$this->_slider->getIsActive()],
            'style'		=>	'width:300px',
            'options'	=>	array(1=>'Yes', 0=>'No')
        ));        
        $fieldset->addField('title', 'text', array(
            'name'      => 'slider[title]',
            'label'     => 	Mage::helper('imageslider')->__('Title'),
            'note'      => 	Mage::helper('imageslider')->__('The title if your slider. For internal use only'),
            'value'		=>	$this->_slider->getTitle(),
            'style'		=>	'width:300px'
        ));
        $fieldset->addField('seconds', 'text', array(
            'name'      => 'slider[seconds]',
            'label'     => 	Mage::helper('imageslider')->__('Seconds between transition<span style="color:red">*</span>'),
            'note'      => 	Mage::helper('imageslider')->__('The number of seconds between image change'),
            'value'		=>	$this->_slider->getSeconds(),
            'style'		=>	'width:300px',
            'class'		=>	'input-text validate-number required-entry'
        ));
        $fieldset->addField('window', 'select', array(
            'name'      => 'slider[window]',
            'label'     => 	Mage::helper('imageslider')->__('Open Links in new window'),
            'note'      => 	Mage::helper('imageslider')->__('Whether links will open in a new tab/window'),
            'value'		=>	$this->_slider->getOpenNewWindow(),
            'style'		=>	'width:300px',
            'options'	=>	array(1=>'Yes', 0=>'No')
        ));
        $fieldset->addField('width', 'text', array(
            'name'      => 'slider[width]',
            'label'     => 	Mage::helper('imageslider')->__('Pixel Width (0 will use image width)'),
            'note'      => 	Mage::helper('imageslider')->__('Slider width in pixels'),
            'value'		=>	$this->_slider->getSliderWidth(),
            'style'		=>	'width:300px',
            'class'		=>	'input-text validate-number required-entry'
        ));
        $fieldset->addField('height', 'text', array(
            'name'      => 'slider[height]',
            'label'     => 	Mage::helper('imageslider')->__('Pixel Height (0 will use image height)'),
            'note'      => 	Mage::helper('imageslider')->__('Slider height in pixels'),
            'value'		=>	$this->_slider->getSliderHeight(),
            'style'		=>	'width:300px',
            'class'		=>	'input-text validate-number required-entry'
        ));
        $fieldset->addField('show_arrows', 'select', array(
            'name'      => 'slider[show_arrows]',
            'label'     => 	Mage::helper('imageslider')->__('Display arrows to cycle between images'),
            'note'      => 	Mage::helper('imageslider')->__('Display arrows to cycle between images'),
            'value'		=>	$yesNo[$this->_slider->getShowArrows()],
            'style'		=>	'width:300px',
            'options'	=>	array(0=>$this->__('No'), 1=>$this->__('Yes'))
        ));
        $fieldset->addField('show_dots', 'select', array(
            'name'      => 'slider[show_dots]',
            'label'     => 	Mage::helper('imageslider')->__('Display image bullet points'),
            'note'      => 	Mage::helper('imageslider')->__('Display image bullet points'),
            'value'		=>	$yesNo[$this->_slider->getShowDots()],
            'style'		=>	'width:300px',
            'options'	=>	array(0=>$this->__('No'), 1=>$this->__('Yes'))
        ));
		if (Mage::registry('location_data')) {
            $form->setValues(Mage::registry('location_data')->getData());
        }
        
        $this->_form	=	$form;
	}
}
