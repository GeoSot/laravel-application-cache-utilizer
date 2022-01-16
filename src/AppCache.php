<?php

namespace GeoSot\AppCache;

use Closure;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;

class AppCache
{
    protected Filesystem $files;

    protected string $cacheDirectory;

    private Repository $packageConfig;

    /**
     * Constructor.
     *
     * @param  string  $cacheDirectory
     * @param  Repository  $packageConfig
     * @param  Filesystem  $files
     */
    public function __construct(string $cacheDirectory, Repository $packageConfig, Filesystem $files)
    {
        $this->files = $files;
        $files->ensureDirectoryExists($cacheDirectory);
        $this->packageConfig = $packageConfig;
        $this->cacheDirectory = $cacheDirectory;
    }

    /**
     * @param  string  $key
     * @param  array<string,mixed>  $default
     * @return array<string,mixed>
     */
    public function get(string $key, array $default = []): array
    {
        return $this->has($key) ? require $this->getPath($key) : $default;
    }

    public function has(string $key): bool
    {
        return is_file($this->getPath($key));
    }

    /**
     * @param  string  $key
     * @param  array<string,mixed>  $data
     * @param  bool  $force
     * @return bool
     */
    public function add(string $key, array $data, bool $force = false): bool
    {
        if ($force) {
            return $this->set($key, $data);
        }

        return ! $this->has($key) && $this->set($key, $data);
    }

    /**
     * @param  string  $key
     * @param  array<string,mixed>  $data
     * @return bool
     */
    private function set(string $key, array $data): bool
    {
        if (! $this->packageConfig->get('enabled', true)) {
            return false;
        }

        $fileName = $this->getPath($key);
        @file_put_contents($fileName, '<?php return '.var_export($data, true).';'.PHP_EOL);

        try {
            require $fileName;
        } catch (\Throwable $e) {
            $this->files->delete($fileName);

            return $this->packageConfig->get('throw_exception_if_add_fails', true)
                ? throw new \LogicException('Your data could not be serialized', 0, $e)
                : false;
        }

        return true;
    }

    /**
     * @param  string  $key
     * @param  Closure  $callback
     * @return array<string,mixed>
     */
    public function remember(string $key, Closure $callback): array
    {
        $value = $this->get($key);
        if (empty($value)) {
            $this->add($key, $value = $callback());
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
