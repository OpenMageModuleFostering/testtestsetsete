<?php

$installer = $this;
$installer->startSetup();

$installer->setCustomerAttributes(
    array(
       'cb_socialpopup_customer' => array(
           'type' => 'text',
           'visible' => false,
           'required' => false,
           'user_defined' => false                
       ),
        'cb_gid' => array(
            'type' => 'text',
            'visible' => false,
            'required' => false,
            'user_defined' => false                
        ),            
        'cb_gtoken' => array(
            'type' => 'text',
            'visible' => false,
            'required' => false,
            'user_defined' => false                
        ),
        'cb_fid' => array(
            'type' => 'text',
            'visible' => false,
            'required' => false,
            'user_defined' => false                
        ),            
        'cb_ftoken' => array(
            'type' => 'text',
            'visible' => false,
            'required' => false,
            'user_defined' => false                
        )    
    )
);

// Install our custom attributes
$installer->installCustomerAttributes();

// Remove our custom attributes (for testing)
//$installer->removeCustomerAttributes();

$installer->endSetup();