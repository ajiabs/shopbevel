<?php
class Pixafy_Pixvote_Block_Adminhtml_Challenges_Grid extends Mage_Adminhtml_Block_Widget_Grid
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

	protected function _prepareCollection()
	{
		$helper = Mage::helper("pixvote");
		$collection = Mage::getModel($helper->CHALLENGES_MODEL_PATH)->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$this->addColumn('id', array(
				'header'	=>	Mage::helper('adminhtml')->__('ID'),
				'align'		=>	'right',
				'width'		=>	'50px',
				'index'		=>	'id',
        ));

		$this->addColumn('name', array(
				'header'	=>	Mage::helper('adminhtml')->__('Challenge'),
				'align'		=>	'left',
				'index'		=>	'name',
        ));
		
		$this->addColumn('vote_start_time', array(
                'header'    => Mage::helper('adminhtml')->__('Vote start time'),
                'align'     => 'left',
                'width'     => '180px',
                'type'      => 'datetime',
				'time'		=> true,
                'default'   => '--',
                'index'     => 'vote_start_time',
				'renderer'  => 'Pixafy_Pixvote_Block_Adminhtml_Challenges_Grid_Renderer_Time',
		));
		
		$this->addColumn('vote_end_time', array(
                'header'    => Mage::helper('adminhtml')->__('Vote end time'),
                'align'     => 'left',
                'width'     => '180px',
                'type'      => 'datetime',
				'time'		=> true,
                'default'   => '--',
                'index'     => 'vote_end_time',
				'renderer'  => 'Pixafy_Pixvote_Block_Adminhtml_Challenges_Grid_Renderer_Time',
		));
		
		$this->addColumn('submission_start_time', array(
                'header'    => Mage::helper('adminhtml')->__('Submission start time'),
                'align'     => 'left',
                'width'     => '180px',
                'type'      => 'datetime',
				'time'		=> true,
                'default'   => '--',
                'index'     => 'submission_start_time',
				'renderer'  => 'Pixafy_Pixvote_Block_Adminhtml_Challenges_Grid_Renderer_Time',
		));
		
		$this->addColumn('submission_end_time', array(
                'header'    => Mage::helper('adminhtml')->__('Submission end time'),
                'align'     => 'left',
                'width'     => '180px',
                'type'      => 'datetime',
				'time'		=> true,
                'default'   => '--',
                'index'     => 'submission_end_time',
				'renderer'  => 'Pixafy_Pixvote_Block_Adminhtml_Challenges_Grid_Renderer_Time',
		));
		
		$this->addColumn('created_on', array(
                'header'    => Mage::helper('adminhtml')->__('Date Created'),
                'align'     => 'left',
                'width'     => '180px',
                'type'      => 'datetime',
				'time'		=> true,
                'default'   => '--',
                'index'     => 'created_on',
				'renderer'  => 'Pixafy_Pixvote_Block_Adminhtml_Challenges_Grid_Renderer_Time',
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