<?php

require_once Mage::getModuleDir('controllers', 'Mage_Customer') . DS . 'AccountController.php';

class ConversionBug_SocialPopup_AccountController extends Mage_Customer_AccountController {

    public function createPostAction() {

        $params = $this->getRequest()->getParams();
        //echo "<pre>";print_r($params);exit;
        if (isset($params['cb-registartion'])) {
            $response = array();
            $session = $this->_getSession();
            if ($session->isLoggedIn()) {
                $this->_redirect('*/*/');
                return;
            }
            $session->setEscapeMessages(true); // prevent XSS injection in user input
            //$customer = $this->_getCustomer();
            $customer = Mage::getModel("customer/customer");
            $websiteId = Mage::app()->getWebsite()->getId();
            $store = Mage::app()->getStore();
            $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
            $customer->loadByEmail($params['email']);
            if ($customer->getId()) {
                $response['msg'] = 'emailisexist';
                $this->_forward('loginpost');
                //$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
                return;
            }

            //setting first & lastname as email first part
            $parts = explode('@', $params['email']);
            $params['firstname'] = $params['lastname'] = $parts[0];


            $customer->setWebsiteId($websiteId)
                    ->setStore($store)
                    ->setFirstname($params['firstname'])
                    ->setLastname($params['lastname'])
                    ->setCbSocialpopupCustomer(1)
                    ->setEmail($params['email'])
                    ->setPassword($params['password']);

            // Try create customer
            try {
                $customer->save();
                $customer->setConfirmation(null);
                $customer->save();
                $response['msg'] = 'success';
                $storeId = $customer->getSendemailStoreId();
                $customer->sendNewAccountEmail('registered', '', $storeId);

                Mage::getSingleton('customer/session')->loginById($customer->getId());

                $this->_redirect('*/*/');
                //$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
                return;
            } catch (Exception $e) {
                $response['msg'] = 'error' . $e->getMessage();
                $this->_redirect('*/*/');
                //$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
                return;
            }
        } else {
            return parent::createPostAction();
        }
    }

    /**
     * Login post action
     */
    public function loginPostAction() {


        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $session = $this->_getSession();

        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost();
            if (!empty($login['email']) && !empty($login['password'])) {
                try {
                    $session->login($login['email'], $login['password']);
                    if ($session->getCustomer()->getIsJustConfirmed()) {
                        $this->_welcomeCustomer($session->getCustomer(), true);
                    }
                } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $value = $this->_getHelper('customer')->getEmailConfirmationUrl($login['email']);
                            $message = $this->_getHelper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage();
                            break;
                        default:
                            $message = $e->getMessage();
                    }
                    $session->addError($message);
                    $session->setUsername($login['email']);
                } catch (Exception $e) {
                    // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
                }
            } else {
                return parent::loginPostAction();
                //$session->addError($this->__('Login and password are required.'));
            }
        }

        $this->_loginPostRedirect();
    }

}
