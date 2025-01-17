<?php

namespace Alnaggar\Mujam;

use Alnaggar\Mujam\Contracts\Factory as TranslationManagerContract;
use Alnaggar\Mujam\Contracts\Store;
use Alnaggar\Mujam\Stores\DatabaseStore;
use Alnaggar\Mujam\Stores\JsonStore;
use Alnaggar\Mujam\Stores\MoStore;
use Alnaggar\Mujam\Stores\PhpStore;
use Alnaggar\Mujam\Stores\PoStore;
use Alnaggar\Mujam\Stores\XliffStore;
use Alnaggar\Mujam\Stores\YamlStore;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Traits\Macroable;

/**
 * @mixin \Alnaggar\Mujam\Contracts\Store
 */
class TranslationManager implements TranslationManagerContract
{
    use Macroable {
        __call as macroCall;
    }

    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The configuration repository instance.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * The array of resolved translation stores.
     *
     * @var array<string, \Alnaggar\Mujam\Contracts\Store>
     */
    protected $stores = [];

    /**
     * The registered custom driver creators.
     *
     * @var array<string, callable>
     */
    protected $customCreators = [];

    /**
     * Create a new instance.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->config = $app['config'];

        $this->setApplication($app);
    }

    /**
     * Add translation store.
     * 
     * @param string $name The name of the store.
     * @param \Alnaggar\Mujam\Contracts\Store $store The store instance to associate with the given `name`.
     * @return static
     */
    public function addStore(string $name, Store $store)
    {
        $this->stores[$name] = $store;

        return $this;
    }

    /**
     * Retrieve a translation store instance by its name.
     *
     * @param string|null $name The name of the store to retrieve. If `null`, the default store is returned.
     * @throws \InvalidArgumentException If the specified store name is not defined in the configuration.
     * @return \Alnaggar\Mujam\Contracts\Store The store instance associated with the given `name`.
     */
    public function store(?string $name = null) : Store
    {
        $name = $name ?? $this->getDefaultStore();
        $stores = $this->getStores();

        if (! array_key_exists($name, $stores)) {
            throw new \InvalidArgumentException("Translation store [{$name}] is not defined.");
        }

        return $stores[$name];
    }

    /**
     * Get the default translation store name.
     *
     * @return string
     */
    public function getDefaultStore() : string
    {
        return $this->config->get('mujam.default');
    }

    /**
     * Set the default translation store.
     *
     * @param string $name
     * @return static
     */
    public function setDefaultStore(string $name)
    {
        $this->config->set('mujam.default', $name);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getStores() : array
    {
        if (empty($this->stores)) {
            $this->registerStores();
        }

        return $this->stores;
    }

    /**
     * {@inheritDoc}
     */
    public function setStores(array $stores)
    {
        $this->stores = $stores;

        return $this;
    }

    /**
     * Register the predefined stores.
     * 
     * @return void
     */
    protected function registerStores() : void
    {
        $stores = $this->config->get('mujam.stores', []);

        foreach ($stores as $name => $config) {
            $store = $this->resolve($config);

            $this->addStore($name, $store);
        }
    }

    /**
     * Resolve the store using the given configurations.
     *
     * @param array $config
     * @throws \InvalidArgumentException
     * @return \Alnaggar\Mujam\Contracts\Store
     */
    protected function resolve(array $config) : Store
    {
        $driver = $config['driver'];

        if (isset($this->customCreators[$driver])) {
            return $this->callCustomCreator($config);
        } else {
            $storeMethod = 'create' . ucfirst($driver) . 'Store';

            if (method_exists($this, $storeMethod)) {
                return $this->{$storeMethod}($config);
            } else {
                throw new \InvalidArgumentException("Driver [{$driver}] is not supported.");
            }
        }
    }

    /**
     * Call a custom driver creator.
     *
     * @param array $config
     * @return \Alnaggar\Mujam\Contracts\Store
     */
    protected function callCustomCreator(array $config) : Store
    {
        return call_user_func($this->customCreators[$config['driver']], $this->getApplication(), $config);
    }

    /**
     * Create an instance of the database translation store.
     *
     * @param array $config
     * @return \Alnaggar\Mujam\Stores\DatabaseStore
     */
    protected function createDatabaseStore(array $config) : DatabaseStore
    {
        $connection = $this->getApplication()->make('db')->connection($config['connection'] ?? null);

        return new DatabaseStore($connection, $config['table']);
    }

    /**
     * Create an instance of the json translation store.
     *
     * @param array $config
     * @return \Alnaggar\Mujam\Stores\JsonStore
     */
    protected function createJsonStore(array $config) : JsonStore
    {
        return new JsonStore($config['paths'], $config['metadata'] ?? []);
    }

    /**
     * Create an instance of the mo translation store.
     *
     * @param array $config
     * @return \Alnaggar\Mujam\Stores\MoStore
     */
    protected function createMoStore(array $config) : MoStore
    {
        return new MoStore($config['paths'], $config['metadata'] ?? []);
    }

    /**
     * Create an instance of the php translation store.
     *
     * @param array $config
     * @return \Alnaggar\Mujam\Stores\PhpStore
     */
    protected function createPhpStore(array $config) : PhpStore
    {
        return new PhpStore($config['paths'], $config['metadata'] ?? []);
    }

    /**
     * Create an instance of the po translation store.
     *
     * @param array $config
     * @return \Alnaggar\Mujam\Stores\PoStore
     */
    protected function createPoStore(array $config) : PoStore
    {
        return new PoStore($config['paths'], $config['metadata'] ?? []);
    }

    /**
     * Create an instance of the xliff translation store.
     *
     * @param array $config
     * @return \Alnaggar\Mujam\Stores\XliffStore
     */
    protected function createXliffStore(array $config) : XliffStore
    {
        return new XliffStore($config['paths'], $config['metadata'] ?? []);
    }

    /**
     * Create an instance of the yaml translation store.
     *
     * @param array $config
     * @return \Alnaggar\Mujam\Stores\YamlStore
     */
    protected function createYamlStore(array $config) : YamlStore
    {
        return new YamlStore($config['paths'], $config['metadata'] ?? []);
    }

    /**
     * Forget the store associated with the given `name`.
     * 
     * @param string $name The name of the store to forget.
     * @return static
     */
    public function forgetStore(string $name)
    {
        unset($this->stores[$name]);

        return $this;
    }

    /**
     * Forget all of the resolved store instances.
     *
     * @return static
     */
    public function forgetStores()
    {
        $this->stores = [];

        return $this;
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param string $driver The driver name.
     * @param callable $callback The driver creator Closure.
     * @return static
     */
    public function extend(string $driver, callable $callback)
    {
        $this->customCreators[$driver] = $callback;

        return $this;
    }

    /**
     * Get the application instance used by the manager.
     *
     * @return \Illuminate\Contracts\Foundation\Application The application instance used by the manager.
     */
    public function getApplication() : Application
    {
        return $this->app;
    }

    /**
     * Set the application instance to be used by the manager.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app The application instance to be used.
     * @return static
     */
    public function setApplication(Application $app)
    {
        $this->app = $app;

        return $this;
    }

    /**
     * Dynamically call the default store instance.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        if (static::hasMacro($method)) {
            return static::macroCall($method, $parameters);
        }

        return $this->store()->$method(...$parameters);
    }
}
