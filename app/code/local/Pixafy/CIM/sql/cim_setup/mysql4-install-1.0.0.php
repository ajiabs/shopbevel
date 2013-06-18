<?php
$installer	=	$this;
$installer->getConnection()->addColumn($installer->getTable('customer_entity'), 'cim_id', "varchar(30)");
$installer->getConnection()->addColumn($installer->getTable('customer_entity'), 'cim_created', "int(1)");

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->addAttribute('customer', 'cim_id', array(
'label'         => 'Cim Id',
'type'          => 'static',
'visible'       => 0,
'required'      => 0,
'position'      => 999,
));

$setup->addAttribute('customer', 'cim_created', array(
'label'         => 'Cim Id',
'type'          => 'static',
'visible'       => 0,
'required'      => 0,
'position'      => 1000,
));
$installer->startSetup();
$installer->run("
CREATE TABLE IF NOT EXISTS `cim_paymentprofiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `profile_id` varchar(20) DEFAULT NULL,
  `cim_id` varchar(30) DEFAULT NULL,
  `cc_type` varchar(20) DEFAULT NULL,
  `cc_lastfour` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");
$installer->endSetup();


