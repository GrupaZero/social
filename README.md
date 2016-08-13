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

### OAuth credentials

Set required credentials for given service in services config file
 
 ```PHP
 'google' => [
     'client_id'     => 'your client_id',
     'client_secret' => 'your client_secret',
 ],

 'facebook' => [
     'client_id'     => 'your client_id',
     'client_secret' => 'your client_secret',
 ],

 'twitter' => [
     'client_id'     => 'your client_id',
     'client_secret' => 'your client_secret',
 ],
 ```
 
 ### Like buttons credentials
 
 Set app id for given service in services config file
  
  ```PHP
  'facebook' => [
      'app_id'     => 'your app_id',
  ],
  ```
