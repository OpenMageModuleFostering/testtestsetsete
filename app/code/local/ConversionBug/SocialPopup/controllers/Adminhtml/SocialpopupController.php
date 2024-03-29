<?php

class ConversionBug_SocialPopup_Adminhtml_SocialpopupController extends Mage_Adminhtml_Controller_action {

    protected $_countTotals = true;

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('socialpopup/items')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('CB Dashboard'), Mage::helper('adminhtml')->__('CB Dashboard'));

        return $this;
    }

    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    public function exportCsvAction() {
        $fileName = 'socialpopup.csv';
        $content = $this->getLayout()->createBlock('socialpopup/adminhtml_socialpopup_grid')
                ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName = 'socialpopup.xml';
        $content = $this->getLayout()->createBlock('socialpopup/adminhtml_socialpopup_grid')
                ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream') {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }

}
