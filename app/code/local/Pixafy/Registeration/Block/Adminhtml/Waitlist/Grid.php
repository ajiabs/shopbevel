<?php

class Pixafy_Registeration_Block_Adminhtml_Waitlist_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
public function __construct()
    {
        parent::__construct();
        $this->setId('waitlist_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
    }
 
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('registeration/waitlist')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
 
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('registeration')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'entity_id',
        ));
 
        $this->addColumn('email', array(
            'header'    => Mage::helper('registeration')->__('Email'),
            'align'     =>'left',
            'index'     => 'cust_email',
        ));
        $this->addColumn('first_name', array(
        		'header'    => Mage::helper('registeration')->__('First Name'),
        		'align'     =>'left',
        		'index'     => 'first_name',
        ));
        $this->addColumn('last_name', array(
        		'header'    => Mage::helper('registeration')->__('Last Name'),
        		'align'     =>'left',
        		'index'     => 'last_name',
        ));
 
        $this->addColumn('rank', array(
            'header'    => Mage::helper('registeration')->__('Rank'),
            'align'     =>'left',
            'index'     => 'wait_rank',
        ));

        $this->addColumn('status', array(
        		'header'    => Mage::helper('registeration')->__('Status'),
        		'align'     =>'left',
        		'index'     => 'actived',
        ));
        return parent::_prepareColumns();
    }
 
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
    
}
