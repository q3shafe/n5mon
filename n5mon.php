<?php
/* N5 NETWORKS SERVER MONITOR */
/* brian@n5net */
include_once("n5mon-config.php");		
require_once("PHPMailerAutoload.php");

/*
TODO
- Specify what monitors to run (i.e. exclude load average or some shit like that)
- SSL Certificate checks on remote urls
- Streamline initial configuration process
- Fix all php warnings.
- checkurl functionality except defined like services are in config.
*/

error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED); // Don't show warnings


array_shift($argv);
$action = $argv[0];
$id = $argv[1];
	echo "\n";			
	echo "-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-\n";
	echo "N5 Networks System Monitor\n";			
	echo "Low overhead all purpose system monitor and maintenance tool\n";		
	echo "\n";		
	echo "2016, 2017 Brian Shaffer / N5 Networks\n";		
	echo "brian@n5net.com\n";		
	echo "http://dev.n5net.com/software/ \n";
	echo "Licensed under the GPL v2.0\n";		
	echo "\n";		

	if (!$action) 
	{

		echo "Command line options:\n";		
		echo "	php ./n5mon.php monitor - Runs all monitors\n";		
		echo "	php ./n5mon.php backup - Runs all backups\n";			
		echo "	php ./n5mon.php dbbackup - Backup and archive all databases\n";			
		echo "	php ./n5mon.php vscan - Perform Virus Scan\n";			
		echo "	php ./n5mon.php vscan-clean - Perform Virus Scan and moves infected files to specified quarantine folder\n";			
		echo "	php ./n5mon.php purge - Purge oldest backup files - saves the last 5\n";					
		echo "	php ./n5mon.php checksites - The same as checkurl below, will check a list of sites specified in the config.\n";
		echo "	php ./n5mon.php checkurl http://domain.com - check's to see the url is returning content and correct status codes\n";							
		echo "	php ./n5mon.php blacklisted xxx.xxx.xxx.xxx - Check an IP address against a list of email blacklists\n";
		echo "	php ./n5mon.php checkdnsbl - Check same as blacklisted, but will check a list of site specified in the config\n";
		echo "\n";
		echo "	php ./n5mon.php testemail - Sends a test message to all enabled emails in cfg file\n";							
		echo "\n";		
		echo "All options are stored in n5mon-config.php\n";		
		echo "\n";			
		echo "\n";			
		exit;
	}
	echo "-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-\n";
	echo "[NOTICE] Starting.\n";
	
// Check that alerts.dat exists and is writable
$path = $GLOBALS['n5mon_path'];
$file = $path . "/alerts.dat";	
if (!file_exists($file))
{
	echo "[NOTICE] alerts.dat not found creating a new one.\n";
	$cmdline = "touch " . $file;
	system($cmdline);
	$cmdline = "chmod 0777 " . $file;
	system($cmdline);
}
	 
if ($action == "checksites")
{
	foreach ($link_urls as $x => $x_value) 
	{
			echo "[ACTION] Checking site " . $x_value . "\n";	
			checkurl($x_value);
	}
}	
	
if ($action == "checkurl")
{		
		// check for url
		if (!$id)
		{
			echo "[ERROR!] No URL Provided, exiting\n";
			exit;
		}
		
		checkurl($id);
}	


if ($action == "blacklisted")
{		
		// check for url
		if (!$id)
		{
			echo "[ERROR!] No IP Provided, exiting\n";
			exit;
		}
		 echo "[ACTION] Checking IP " . $id . " against DNSDL databases.\n";
		dnsbllookup($id);
		exit;
}	


if ($action == "checkdnsbl")
{
	foreach ($dnsdb_ips as $x => $x_value) 
	{
			echo "[ACTION] Checking IP against dnsbl blacklists " . $x . " : " . $x_value . " \n";	
			dnsbllookup($x_value, $x);
	}
	exit;
}	

