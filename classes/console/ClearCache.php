<?php namespace BizMark\Quicksilver\Classes\Console;

use Exception;
use Illuminate\Console\Command;
use BizMark\Quicksilver\Classes\Contracts\Cache;

class ClearCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'page-cache:clear {slug? : URL slug of page to delete}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the page cache.';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws Exception
     */
    public function handle(Cache $cache): void
    {
        $slug = $this->argument('slug');
        $this->{$slug ? 'forget' : 'clear'}($cache, $slug);
    }

    /**
     * Remove the cached file for the given slug.
     *
     * @param Cache $cache
     * @param string|null $slug
     * @return void
     * @throws Exception
     */
    protected function forget(Cache $cache, ?string $slug): void
    {
        if ($cache->forget($slug)) {
            $this->info('Page cache cleared for "'. $slug .'"');
        } else {
            $this->info('No page cache found for "' .$slug .'"');
        }
    }

    /**
     * Clear the full page cache.
     *
     * @param Cache $cache
     * @return void
     * @throws Exception
     */
    protected function clear(Cache $cache): void
    {
        if ($cache->clear()) {
            $this->info('Page cache cleared at '. $cache->getCachePath());
        } else {
            $this->warn('Page cache not cleared at '. $cache->getCachePath());
        }
    }
}
