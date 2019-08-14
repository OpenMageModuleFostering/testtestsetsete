<?php

/** 
 * @category 	ConversionBug
 * @package 	ConversionBug_SocialPopup
 * @copyright 	Copyright (c) 2015 ConversionBug (http://www.ConversionBug.com/)
 * @license 	http://www.ConversionBug.com/license-agreement.html
 * @author  shiv kumar singh
 * @email shivam.kumar@conversionbug.com
 */
class ConversionBug_SocialPopup_Helper_Data extends Mage_Core_Helper_Abstract {
    
    const XML_PATH_ENABLED = 'socialpopup/socialpopup/enable';
        
    protected $isEnabled = null;
    
    public function __construct()
    {
        $this->isEnabled = $this->_isEnabled();
    }

    public function isEnabled()
    {
        return (bool) $this->isEnabled;
    }
    
    protected function _isEnabled()
    {
        return $this->_getStoreConfig(self::XML_PATH_ENABLED);
    }
    protected function _getStoreConfig($xmlPath)
    {
        return Mage::getStoreConfig($xmlPath, Mage::app()->getStore()->getId());
    }
}
