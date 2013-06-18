<?php
/**
 * Magento Commercial Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Commercial Edition License
 * that is available at: http://www.magentocommerce.com/license/commercial-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Adminhtml customer grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Pixafy_ImageSlider_Block_Adminhtml_Sliders_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('slidersGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('product_filter');

    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        $collection = Mage::getModel('imageslider/sliders')->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField('websites',
                    'catalog/product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left');
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id',
            array(
                'header'=> Mage::helper('catalog')->__('ID'),
                'width' => '20px',
                'type'  => 'number',
                'index' => 'id',
        ));
        $this->addColumn('title',
            array(
                'header'=> Mage::helper('imageslider')->__('Title'),
                'width' => '150px',
                'type'  => 'text',
                'index' => 'title',
        ));    
        $this->addColumn('is_active',
            array(
                'header'=> Mage::helper('imageslider')->__('Enabled'),
                'width' => '150px',
                'type'  => 'text',
                'index' => 'is_active',
        ));           
        $this->addColumn('slider_height',
            array(
                'header'=> Mage::helper('imageslider')->__('Height'),
                'width' => '150px',
                'type'  => 'text',
                'index' => 'slider_height',
        ));
        $this->addColumn('slider_width',
            array(
                'header'=> Mage::helper('imageslider')->__('Width'),
                'width' => '150px',
                'type'  => 'text',
                'index' => 'slider_width',
        ));
        $this->addColumn('show_arrows',
            array(
                'header'=> Mage::helper('imageslider')->__('Display Arrows'),
                'width' => '150px',
                'type'  => 'text',
                'index' => 'show_arrows',
        ));
        $this->addColumn('show_dots',
            array(
                'header'=> Mage::helper('imageslider')->__('Display Bullets'),
                'width' => '150px',
                'type'  => 'text',
                'index' => 'show_dots',
        ));                
        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('View / Edit'),
                        'url'     => array(
                            'base'=>'*/*/edit',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
        ));

        $this->addRssList('rss/catalog/notifystock', Mage::helper('catalog')->__('Notify Low Stock RSS'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('sliders');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> Mage::helper('catalog')->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete'),
             'confirm' => Mage::helper('catalog')->__('This will permanently delete the slider(s) and all of its images')
        ));

        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return '#';
        //*$this->getUrl('*/*/edit', array(
            //'store'=>$this->getRequest()->getParam('store'),
            //'id'=>$row->getId())
        //);
    }
}