if ($action == "testemail")
{
	$subject = "Test Message from N5MON on " . $GLOBALS['server'];	
	$body = "This is a test message.  If you got it, it works!";
	send_alert($subject, $body);
	send_helpdesk($subject, $body);
	echo "[ACTION] Sending test email...\n";
}


	
/* Remove Oldest Backup */
if($action == "purge")
{
		
		echo "[ACTION] Removing mysql backups older than ". $GLOBALS['dbbackup_days'] . " days\n";
		echo "[ACTION] Removing regular backups older than ". $GLOBALS['backup_days'] . " days\n";

		// Regular backups
		$xfile =  get_oldest_file($GLOBALS['backup_dir'],$GLOBALS['backup_days'] ); 
		echo "[NOTICE] Oldest Is: ";
		echo $xfile;
		echo "\n";
		if ($xfile) { system("rm " . $GLOBALS['backup_dir']  . $xfile); }
		// Databases backups
		$xfile =  get_oldest_file($GLOBALS['dbbackup_dir'],$GLOBALS['dbbackup_days'] ); 
		echo "[NOTICE] Oldest Is: ";
		echo $xfile;
		echo "\n";
		if ($xfile) { 		system("rm " . $GLOBALS['dbbackup_dir']  . $xfile);	}
}		
	
/* Backups */
if($action == "backup")
{			
	make_backup_dir();
	foreach($backup_dirs as $x => $x_value) 
	{
			echo "[NOTICE] Backup directory " . $x_value . "\n";	
			$today = date("Y-m-d");
			$server = $GLOBALS['server'];
			$server = str_replace(" ", "_" , $server);					
			$server = str_replace("'", "_" , $server);								
			$dest = $GLOBALS['backup_dir'];
			$outname = $server . "_" . $today . ".tar.gz";
			system("tar -cvzf " . $dest . $outname . " " . $x_value . "/*");	
	}       
}

