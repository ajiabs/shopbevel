<?php
    class Pixafy_CIM_Block_Cards extends Mage_Core_Block_Template
    {
        public function _construct()
        {
        }
        
        public function loadProfiles()
        {
            $this->setProfiles(Mage::getModel("cim/paymentprofiles")->getCollection()->addFilter("customer_id", Mage::helper("customer")->getCustomer()->getId()));
        }
        
        public function loadProfileInfo($profile)
        {
            $profileXml = $this->helper("cim")->paymentProfileRequest($profile->getCimId(), $profile->getProfileId(), $this->helper("cim")->_getProfileRequest);
            $this->setProfileXml(simplexml_load_string($profileXml));
            $this->setProfile($profile);
        }
        /*
        //Brought over from Mage Checkout Block
        public function getCountryCollection()
        {
            if (!$this->_countryCollection) {
                $this->_countryCollection = Mage::getSingleton('directory/country')->getResourceCollection()
                    ->loadByStore();
            }
            return $this->_countryCollection;
        }
        
        //Brought over from Mage Checkout
        public function getCountryOptions()
        {
            $options    = false;
            $useCache   = Mage::app()->useCache('config');
            if ($useCache) {
                $cacheId    = 'DIRECTORY_COUNTRY_SELECT_STORE_' . Mage::app()->getStore()->getCode();
                $cacheTags  = array('config');
                if ($optionsCache = Mage::app()->loadCache($cacheId)) {
                    $options = unserialize($optionsCache);
                }
            }

            if ($options == false) {
                $options = $this->getCountryCollection()->toOptionArray();
                if ($useCache) {
                    Mage::app()->saveCache(serialize($options), $cacheId, $cacheTags);
                }
            }
            return $options;
        }
        
        //Modified version of get country select as seen in Mage Checkout Block
        public function getCountryHtmlSelect($type='card')
        {
            $countryId = Mage::helper('core')->getDefaultCountry();
            $select = $this->getLayout()->createBlock('core/html_select')
                ->setName($type.'[country_id]')
                ->setId($type.':country_id')
                ->setTitle(Mage::helper('customer')->__('Country'))
                ->setClass('validate-select')
                ->setValue($countryId)
                ->setOptions($this->getCountryOptions());
            return $select->getHtml();
        }
        
        */
    }