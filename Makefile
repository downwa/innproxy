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
