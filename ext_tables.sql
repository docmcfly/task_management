CREATE TABLE tx_taskmanagement_domain_model_task (
	title varchar(255) NOT NULL DEFAULT '',
	done_at int(11) NOT NULL DEFAULT '0',
	user int(11) unsigned NOT NULL DEFAULT '0',
	repeat_period_count int(11) NOT NULL DEFAULT '0',
	repeat_period_unit varchar(6) NOT NULL DEFAULT 'month',
	next_repetition date  NOT NULL DEFAULT '0000-00-00'
);

#
# Table structure for table 'fe_users' 
#
CREATE TABLE fe_users (
   info_mail_when_repeated_task_added  SMALLINT (5) UNSIGNED DEFAULT '0' NOT NULL,
);

