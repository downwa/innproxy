# NOTE: Forced removal of unwanted objects (even in history) using:
# http://git-scm.com/book/ca/Git-Internals-Maintenance-and-Data-Recovery
# then git push -f origin master

default:
	echo "Choose checkin, checkout, bin, install, or gitconfig"

gitconfig:
	git config --global push.default simple
	git config --global credential.helper 'cache --timeout=3600'

checkout:
	git pull

checkin: # e.g. downwa
	git add -v --all
	git commit -v
	git push

bin:
	make -C scripts

install: bin
	./doinstall.sh

update:
	cp -av /home/administrator/innproxy/install/* /

start:
	/etc/init.d/innproxy start

stop:
	/etc/init.d/innproxy stop
