<?php

class Application_Profiler
{
    private static $_profiling = false;

    public static function start()
    {
        if (!self::$_profiling && getenv('APPLICATION_PROFILER') == 'xhprof' && function_exists('xhprof_enable')) {
            xhprof_enable();
            self::registerShutdown();
            self::$_profiling = 'xhprof';
        }
    }

    private static function registerShutdown()
    {
        register_shutdown_function(function () {
            Application_Profiler::end();
        });
    }

    public static function end()
    {
        if (self::_profiling == 'xhprof') {
            self::$_profiling = false;

            $profile = xhprof_disable();

            // TODO handle $profile
        }
    }
}
