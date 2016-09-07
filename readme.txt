N5MON Simple Monitoring and Maintenance Tool.
(c)2016 N5 Networks

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


					  
					  
This script is licensed under the GNU General Public License (GPL) version 2 ( https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html )

Changelog:
1.0r13
- Added in the ability to specify a separate helpdesk email and pick which alerts go to that email.
1.0r12
- fixed formatting of load alerts.
- Removed backup days defaults.
- Cleaned up config file.
- Added more documentation to n5mon-config.php

1.0r11 
- fixed bug in purge process
1.0r10
- load average alerts now send to 5 cpu processes
1.0r9
- Added readme.txt
- began work on the http remote monitoring options
- more explanation added to n5mon-config.php
1.0r8
- fixed alerts.dat path issue
1.0r7
- Added in config options to set number of days to keep backups.
1.0r6
- code cleanup
1.0r5
- Fixed issue where alerts got sent over and over, 1 alert gets sent now until the monitor goes back up.   - alerts.dat must be writable
1.0r4
- fixed issue with load test logic
1.0r3
- Tons of fixes
1.0r2
- Version 1 Beta (will go into production) - Added Cron file
1.0r1
-initial commit

ROADMAP
Monthy/Weekly Reporting of service uptime
Remote http check (like siteguard)
FTP Backups Out
							

	

	



