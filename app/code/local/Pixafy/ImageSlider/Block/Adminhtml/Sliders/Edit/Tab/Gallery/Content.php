<?php
class Pixafy_ImageSlider_Block_Adminhtml_Sliders_Edit_Tab_Gallery_Content extends Mage_Adminhtml_Block_Widget
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('imageslider/sliders/tab/gallery.phtml');
    }

    protected function _prepareLayout()
    {
        $this->setChild('uploader',
            $this->getLayout()->createBlock('imageslider/adminhtml_media_uploader')
        );
        $url	=	Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/catalog_product_gallery/upload');
        $url	=	str_replace("catalog_product_gallery", "adminhtml_gallery", $url);
        
        $this->getUploader()->getConfig()
            ->setUrl($url)
            ->setFileField('image')
            ->setFilters(array(
                'images' => array(
                    'label' => Mage::helper('adminhtml')->__('Images (.gif, .jpg, .png)'),
                    'files' => array('*.gif', '*.jpg','*.jpeg', '*.png')
                )
            ));

        return parent::_prepareLayout();
    }

    /**
     * Retrive uploader block
     *
     * @return Mage_Adminhtml_Block_Media_Uploader
     */
    public function getUploader()
    {
        return $this->getChild('uploader');
    }

    /**
     * Retrive uploader block html
     *
     * @return string
     */
    public function getUploaderHtml()
    {
        return $this->getChildHtml('uploader');
    }

    public function getJsObjectName()
    {
        return $this->getHtmlId() . 'JsObject';
    }

    public function getAddImagesButton()
    {
        return $this->getButtonHtml(
            Mage::helper('catalog')->__('Add New Images'),
            $this->getJsObjectName() . '.showUploader()',
            'add',
            $this->getHtmlId() . '_add_images_button'
        );
    }
    public function getImagesJson()
    {
		$images	=	Mage::getModel('imageslider/images')->getCollection()->addFieldToFilter('slider_id', $this->getRequest()->getParam('id'))->addOrder('sort_order', 'ASC');
		$imagesArray	=	array();
		foreach($images as $img){
			$tempImage	=	array();
			$tempImage['value_id']			=	$img->getId();
			$tempImage['file']				=	$img->getImagePath();
			$tempImage['label']				=	$img->getExternalUrl();
			$tempImage['disabled']			=	($img->getIsActive()	==	1 ? 0 : 1);
			$tempImage['label_default']		=	'';
			$tempImage['position']			=	$img->getSortOrder();
			$tempImage['position_default']	=	$img->getSortOrder();
			$tempImage['url']				=	Mage::getBaseUrl('web').'media/imageslider/'.$img->getImagePath();
			$tempImage['imageText']		=	$img->getImageText();
			//print_r($tempImage);
			$imagesArray[]	=	$tempImage;
		}
		//print_r($imagesArray);
		return Mage::helper('core')->jsonEncode($imagesArray);
        if(is_array($this->getElement()->getValue())) {
            $value = $this->getElement()->getValue();
            if(count($value['images'])>0) {
                foreach ($value['images'] as &$image) {
                    $image['url'] = Mage::getSingleton('catalog/product_media_config')
                                        ->getMediaUrl($image['file']);
                }
                return Mage::helper('core')->jsonEncode($value['images']);
            }
        }
        return '[]';
    }

    public function getImagesValuesJson()
    {
        $values = array();
        foreach ($this->getMediaAttributes() as $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
            $values[$attribute->getAttributeCode()] = $this->getElement()->getDataObject()->getData(
                $attribute->getAttributeCode()
            );
        }
        return Mage::helper('core')->jsonEncode($values);
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    public function getImageTypes()
    {
        $imageTypes = array();
        foreach ($this->getMediaAttributes() as $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
            $imageTypes[$attribute->getAttributeCode()] = array(
                'label' => $attribute->getFrontend()->getLabel() . ' '
                         . Mage::helper('catalog')->__($this->getElement()->getScopeLabel($attribute)),
                'field' => $this->getElement()->getAttributeFieldName($attribute)
            );
        }
        return $imageTypes;
    }

    public function hasUseDefault()
    {
        foreach ($this->getMediaAttributes() as $attribute) {
            if($this->getElement()->canDisplayUseDefault($attribute))  {
                return true;
            }
        }

        return false;
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    public function getMediaAttributes()
    {
        return $this->getElement()->getDataObject()->getMediaAttributes();
    }

    public function getImageTypesJson()
    {
        return Mage::helper('core')->jsonEncode($this->getImageTypes());
    }

}
