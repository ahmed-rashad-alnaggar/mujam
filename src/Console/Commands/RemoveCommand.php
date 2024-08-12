<?php

namespace Alnaggar\Mujam\Console\Commands;

use Alnaggar\Mujam\Contracts\StructuredStore;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class RemoveCommand extends Command
{
    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'mujam:remove';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove translation from the specified store.';

    /**
     * Execute the console command.
     * 
     * @return int
     */
    public function handle() : int
    {
        $store = $this->getLaravel()->make('mujam')->store($this->option('store'));

        $key = $this->argument('key');
        $locale = $this->argument('locale');

        if ($store instanceof StructuredStore) {
            [$namespace, $group, $item] = $this->getLaravel()->make('translator')->parseKey($key);

            $store->remove([$item], $group, $namespace, $locale);
        } else {
            $store->remove([$key], $locale);
        }

        $this->info('Translation is removed successfully.');

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
                'key',
                InputArgument::REQUIRED,
                'Translation key.'
            ),
            new InputArgument(
                'locale',
                InputArgument::OPTIONAL,
                'Translation locale.',
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
                'store',
                null,
                InputOption::VALUE_REQUIRED,
                'Store to remove the translation from. If not provided, the default store will be used.',
                null
            )
        ];
    }
}