/* Backup All Databases */
if ($action == "dbbackup")
{			
	make_backup_dir();
	echo "[ACTION] Backup All Databases\n";	
	dodumps($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass']);
	echo "[ACTION] Compressing backups\n";	
	zipdump("databases");
}

/* Scan for Viruses */
if($action == "vscan")
{			
	 // Update Virus Definitions
	echo "[ACTION] Updating Virus definitions\n";	
	$cmdline = "freshclam";
	system($cmdline);
	
	foreach($scan_dirs as $x => $x_value) {
		echo "[ACTION] Scanning directory " . $x_value . " for viruses\n";	
		virus_scan($x_value);
	}
}

/* Scan for Viruses and quarantine */
if($action == "vscan-clean")
{			
	 // Update Virus Definitions
	echo "[ACTION] Updating Virus definitions\n";	
	$cmdline = "freshclam";
	system($cmdline);
	
	// check that quarantine directory exists if not create it.
	$qdir = $GLOBALS['qdir'];
	echo "[NOTICE] Checking if " . $qdir . " exists...\n";	
	if (!file_exists($qdir) && !is_dir($qdir)) 
	{	
		echo "[NOTICE] Quarantine directory, " . $qdir . " does not exist, creating it...\n";	
		mkdir($qdir);         
		// double check that it was created
		if (!file_exists($qdir) && !is_dir($qdir)) 
		{	
			echo "[ERROR!] could not create directory, " . $qdir . ".  N5MON is exiting...\n";	
			
		} else {
			echo "[NOTICE] directory, " . $qdir . " Has been created...\n";	
		}
	} else {
		echo "[NOTICE] quaratine folder is ok\n";	
	}
		
	
	foreach ($scan_dirs as $x => $x_value) {
		echo "[ACTION] Scanning directory " . $x_value . " for viruses [quarantine enabled]\n";	
		virus_scan_q($x_value);
	}
}

/* Run Normal Monitors */
if ($action == "monitor")
{	

	echo "\n";
	echo "[ACTION] RUNNING ALL MONITORS\n";	
	echo "\n";
	

	// DNSBL Monitor
	$server = $GLOBALS['server'];
	$host = gethostname();
	$thisIP = gethostbyname($host);
	echo "[ACTION] Checking IP against DNSDL databases (" . $thisIP . ").\n";
	dnsbllookup($thisIP, $server);
	echo "\n";

	// DISK MONITOR
	$bytes = disk_free_space("/");
	$si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );
	$base = 1024;
	$class = min((int)log($bytes , $base) , count($si_prefix) - 1);
	$gb_free = sprintf('%1.2f' , $bytes / pow($base,3));
	
	//echo $gb_free . " FREE\n\n";
	
	
	echo "[ACTION] Checking if free disk space is at least " . $GLOBALS['disk_limit'] . "GB ...\n";
	if($GLOBALS['disk_limit']>$gb_free) {
		echo "[RESULT] FAILED! Disk space check " . $gb_free . " GB available...\n";
		$server = $GLOBALS['server'];
		$subject = '[SERVER MONITOR] ' . $server . ' IS LOW ON DISK SPACE!';
		$body = $server . ' IS LOW ON DISK SPACE! There is currently ' . $gb_free . ' GB free space.';
		$body .= "\nGenerated by n5mon: https://github.com/q3shafe/n5mon by N5 Networks\n";
		if(!already_alerted("disk","1")) 
		{
			send_alert($subject,$body);
			record_alert("disk","1");
			if($GLOBALS['disk_helpdesk']) 
			{
				send_helpdesk($subject,$body);
			}
		}
		
	} else {
		echo "[RESULT] PASSED! Disk space check, " . $gb_free . " GB available.\n";
		remove_alerted("disk","1");
	}
	echo "\n";
	// END DISK MONITOR

			
	foreach($processes as $x => $x_value) {
		echo "[ACTION] Checking Process: " . $x . "\n";	
		exec("ps aux | grep " . $x_value, $pids);
		if(!$pids[2]) {
			// attempt to restart then check again
			echo "[RESULT] FAILED! service " . $x . " (" . $x_value . ") is NOT running, Attempting restart.\n";
			write_service_log($x,$x_value);
			exec($rprocesses[$x], $null);
			exec("sleep 10", $null);
			exec("ps aux | grep " . $x_value, $xpids);

			if(!$xpids[2]) {
				echo "[RESULT] FAILED! service " . $x . " (" . $x_value . ") IS DOWN could not restart.\n";
				$server = $GLOBALS['server'];
				$subject = '[SERVER MONITOR] ' . $server . ' - ' . $x_value . ' IS DOWN!';
				$body = $server . ' Is reporting that service ' . $x . ' running ' . $x_value . ' IS NOT RUNNING!';
				$body .= "\nGenerated by n5mon: https://github.com/q3shafe/n5mon by N5 Networks\n";
				if(!already_alerted("process",$x)) 
				{
					send_alert($subject,$body);
					if($GLOBALS['process_helpdesk']) 
					{
						send_helpdesk($subject,$body);
					}
					record_alert("process",$x);
				}
						
			} else {
				echo "[RESULT] OK! service " . $x . " (" . $x_value . ") HAS BEEN RESTARTED\n";
				$server = $GLOBALS['server'];
				$subject = '[SERVER MONITOR WARNING] ' . $server . ' - ' . $x_value . ' WAS DOWN!';
				$body = $server . ' Is reporting that service ' . $x . ' running ' . $x_value . ' was down, but I was able to restart it.  This may be worth investigating.';
				$body .= "\nGenerated by n5mon: https://github.com/q3shafe/n5mon by N5 Networks\n";
				
				send_alert($subject, $body);
				remove_alerted("process", $x);
			}
		} else {
			
			echo "[RESULT] PASSED! service " . $x . " (" . $x_value . ") is running\n";
			remove_alerted("process", $x);
			
		}
		echo "\n";
		unset($pids);
		unset($xpids);
	}
	echo"\n";

	
	// LOAD Averages 1, 5 and 15
	$load = sys_getloadavg();
	$doload = 1;
	unset($pids);
	exec("ps aux | grep gzip", $pids);
	if ($pids[2]) {		
		$doload = 0;
	}
	unset($pids);
	exec("ps aux | grep clamscan", $pids);
	if ($pids[2]) {		
		$doload = 0;
	}
	
	if ($doload) {
	
		// 5 minute load
		echo "[ACTION] Checking 1 Minute load average\n";	
		if ($load[0]>$load_limits[0])
		{
					echo "[RESULT] FAILED! 1 minute load average is above " . $load_limits[0] . ", currently . " . $load[0] . "\n";
					write_load_log($load[0], "1 minute load average");
					$server = $GLOBALS['server'];
					$subject = '[SERVER MONITOR] ' . $server . ' - Load Average Is High';
					$body = $server . " Load average test FAILED! 1 minute load average is above " . $load_limits[0] . ", currently . " . $load[0] . "\n\n";
					exec("ps aux | sort -nrk 3,3 | head -n 5", $topfive);
					$x = 0;
					$y = count($topfive);
					while ($x<$y) {
							$body .= $topfive[$x] . "\n";
							$x++;
					}
					$body .= "\nGenerated by n5mon: https://github.com/q3shafe/n5mon by N5 Networks\n";
					
					if (!already_alerted("load1", "1")) 
					{
						send_alert($subject, $body);
						record_alert("load1", "1");
					} 
		} else {
					echo "[RESULT] PASSED\n";
					remove_alerted("load1", "1");
		}
		echo"\n";
		echo "[ACTION] Checking 5 Minute load average\n";	
		if ($load[1]>$load_limits[1])
		{
					echo "[RESULT] FAILED! 5 minute load average is above " . $load_limits[1] . ", currently . " . $load[1] . "\n";
					write_load_log($load[1], "5 minute load average");
					$server = $GLOBALS['server'];
					$subject = '[SERVER MONITOR] ' . $server . ' - Load Average Is High';
					$body = $server . " Load average test FAILED! 5 minute load average is above " . $load_limits[1] . ", currently . " . $load[1] . "\n\n";
					$x = 0;
					$y = count($topfive);
					while ($x<$y) {
							$body .= $topfive[$x] . "\n";
							$x++;
					}
					$body .= "\nGenerated by n5mon: https://github.com/q3shafe/n5mon by N5 Networks\n";
					
					if (!already_alerted("load5", "1")) 
					{
						send_alert($subject, $body);
						record_alert("load5", "1");
					} else {
						/*
						// r11?  run script for load averages?	- 	FIXME
						echo "   FAILED! service " . $x . " (" . $x_value . ") is NOT running, Attempting restart.\n";
						exec($rprocesses[$x], $null);
						*/
						
					}
		} else {
					echo "[RESULT] PASSED\n";
					remove_alerted("load5", "1");
		}
		echo"\n";
		echo "[ACTION] Checking 15 Minute load average\n";	
		if ($load[2]>$load_limits[2])
		{
					echo "[RESULT] FAILED! 15 minute load average is above " . $load_limits[2] . ", currently . " . $load[2] . "\n";
					write_load_log($load[2], "15 minute load average");
					$server = $GLOBALS['server'];
					$subject = '[SERVER MONITOR] ' . $server . ' - Load Average Is High';
					$body = $server . " Load average test FAILED! 15 minute load average is above " . $load_limits[2] . ", currently . " . $load[2] . "\n\n";
					$x = 0;
					$y = count($topfive);
					while ($x<$y) {
							$body .= $topfive[$x] . "\n";
							$x++;
					}
					$body .= "\nGenerated by n5mon: https://github.com/q3shafe/n5mon by N5 Networks\n";
					
					if (!already_alerted("load15", "1")) 
					{
						send_alert($subject, $body);
						record_alert("load15", "1");
					}
		} else {
					echo "[RESULT] PASSED\n";
					remove_alerted("load15", "1");
		}
	} else {
		echo "[NOTICE] !! Skipping Load Check, Backup Or Virus Scan in Progress !!\n";	
	}
	
	
	/* ADD IN HTTP CHECKS */
	// -- FIXME
	
	echo "\n";
	echo "[NOTICE] **** All tests have been completed. ****\n";
	
}	

