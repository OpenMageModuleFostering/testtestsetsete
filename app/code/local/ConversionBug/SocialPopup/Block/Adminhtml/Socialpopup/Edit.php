<?php

class ConversionBug_SocialPopup_Block_Adminhtml_Socialpopup_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'socialpopup';
        $this->_controller = 'adminhtml_socialpopup';
        
        $this->_updateButton('save', 'label', Mage::helper('socialpopup')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('socialpopup')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('socialpopup_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'socialpopup_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'socialpopup_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('socialpopup_data') && Mage::registry('socialpopup_data')->getId() ) {
            return Mage::helper('socialpopup')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('socialpopup_data')->getTitle()));
        } else {
            return Mage::helper('socialpopup')->__('Add Item');
        }
    }
}