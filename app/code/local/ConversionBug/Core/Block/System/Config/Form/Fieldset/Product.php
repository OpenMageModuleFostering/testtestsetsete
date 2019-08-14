<?php
class ConversionBug_Core_Block_System_Config_Form_Fieldset_Product extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{

    protected $_dummyElement;
    protected $_fieldRenderer;
    protected $_values;

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html =  $this->getLayout()->createBlock('core/template')->setTemplate('conversionbugcore/info.phtml')->toHtml();

        return $html;


    }


}
