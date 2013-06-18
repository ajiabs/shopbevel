<?php
	class Pixafy_Pixvote_Adminhtml_DesigntipsController extends Mage_Adminhtml_Controller_Action
	{
		public function indexAction()
		{
			$this->loadLayout();
			$this->_setActiveMenu('pixvotemenuedesigners/view_designtips');
			$this->renderLayout();
		}
		
		public function newAction()
		{
			$this->_forward('edit');
		}
		
		public function editAction()
		{
			$id = $this->getRequest()->getParam("id");
			$pixvoteHelper = Mage::helper("pixvote");
			$tip = mage::getModel($pixvoteHelper->DESIGN_TIPS_MODEL_PATH)->load($id);
			if(is_object($tip))
			{
				Mage::register('designtip_data', $tip);
				$this->loadLayout();
				$this->_setActiveMenu('pixvotemenuedesigners/edit_designtip');
				$this->renderLayout();
			}
			else
			{
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('pixvote')->__('Record not found'));
				$this->_redirect('*/*/');
			}
		}
		
		public function saveAction()
		{
			$tipData = $this->getRequest()->getParam("designtip");
			$pixvoteHelper = Mage::helper("pixvote");
			if($tipData)
			{
				try
				{
					$tip = mage::getModel($pixvoteHelper->DESIGN_TIPS_MODEL_PATH)->load($tipData['id']);

					//if working with a new record, unset id 
					if(!$tip->getId())
					{
						unset($tipData['id']);
					}
					
					if(!$tipData['send_date'])
					{
						unset($tipData['send_date']);
					}
					$tip->setData($tipData);
					if($tip->save())
					{
						$action = isset($tipData['id']) ? "updated" : "created";
						$message = "designer {$action}";
						$method = "addSuccess";
						Mage::getSingleton('adminhtml/session')->setDesigntipData(false);
					}
					else
					{
						$message = "An error occured while editing the designer";
						$method = "addError";
					}
					Mage::getSingleton('adminhtml/session')->$method(Mage::helper('adminhtml')->__($message));
					if ($this->getRequest()->getParam('back')) 
					{
						$this->_redirect('*/*/edit', array('id' => $tip->getId()));
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
					$tip = Mage::getModel($pixvoteHelper->DESIGN_TIPS_MODEL_PATH)->load($this->getRequest()->getParam('id'));
					$tip->delete();
					Mage::getSingleton('adminhtml/session')->addSuccess($pixvoteHelper->__('Tip was successfully deleted'));
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
