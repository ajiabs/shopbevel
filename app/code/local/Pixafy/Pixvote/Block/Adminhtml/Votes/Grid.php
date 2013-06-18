<?php
class Pixafy_Pixvote_Block_Adminhtml_Votes_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('entryGrid');
		$this->setDefaultSort('id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}
	
	protected function _prepareCollection()
	{
		$helper = Mage::helper("pixvote");
		$resource = Mage::getSingleton('core/resource');
		$challenges = Mage::getModel($helper->CHALLENGES_MODEL_PATH)->getCollection();
		$designsTable = $resource->getTableName($helper->DESIGNS_MODEL_PATH);
		$designVotesTable = $resource->getTableName($helper->DESIGN_VOTES_MODEL_PATH);
		$challenges->addFieldToSelect(array('name'));
		$challenges->getSelect()->join($designsTable, $designsTable.'.challenge_id = main_table.id', false);
		$challenges->getSelect()->joinLeft($designVotesTable, $designVotesTable.'.design_id = '.$designsTable.'.id', array('count('.$designVotesTable.'.id) as votes'));
		$challenges->getSelect()->group("main_table.id");
		//die((string)$challenges->getSelect());
		$this->setCollection($challenges);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$helper = Mage::helper("pixvote");
		$resource = Mage::getSingleton('core/resource');
		$designVotesTable = $resource->getTableName($helper->DESIGN_VOTES_MODEL_PATH);
		$this->addColumn('name', array(
				'header'	=>	Mage::helper('adminhtml')->__('Challenge'),
				'align'		=>	'left',
				'index'		=>	'name',
				'default'	=>	'---',
				'filter_index' => 'main_table.name'
				//'filter'	=> false
        ));
		
		$this->addColumn('votes', array(
				'header'	=>	Mage::helper('adminhtml')->__('Total Votes'),
				'align'		=>	'left',
				'index'		=>	'votes',
				'default'	=>	'0',
				'filter'	=> false
        ));
		
		
		$this->addColumn('period', array(
                'header'    => Mage::helper('adminhtml')->__('Date'),
                'align'     => 'left',
                'width'     => '180px',
				'type'      => 'date',
				'time'		=> true,
				'filter_index' => $designVotesTable.'.created_on',
				'renderer'  => 'Pixafy_Pixvote_Block_Adminhtml_Votes_Grid_Renderer_Period',
		));
		
		return parent::_prepareColumns();
	}

	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/view', array('id' => $row->getId()));
	}

	public function getGridUrl()
	{
		return $this->getUrl('*/*/grid', array('_current' => true));
	}
}