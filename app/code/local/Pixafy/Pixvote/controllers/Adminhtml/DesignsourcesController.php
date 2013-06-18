<?php
	class Pixafy_Pixvote_Adminhtml_DesignsourcesController extends Mage_Adminhtml_Controller_Action
	{
		function indexAction()
		{
			$this->loadLayout();
			$this->_setActiveMenu('pixvotemenu/design_sources_view');
			$this->renderLayout();
		}
		
		function newAction()
		{
			$this->_forward('edit');
		}
		
		function editAction()
		{
			$id = $this->getRequest()->getParam("id");
			$pixvoteHelper = Mage::helper("pixvote");
			$source = mage::getModel($pixvoteHelper->DESIGN_SOURCES_MODEL_PATH)->load($id);
			if(is_object($source))
			{
				Mage::register($pixvoteHelper->REGISTRY_DESIGNSOURCE_KEY, $source);
				$this->loadLayout();
				$this->_setActiveMenu('pixvotemenuedesigners/edit_designsources');
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
			$sourceData = $this->getRequest()->getParam("designsource");
			$pixvoteHelper = Mage::helper("pixvote");
			if($sourceData)
			{
				try
				{
					$source = mage::getModel($pixvoteHelper->DESIGN_SOURCES_MODEL_PATH)->load($sourceData['id']);

					//if working with a new record, unset id 
					if(!$source->getId())
					{
						unset($sourceData['id']);
					}
					$source->addData($sourceData);
					if($source->save())
					{
						$action = isset($sourceData['id']) ? "updated" : "created";
						$message = "designer {$action}";
						$method = "addSuccess";
						Mage::getSingleton('adminhtml/session')->setDesignSourceData(false);
					}
					else
					{
						$message = "An error occured while editing the designer";
						$method = "addError";
					}
					Mage::getSingleton('adminhtml/session')->$method(Mage::helper('adminhtml')->__($message));
					if ($this->getRequest()->getParam('back')) 
					{
						$this->_redirect('*/*/edit', array('id' => $source->getId()));
					} else 
					{
						$this->_redirect('*/*/');
					}
					return;

				}
				catch(Exception $e)
				{
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					Mage::getSingleton('adminhtml/session')->setChallengeData($this->getRequest()->getPost());
					$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
					return;
				}
			}
			
		}
		
		public function deleteAction()
		{
			if($this->getRequest()->getParam('id') > 0)
			{
				$pixvoteHelper = Mage::helper("pixvote");
				try
				{
					$source = Mage::getModel($pixvoteHelper->DESIGN_SOURCES_MODEL_PATH);

					$source->setId($this->getRequest()->getParam('id'))->delete();

					Mage::getSingleton('adminhtml/session')->addSuccess($pixvoteHelper->__('Source was successfully deleted'));
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
	}
?>
