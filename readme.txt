N5MON Simple Monitoring and Maintenance Tool.
2016 N5 Networks
Licensed under the GPL v2.0
http://dev.n5net.com
support@n5net.com

requirements: PHP 5.4.x or newer, Curl, ClamAV (for virus scanning).

Overview:
N5MON is a lightweight, low overhead simple system monitor, maintenance tool for doing simple server monitoring as well as automated local 
backups and virus scans.  It runs under PHP-CLI either from the command line or from cron.

It monitors diskspace, running processes and load averages.  N5MON will attempt to restart services that are down or run any commands you specify.  Alerts are 
sent through email when a problem is found.

Installation:
1) Unpack all files in the archive, be sure they are readable, exectuable and that alerts.dat is writable.
2) Edit n5mon-config.php with all of your preferences. This options in this file are well documented.
3) Use the cron job example setup in crontab.txt as a roadmap to setup your cron jobs for n5mon to your liking.

Command line Options

	Usage:
	php ./n5mon.php {option}
	
	Command line options:
		monitor 	- Runs all monitors
		backup 		- Runs all backups
		dbbackup 	- Backup and archive all databases
		vscan 		- Perform Virus Scan
		purge 		- Purge oldest backup files (db and regular backups), number of days to hold back is
					  specified in n5mon-config.php
		checkurl 	- http://domain.com - check's to see the url is returning content and correct status codes. (new not really tested yet)
					  
		testemail	- Sends test email(s) to emails specified in config file.


					  
					  
This script is licensed under the GNU General Public License (GPL) version 2 ( https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html )

