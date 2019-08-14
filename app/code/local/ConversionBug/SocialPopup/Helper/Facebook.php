<?php

/**
 * @category 	ConversionBug
 * @package 	ConversionBug_SocialPopup
 * @copyright 	Copyright (c) 2015 ConversionBug (http://www.ConversionBug.com/)
 * @license 	http://www.ConversionBug.com/license-agreement.html
 * @author  shiv kumar singh
 * @email shivam.kumar@conversionbug.com
 */
 $includePath = Mage::getBaseDir(). "/lib/SocialLogin/facebook/facebook.php";
 require_once $includePath;
  
class ConversionBug_SocialPopup_Helper_Facebook extends ConversionBug_SocialPopup_Helper_Data {

    const XML_PATH_ENABLED = 'socialpopup/facebook/enable';
    const XML_PATH_CLIENT_ID = 'socialpopup/facebook/client_id';
    const XML_PATH_CLIENT_SECRET = 'socialpopup/facebook/client_secret';
   

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

   public function createAuthUrl(){

    //Call Facebook API
    $facebook = new Facebooklib(array(
      'appId'  => $this->clientId,
      'secret' => $this->clientSecret

    ));
    $redirect_url = Mage::getUrl('socialpopup/index/facebook');
    $fbuser = null;
    $loginUrl = $facebook->getLoginUrl(array('redirect_uri'=>$redirect_url,'scope'=>'email'));
    return $loginUrl;   
    
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
                ->setCbFid($googleId)
                ->setCbFtoken($token)
                ->setPassword($customer->generatePassword(10))
                ->setCbSocialpopupCustomer(1)
                ->save();

        $customer->setConfirmation(null);
        $customer->save();

        $customer->sendNewAccountEmail();

        Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
    }

}
