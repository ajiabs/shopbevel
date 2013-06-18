<?php
$installer = $this;

$installer->startSetup();

$firstViewFields = array(
        'type'              => 'varchar',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Has Designer viewed profile?',
        'input'             => 'text',
        'class'             => '',
        'source'            => '',
        'global'            => '0',
        'visible'           => false,
        'required'          => false,
        'user_defined'      => true,
        'default'           => '',
        //'visible_on_front'  => true,
    );

$installer->addAttribute('customer', 'has_viewed_profile', $firstViewFields);

$installer->endSetup();
?>
