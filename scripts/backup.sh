#!/bin/sh

f="../backup/captive-improvements-$(date +"%Y%m%d").tgz"
tar cvfz "$f" /var/www/*.php /var/www/css/*.css /var/www/*.html *
scp "$f" administrator@192.168.200.12:/shares/Backup/InnProxy/
