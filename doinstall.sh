#!/bin/sh

# Link http (apache2) to https (nginx) directory
[ -f /var/www/index.html ] && mv /var/www/index.html /usr/share/zentyal/www/oldindex.html
[ -L /var/www ] && rm /var/www
[ -d /var/www ] && rmdir /var/www
ln -s /usr/share/zentyal/www /var/

# Install PHP and additional utilities (binutils for strings, bind9-host for host)
apt-get install php5-fpm php5-json libapache2-mod-php5 mlocate binutils realpath bc bind9-host apache2-utils inotify-tools

# Enable Apache2 modules
a2enmod rewrite ssl

mkdir -p /var/log/innproxy

# Install patches
sudo chown -R root.root etc/ usr/local var
sudo chown -R www-data.www-data usr/share/
cp -av /home/administrator/innproxy/install/* /
patch -Nu /usr/share/zentyal/stubs/core/nginx.conf.mas </home/administrator/innproxy/nginx.patch
if [ ! -f /etc/apache2/passwd/passwords ]; then

mkdir /var/lib/innproxy/{firewalls,sessions,ssl,users}
touch /var/lib/innproxy/users.json
mkdir -p /etc/apache2/passwd
for user in wdowns frontdesk justin; do
	echo "Setting web password for $user..."
	parm=""
	[ "$user" = "wdowns" ] && parm="-c"
	htpasswd $parm /etc/apache2/passwd/passwords "$user"
done

fi

# Restart servers
/etc/init.d/php5-fpm restart
/etc/init.d/zentyal webadmin restart
/etc/init.d/apache2 restart

echo "Add this line using 'crontab -e' to have server restart every day."
echo "0   5  *   *   *     /etc/init.d/zentyal restart"

if [ ! -f /var/lib/innproxy/ssl/ssl.pem -o ! -f /var/lib/innproxy/ssl/certs/gd_bundle.crt ]; then
	echo "SSL certs need to be installed:"
	echo "  /var/lib/innproxy/ssl/ssl.pem"
	echo "  /var/lib/innproxy/ssl/certs/gd_bundle.crt"
fi
