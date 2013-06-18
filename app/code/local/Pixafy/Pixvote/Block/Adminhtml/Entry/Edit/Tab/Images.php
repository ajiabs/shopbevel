<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Pixafy_Pixvote_Block_Adminhtml_Entry_Edit_Tab_Images extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		//New form Object
		//Prefix for form data
		$form = new Varien_Data_Form();
		$inputPrefix = "entry-image";
		$inputEditPrefix = "entry-image-delete";
		$entry = Mage::registry("entry_data");
		$this->setForm($form);
		$fieldset = $form->addFieldset('pixvote_form', array('legend' => Mage::helper('pixvote')->__('Design Images')));
		
		//Load entry images
		$images = Mage::registry('entry_images');
        $imageHtml = '<div class="admin-product-images">';
        foreach($images as $image) {
			$imageUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$image->getUrl();
            $imageHtml.='<a target="_blank" href="'.$imageUrl.'"><img src="'.$imageUrl.'" height="100" width="auto" /></a>
			<input type="checkbox" name="'.$inputEditPrefix.'[]" value="'.$image->getId().'"/>Delete';
        }
        $imageHtml.= '</div>';
		if($imageHtml)
        {
			$fieldset->addField('images', 'note', array(
            'label' => Mage::helper('pixvote')->__('Entry Images'),
            'text'  => Mage::helper('pixvote')->__($imageHtml),
			));
		}
		
		$fieldset->addField('image', 'image', array(
          'label'     => Mage::helper('pixvote')->__('Upload an Image'),
          'required'  => false,
          'name'      => $inputPrefix.'[]',
		  'class' => 'entry-images'
		 ));    
		 
		 $fieldset->addField('add-image', 'link', array(
         // 'label'=> Mage::helper('pixvote')->__('Add image'),
          'style'=> "",
          'href' => 'javascript:void(0)',
          'value'=> Mage::helper('pixvote')->__('Add image')	
        ));
		return parent::_prepareForm();
	}
}
?>
