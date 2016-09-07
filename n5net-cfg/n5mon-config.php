<?

/* N5 NETWORKS SERVER MONITOR CONFIG */

	// Server Name
	$GLOBALS['server'] = "Nico";
	
	
	// 	Database Connection
	$GLOBALS['db_host'] = "localhost";
	$GLOBALS['db_user'] = "root";
	$GLOBALS['db_pass'] = "br400doh";
			
	
	// Notification Emails	/////////////////////////////////////////
	$GLOBALS['sms_email'] = "5413259183@tmomail.net";
	$GLOBALS['alert_email'] = "q3shafe@gmail.com";
	
	// Minimum Disk Space In GB	/////////////////////////////////////
	$GLOBALS['disk_limit'] = '5.0';
	
	// Define services to monitor  Name -> Process name	/////////////
	$processes = array(
                        'fake'=>'fake',
			'web'=>'apache2',
			'ftp'=>'proftpd',			
			'mysql'=>'mysql',
			'smtp'=>'smtp'			
			);

	// command to restart named service:  Name (per above) -> restart command	/////////////
	$rprocesses = array(
			'web'=>'/etc/init.d/apache2 stop;/etc/init.d/apache2 start',
			'ftp'=>'/etc/init.d/proftpd stop;/etc/init.d/proftpd start',			
			'mysql'=>'/etc/init.d/mysql stop;/etc/init.d/mysql start',
			'smtp'=>'ls -lah'			
			);

			
	// Virus Scan Directories  What -> Directories	//////////////////
	$scan_dirs = array(
			'web'=>'/var/logs/',
			'web'=>'/home/'
			);

	// BACKUP Directories  prefix -> directory	//////////////////////
	$backup_dirs = array(
			'web'=>'/home/',
			'apachecfgs'=>'/etc/apache2/sites-enabled/',
			);
			
	// Load average limits 1, 5 and 15 minutes	//////////////////////
	$load_limits = array(
			'0'=>'0.0',
			'1'=>'0.0',
			'2'=>'0.0'
			);
			
	// Where do backups go?
	$GLOBALS['backup_dir'] = "/backups/sites/";
	$GLOBALS['dbbackup_dir'] = "/backups/db/";
	
	// How many days of backups to keep
	$GLOBALS['backup_days'] = "2";
	$GLOBALS['dbbackup_days'] = "3";	

	
	//print_r($GLOBALS);
	
	?>