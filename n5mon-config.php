<?php

/* 
		N5 NETWORKS SERVER MONITOR CONFIG 
		Version 1.0r12

		Each option is documented.  If you need help visit http://support.n5net.com.
*/



	// SERVER NAME /////////////////////////////////////////////////////////////////////////////
	/* 	This is used to identify the server in alerts and is used to name backup archives.	*/
	$GLOBALS['server'] = "My Server";


	// NOTIFICATION EMAILS	///////////////////////////////////////////////////////////////////
	$GLOBALS['sms_email'] = "5555551212@tmomail.net";  // This is simply a secondary email, I use an email to sms gateway
	$GLOBALS['alert_email'] = "me@dmail.com";
	$GLOBALS['helpdesk_email'] = "helpdesk@domain.com";

	
	
	// MINIMUM DISK SPACE IN GB	////////////////////////////////////////////////////////////////
	$GLOBALS['disk_limit'] = '80.0';
	
	// Send disk alert email to helpdesk email address?
	$GLOBALS['disk_helpdesk'] = 1;	

	
	// PROCESSES TO MONITOR ///////////////////////////////////////////////////////////////////
	/* 
		Add as many services as you want
		the format is 'Name' => 'Process"  
		Example 'WebServer' => 'httpd'
	*/
	$processes = array(
			'vpn'=>'n5vpn',
			'web'=>'apache2',
			'ftp'=>'proftpd',			
			'mysql'=>'mysql',
			'smtp'=>'smtp'			
			);
			
	
	
	// SERVICE DOWN, RECOVERY COMMANDS	///////////////////////////////////////////////////////
		/* 
		These correspond to the names of each service specified in the $processes section above
		the format is 'Name' => '#commandsToRun"  
		Example 'WebServer' => 'service restart httpd'
	*/
	$rprocesses = array(
			'vpn'=>'/etc/init.d/proftpd status;',
			'web'=>'/etc/init.d/apache2 stop;/etc/init.d/apache2 start',
			'ftp'=>'/etc/init.d/proftpd stop;/etc/init.d/proftpd start',			
			'mysql'=>'/etc/init.d/mysql stop;/etc/init.d/mysql start',
			'smtp'=>'ls -lah'			
			);

	// Send email to helpdesk in the event a process is down
	$GLOBALS['process_helpdesk'] = 0;		
			
			
	// VIRUS SCAN DIRECTORIES ///////////////////////////////////////////////////////////////////
	/*
		You can virus scan as many different folders as you want.  Specify those
		paths here in the following format
			'FriendlyName' => 'Path-to-scan'
	*/
	$scan_dirs = array(
			'web'=>'/var/logs/',
			'web'=>'/home/'
			);
	// If a virus is found send an email to the helpdesk address?
	$GLOBALS['virus_helpdesk'] = 1;		
	
	
	// BACKUP DIRECTORY ////////////////////////////////////////////////////////////////////////////
	/*
		You can backup as many different folders as you want.  Specify those
		paths here in the following format
			'FriendlyName' => 'Path-to-backup'
	*/	
	$backup_dirs = array(
			'web'=>'/home/',
			'apachecfgs'=>'/etc/apache2/sites-enabled/',
			);
			
	
	// MAXIMUM LOAD AVERAGES 1, 5 and 15 minutes	/////////////////////////////////////////////////
	$load_limits = array(
			'0'=>'0.0',
			'1'=>'1.5',
			'2'=>'1.5'
			);
			
	// WHERE DO BACKUPS GO? //////////////////////////////////////////////////////////////////////////
	$GLOBALS['backup_dir'] = "/backups/sites/";
	$GLOBALS['dbbackup_dir'] = "/backups/db/";
	
	
	// HOW MANY DAYS TO KEEP BACKUPS? ////////////////////////////////////////////////////////////////
	$GLOBALS['backup_days'] = "7";
	$GLOBALS['dbbackup_days'] = "7";	
	
	
	// 	DATABASE CONNECTION //////////////////////////////////////////////////////////////////////
	$GLOBALS['db_host'] = "localhost";
	$GLOBALS['db_user'] = "root";
	$GLOBALS['db_pass'] = "password";

	
?>