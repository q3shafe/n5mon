<?php
/* 
    N5 NETWORKS SERVER MONITOR CONFIG 

    Each option is documented. 
*/

// SERVER NAME /////////////////////////////////////////////////////////////////////////////
/*  This is used to identify the server in alerts and is used to name backup archives.    */
 $GLOBALS['server'] = "My Server";

// Path to the n5mon directory
 $GLOBALS['n5mon_path'] = "/home/n5net/n5mon/";  


// NOTIFICATION EMAILS    ///////////////////////////////////////////////////////////////////
 $GLOBALS['sms_email'] = "5555551212@tmomail.net"; // This is simply a secondary email, I use an email to sms gateway
 $GLOBALS['alert_email'] = "me@domain.com";
 $GLOBALS['helpdesk_email'] = "helpdesk@domain.com";

// The from address on the alert emails
 $GLOBALS['from_email'] = "me@domain.com";

// MINIMUM DISK SPACE IN GB    ////////////////////////////////////////////////////////////////
 $GLOBALS['disk_limit'] = '80.0';

// Send disk alert email to helpdesk email address?
 $GLOBALS['disk_helpdesk'] = 1;    


// NUMBER OF FAILURES BEFORE ALERTING FOR CHECKURL /////////////////////////////////////////
/* 
    Sometimes checking a url too often can result on a server side failure.
    This allows a certain amount of grace before sending an alert when using the
    'checkurl' command line option.
*/
 $GLOBALS['checkurl_failures'] = 3;    


// PROCESSES TO MONITOR ///////////////////////////////////////////////////////////////////
/* 
    Add as many services as you want
    the format is 'Name' => 'Process'
    Example 'WebServer' => 'httpd'
*/
 $processes = array(
    'web'=>'apache2',
    'ftp'=>'proftpd',            
    'mysql'=>'mysql',
);
        
    
// SERVICE DOWN, RECOVERY COMMANDS    /////////////////////////////////////////////////////////
/* 
    These correspond to the names of each service specified in the $processes section above
    the format is 'Name' => '#commandsToRun'  
    Example 'WebServer' => 'service restart httpd'
*/
 $rprocesses = array(
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
    'web'=>'/var/www/',
    'homes'=>'/home/'
);
// If a virus is found send an email to the helpdesk address?
 $GLOBALS['virus_helpdesk'] = 1;        

// Quarantine Directory
// This is used with the vscan-clean option
 $GLOBALS['qdir'] = '/root/.viruses/';

// BACKUP DIRECTORY ////////////////////////////////////////////////////////////////////////////
/*
    You can backup as many different folders as you want.  Specify those
    paths here in the following format
        'FriendlyName' => 'Path-to-backup'
*/    
 $backup_dirs = array(
    'web'=>'/var/home/',
    'apachecfgs'=>'/etc/apache2/sites-enabled/',
);
        
    
// MAXIMUM LOAD AVERAGES 1, 5 and 15 minutes    /////////////////////////////////////////////////
 $load_limits = array(
    '0'=>'1.0',
    '1'=>'1.5',
    '2'=>'1.5'
);

// LOAD MANAGEMENT COMMANDS    /////////////////////////////////////////////////////////////
/* 
    These commands will be executed when load average is high.
    The format is an array of commands for each time period (1, 5, 15 minutes).
    Example: array('killall -9 chrome', 'service restart apache2')
*/
 $GLOBALS['load_commands'] = array(
    '0' => array('service restart apache2'), // Commands for 1 minute high load
    '1' => array('echo "High load detected" > /var/log/load_alert.log'), // Commands for 5 minute high load
    '2' => array('echo "High load detected" > /var/log/load_alert.log') // Commands for 15 minute high load
);

// Send email to helpdesk in the event load management commands are executed
 $GLOBALS['load_helpdesk'] = 1;

// Time to wait after executing load management commands before rechecking (in seconds)
 $GLOBALS['load_command_wait'] = 30;

// URL CHECKER ////////////////////////////////////////////////////////////////////////////
/*
    You can specify a list of urls to check to see if they are online and returning a valid 
    status code.  Specify those urls here in the following format
        'FriendlyName' => 'http://www.domain.com'
*/    
 $link_urls = array(
    'N5Networks'=>'http://dev.n5net.com',
    'N5Hosting'=>'http://n5net.com',
);



// DNSBL CHECKER ////////////////////////////////////////////////////////////////////////////
/*
    You can specify a list of IP Addresses to check to see if they are listed in many of the
    poular email blacklists.  Specify those IP addresses here in the following format
        'FriendlyName' => '0.0.0.0'
*/    
 $dnsdb_ips = array(
    'N5Networks'=>'0.0.0.0',
);



            
// WHERE DO BACKUPS GO? //////////////////////////////////////////////////////////////////////////
 $GLOBALS['backup_dir'] = "/backups/sites/";
 $GLOBALS['dbbackup_dir'] = "/backups/db/";

// HOW MANY DAYS TO KEEP BACKUPS? ////////////////////////////////////////////////////////////////
 $GLOBALS['backup_days'] = "7";
 $GLOBALS['dbbackup_days'] = "7";    

/* LOGGING */
// This will write to a logfile whenever a service is down for later analysis.  Leave blank to not write a logfile.
 $GLOBALS['service_log'] = '';

// This will write to a logfile whenever load average is above specified limits stating the load avg, time and date 
// for later analysis.  Leave blank to not write a logfile.
 $GLOBALS['load_log'] = '';

//  DATABASE CONNECTION //////////////////////////////////////////////////////////////////////
 $GLOBALS['db_host'] = 'localhost';
 $GLOBALS['db_user'] = 'root';
 $GLOBALS['db_pass'] = 'password';


//  MAILER //////////////////////////////////////////////////////////////////////
// If set to 1 you can specify an smtp server (requires PHPMailer).  If set to 0, it will use php mail to send alerts.
 $GLOBALS['use_smtp'] = '1';

// SMTP hostname
 $GLOBALS['smtp_host'] = 'smtp.mailer.com';

// SMTP Port
 $GLOBALS['smtp_port'] = '465';

// SMTP Username and Password respectively
 $GLOBALS['smtp_user'] = 'myusername';
 $GLOBALS['smtp_pass'] = 'mypassword';

// SMTP Encryption (ssl or tls)
 $GLOBALS['smtp_encryption'] = 'ssl';

?>