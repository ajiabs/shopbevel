<?php
class Pixafy_Registeration_Adminhtml_WaitlistController extends Mage_Adminhtml_Controller_Action{
	
 	public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
 
    public function newAction()
    {
        $this->_forward('edit');
    }
 
    public function editAction()
    {
     	   $id = $this->getRequest()->getParam('id');
           $model = Mage::getModel('registeration/waitlist')->load($id);
           if ($model->getId() || $id == 0)
           {
             Mage::register('registeration_data', $model);
             $this->loadLayout();
             $this->_setActiveMenu('registeration/waitlist');

             $this->getLayout()->getBlock('head')
                  ->setCanLoadExtJs(true);
             $this->_addContent($this->getLayout()->createBlock('registeration/adminhtml_waitlist_edit'))
             		->_addLeft($this->getLayout()->createBlock('registeration/adminhtml_waitlist_edit_tabs'));
             $this->renderLayout();
           }
           else
           {
                 Mage::getSingleton('adminhtml/session')
                       ->addError('Test does not exist');
                 $this->_redirect('*/*/');
            }
    }
 
    public function saveAction()
    {
        if ( $this->getRequest()->getPost() ) {
            try {
                $postData = $this->getRequest()->getPost();
                $Model = Mage::getModel('registeration/waitlist');
                $id = $this->getRequest()->getParam('id');
               
                $Model->setId($id)
                    ->setCustEmail($postData['cust_email'])
                    ->setFirstName($postData['first_name'])
                    ->setLastName($postData['last_name'])
                    ->setActived($postData['actived'])
                    ->save();
               	if(!!$id && $postData['actived'] == 1 ){
               		Mage::helper('registeration')->createCustomer($id);
               	}
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setRegisterationData(false);
 
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setRegisterationData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }
 
    public function deleteAction()
    {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $Model = Mage::getModel('registeration/waitlist');
               
                $Model->setId($this->getRequest()->getParam('id'))
                    ->delete();
                   
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
     }
	
}
