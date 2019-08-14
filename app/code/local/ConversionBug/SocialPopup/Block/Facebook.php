<?php

/** 
 * @category 	ConversionBug
 * @package 	ConversionBug_SocialPopup
 * @copyright 	Copyright (c) 2015 ConversionBug (http://www.ConversionBug.com/)
 * @license 	http://www.ConversionBug.com/license-agreement.html
 * @author  shiv kumar singh
 * @email shivam.kumar@conversionbug.com
 */
class ConversionBug_SocialPopup_Block_Facebook extends ConversionBug_SocialPopup_Block_SocialPopup {
    
    protected $client = null;
    
    protected function _construct() {
        parent::_construct();

        $this->client = Mage::Helper('socialpopup/facebook');
        if(!($this->client->isEnabled())) {
            return;
        }        
    }
    
    protected function _getButtonUrl()
    {
        return $this->client->createAuthUrl();        
    }
}