function make_backup_dir()
{
	$dirname = $GLOBALS['backup_dir'];
	$filename = $dirname;
	if (!file_exists($filename)) {
	   mkdir($dirname, 0777);
	}	
	$dirname = $GLOBALS['dbbackup_dir'];
	$filename = $dirname;
	if (!file_exists($filename)) {
	   mkdir($dirname, 0777);
	}	

}

function zipdump ($servername) {
		$today = date("Y-m-d");		
		$outname = $GLOBALS['dbbackup_dir'] . $servername . "_" . $today . ".tar.gz";
		system("tar -cvzf " . $outname . " " . $GLOBALS['dbbackup_dir'] . "*.sql");
		system("rm " . $GLOBALS['dbbackup_dir'] . "*.sql");
}

function dodumps($db_host, $db_user, $db_pass) {

	$conn = mysqli_connect($db_host, $db_user, $db_pass, '');
	// Check connection
	
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error()); // TODO: log this or send alert 
	}
		echo '[NOTICE] connected to database';
		echo "\n";
		echo '[ACTION] Getting Database List';
		$result = mysqli_query($conn, "show databases;")
		or die(mysql_error());
		//print_r($result);
		while($row = mysqli_fetch_assoc($result)) 
		{
				print 'Dumping ' . $row['Database'];
				print '
                ';
				$db = $row['Database'];
				$today = date("Y-m-d");
				$outname = $GLOBALS['dbbackup_dir'] . $db . "_" . $today . ".sql";
				system("mysqldump -P3308 -h" . $db_host ." -u" . $db_user . " -p" . $db_pass . " " . $db . " > " . $outname);
		}
}


