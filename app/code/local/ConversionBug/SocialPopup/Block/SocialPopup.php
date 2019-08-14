<?php

/** 
 * @category 	ConversionBug
 * @package 	ConversionBug_SocialPopup
 * @copyright 	Copyright (c) 2015 ConversionBug (http://www.ConversionBug.com/)
 * @license 	http://www.ConversionBug.com/license-agreement.html
 * @author  shiv kumar singh
 * @email shivam.kumar@conversionbug.com
 */
class ConversionBug_SocialPopup_Block_SocialPopup extends Mage_Core_Block_Template {
    protected $google = null;
    protected $facebook = null;
    
    protected function _construct() {
        parent::_construct();
        $this->google = Mage::Helper('socialpopup/google');
        $this->facebook = Mage::Helper('socialpopup/facebook');
    
    }
    
    protected function _googleEnabled()
    {
        return $this->google->isEnabled();
    }

    protected function _facebookEnabled()
    {
        return $this->facebook->isEnabled();
    }
}
