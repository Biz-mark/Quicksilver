<?php namespace BizMark\Quicksilver\Console;

use BizMark\Quicksilver\Classes\Contracts\Quicksilver;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Clear Command
 * @package BizMark\Quicksilver\Console
 * @author Nick Khaetsky, Biz-Mark
 */
class Clear extends Command
{
    /**
     * @var string name is the console command name
     */
    protected $name = 'quicksilver:clear';

    /**
     * @var string description is the console command description
     */
    protected $description = 'Clearing of quicksilver storage.';

    /**
     * Handle executes the console command
     *
     * @param Quicksilver $quicksilver
     * @return void
     */
    public function handle(Quicksilver $quicksilver): void
    {
        $path = $this->argument('path');

        if (empty($path)) {
            if ($quicksilver->clear()) {
                $this->success();
                return;
            }
        } else {
            if ($quicksilver->forget($path)) {
                $this->success();
                return;
            }
        }

        $this->failed();
    }

    /**
     * Return success response
     *
     * @return void
     */
    protected function success(): void
    {
        $this->output->success('Quicksilver cache clearing done successfully.');
    }

    /**
     * Return failed response
     *
     * @return void
     */
    protected function failed(): void
    {
        $this->output->warning('Quicksilver cache clearing failed or nothing to delete.');
    }

    /**
     * getArguments get the console command arguments
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['path', InputArgument::OPTIONAL, 'Optional clearing path. Empty to clear everything.'],
        ];
    }
}
