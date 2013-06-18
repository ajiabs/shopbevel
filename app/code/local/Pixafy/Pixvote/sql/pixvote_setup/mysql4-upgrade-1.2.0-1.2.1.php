<?php
$installer = $this;
$installer->startSetup();
$fields = array(
        'type'              => 'int',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Received vote store credit?',
        'input'             => 'boolean',
        'class'             => '',
        'source'            => '',
        'global'            => '0',
        'visible'           => false,
        'required'          => false,
        'user_defined'      => true,
        'default'           => '',
    );
$installer->addAttribute('customer', 'received_vote_credit', $fields);

$installer->endSetup();
?>
