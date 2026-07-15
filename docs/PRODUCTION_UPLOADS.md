# Production uploads

The news and gallery modules require the web-server process to write to Laravel's `storage` directory.

After deploying to `/var/www/html/fsrp`, run inside the container or VM as root:

```sh
cd /var/www/html/fsrp
sh scripts/prepare-production-storage.sh
```

The script creates the upload/runtime directories, assigns them to `www-data:www-data`, applies group-writable permissions, ensures the `public/storage` link exists, and clears cached Laravel configuration. For a server using a different account:

```sh
WEB_USER=nginx WEB_GROUP=nginx sh scripts/prepare-production-storage.sh
```

PHP upload limits are supplied in `public/.user.ini` for PHP-FPM/FastCGI and `public/.htaccess` for Apache module deployments. Restart PHP-FPM or Apache after deployment so the limits are reloaded.

For Nginx, its request limit must also be configured in the site/server block:

```nginx
client_max_body_size 64M;
```

Reload Nginx after changing that setting.
