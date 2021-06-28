## Cafe Variome Net
---

This is the repository for Cafe Variome Net in CodeIgniter 4.  

## Installation
---  
### Cloning the repositories:

$ `git clone https://github.com/CafeVariomeUoL/CafeVariomeNet.git`

### Changing Owner ship and renaming directories:

$ `mv CafeVariomeNet/ you_desired_directory/`  
$ `sudo chown $USER:$USER your_desired_directory -R`

### Creating the database:

$ `mysql -u [username] -p`  
$ `CREATE DATABASE cafevariomenet;`  

The CafeVariomeNet database must be populated with the following command:

$ `mysql -u [username] -p cafevariomenet < cafevariomenet-schema.sql`

### Setting the permission for the writable folder of CodeIgniter:

Set the permission within the root directory of Cafe Variome Net:
Checking the corresponding user within the Linux distribution with the following command:  

$ `ps aux | egrep '(apache|httpd)'`   

On Ubuntu the Apache user is _www-data_.  

$ `setfacl -m u:www-data:rwx -R writable/ writable/logs writable/session/ writable/cache/`

### Editing configurations in App.php and Database.php

The base URL needs to be set in the system using the following commands:

$ `vim app/Config/App.php`  
public $baseURL=’<URL_TO_ACCESS_CAFEVARIOMENET>’;

Similarly, the database credentials need to be set using the following commands:  

$ `vim app/Config/Database.php`

> public $default = [  
>               'DSN'      => '',  
>               'hostname' => 'localhost',  
>               'username' => 'root',  
>               'password' => 'Your Password',  
>               'database' => 'cafevariome',  
>               'DBDriver' => 'MySQLi',  
>               'DBPrefix' => '',  
>               'pConnect' => false,  
>               'DBDebug'  => (ENVIRONMENT !== 'production'),  
>               'cacheOn'  => false,  
>               'cacheDir' => '',  
>               'charset'  => 'utf8',  
>               'DBCollat' => 'utf8_general_ci',  
>               'swapPre'  => '',  
>               'encrypt'  => false,  
>               'compress' => false,  
>               'strictOn' => false,  
>               'failover' => [],  
>               'port'     => 3306,  
>        ];



### Installing dependencies through Composer:  

In the root directory of Cafe Variome Net where the composer.json resides, run the below command:

$ `composer install`


### Getting the installation key from Cafe Variome Net and registering a Cafe Variome instance:

The following command needs to be executed in the root directory of Cafe Variome Net where index.php resides:  

$ `php index.php CLI addInstallation`