N5MON Simple Monitoring and Maintenance Tool.
2016, 2017 N5 Networks
Licensed under the GPL v2.0
http://dev.n5net.com
helpdesk@n5net.com

requirements: PHP 5.4.x or newer, Curl, ClamAV (for virus scanning).

Overview:
N5MON is a lightweight, low overhead simple system monitor, maintenance tool for doing simple server monitoring as well as automated local 
backups and virus scans.  It runs under PHP-CLI either from the command line or from cron.

It monitors diskspace, running processes and load averages.  N5MON will attempt to restart services that are down or run any commands you specify.  Alerts are 
sent through email when a problem is found.

You can specify the standnard phpmailer or an SMTP host for alerts.

Installation:
1) Unpack all files in the archive, be sure they are readable, exectuable and that alerts.dat is writable.
2) Edit n5mon-config.php with all of your preferences. This options in this file are well documented.
3) Use the cron job example setup in crontab.txt as a roadmap to setup your cron jobs for n5mon to your liking.

Command line Options

	Usage:
	php ./n5mon.php {option}
	
	Command line options:
		php ./n5mon.php monitor - Runs all monitors
        php ./n5mon.php backup - Runs all backups
        php ./n5mon.php dbbackup - Backup and archive all databases
        php ./n5mon.php vscan - Perform Virus Scan
        php ./n5mon.php vscan-clean - Perform Virus Scan and moves infected files to specified quarantine folder
        php ./n5mon.php purge - Purge oldest backup files - saves the last 5
        php ./n5mon.php checksites - The same as checkurl below, will check a list of sites specified in the config.
        php ./n5mon.php checkurl http://domain.com - check's to see the url is returning content and correct status codes

        php ./n5mon.php testemail - Sends a test message to all enabled emails in cfg file


					  
=====================
HEALTHCHECK.PHP
=====================
This is a quick report on the system's health.  If you would like it to also run a  virus scan add a parameter "dovirus" when running.

					  
					  
This script is licensed under the GNU General Public License (GPL) version 2 ( https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html )

