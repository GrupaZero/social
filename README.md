# GZERO SOCIAL
===
Social package for GZERO CMS

## Installation

Begin by installing this package through Composer. Edit your project's composer.json file to require gzero/social.

```json
"require": {
    "gzero/social": "dev-master",
},
"minimum-stability" : "dev"
```
Next, update Composer from the Terminal:

```
composer update
```
 - create database schema (remember to set env to dev)
 
```
php artisan migrate --path=vendor/gzero/social/src/migrations
```
## Configuration

Add the service provider to platform configuration in `app/config/app.php`

```PHP
'Gzero\Social\ServiceProvider'
```

### Overriding configuration

In order to override some of the configuration options publish configuration file:

```
php artisan config:publish gzero/social
```

Set required credentials for given service in published package config file
 
 ```PHP
'services' => [
        'facebook' => [
            'key' => 'your app_id',
            'secret' => 'your secret_key',
            'scope' => ['email'] // data you want to access
        ]
    ]
 ```
