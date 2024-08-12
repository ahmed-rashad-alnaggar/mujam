<?php

namespace Alnaggar\Mujam\Console\Commands;

use Alnaggar\Mujam\Contracts\StructuredStore;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class FlushCommand extends Command
{
    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'mujam:flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flush translations in the specified store.';

    /**
     * Execute the console command.
     * 
     * @return int
     */
    public function handle() : int
    {
        $store = $this->getLaravel()->make('mujam')->store($this->option('store'));

        $locale = $this->argument('locale');

        if ($store instanceof StructuredStore) {
            $group = $this->option('group');
            $namespace = $this->option('namespace');

            $store->flush($group, $namespace, $locale);
        } else {
            $store->flush($locale);
        }

        $this->info('Translations are flushed successfully.');

        return self::SUCCESS;
    }

    /**
     * Get the console command arguments.
     * 
     * @return array<InputArgument>
     */
    public function getArguments() : array
    {
        return [
            new InputArgument(
                'locale',
                InputArgument::OPTIONAL,
                'Translations locale.',
                null
            )
        ];
    }

    /**
     * Get the console command options.
     * 
     * @return array<\Symfony\Component\Console\Input\InputOption>
     */
    public function getOptions() : array
    {
        return [
            new InputOption(
                'namespace',
                null,
                InputOption::VALUE_REQUIRED,
                'Translations namespace for structured stores.',
                null
            ),
            new InputOption(
                'group',
                null,
                InputOption::VALUE_REQUIRED,
                'Translations group for structured stores.',
                '*'
            ),
            new InputOption(
                'store',
                null,
                InputOption::VALUE_REQUIRED,
                'Store to flush the translations from. If not provided, the default store will be used.',
                null
            )
        ];
    }
}
