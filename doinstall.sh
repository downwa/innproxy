#!/bin/sh

# Link http (apache2) to https (nginx) directory
[ -f /var/www/index.html ] && mv /var/www/index.html /usr/share/zentyal/www/oldindex.html
[ -L /var/www ] && rm /var/www
[ -d /var/www ] && rmdir /var/www
ln -s /usr/share/zentyal/www /var/

# Install patches
cp -av /home/administrator/innproxy/install/* /
if [ ! -f /etc/apache2/passwd/passwords ]; then

for user in wdowns frontdesk justin; do
	echo "Setting web password for $user..."
	[ "$user" = "wdowns" ] && parm="-c"
	htpasswd $parm /etc/apache2/passwd/passwords "$user"
done

fi

# Install PHP and additional utilities (binutils for strings)
apt-get install php5-fpm libapache2-mod-php5 mlocate binutils realpath bc

# Restart servers
/etc/init.d/php5-fpm restart
/etc/init.d/zentyal webadmin restart
/etc/init.d/apache2 restart

echo "Add this line using 'crontab -e' to have server restart every day."
echo "0   5  *   *   *     /etc/init.d/zentyal restart"