function get_oldest_file($directory, $days) 
{ 
	$c=0;
	if ($handle = opendir($directory)) 
	{ 
		while (false !== ($file = readdir($handle))) 
		{ 
			$files[] = $file; 
		} 
		foreach ($files as $val) 
		{ 
			//	echo $val . "\n";
			if (is_file($directory.$val)) 
			{
				//echo $val;
				//echo "\n"; 
				$file_date[$val] = filemtime($directory.$val);
				$c++;
			} 
		}     
	} 
	if($c>$days) {
		closedir($handle);
		asort($file_date, SORT_NUMERIC); 
		reset($file_date); 
		$oldest = key($file_date); 
		return $oldest; 
	} else {
		return 0;
	}
	
} 

/// VIRUS Scans
function virus_scan($dir)
{
	   $today = date("Y-m-d");

		// Run The Scan
		$cmdline = "clamscan -r -i " . $dir . " > /var/log/" . $today . "_virusscan.log";

		//echo $cmdline;
		//echo "\n";
		system($cmdline);

		$file = file_get_contents("/var/log/" . $today . "_virusscan.log");
		//echo $file;
		//echo "\n";
		//echo "\n";

		if (strpos($file,'FOUND') == true)
		{
			// Virus Found!
			$server = $GLOBALS['server'];
			$subject = "[SERVER MONITOR] " . $server . " - ACTION REQUIRED: A virus has been found on the server";
			$body = "A virus has been found on the server.\n\n";
			$body .= $file;
			$body .= "\n\nPlease take immediate action and remove these potential threats.";
			echo "[RESULT] " . $body;
			//echo "\n";
			send_alert($subject,$body);
			if($GLOBALS['virus_helpdesk']) 
			{
				send_helpdesk($subject,$body);
			}
			
		} else {
			$subject = "Virus Scan Completed - No Viruses Found";
			$body = "Virus Scan Completed - No Viruses Found";
			echo "[RESULT] " .$subject . "\n";
		}
}	

