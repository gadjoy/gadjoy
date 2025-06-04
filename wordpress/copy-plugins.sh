#!/bin/sh
PLUGIN_SRC="/plugin-src/all-in-one-wp-migration"
PLUGIN_DEST="/var/www/html/wp-content/plugins/all-in-one-wp-migration"

# Only copy if the plugin doesn't already exist in the volume
if [ ! -d "$PLUGIN_DEST" ]; then
  cp -r "$PLUGIN_SRC" "$PLUGIN_DEST"
fi

# Always fix permissions (in case volume overwrites them)
chown -R www-data:www-data "$PLUGIN_DEST"
chmod -R 755 "$PLUGIN_DEST"
if [ -d "$PLUGIN_DEST/storage" ]; then
  chown -R www-data:www-data "$PLUGIN_DEST/storage"
  chmod -R 755 "$PLUGIN_DEST/storage"
fi

# Continue with the default entrypoint
exec docker-entrypoint.sh "$@"
