<?php
/**
 * Pixafy
 *
 * @category    Pixafy
 * @package     Pixafy_Cachebuster
 * @copyright   Copyright (c) 2013 Pixafy (http://www.pixafy.com)
 * @author      Thomas Lackemann
 */
class Pixafy_Cachebuster_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_CACHEBUSTER_IS_ENABLED = 'cachebuster/cachebuster_general/is_enabled';
    const XML_PATH_CACHEBUSTER_CACHE_VERSION = 'cachebuster/cachebuster_general/cache_version';
    
    public function getIsEnabled()
    {
        return Mage::getStoreConfig(self::XML_PATH_CACHEBUSTER_IS_ENABLED);
    }
    
    public function getCacheTag()
    {
        return Mage::getStoreConfig(self::XML_PATH_CACHEBUSTER_CACHE_VERSION);
    }
}