/// VIRUS Scans
function virus_scan_q($dir)
{
	   $today = date("Y-m-d");

		// Run The Scan
		$qdir = $GLOBALS['qdir'];
		$quar = $qdir . $today;
		// create the dated quaratine folder
		echo "[NOTICE] Creating new quaratine sub-directory " . $quar . "\n";
		mkdir($quar);
		
		$cmdline = "clamscan -r -i --move=" . $quar . " " . $dir . " > /var/log/" . $today . "_virusscan.log";	
				
		echo "[NOTICE] Using command: " . $cmdline;
		echo "\n";
		system($cmdline);

		$file = file_get_contents("/var/log/" . $today . "_virusscan.log");
		//echo $file;
		//echo "\n";
		//echo "\n";

		if (strpos($file,'FOUND') == true)
		{
			// Virus Found!
			$server = $GLOBALS['server'];
			$subject = "[SERVER MONITOR] " . $server . " - ACTION REQUIRED: A virus has been found on the server";
			$body = "A virus has been found on the server.  I have moved the infected files to " . $quar . " for review.\n\n";
			$body .= $file;
			$body .= "\n\nPlease take immediate action and remove these potential threats.";
			echo "[RESULT] " . $body;
			echo "\n";
			send_alert($subject,$body);
			if($GLOBALS['virus_helpdesk']) 
			{
				send_helpdesk($subject,$body);
			}
			
		} else {
			$subject = "Virus Scan Completed - No Viruses Found";
			$body = "Virus Scan Completed - No Viruses Found";
			echo "[RESULT] " .$subject . "\n";
			echo "\n";
		}
}

function remove_alerted($type,$what)
{
	global $GLOBALS;
	$path = $GLOBALS['n5mon_path'];
	$rline = $type . "," . $what . "\n";
	$file = file_get_contents($path . "/alerts.dat");
	$file = str_replace($rline, "", $file);			
	file_put_contents($path . "/alerts.dat", $file);		
}

function already_alerted($type, $what)
{
	global $GLOBALS;
	$alerted = 0;
	$path = $GLOBALS['n5mon_path'];
	$fp = fopen($path . '/alerts.dat', 'r');
	while (!feof($fp))
	{
		$line = fgets($fp, 2048);
		$delimiter = ",";
		$data = str_getcsv($line, $delimiter);
		$xtype = $data[0];
		$xwhat = $data[1];
		if (($xwhat == $what) && ($xtype == $type)) { $alerted++; }
	}
	fclose($fp);
	return $alerted;
}


function record_url_alert($type, $what) 
{
		 $path = $GLOBALS['n5mon_path'];
		 $file = $path . "/alerts.dat";
		 $line = $type . "," . $what . "\n";
		 echo $file;
		 file_put_contents($file, $line, FILE_APPEND);
}


function record_alert($type, $what) 
{
	
	if (!already_alerted($type, $what)) 
	{
		 $path = $GLOBALS['n5mon_path'];
		 $file = $path . "/alerts.dat";
		 $line = $type . "," . $what . "\n";
		 //echo $file;
		 file_put_contents($file, $line, FILE_APPEND);
	}
}

function write_load_log($load, $desc) 
{
	global $GLOBALS;
		$file = $GLOBALS;
		 $time = date("h:i:sa");
		 $today = date("Y-m-d");
		 $line = $desc . "," . $load . "," . $time . "," . $today . "\n";
		 if ($file) 
		 {
			file_put_contents($file, $line, FILE_APPEND);
		 }
}
	
