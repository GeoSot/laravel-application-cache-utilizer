<?php

namespace GeoSot\AppCache;

use Closure;
use Illuminate\Filesystem\Filesystem;


class AppCache
{

    protected Filesystem $files;
    /**
     * @var array<string, mixed>
     */
    protected array $packageConfig;
    protected string $cacheDirectory;

    /**
     * Constructor.
     *
     * @param  Filesystem  $files
     * @param  string  $cacheDirectory
     * @param  array  $packageConfig
     */
    public function __construct(Filesystem $files, string $cacheDirectory, array $packageConfig = [])
    {
        $this->files = $files;
        $this->packageConfig = $packageConfig;
        $this->cacheDirectory = $cacheDirectory;
    }

    /**
     * retrieve config
     */
    public function get(string $key, array $default = []): array
    {
        return $this->has($key) ? require $this->getPath($key) : $default;
    }

    public function has(string $key): bool
    {
        return is_file($this->getPath($key));
    }

    public function add(string $key, array $data, bool $force = false): bool
    {
        if ($force) {
            return $this->set($key, $data);
        }

        return !$this->has($key) && $this->set($key, $data);
    }


    private function set(string $key, array $data): bool
    {
        $fileName = $this->getPath($key);
        @file_put_contents($fileName, '<?php return '.var_export($data, true).';'.PHP_EOL);

        try {
            require $fileName;
        } catch (\Throwable $e) {
            $this->files->delete($fileName);
            return false;
//            throw new \LogicException('Your configuration files are not serializable.', 0, $e);
        }
        return true;
    }


    /**
     * @param  string  $key
     * @param  Closure<array>  $callback
     * @return array
     */
    public function remember(string $key, Closure $callback): array
    {
        $value = $this->get($key);
        if (empty($value)) {
            $this->set($key, $value = $callback());
        }

        return $value;
    }


    public function forget(string $key): bool
    {
        return $this->files->delete($this->getPath($key));
    }


    public function clear(): bool
    {
        return $this->files->cleanDirectory($this->cacheDirectory);
    }


    protected function getPath(string $key): string
    {
        $key = str_replace('.php', '', $key); // omit extension
        $key = preg_replace('/[^a-z\d]/i', '_', $key); // sanitize fileName
        return $this->cacheDirectory.DIRECTORY_SEPARATOR.$key.'.php';
    }

}
