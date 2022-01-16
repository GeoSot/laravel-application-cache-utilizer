# Laravel app cache utilizer
This Package imitates the default laravel app cache functionality, where demanding processes that calculate registered data (config, providers, routes),
are cached there in order to be used in each app bootstrap, avoiding recalculation time

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
```php

* get(string $key, array $default = []): array
* has(string $key): bool
* add(string $key, array $data, bool $force = false): bool
* remember(string $key, Closure $callback): array // saves given callback result (if is not saved already), and returns the saved data
* forget(string $key): bool // clear the given cached file, if exists
* clear(): bool  // clear all package's cached files


```
 
