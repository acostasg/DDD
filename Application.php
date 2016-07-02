<?php
require_once 'Zend/Application.php';

class Application extends Zend_Application
{

    protected static $_catchErrors = false;
    protected $_error;

    private static $_registry;
    private static $_container;

    /**
     * Constructor
     *
     * Initialize application. Potentially initializes include_paths, PHP
     * settings, and bootstrap class.
     *
     * @param  string|array|Zend_Config $options String path to configuration file,
     *  or array/Zend_Config of configuration options
     * @throws Zend_Application_Exception When invalid options are provided
     * @return void
     */
    public function __construct($options = null)
    {
        $isCli = (php_sapi_name() === 'cli');
        if (self::$_catchErrors && !$isCli) {
            ini_set('display_errors', '0');
            set_error_handler(array($this, 'handleError'));
            register_shutdown_function(array($this, 'onShutdown'));
        }
        if (!defined('APPLICATION_ENV')) {
            if (!$env = getenv('APPLICATION_ENV')) {
                throw new Exception("APPLICATION_ENV is not defined");
            }
            define('APPLICATION_ENV', $env);
        }
        parent::__construct(APPLICATION_ENV, $options);
        if (self::$_catchErrors && !$isCli) {
            restore_error_handler();
        }
    }

    /**
     * Bootstrap application
     *
     * @param  null|string|array $resource
     * @return Zend_Application
     */
    public function bootstrap($resource = null)
    {
        if (self::$_catchErrors) {
            set_error_handler(array($this, 'handleError'));
        }
        parent::bootstrap($resource);
        if (self::$_catchErrors) {
            restore_error_handler();
        }
        return $this;
    }

    /**
     * Run the application
     *
     * @return void
     */
    public function run()
    {
        if (self::$_catchErrors) {
            set_error_handler(array($this, 'handleError'));
        }
        parent::run();
        if (self::$_catchErrors) {
            restore_error_handler();
        }
    }

    public function onShutdown()
    {
        if ($this->_error === null) {
            $this->_error = error_get_last();
            if ($this->_error === null) {
                return;
            }
        }
        $this->sendErrorResponse();
    }

    /*
     * A catch-all error handler
     *
     * Usually, if something goes horribly wrong, all errors will handled by 
     * the ErrorController. As a last line of defense, there's this.
     */
    public function handleError($errno, $errstr, $errfile, $errline, $errcontext)
    {
        if ($errno & error_reporting()) {
            $this->_error = array(
                'type' => $errno,
                'message' => $errstr,
                'file' => $errfile,
                'line' => $errline,
            );
            die;
        }
    }

    public function sendErrorResponse()
    {
        $errorName = null;
        if (is_int($this->_error['type'])) {
            $constants = get_defined_constants(true);
            foreach ($constants['Core'] as $cnst => $val) {
                if (substr($cnst, 0, 2) === 'E_' && $val == $this->_error['type']) {
                    $errorName = $cnst;
                    break;
                }
            }
        }
        extract($this->_error);
        header_remove();
        header('Content-type: text/html');
        header("HTTP/1.0 500 Internal server error");
        include dirname(__file__) . '/Application/ErrorTemplate.html';
    }

    /**
     * Retrieve model object singleton
     *
     * @param   string $modelClass
     * @param   array $arguments
     * @return  mixed
     */
    public static function getSingleton($class = '')
    {
        $registryKey = '_singleton/' . $class;
        if (!self::_registry($registryKey)) {
            if (!class_exists($class)) {
                throw new Application_Model_Exception(
                    'PHP says that this class not exists.',
                    Application_Model_Exception::VALIDATION_INVALID_PARAM);
            }
            self::register($registryKey, new $class());
        }
        return self::_registry($registryKey);
    }

    /**
     * Retrieve a value from registry by a key
     *
     * @param string $key
     * @return mixed
     */
    private static function _registry($key)
    {
        if (isset(self::$_registry[$key])) {
            return self::$_registry[$key];
        }
        return null;
    }

    /**
     * Register a new variable
     *
     * @param string $key
     * @param mixed $value
     * @param bool $graceful
     * @throws Application_Model_Exception
     */
    public static function register($key, $value, $graceful = false)
    {
        if (isset(self::$_registry[$key])) {
            if ($graceful) {
                return;
            }
            throw new Application_Model_Exception(__CLASS__ . ' registry key "' . $key . '" already exists');
        }
        self::$_registry[$key] = $value;
    }

    /**
     * Unregister a variable from register by key
     *
     * @param string $key
     */
    public static function unregister($key)
    {
        if (isset(self::$_registry[$key])) {
            if (is_object(self::$_registry[$key]) && (method_exists(self::$_registry[$key], '__destruct'))) {
                self::$_registry[$key]->__destruct();
            }
            unset(self::$_registry[$key]);
        }
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    public static function getContainer()
    {
        if (self::$_container == null) {
            self::$_container = new Symfony\Component\DependencyInjection\ContainerBuilder();
            $loader = new Symfony\Component\DependencyInjection\Loader\YamlFileLoader(
                self::$_container,
                new Symfony\Component\Config\FileLocator(APPLICATION_PATH . '/../../fa-setup/setup/')
            );
            $loader->load('services.yml');
        }
        return self::$_container;
    }

    /**
     * Unregister a variable from register by key
     *
     * @param string $key
     */
    public static function unregisterAll()
    {
        if (!empty(self::$_registry)) {
            foreach (array_keys(self::$_registry) as $key) {
                if (is_callable(self::$_registry[$key], '__destruct')) {
                    self::$_registry[$key]->__destruct();
                }
                unset(self::$_registry[$key]);
            }
        }
    }
}
