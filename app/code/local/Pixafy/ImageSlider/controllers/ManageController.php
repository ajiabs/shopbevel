<?php
class Pixafy_ImageSlider_ManageController extends Mage_Adminhtml_Controller_Action{
	
	private $_debug	=	0; // 0: disable debug ||  1: enagble debug
	public function indexAction(){
		$this->_title('Manage Sliders');
		$this->loadLayout();
		$this->renderLayout();
	}
	public function editAction(){
        $sliderId  	= (int) $this->getRequest()->getParam('id');
        $slider 	= Mage::getModel('imageslider/sliders')->load($sliderId);

        if ($sliderId && !$slider->getId()) {
            $this->_getSession()->addError(Mage::helper('catalog')->__('This product no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }
        $this->_title($slider->getTitle());
        $this->loadLayout();
        $this->_setActiveMenu('imageslider');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $block = $this->getLayout()->getBlock('catalog.wysiwyg.js');
        if ($block) {
            $block->setStoreId($slider->getStoreId());
        }

        $this->renderLayout();
	}
    public function saveAction()
    {
		$postData 	=	$this->getRequest()->getPost();
		unset($postData['product']['media_gallery']['image_text']["'__file__'"]);		
		$media		=	$postData['product']['media_gallery'];
		$imageText	=	$media['image_text'];	
		foreach($imageText as $k => $v){
			unset($imageText[$k]);
			$imageText[str_replace("'", '', $k)] = $v;
		}			

		//$this->_debug=1;
		if($this->_debug == 1){
			echo '<pre>';
			print_r($postData);
			print_r($imageText);
			exit;
		}
		
		$yesNo		=	array(0 => $this->__('No'), 1 => $this->__('Yes'));
		$helper		=	Mage::helper('imageslider');
		
		$sliderData	=	$postData['slider'];
		$sliderId	=	$sliderData['id'];
		if($sliderId){
			$slider	=	Mage::getModel('imageslider/sliders')->load($sliderId);
			$slider->setTitle($sliderData['title']);
			$slider->setSeconds($sliderData['seconds']);
			$slider->setOpenNewWindow($sliderData['window']);
			$slider->setSliderHeight($sliderData['height']);
			$slider->setSliderWidth($sliderData['width']);
			$slider->setShowArrows($yesNo[$sliderData['show_arrows']]);
			$slider->setShowDots($yesNo[$sliderData['show_dots']]);
			$slider->setIsActive($yesNo[$sliderData['is_active']]);
			$slider->save();
		}
		

		//print_r(json_decode($media['images']));
		$media		=	json_decode($media['images']);
		$baseDir	=	Mage::getBaseDir();
		foreach($media as $image){
			$copyFailed	=	FALSE;
			if($image->value_id){
				$imageModel	=	Mage::getModel('imageslider/images')->load($image->value_id);
				if($image->removed == 1){
					$imageModel->delete();
					unlink($helper->_fullPath.$imageModel->getImagePath());
					continue;
				}
			}
			else{
				if($image->removed == 1){
					continue;
				}
				$imageExt 			= 	end(explode(".", ($image->url)));
				$imageFileName		=	'sliderimage_'.str_replace(".","", microtime(true)).'.'.$imageExt;
				$newPath			=	$baseDir.'/media/imageslider/'.$imageFileName;	
				$imageModel			=	Mage::getModel('imageslider/images');
				$imageModel->setImagePath($imageFileName);
				$imageModel->setSliderId($postData['slider']['id']);
				$oldPath			=	$baseDir.'/media/tmp/catalog/product'.str_replace('.tmp', '', $image->file);
				if(!copy($oldPath, $newPath)){			
					Mage::getSingleton('adminhtml/session')->addError('Could not upload image. Please make sure the destination folder has write permissions (default: media/imageslider/)');
					$copyFailed	=	TRUE;
				}
			}
			if(!$copyFailed){
				$imageModel->setImageText($imageText[$imageModel->getImagePath()]);
				//echo '<br><br><br><br>';
				//print_r($imageModel->getData());
				//echo $imageModel->getImagePath();
				//echo '<br>';
				//print_r($imageText);
				//echo '<br><br>a: '.$imageText[$imageModel->getImagePath()];
				//exit;
			
				$imageModel->setExternalUrl($image->label);
				$imageModel->setSortOrder($image->position);			
				($image->disabled == 1 ? $imageModel->setIsActive(0) : $imageModel->setIsActive(1));
				$imageModel->save();
			}
			
		}
		Mage::getSingleton('adminhtml/session')->addSuccess('Slider saved successfully');
		if($this->getRequest()->getParam('back') == 'edit'){
			$this->_redirect('*/manage/edit/', array('id'=>$postData['slider']['id']));
		}
		else{
			$this->_redirect('*/manage');
		}
    }
    /**
     * Validate product
     *
     */
    public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(false);

        try {
            $productData = $this->getRequest()->getPost('product');

            if ($productData && !isset($productData['stock_data']['use_config_manage_stock'])) {
                $productData['stock_data']['use_config_manage_stock'] = 0;
            }
            $product = Mage::getModel('catalog/product');
            $product->setData('_edit_mode', true);
            if ($storeId = $this->getRequest()->getParam('store')) {
                $product->setStoreId($storeId);
            }
            if ($setId = $this->getRequest()->getParam('set')) {
                $product->setAttributeSetId($setId);
            }
            if ($typeId = $this->getRequest()->getParam('type')) {
                $product->setTypeId($typeId);
            }
            if ($productId = $this->getRequest()->getParam('id')) {
                $product->load($productId);
            }

            $dateFields = array();
            $attributes = $product->getAttributes();
            foreach ($attributes as $attrKey => $attribute) {
                if ($attribute->getBackend()->getType() == 'datetime') {
                    if (array_key_exists($attrKey, $productData) && $productData[$attrKey] != ''){
                        $dateFields[] = $attrKey;
                    }
                }
            }
            $productData = $this->_filterDates($productData, $dateFields);

            $product->addData($productData);
            $product->validate();
            /**
             * @todo implement full validation process with errors returning which are ignoring now
             */
//            if (is_array($errors = $product->validate())) {
//                foreach ($errors as $code => $error) {
//                    if ($error === true) {
//                        Mage::throwException(Mage::helper('catalog')->__('Attribute "%s" is invalid.', $product->getResource()->getAttribute($code)->getFrontend()->getLabel()));
//                    }
//                    else {
//                        Mage::throwException($error);
//                    }
//                }
//            }
        }
        catch (Mage_Eav_Model_Entity_Attribute_Exception $e) {
            $response->setError(true);
            $response->setAttribute($e->getAttributeCode());
            $response->setMessage($e->getMessage());
        }
        catch (Mage_Core_Exception $e) {
            $response->setError(true);
            $response->setMessage($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_initLayoutMessages('adminhtml/session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setBody($response->toJson());
    }
    public function massDeleteAction(){
		$postData	=	$this->getRequest()->getPost();
		if(is_array($postData['sliders'])){
			$helper		=	Mage::helper('imageslider');
			foreach($postData['sliders'] as $sliderId){
				$helper->deleteSlider($sliderId);
			}
		}
		$this->_redirect('*/*/');
	}
	
	public function deleteAction(){
		try{
			$sliderId = $this->getRequest()->getParam('id');
			Mage::helper('imageslider')->deleteSlider($sliderId);
			Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Slider deleted'));
		}
		catch(Exception $e){
			Mage::getSingleton('adminhtml/session')->addError($this->__('Unable to delete slider'));
		}
		$this->_redirect('*/*/');
	}
	
	
	public function addAction(){
		$slider	=	Mage::getModel("imageslider/sliders");
		$slider->setIsActive(1);
		$slider->save();
		
		$slider->setEmbedCode('<block type="imageslider/slider" template="imageslider/slider.phtml" name="home.slider'.$slider->getId().'" as="slider'.$slider->getId().'"><action method="setSliderId"><id>'.$slider->getId().'</id></action></block>');
		$slider->setWysiwygCode('{{block type="imageslider/slider" template="imageslider/slider.phtml" id="'.$slider->getId().'"}}');
		$slider->setPhpCode('<?php echo $this->getLayout()->createBlock(\'imageslider/slider\')->setTemplate(\'imageslider/slider.phtml\')->setId('.$slider->getId().')->toHtml() ?>');
		$slider->save();
		$this->_redirect('*/*/edit', array('id'=>$slider->getId()));
		
	}
	
	public function gridAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
}
