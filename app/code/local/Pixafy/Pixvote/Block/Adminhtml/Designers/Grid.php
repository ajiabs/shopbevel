<?php
class Pixafy_Pixvote_Block_Adminhtml_Designers_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('challengeGrid');
		$this->setDefaultSort('id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}
	
	protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('designers');
		$this->getMassactionBlock()->addItem('remainder_email', array(
				 'label'    => Mage::helper('pixvote')->__('Send profile not complete email'),
				 'url'      => $this->getUrl('*/*/sendProfileRemainder')
		));
		$this->getMassactionBlock()->addItem('export_stats', array(
				 'label'    => Mage::helper('pixvote')->__('Export designer stats'),
				 'url'      => $this->getUrl('*/*/exportStats')
		));
		
        return $this;
    }

	protected function _prepareCollection()
	{
		$helper = Mage::helper("pixvote");
		$collection = Mage::getResourceModel('customer/customer_collection')
		->addNameToSelect()
		->addAttributeToSelect('email')
		->addAttributeToSelect('created_at')
		->addAttributeToFilter('is_designer', 1);
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$this->addColumn('entity_id', array(
				'header'	=>	Mage::helper('adminhtml')->__('ID'),
				'align'		=>	'right',
				'width'		=>	'50px',
				'index'		=>	'entity_id',
        ));

		$this->addColumn('name', array(
				'header'	=>	Mage::helper('adminhtml')->__('Name'),
				'align'		=>	'left',
				'index'		=>	'name',
        ));
		
		$this->addColumn('email', array(
				'header'	=>	Mage::helper('adminhtml')->__('Email'),
				'align'		=>	'left',
				'index'		=>	'email',
        ));

		
//		
//		$this->addColumn('price', array(
//				'header'	=>	Mage::helper('adminhtml')->__('Designer price'),
//				'align'		=>	'left',
//				'index'		=>	'price',
//        ));
//		
//		$this->addColumn('bevel_price', array(
//				'header'	=>	Mage::helper('adminhtml')->__('Bevel price'),
//				'align'		=>	'left',
//				'index'		=>	'bevel_price',
//        ));
//		
//		$this->addColumn('description', array(
//				'header'	=>	Mage::helper('adminhtml')->__('Design Description'),
//				'align'		=>	'left',
//				'index'		=>	'description',
//        ));
//		
//		$this->addColumn('created_on', array(
//                'header'    => Mage::helper('adminhtml')->__('Date Submitted'),
//                'align'     => 'left',
//                'width'     => '180px',
//                'type'      => 'datetime',
//				'time'		=> true,
//                'default'   => '--',
//                'index'     => 'created_on',
//		));

		/*
        $this->addColumn('updated_at', array(
				'header'    => Mage::helper('adminhtml')->__('Update Time'),
				'align'     => 'left',
				'width'     => '180px',
				'type'      => 'datetime',
				'default'   => '--',
				'index'     => 'updated_at',
		));  
		*/
		
		return parent::_prepareColumns();
	}

	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

	public function getGridUrl()
	{
		return $this->getUrl('*/*/grid', array('_current' => true));
	}
}