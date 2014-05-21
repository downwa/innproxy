# NOTE: Forced removal of unwanted objects (even in history) using:
# http://git-scm.com/book/ca/Git-Internals-Maintenance-and-Data-Recovery
# then git push -f origin master

default:
	echo "Choose checkin, checkout, bin, doinstall, or gitconfig"

diff:
	diff -Naur /usr/share/zentyal/stubs/core/nginx.conf.mas usr_share_zentyal_stubs_core_nginx.conf.mas >/home/administrator/innproxy/nginx.patch || true

gitconfig:
	git config --global push.default simple
	git config --global credential.helper 'cache --timeout=3600'

checkout:
	git pull

checkin: # e.g. downwa
	git add -v --all
	git commit -v
	git push

doinstall:
	./doinstall.sh

update:
	cp -av /home/administrator/innproxy/install/* /

start:
	/etc/init.d/innproxy start

stop:
	/etc/init.d/innproxy stop
