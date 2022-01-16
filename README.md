# Laravel app cache utilizer
This Package allows to manage Laravel .env file values on the Fly (add, edit, delete keys), upload another .env or create backups
<br/>
Management can be done through the user interface, or programmatically by using the `AppCache` Facade, without breaking the files structure. 
<br/>

*   [Installation](#installation)
*   [Available Methods](#available_methods)


## <a name="installation">Installation:</a>

1. Install package
    ```bash
    composer require geo-sot/laravel-app-cache-utilizer
    ```
 3. Publish assets 
     ```bash
     php artisan vendor:publish --provider=GeoSot\AppCache\ServiceProvider     
      ```      
      This will publish all files:
    * config -> app-cache-utilizer.php
      
     Or publish specific tags
     
## <a name="available_methods">Available Methods:</a>

* get(string $key, array $default = []): array
* has(string $key): bool
* add(string $key, array $data, bool $force = false): bool
* remember(string $key, Closure $callback): array // saves given callback result (if is not saved already), and returns the saved data
* forget(string $key): bool // clear the given cached file, if exists
* clear(): bool  // clear all package's cached files

 
