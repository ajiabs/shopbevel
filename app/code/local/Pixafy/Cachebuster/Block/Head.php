<?php
/**
 * Pixafy
 *
 * @category    Pixafy
 * @package     Pixafy_Cachebuster
 * @copyright   Copyright (c) 2013 Pixafy (http://www.pixafy.com)
 * @author      Thomas Lackemann
 */
class Pixafy_Cachebuster_Block_Head extends Mage_Page_Block_Html_Head
{
    protected $_v;
    protected $_helper;
    
    public function _construct()
    {
        parent::_construct();
        $this->_helper = $this->helper('cachebuster');
        $this->_v = ($this->_helper->getCacheTag())? '' :time();
    }
    
    protected function &_prepareStaticAndSkinElements($format, array $staticItems, array $skinItems, $mergeCallback = null)
    {
        if ($this->_helper->getIsEnabled())
        {
            $designPackage = Mage::getDesign();
            $baseJsUrl = Mage::getBaseUrl('js');
            $items = array();
            if ($mergeCallback && !is_callable($mergeCallback)) {
                $mergeCallback = null;
            }

            // get static files from the js folder, no need in lookups
            foreach ($staticItems as $params => $rows) {
                foreach ($rows as $name) {
                    $items[$params][] = $mergeCallback ? Mage::getBaseDir() . DS . 'js' . DS . $name.'?v='.$this->_v : $baseJsUrl . $name.'?v='.$this->_v;
                }
            }

            // lookup each file basing on current theme configuration
            foreach ($skinItems as $params => $rows) {
                foreach ($rows as $name) {
                    $items[$params][] = $mergeCallback ? $designPackage->getFilename($name, array('_type' => 'skin'))
                        : $designPackage->getSkinUrl($name, array()).'?v='.$this->_v;
                }
            }

            $html = '';
            foreach ($items as $params => $rows) {
                // attempt to merge
                $mergedUrl = false;
                if ($mergeCallback) {
                    $mergedUrl = call_user_func($mergeCallback, $rows);
                }
                // render elements
                $params = trim($params);
                $params = $params ? ' ' . $params : '';
                if ($mergedUrl) {
                    $html .= sprintf($format, $mergedUrl, $params);
                } else {
                    foreach ($rows as $src) {
                        $html .= sprintf($format, $src, $params);
                    }
                }
            }
            return $html;
        }
        else
        {
            return parent::_prepareStaticAndSkinElements($format, $staticItems, $skinItems, $mergeCallback);
        }
    }
}