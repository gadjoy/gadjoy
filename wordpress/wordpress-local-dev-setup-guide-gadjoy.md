# WordPress Local Development Setup Guide

This guide helps you run a local WordPress development environment using Docker and import an existing site using All-in-One WP Migration.

## Prerequisites

- Docker and Docker Compose installed
- All-in-One WP Migration backup file (.wpress)
- Terminal/Command Line access
- The provided files in the `auth` folder for default credentials

## Step 1: Use the Provided Dockerfile

The repository includes a pre-configured `Dockerfile` at `wordpress/Dockerfile` that:
- Sets PHP memory and upload limits
- Installs WP-CLI
- Copies the All-in-One WP Migration plugin (older version, supports large imports)
- Adds a password hash generator script for WordPress

**No need to create your own Dockerfile.**

## Step 2: Use the Provided Docker Compose File

The repository includes a ready-to-use `docker-compose.yml` at `wordpress/docker-compose.yml`.
- It configures WordPress, MySQL, and phpMyAdmin
- Sets up environment variables and resource limits

**No need to create your own docker-compose.yml.**

## Step 3: (Optional) Create Environment File

You can create a `.env` file in the `wordpress/` directory to store your credentials:

```env
WP_DB_NAME=wordpress
WP_DB_USER=wp_user
WP_DB_PASSWORD=wp_password
MYSQL_ROOT_PASSWORD=root_password
```

## Step 4: Start WordPress Environment

From the `wordpress/` directory, run:

```bash
docker-compose up -d --build
```

## Step 5: Initial WordPress Setup

1. Open your browser and navigate to `http://localhost:8080`
2. Complete the WordPress installation with any temporary credentials (these will be replaced after import)

## Step 6: Activate the All-in-One WP Migration Plugin

- The All-in-One WP Migration plugin is already present (older version, supports large imports). Go to **Plugins** in the WordPress admin and **activate** it. No need to install or upload the plugin.

## Step 7: Import Your Backup

1. Go to WordPress admin → All-in-One WP Migration → Import
2. If your backup is split into multiple files, stitch them together as needed (see plugin documentation or use `cat` on the command line). For detailed instructions on splitting and merging large files, see [`linux-file-split-and-merge-guide.md`](./linux-file-split-and-merge-guide.md).
3. Import your `.wpress` backup file
4. Confirm the import when prompted
5. Wait for the import process to complete
6. Click "Finish" when prompted to log in again

## Step 8: Post-Import Steps

After the import, you may see:
- A message: **You must save your permalinks structure twice. Permalink Settings**
- **Note:** Sometimes, the permalinks settings page does not appear immediately after import. Instead, you may be redirected directly to the **Database Update Required** screen.

If you are redirected to the database update screen:
1. Click **Update WordPress Database** and wait for the process to finish
2. Click **Continue**
3. If prompted for administration email verification, click **Remind me later**
4. Then, manually go to **Settings → Permalinks** in the WordPress admin
5. Select **Day and name** (or your preferred structure) and click **Save Changes** twice

Your site links should now work correctly.

## Step 9: Restore Default User Credentials

After the import, the old username and password may not work. To restore access with the default credentials:

1. Open phpMyAdmin at [http://localhost:8081](http://localhost:8081)
2. Log in with:
   - Username: `root`
   - Password: `root_password` (or value from your `.env` file)
3. Select your WordPress database
4. Open the **SQL** tab
5. Copy and run the contents of `wordpress/create_vivek.sql` to create a default admin user:
   - Username: from `wordpress/auth/vivek.username`
   - Password: from `wordpress/auth/VWxSnbF9oreOH$WJoN.pass` (the hash is already in the SQL file)

## Step 10: Log In with Default Credentials

- Go to [http://localhost:8080/wp-login.php](http://localhost:8080/wp-login.php)
- Log in with the username and password from the `auth` folder
- If prompted for administration email verification, click **Remind me later**

## Step 11: Finalize Permalink Settings

- Go to **Settings → Permalinks**
- Select **Day and name** (or your preferred structure)
- Click **Save Changes**
- Repeat the save once more (as recommended by the import plugin)

Your site links should now work correctly.

## Step 12: (Optional) Generate a WordPress Password Hash (Local Dev Only)

A script is included for generating a WordPress-compatible password hash for local development. The script is hardcoded to use the password from the `auth` folder.

- File: `wordpress/wp-generate-pass.php` (copied to `/var/www/html/wp-generate-pass.php` in the container)
- Access in your browser at: [http://localhost:8080/wp-generate-pass.php](http://localhost:8080/wp-generate-pass.php)
- The page will display the hash for the password in the `auth` folder. You can use this hash to update the `wp_users` table in your database if needed.

## Troubleshooting

### Permission Issues
```bash
docker exec -it $(docker-compose ps -q wordpress) bash -c "chown -R www-data:www-data /var/www/html"
```

### Complete Reset
To start fresh:
```bash
docker-compose down -v
docker system prune -af --volumes
docker-compose up -d --build
```

### Image Loading Issues
If images aren't loading correctly:
1. Check WordPress Address (URL) and Site Address (URL) in Settings → General
2. Ensure they match the WP_HOME and WP_SITEURL values in docker-compose.yml
3. Clear browser cache and WordPress cache if using a caching plugin

### Database Connection Issues
1. Wait for MySQL container to be healthy (check with `docker-compose ps`)
2. Verify credentials in .env file match docker-compose.yml
3. Try using root credentials for phpMyAdmin if wp_user access fails

### Security Notes
- All database ports are bound to localhost only
- Environment variables are used for sensitive data
- Health checks ensure services are ready before dependencies start
- Resource limits prevent container resource exhaustion
