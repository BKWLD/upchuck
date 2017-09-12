<?php namespace Bkwld\Upchuck;

class Facade extends \Illuminate\Support\Facades\Facade {

    /**
     * Get a URL given the path to an asset uploaded via Upchuck
     *
     * @param string $path
     * @return string
     */
    public static function url($path) {
        return static::$app['upchuck']->url($path);
    }

    /**
     * Return the Flysystem remote disk as the main facade so
     * the remote disk can be easily interacted with
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'upchuck.disk';
    }

}
