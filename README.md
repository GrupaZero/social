# GZERO SOCIAL
===
Social package for GZERO CMS

## Installation

Begin by installing this package through Composer. Edit your project's composer.json file to require gzero/social.

```json
"require": {
    "gzero/social": "3.*",
},
"minimum-stability" : "stable"
```
Next, update Composer from the Terminal:

```
composer update
```
 - create database schema (remember to set env to dev)
 
```
php artisan migrate
```
## Configuration

Add the service provider to platform configuration in `config/app.php`

```PHP
Gzero\Social\ServiceProvider::class
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
 
 Set only client_id for given service in services config file
  
  ```PHP
  'facebook' => [
      'client_id'     => 'your client_id',
  ],
  ```