function write_service_log($service, $desc) 
{
		 global $GLOBALS;
		 $file = $GLOBALS['service_log'];
		 $time = date("h:i:sa");
		 $today = date("Y-m-d");
		 $line = $desc . "," . $service . "," . $time . "," . $today . "\n";
		 if ($file) 
		 {
			file_put_contents($file, $line, FILE_APPEND);
		 }
}
function send_alert($subject, $body)
{
	global $GLOBALS;

	echo "[ALERT!] Sending Alert to " . $GLOBALS['alert_email'] . " - " . $subject . "\n"; 
	echo "[ALERT!] " . $body;
	$headers = "From: " . $GLOBALS['from_email'] . "\r\n";
	
	if ($GLOBALS['use_smtp'])
	{
		
		$mail = new PHPMailer(true);	
		$mail->IsSMTP(); // telling the class to use SMTP
		$mail->SMTPAuth = true; // enable SMTP authentication
		$mail->SMTPSecure = "ssl"; // sets the prefix to the servier
		$mail->Host = $GLOBALS['smtp_host']; // sets GMAIL as the SMTP server
		$mail->Port = $GLOBALS['smtp_port']; // set the SMTP port for the GMAIL server
		$mail->Username = $GLOBALS['smtp_user']; // GMAIL username
		$mail->Password = $GLOBALS['smtp_pass']; // GMAIL password

		$mail->AddAddress($GLOBALS['alert_email'], "Admin");
		$mail->SetFrom($GLOBALS['from_email'], "N5MON");
		$mail->Subject = $subject;
		$mail->Body = $body;

		try {
			$mail->Send();
			echo "[NOTICE] Message Sent!\n";			 
		} catch (Exception $e) {
			//Something went bad
			echo "[NOTICE] Failure - " . $mail->ErrorInfo;
	}
 	   

			
	} else {
		mail($GLOBALS['alert_email'], $subject, $body, $headers);	
		mail($GLOBALS['sms_email'], $subject, $body, $headers);	
	}
			
			
			
}	

function send_helpdesk($subject, $body)
{
	global $GLOBALS;
			echo "[ALERT!] HELPDESK Alert  to " . $GLOBALS['helpdesk_email'] . " - " . $subject . "\n"; 
			echo "[ALERT!] " .$body;
			$headers = "From: " . $GLOBALS['from_email'] . "\r\n";
			mail($GLOBALS['helpdesk_email'],$subject,$body,$headers);	
		
}	

/*
		http status stuff // future
*/

/*
  Returns the contents of any given url
*/
function get_url_contents($url){
		$crl = curl_init();
		$timeout = 15;
		curl_setopt ($crl, CURLOPT_URL,$url);
		curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt ($crl, CURLOPT_TIMEOUT, $timeout);
		$ret = curl_exec($crl);
		$http_status = curl_getinfo($crl, CURLINFO_HTTP_CODE);				
		curl_close($crl);
		echo "[RESULT] STATUS: " . $http_status . "\n";
		return $ret;
}

/*
  Get's the http status code of any url
  returns 404,500 etc.
*/

function get_url_status($url){
		$crl = curl_init();
		$timeout = 15;
		curl_setopt ($crl, CURLOPT_URL,$url);
		curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
		$ret = curl_exec($crl);
		$http_status = curl_getinfo($crl, CURLINFO_HTTP_CODE);				
		curl_close($crl);
		echo "[RESULT] STATUS: " . $http_status . "\n";
		return $http_status;
}


/*
 Checks if a url loads any headers
*/

function url_exists($url){
	 if ((strpos($url, "http")) === false) $url = "http://" . $url;
	 if (is_array(@get_headers($url)))
		  return true;
	 else
		  return false;
}

/*
  Adds http:// to a string
*/

function addHttp($siteurl)
{
	
	$parsed = parse_url($siteurl);
	if (empty($parsed['scheme'])) {
		$siteurl = 'http://' . ltrim($siteurl, '/');
	}
 return $siteurl;
}

