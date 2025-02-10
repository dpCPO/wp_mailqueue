CREATE TABLE tx_wpmailqueue_domain_model_mail (

	subject varchar(255) DEFAULT '' NOT NULL,
	body text,
	body_html text,
	sender varchar(255) DEFAULT '' NOT NULL,
	recipient varchar(255) DEFAULT '' NOT NULL,
	cc varchar(255) DEFAULT '' NOT NULL,
	bcc varchar(255) DEFAULT '' NOT NULL,
	attachements text DEFAULT '' NOT NULL,
	date_sent int(11) DEFAULT 0 NOT NULL,
	type varchar(255) DEFAULT '' NOT NULL

);
