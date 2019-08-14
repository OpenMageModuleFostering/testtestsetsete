<?php
$content = '<p><img alt="" src="{{media url="wysiwyg/image.png"}}" /><img alt="" src="{{media url="wysiwyg/line.png"}}" />Catchy offer line goes here</p>';
//if you want one block for each store view, get the store collection
$stores = Mage::getModel('core/store')->getCollection()->addFieldToFilter('store_id', array('gt'=>0))->getAllIds();
//if you want one general block for all the store viwes, uncomment the line below
$stores = array(0);
foreach ($stores as $store){
    $block = Mage::getModel('cms/block');
    $block->setTitle('cb-static-block');
    $block->setIdentifier('cb-static-block');
    $block->setStores(array($store));
    $block->setIsActive(1);
    $block->setContent($content);
    $block->save();
}