<?php
class ConversionBug_SocialPopup_Block_Adminhtml_Socialpopup extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_socialpopup';
    $this->_blockGroup = 'socialpopup';
    $this->_headerText = Mage::helper('socialpopup')->__('CB Dashboard');
    $this->_addButtonLabel = Mage::helper('socialpopup')->__('Add Item');
    parent::__construct();
    $this->_removeButton('add');
  }
}