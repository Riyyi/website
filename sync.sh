#!/bin/sh

# Syncs website contents to remote server
# Depends: rsync, ssh

# User-config---------------------------

config="./syncconfig.sh"

# ------------------------------------------

if [ -f "$config" ]; then
	. "$config"
else
	echo "Please configure the $config file"
	exit 1
fi

if [ "$(dirname "$0")" != "." ]; then
	echo "Please run this script from the directory it resides."
	exit 1
fi

directories=\
"app
public"

source="."
destination="$user@$ip:$remote_path"

echo "Syncing files to remote.."

# Directory syncing
for dir in $directories; do
	cd "$dir" || exit 1
	rsync -ar --delete-after --rsh="ssh -p $port" \
		  --out-format="- %n" \
		  --exclude="media" \
		"$source" "$destination/$dir"
	cd ..
done

# File syncing
rsync -a --rsh="ssh -p $port" \
	  --out-format="- %n" \
	  --include="composer.json" \
	  `# --include="config.php"` \
	  --include="route.php" \
	  --exclude="*" \
	  "$source" "$destination"

echo "Synced all files to remote."
