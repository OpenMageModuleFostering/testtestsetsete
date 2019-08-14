<?php

/**
 * @category 	ConversionBug
 * @package 	ConversionBug_SocialPopup
 * @copyright 	Copyright (c) 2015 ConversionBug (http://www.ConversionBug.com/)
 * @license 	http://www.ConversionBug.com/license-agreement.html
 * @author  shiv kumar singh
 * @email shivam.kumar@conversionbug.com
 */
 $includePath = Mage::getBaseDir(). "/lib/SocialLogin/Google_Client.php";
 require_once $includePath;
 
 $includePath = Mage::getBaseDir(). "/lib/SocialLogin/contrib/Google_Oauth2Service.php";
 require_once $includePath;
 
class ConversionBug_SocialPopup_Helper_Google extends ConversionBug_SocialPopup_Helper_Data {

    const XML_PATH_ENABLED = 'socialpopup/gmail/enable';
    const XML_PATH_CLIENT_ID = 'socialpopup/gmail/client_id';
    const XML_PATH_CLIENT_SECRET = 'socialpopup/gmail/client_secret';

    protected $isEnabled = null;
    protected $clientId = null;
    protected $clientSecret = null;

    public function __construct() {
            if ($this->isEnabled = $this->_isEnabled()) {
                $this->clientId = $this->_getClientId();
                $this->clientSecret = $this->_getClientSecret();
            }
    }

    public function isEnabled() {
        return (bool) $this->isEnabled;
    }

    public function _isEnabled() {
        return $this->_getStoreConfig(self::XML_PATH_ENABLED);
    }

    public function _getClientId() {
        return $this->_getStoreConfig(self::XML_PATH_CLIENT_ID);
    }

    public function _getClientSecret() {
        return $this->_getStoreConfig(self::XML_PATH_CLIENT_SECRET);
    }
    
    public function createAuthUrl()
    {
        
        $gClient = new Google_Client();
        $gClient->setApplicationName('Login to '.Mage::getUrl());
        $gClient->setClientId($this->_getClientId());
        $gClient->setClientSecret($this->_getClientSecret());
        $gClient->setRedirectUri(Mage::getUrl('socialpopup/index/redirecUri'));
        $google_oauthV2 = new Google_Oauth2Service($gClient);
        return $gClient->createAuthUrl();
        
    }

    public function createAccountAndConnect(
            $email,
            $firstName,
            $lastName,
            $googleId,
            $token)
    {
        $customer = Mage::getModel('customer/customer');
        
        $customer->setEmail($email)
                ->setFirstname($firstName)
                ->setLastname($lastName)
                ->setCbGid($googleId)
                ->setCbGtoken($token)
                ->setPassword($customer->generatePassword(10))
                ->setCbSocialpopupCustomer(1)
                ->save();

        $customer->setConfirmation(null);
        $customer->save();

        $customer->sendNewAccountEmail();

        Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
    }

}
