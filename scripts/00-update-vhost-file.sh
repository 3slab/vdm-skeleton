#!/bin/bash

set -e

DEFAULT_VHOST='/etc/nginx/sites-available/default.conf'

# Configure apache with "SYMFONY_..."  environements variables
/bin/sed -i "s/APP_ENV dev/APP_ENV $APP_ENV/" $DEFAULT_VHOST

_ENV_VARS=(
DATABASE_URL
)

_FILTER=DUMMY_FILTER
for i in ${_ENV_VARS[@]}; do
        _FILTER="$_FILTER\|${i}"
done

/usr/bin/env | grep -i "^\($_FILTER\)" | /bin/sed -r 's/([0-9A-Z_]*)=(.*)/fastcgi_param \1 '\''\2'\'';/g' > /tmp/replace_env.txt
/bin/sed -i '/# fastcgi_param SYMFONY_VARS/ {
  r /tmp/replace_env.txt
  d
}' $DEFAULT_VHOST
/bin/rm /tmp/replace_env.txt
