<?php
$installer = $this;

$installer->startSetup();

$installer->addAttribute('customer', 'how_it_works_email_sent', array(
        'type'              => 'int',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'How it works email sent',
        'input'             => 'boolean',
        'class'             => '',
        'source'            => '',
        'global'            => '0',
        'visible'           => true,
        'required'          => false,
        'user_defined'      => true,
        'default'           => '',
        'visible_on_front'  => true,
    ));

$installer->addAttribute('customer', 'gift_email_sent', array(
        'type'              => 'int',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Gift certificate email sent?',
        'input'             => 'boolean',
        'class'             => '',
        'source'            => '',
        'global'            => '0',
        'visible'           => true,
        'required'          => false,
        'user_defined'      => true,
        'default'           => '',
        'visible_on_front'  => true,
    ));

$installer->addAttribute('customer', 'profile_completed_email_sent', array(
        'type'              => 'int',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Profile completed email sent?',
        'input'             => 'boolean',
        'class'             => '',
        'source'            => '',
        'global'            => '0',
        'visible'           => true,
        'required'          => false,
        'user_defined'      => true,
        'default'           => '',
        'visible_on_front'  => true,
    ));
$installer->endSetup();
?>
