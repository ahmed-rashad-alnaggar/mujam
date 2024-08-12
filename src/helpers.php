<?php

if (! function_exists('mujam')) {
    /**
     * Get the Mujam instance or a specific translation store.
     *
     * If a store name is **provided**, it returns the store instance associated with that name.
     * If `null` is **passed** as the store name, the default store is returned.
     *
     * @param string|null $name
     * @return \Alnaggar\Mujam\TranslationManager|\Alnaggar\Mujam\Contracts\Store
     */
    function mujam()
    {
        $arguments = func_get_args();

        if (empty($arguments)) {
            return app('mujam');
        }

        return app('mujam')->store($arguments[0]);
    }
}
