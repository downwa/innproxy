#!/bin/sh

cd "install"

# Update any source scripts from the installed system
find -type f -name '*.php' | while read name; do diff "$name" "/$name" >/dev/null || cp -av "/$name" "$name"; done
find -type f -wholename '**' | while read name; do diff "$name" "/$name" >/dev/null || cp -av "/$name" "$name"; done
rsync -av /usr/share/zentyal/www/admin usr/share/zentyal/www/

# Now, update the system from the source
changed=$(find -type f | while read name; do diff -wB "$name" "/$name" >/dev/null || echo "$name"; done)
count=$(printf "%s\n" "$changed" | grep -v "^$" | wc -l)
echo "$count changed files."
if [ "$count" -gt 0 ]; then
	echo "$changed" | while read name; do
		cp -av "$name" "/$name"
	done

	echo "Restarting (possibly) affected modules..."
	for module in captiveportal webadmin; do /etc/init.d/zentyal "$module" restart; done
fi
