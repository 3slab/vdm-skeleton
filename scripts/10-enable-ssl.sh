#!/bin/bash

CONFIG_FILES=${DOCTRINE_CONFIG_FILES:="config/packages/doctrine.yaml"}

if [ "$DOCTRINE_ENABLE_SSL" == "true" ]; then
  echo "Enable ssl"
  for CONFIG_FILE in "${CONFIG_FILES[@]}"; do
    sed -i '/    dbal:/a\
        options:\
            !php/const:PDO::MYSQL_ATTR_SSL_CA : "/var/www/html/BaltimoreCyberTrustRoot.crt.pem"' $CONFIG_FILE
  done
else
  echo "SSL not enabled"
fi