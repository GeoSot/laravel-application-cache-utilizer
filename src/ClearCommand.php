<?php

namespace GeoSot\AppCache;

use Illuminate\Console\Command;

class ClearCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'app-cache-utilizer:clear {key? : delete a specific cache or all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the "app-cache-utilizer" cache file(s)';

    protected AppCache $appCache;

    public function __construct(AppCache $appCache)
    {
        parent::__construct();

        $this->appCache = $appCache;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        /** @var string $key */
        $key = $this->argument('key');

        $result = $key ? $this->appCache->forget($key) : $this->appCache->clear();

        if ($result) {
            $this->info($key ? "Configuration cache key:'$key' cleared!" : 'Configuration cache cleared!');

            return;
        }
        $this->warn('Configuration cache NOT cleared!');
    }
}
