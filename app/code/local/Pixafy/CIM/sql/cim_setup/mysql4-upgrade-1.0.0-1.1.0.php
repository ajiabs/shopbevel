<?php
/**
 * @package CIM.Setup
 * @author Tariq Chaudhry <tchaudhry@pixafy.com>
 * 
 * Upgrades CIM module to 1.1.0 - adds new columns to cim_paymentprofiles table.
 */

$installer = $this;

/*
 * add exp month and year to table
 */
$installer->getConnection()->addColumn($this->getTable('cim_paymentprofiles'), 'cc_exp_month', 'varchar(2) NOT NULL' );
$installer->getConnection()->addColumn($this->getTable('cim_paymentprofiles'), 'cc_exp_year', 'varchar(4) NOT NULL' );

$installer->endSetup();