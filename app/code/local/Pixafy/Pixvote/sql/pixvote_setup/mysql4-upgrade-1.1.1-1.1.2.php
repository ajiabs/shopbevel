<?php
$installer = $this;

$installer->startSetup();

$fieldsDefault = array(
        'type'              => 'varchar',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Location',
        'input'             => 'textarea',
        'class'             => '',
        'source'            => '',
        'global'            => '0',
        'visible'           => true,
        'required'          => false,
        'user_defined'      => true,
        'default'           => '',
        //'visible_on_front'  => true,
    );

$fieldsDescription = $fieldsDefault;
$installer->addAttribute('customer', 'description', $fieldsDescription);

$fieldsFbUrl = $fieldsDefault;
$fieldsFbUrl['label'] = "Facebook Url";
$fieldsFbUrl['input'] = "text";
$installer->addAttribute('customer', 'fb_url', $fieldsFbUrl);

$fieldsTwitterUrl = $fieldsDefault;
$fieldsFbUrl['label'] = "Twitter Url";
$fieldsFbUrl['input'] = "text";
$installer->addAttribute('customer', 'twitter_url', $fieldsTwitterUrl);

$fieldsPintrestUrl = $fieldsDefault;
$fieldsFbUrl['label'] = "Pintrest Url";
$fieldsFbUrl['input'] = "text";
$installer->addAttribute('customer', 'pinterest_url', $fieldsPintrestUrl);

$fieldsWebsiteUrl = $fieldsDefault;
$fieldsFbUrl['label'] = "Website Url";
$fieldsFbUrl['input'] = "text";
$installer->addAttribute('customer', 'website_url', $fieldsWebsiteUrl);

$fieldsSpecialties = $fieldsDefault;
$fieldsFbUrl['label'] = "Specialties";
$fieldsFbUrl['input'] = "text";
$installer->addAttribute('customer', 'specialties', $fieldsPintrestUrl);

$installer->endSetup();
?>
