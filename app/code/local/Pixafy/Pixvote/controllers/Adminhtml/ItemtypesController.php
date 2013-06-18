<?php
class Pixafy_Pixvote_Adminhtml_ItemtypesController extends Mage_Adminhtml_Controller_Action
{
	function indexAction()
	{
		$this->loadLayout();
		$this->_setActiveMenu('pixvotemenu/vote');
		$this->renderLayout();
	}
	
	function newAction()
	{
		$this->_forward('edit');
	}
	
	function editAction()
	{
		$id = $this->getRequest()->getParam("id");
		$itemType = mage::getModel("pixvote/itemtypes")->load($id);
		if(is_object($itemType))
		{
			Mage::register('item_type_data', $itemType);
			$this->loadLayout();
			$this->_setActiveMenu('pixvotemenu/itemtypes');
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Entry Manager'), Mage::helper('adminhtml')->__('Item types Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Entry News'), Mage::helper('adminhtml')->__('Item types information'));
			$this->renderLayout();
		}
		else
		{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('pixvote')->__('Record not found'));
			$this->_redirect('*/*/');
		}
	}
	
	function saveAction()
	{
		$itemTypeData = $this->getRequest()->getParam("itemType");
		if($itemTypeData)
		{
			try
			{
				$itemType = mage::getModel(Mage::helper("pixvote")->ITEM_TYPES_MODEL_PATH)->load($itemTypeData['id']);
				
				//if working with a new record, unset id 
				if(!$itemType->getId())
				{
					unset($itemTypeData['id']);
				}
				$itemType->setData($itemTypeData);
				if($itemType->save())
				{
					$message = "Item created!";
					$method = "addSuccess";
					Mage::getSingleton('adminhtml/session')->setItemtypeData(false);
				}
				else
				{
					$message = "An error occured while creating the itme type";
					$method = "addError";
				}
				Mage::getSingleton('adminhtml/session')->$method(Mage::helper('adminhtml')->__($message));
				$this->_redirect('*/*/');
                return;
				
			}
			catch(Exception $e)
			{
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setItemtypeData($this->getRequest()->getPost());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
	}
	
	public function deleteAction()
	{
		if($this->getRequest()->getParam('id') > 0)
		{
			try
			{
				$itemTypeModel = Mage::getModel(Mage::helper("pixvote")->ITEM_TYPES_MODEL_PATH);

				$itemTypeModel->setId($this->getRequest()->getParam('id'))
					->delete();

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Entry was successfully deleted'));
				$this->_redirect('*/*/');
			}
			catch (Exception $e)
			{
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}

		$this->_redirect('*/*/');
	}
	
	public function gridAction()
	{
		$this->loadLayout();
		$this->getResponse()->setBody(
			$this->getLayout()->createBlock('pixvote/adminhtml_itemtypes_grid')->toHtml()
		);
	}
	
	
}
?>
