<?php
function __autoload_application_domain ($class) {
    $libPath = APPLICATION_PATH . '/../../lib/0.1/src/';
    $path = str_replace('\\', '/', $class);
    if (file_exists($libPath .'/'. $path . '.php')) {
        require_once($libPath .'/'. $path . '.php');
    }
}
spl_autoload_register('__autoload_application_domain');
