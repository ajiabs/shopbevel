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

$fieldsMoodboardUrl = $fieldsDefault;
$fieldsMoodboardUrl['label'] = "Moodboard Url";
$fieldsMoodboardUrl['input'] = "text";
$installer->addAttribute('customer', 'moodboard_url', $fieldsMoodboardUrl);

$fieldsCity = $fieldsDefault;
$fieldsCity['label'] = "Designer City";
$fieldsCity['input'] = "text";
$installer->addAttribute('customer', 'designer_city', $fieldsCity);

$fieldsState = $fieldsDefault;
$fieldsState['label'] = "Designer State";
$fieldsState['input'] = "text";
$installer->addAttribute('customer', 'designer_state', $fieldsState);

$fieldsCountry = $fieldsDefault;
$fieldsCountry['label'] = "Designer Country";
$fieldsCountry['input'] = "text";
$installer->addAttribute('customer', 'designer_country', $fieldsCountry);

$fieldsZip = $fieldsDefault;
$fieldsZip['label'] = "Designer Zipcode";
$fieldsZip['input'] = "text";
$installer->addAttribute('customer', 'designer_zip', $fieldsZip);

$installer->endSetup();
?>
