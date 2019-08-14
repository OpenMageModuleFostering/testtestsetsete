<?php

class ConversionBug_SocialPopup_Block_Adminhtml_Socialpopup_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('socialpopup_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('socialpopup')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('socialpopup')->__('Item Information'),
          'title'     => Mage::helper('socialpopup')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('socialpopup/adminhtml_socialpopup_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}