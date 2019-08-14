<?php

class ConversionBug_SocialPopup_Block_Adminhtml_Socialpopup_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('socialpopup_form', array('legend'=>Mage::helper('socialpopup')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('socialpopup')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('socialpopup')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('socialpopup')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('socialpopup')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('socialpopup')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('socialpopup')->__('Content'),
          'title'     => Mage::helper('socialpopup')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getSocialPopupData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getSocialPopupData());
          Mage::getSingleton('adminhtml/session')->setSocialPopupData(null);
      } elseif ( Mage::registry('socialpopup_data') ) {
          $form->setValues(Mage::registry('socialpopup_data')->getData());
      }
      return parent::_prepareForm();
  }
}