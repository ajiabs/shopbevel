<?php
$installer = $this;
$installer->startSetup();
 
$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('registeration/waitlist')};
CREATE TABLE {$this->getTable('registeration/waitlist')} (
  `entity_id` int(10) unsigned NOT NULL auto_increment,
  `cust_email` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `wait_rank` int(40) NOT NULL,
  `cust_ip` varchar(255) NOT NULL,
  `cust_key` varchar(255) NOT NULL,
  `actived` int(1) default 0,
  `store_id` smallint(5) unsigned DEFAULT NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  PRIMARY KEY  (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='PROFILE';


-- DROP TABLE IF EXISTS {$this->getTable('registeration/invitelink')};
CREATE TABLE {$this->getTable('registeration/invitelink')} (
  `entity_id` int(10) unsigned NOT NULL auto_increment,
  `cust_email` varchar(255) NOT NULL,
  `user_ip` varchar(255) NOT NULL,
  `store_id` smallint(5) unsigned DEFAULT NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  PRIMARY KEY  (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='PROFILE';


  ");
 
$installer->endSetup();