function checkurl($id)
{
	
	global $GLOBALS;
		
		$server = $GLOBALS['server'];
		$subject = '[SERVER MONITOR] ' . $server . ' ' . $id . ' FAILED MONITOR UPTIME CHECK!';
		$body = '';
		$siteisonline = 1;
		// Get the status code
		echo "[TESTING] Checking url " . $id . "\n";
		$stcode = get_url_status($id);
		if (($stcode>=400 && $stcode<=599) || ($stcode == 0)) {	
			echo "[RESULT] Status Code Check FAILED Status code = " . $stcode . "\n";
			$body = "URL: " . $id . "\nStatus Code Check FAILED Status code = " . $stcode . "\n";
			$siteisonline = 0;
		} else {
			echo "[RESULT] Status Code Check PASSED Status code = " . $stcode . "\n";
			$siteisonline = 1;
		}
		
		// check for content at url
		$page = get_url_contents($id);
		if (!$page)
		{
			echo "[RESULT] NO CONTENT AT URL!\n";
			$body .= "URL: " . $id . "\nNo content found on page.\n";
			$siteisonline = 0;
		} else {
			echo "[RESULT] Content Check PASSED.\n";
		}	

		if (!$siteisonline)
		{
			
				$gl = (int)$GLOBALS['checkurl_failures'];
				$aa = (int)already_alerted($id, "1") + 1;
				
				if ($gl == $aa)
				{
					send_alert($subject, $body);					
					if ($GLOBALS['disk_helpdesk']) 
					{
						send_helpdesk($subject, $body);
					}
				}
				record_url_alert($id, "1");
			
		} else {
			if (already_alerted($id, "1")) 
			{
				$subject = '[SERVER MONITOR] ' . $server . ' ' . $id . ' IS BACK ONLINE!';
				$body .= "URL: " . $id . "\n is back online.\n";				
				send_alert($subject, $body);
			}		
			remove_alerted($id, "1");			
		}

	
}

function dnsbllookup($ip, $x)
{
	// Add your preferred list of DNSBL's
	$dnsbl_lookup = [
		"dnsbl-1.uceprotect.net",
		"dnsbl-2.uceprotect.net",
		"dnsbl-3.uceprotect.net",
		"dnsbl.dronebl.org",
		"dnsbl.sorbs.net",
		"zen.spamhaus.org",
		"bl.spamcop.net",
		"list.dsbl.org",
		"sbl.spamhaus.org",
		"xbl.spamhaus.org"
	];
	$listed = "";
	if ($ip) {
		$reverse_ip = implode(".", array_reverse(explode(".", $ip)));
		foreach ($dnsbl_lookup as $host) {
			if (checkdnsrr($reverse_ip . "." . $host . ".", "A")) {
				$listed .= $reverse_ip . "." . $host . " Listed\n";
			}
		}
	}
	if (empty($listed)) {
		echo "[RESULT] IP address is not blacklisted\n";
	} else {
		echo "[ALERT!] " . $listed . "\n";
		
$server = $GLOBALS['server'];
		$subject = "[SERVER MONITOR] " . $x . " " . $ip . " - ACTION REQUIRED: IP Address is listed on DNSBL blacklist.";
		$body = "The IP address " . $ip . " has been found on a the following blacklists.\n";
		$body .= $listed;
		$body .= "\n\nPlease take immediate action and remedy the issue.";
		echo "[RESULT] " . $body;
		echo "\n";
		send_alert($subject, $body);

	}
}
if (isset($_GET['ip']) && $_GET['ip'] != null) {
	$ip = $_GET['ip'];
	if (filter_var($ip, FILTER_VALIDATE_IP)) {
		echo dnsbllookup($ip);
	} else {
		echo "[WARNING] IP Address not valid";
		//exit;
	}
}

		



?>