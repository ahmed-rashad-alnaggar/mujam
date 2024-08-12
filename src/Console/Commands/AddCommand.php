<?php

namespace Alnaggar\Mujam\Console\Commands;

use Alnaggar\Mujam\Contracts\StructuredStore;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class AddCommand extends Command
{
    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'mujam:add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add translation to the specified store.';

    /**
     * Execute the console command.
     * 
     * @return int
     */
    public function handle() : int
    {
        $store = $this->getLaravel()->make('mujam')->store($this->option('store'));

        $key = $this->argument('key');
        $value = $this->argument('value');
        $locale = $this->argument('locale');

        if ($store instanceof StructuredStore) {
            [$namespace, $group, $item] = $this->getLaravel()->make('translator')->parseKey($key);

            $store->add([$item => $value], $group, $namespace, $locale);
        } else {
            $store->add([$key => $value], $locale);
        }

        $this->info('Translation is added successfully.');

        return self::SUCCESS;
    }

    /**
     * Get the console command arguments.
     * 
     * @return array<\Symfony\Component\Console\Input\InputArgument>
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
                'value',
                InputArgument::REQUIRED,
                'Translation value.'
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
                'Store to save the translation into. If not provided, the default store will be used.',
                null
            )
        ];
    }
}
