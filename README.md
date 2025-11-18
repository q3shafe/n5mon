

# N5MON - Simple Monitoring and Maintenance Tool

![PHP](https://img.shields.io/badge/PHP-8.0+-blue.svg)
![License](https://img.shields.io/badge/License-GPL%20v2.0-green.svg)
![Platform](https://img.shields.io/badge/Platform-Linux-lightgrey.svg)

**A lightweight, low-overhead system monitoring and maintenance tool for Linux servers.**

2016-2025 WDV Digital
Licensed under the GPL v2.0  
http://wdvdigital.com
support@wedovids.com

## Table of Contents

- [Requirements](#requirements)
- [Overview](#overview)
- [Features](#features)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Command Line Options](#command-line-options)
- [Health Check](#health-check)
- [Contributing](#contributing)
- [License](#license)

## Requirements

- PHP 8.0 or newer
- cURL extension
- ClamAV (for virus scanning)
- Composer (for PHPMailer dependency)

## Overview

N5MON is a lightweight, low-overhead system monitoring and maintenance tool for performing simple server monitoring as well as automated local backups and virus scans. It runs under PHP-CLI either from the command line or from cron.

## Features

- **System Monitoring**: Monitors disk space, running processes, and load averages
- **Service Management**: Attempts to restart services that are down or run any commands you specify
- **Web Monitoring**: Checks URLs to ensure web servers are responding correctly
- **Blacklist Monitoring**: Checks IP addresses against various DNSBL databases
- **Backup Management**: Performs automated backups of directories and databases
- **Virus Scanning**: Integrates with ClamAV for virus detection and quarantine
- **Email Notifications**: Sends alerts through email when problems are detected
- **Modern Email Support**: Uses PHPMailer with SMTP support for reliable email delivery

## Installation

1. Clone or download the repository to your server:
   ```bash
   git clone https://github.com/yourusername/n5mon.git
   cd n5mon
   ```

2. Install PHPMailer via Composer:
   ```bash
   composer require phpmailer/phpmailer
   ```

3. Set proper permissions:
   ```bash
   chmod +x n5mon.php
   chmod 666 alerts.dat
   ```

4. Edit `n5mon-config.php` with all your preferences. The options in this file are well documented.

5. Set up cron jobs using the examples in `crontab.txt` as a roadmap to schedule n5mon tasks.

## Configuration

The `n5mon-config.php` file contains all configuration options:

- Server identification settings
- Email notification settings
- Service monitoring configuration
- Backup directories and retention
- Virus scan settings
- Database connection details
- SMTP configuration for email alerts

Each option is documented in the configuration file. For additional help, visit http://support.n5net.com.

## Usage

Run n5mon from the command line or schedule it with cron:

```bash
php ./n5mon.php [option]
```

## Command Line Options

| Command | Description |
|---------|-------------|
| `php ./n5mon.php monitor` | Runs all monitors |
| `php ./n5mon.php backup` | Runs all backups |
| `php ./n5mon.php dbbackup` | Backup and archive all databases |
| `php ./n5mon.php vscan` | Perform Virus Scan |
| `php ./n5mon.php vscan-clean` | Perform Virus Scan and moves infected files to quarantine |
| `php ./n5mon.php purge` | Purge oldest backup files - saves the last 5 |
| `php ./n5mon.php checksites` | Check a list of sites specified in the config |
| `php ./n5mon.php checkurl http://domain.com` | Check if a URL is returning content and correct status codes |
| `php ./n5mon.php blacklisted xxx.xxx.xxx.xxx` | Check an IP address against a list of email blacklists |
| `php ./n5mon.php checkdnsbl` | Check a list of IPs specified in the config against DNSBLs |
| `php ./n5mon.php testemail` | Sends a test message to all enabled emails in config file |

## Health Check

The `healthcheck.php` script provides a quick report on the system's health. If you would like it to also run a virus scan, add the parameter "dovirus" when running:

```bash
php healthcheck.php dovirus
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request. For major changes, please open an issue first to discuss what you would like to change.

## License

This script is licensed under the GNU General Public License (GPL) version 2.0 (https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html).