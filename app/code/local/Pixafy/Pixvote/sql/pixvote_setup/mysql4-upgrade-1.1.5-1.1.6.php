<?php
$installer = $this;

$installer->startSetup();

$voteEmailSentFields = array(
        'type'              => 'int',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Has recieved vote email?',
        'input'             => 'text',
        'class'             => '',
        'source'            => '',
        'global'            => '0',
        'visible'           => false,
        'required'          => false,
        'user_defined'      => true,
        'default'           => '0',
        //'visible_on_front'  => true,
    );

$installer->addAttribute('customer', 'vote_email_sent', $voteEmailSentFields);

$installer->endSetup();
?>
