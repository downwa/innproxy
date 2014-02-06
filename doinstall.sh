#!/bin/sh

# Link http (apache2) to https (nginx) directory
[ -f /var/www/index.html ] && mv /var/www/index.html /usr/share/zentyal/www/oldindex.html
[ -L /var/www ] && rm /var/www
[ -d /var/www ] && rmdir /var/www
ln -s /usr/share/zentyal/www /var/

# Install patches
cp -av /home/administrator/innproxy/install/* /

# Install PHP and additional utilities (binutils for strings)
apt-get install php5-fpm libapache2-mod-php5 mlocate binutils realpath

# Restart servers
/etc/init.d/php5-fpm restart
/etc/init.d/zentyal webadmin restart
/etc/init.d/apache2 restart
