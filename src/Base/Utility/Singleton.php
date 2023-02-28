<?php
/**
 * @Author yaangvu
 * @Date   Sep 07, 2022
 */

namespace YaangVu\LaravelBase\Base\Utility;

use Exception;

trait Singleton
{
    /**
     * The Singleton's instance is stored in a static field. This field is an
     * array, because we'll allow our Singleton to have subclasses. Each item in
     * this array will be an instance of a specific Singleton's subclass. You'll
     * see how this works in a moment.
     *
     * @var object|null
     */
    private static ?object $instance = null;

    /**
     * The Singleton's constructor should always be private to prevent direct
     * construction calls with the `new` operator.
     */
    protected function __construct()
    {
    }

    /**
     * This is the static method that controls the access to the singleton
     * instance. On the first run, it creates a singleton object and places it
     * into the static field. On subsequent runs, it returns the client existing
     * object stored in the static field.
     *
     * This implementation lets you subclass the Singleton class while keeping
     * just one instance of each subclass around.
     */
    public static function getInstance(): static
    {
        if (is_null(self::$instance))
            self::$instance = new self();

        return self::$instance;
    }

    /**
     * Singletons should not be restartable from strings.
     *
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot serialize a singleton.");
    }

    /**
     * Singletons should not be cloneable.
     */
    protected function __clone()
    {
    }
}
