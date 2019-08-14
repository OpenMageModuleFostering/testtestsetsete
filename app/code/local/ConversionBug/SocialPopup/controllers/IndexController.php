<?php

$includePath = Mage::getBaseDir() . "/lib/SocialLogin/Google_Client.php";
require_once $includePath;

$includePath = Mage::getBaseDir() . "/lib/SocialLogin/contrib/Google_Oauth2Service.php";
require_once $includePath;

$includePath = Mage::getBaseDir() . "/lib/SocialLogin/facebook/facebook.php";
require_once $includePath;

class ConversionBug_SocialPopup_IndexController extends Mage_Core_Controller_Front_Action {

    
    public function redirecUriAction() {
        $data = $this->getRequest()->getParams();
        if (isset($data['code'])) {
            $gClient = new Google_Client();
            $gClient->setApplicationName('Login to ' . Mage::getUrl());
            $gClient->setClientId(Mage::helper('socialpopup/google')->_getClientId());
            $gClient->setClientSecret(Mage::helper('socialpopup/google')->_getClientSecret());
            $gClient->setRedirectUri(Mage::getUrl('socialpopup/index/redirecUri'));
            $google_oauthV2 = new Google_Oauth2Service($gClient);
            $gClient->authenticate($_GET['code']);
            $url = Mage::getUrl('customer/account');
            if ($gClient->getAccessToken()) {
                $user = $google_oauthV2->userinfo->get();
                $email = $user['email'];
                $firstName = $user['given_name'];
                $lastName = $user['family_name'];
                $googleId = $user['id'];
                $token = $gClient->getAccessToken();
                $customerModel = Mage::getModel('customer/customer');
                $customerCollection = $customerModel->getCollection()
                        ->addFieldToFilter('email', $email)
                        ->setPageSize(1);
                if ($customerModel->getSharingConfig()->isWebsiteScope()) {
                    $customerCollection->addAttributeToFilter(
                            'website_id', Mage::app()->getWebsite()->getId()
                    );
                }
                //echo "<pre>";print_R($customerCollection->getData());
                if ($customerCollection->count()) {
                    $customer = $customerCollection->getFirstItem();
                    $cModel = Mage::getModel('customer/customer')->load($customer->getEntityId());
                    //echo "<pre>";print_R($customer->getData());exit;
                    $cModel->setCbGid($googleId)
                ->setCbGtoken($token)
                ->setCbSocialpopupCustomer(1)
                ->save();
                    Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($cModel);
                } else {
                    Mage::Helper('socialpopup/google')->createAccountAndConnect($email, $firstName, $lastName, $googleId, $token);
                }
            }
            Mage::app()->getFrontController()->getResponse()->setRedirect($url);
            return;
        }
    }

    public function facebookAction() {
       
        $facebook = new Facebooklib(array(
            'appId' => Mage::helper('socialpopup/facebook')->_getClientId(),
            'secret' => Mage::helper('socialpopup/facebook')->_getClientSecret()
        ));
        $url = Mage::getUrl('customer/account');
        
        if ($facebook->getAccessToken()) {
            $user = $facebook->api('/me');
            $email = $user['email'];
            $firstName = $user['first_name'];
            $lastName = $user['last_name'];
            $googleId = $user['id'];
            $token = $facebook->getAccessToken();
            $customerModel = Mage::getModel('customer/customer');
            $customerCollection = $customerModel->getCollection()
                    ->addFieldToFilter('email', $email)
                    ->setPageSize(1);
            if ($customerModel->getSharingConfig()->isWebsiteScope()) {
                $customerCollection->addAttributeToFilter(
                        'website_id', Mage::app()->getWebsite()->getId()
                );
            }
        
            if ($customerCollection->count()) {
                $customer = $customerCollection->getFirstItem();
                Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
            } else {
                Mage::Helper('socialpopup/facebook')->createAccountAndConnect($email, $firstName, $lastName, $googleId, $token);
            }
        }
        Mage::app()->getFrontController()->getResponse()->setRedirect($url);
        return;
    }

    public function updateCountAction(){
        $core_data = new Mage_Core_Model_Config();
        $data = $this->getRequest()->getParam('button');
        if(isset($data)){
            $count = Mage::getStoreConfig('socialpopup/gmail/buttonclick',Mage::app()->getStore());
            $core_data ->saveConfig('socialpopup/gmail/buttonclick', $count+1, 'default', 0);

        }else{
            $count = Mage::getStoreConfig('socialpopup/gmail/viewcount',Mage::app()->getStore());
            $core_data ->saveConfig('socialpopup/gmail/viewcount', $count+1, 'default', 0);
        }
    }

}
