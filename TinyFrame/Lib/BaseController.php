<?php

/**
 * TinyFrame
 *
 * @copyright Copyright (c) 2018 Michael Gutierrez
 * @license   MIT License
 */

namespace TinyFrame\Lib;

/**
 * Abstract class BaseController
 *
 * Extend this class in controllers. This class assumes you will use it to set HTTP status codes to provide useful feedback
 * to the consumer of your controller. Please don't return a 200 on a failure especially if using this for a restful service.
 * This class also allows you to use TinyFrames view model
 * */
abstract class BaseController
{
    /**
     * URI parameters
     *
     * @var array
     */
    protected $uriParams;

    /**
     * HTTP Request method. ie POST, GET, PUT, etc.
     *
     * @var string
     */
    protected $requestType;

    /**
     * Config Object
     *
     * @var Configuration
     */
    protected $config;

    /**
     * HTTP Response reason. Useful for errors
     *
     * @var string
     */
    protected $responseReason;

    /**
     * HTTP Response code. Please use this properly and don't use 200 on errors
     *
     * @var string
     */
    protected $responseCode;

    /**
     * Config file. Use default config if none is set
     *
     * @var string
     */
    protected $configFile = null;

    /**
     * View object. Very simple view object that expects php like templates. If designing a restful service this
     * doesn't even need to be used since you can just output the contents of an array or data structure in json.
     *
     * @var string
     */
    protected $view;

    /**
     * Base URL
     * */

    protected $baseUrl;

    /**
     * Construct starts output buffering, and sets some values for child classes to use. Also sets the configuration object.
     * To use a different configuration object overload in the child class. Child classes can also set a config file
     * different from the default.
     *
     * @param array $uriParams
     * @param string $requestType
     */
    public function __construct($uriParams, $requestType)
    {
        ob_start();
        $this->config = new \TinyFrame\Lib\Configuration($this->configFile);
        $this->uriParams = $uriParams;
        $this->requestType = $requestType;

        //Assume everything is going to be OK
        $this->responseCode = 200;
        $this->responseReason = 'OK';

        //Set Object for view
        $viewDir  = BASE_DIR . $this->config->getValue('environment.view_folder');
        $this->baseUrl = $this->config->getValue('environment.base_url');
        $this->view = new View($viewDir, $this->baseUrl);
    }

    /* Flush output buffer and set header */
    public function __destruct()
    {
        $outputBuffer = ob_get_contents();
        ob_end_clean();

        header( 'HTTP/1.1 ' . $this->responseCode .  ' ' . $this->responseReason . ' ', true, (int) $this->responseCode);
        echo $outputBuffer;
    }

    /**
     * Allow router to set 500 error on uncaught exception
     *
     * @param string $errorBody. Error output
     * @param string $responseReason
     * @param int $responseCode. HTTP response code.
     */
    public function setFail($errorBody, $responseReason = 'Internal Error', $responseCode = 500)
    {
        ob_end_clean();
        echo $errorBody;
        $this->responseCode = $responseCode;
        $this->responseReason = $responseReason;
        exit(0);
    }

    abstract public function indexAction();

    /**
     * Use $_GET or $_POST params after passing it through simple clean. $_GET parameters get priority so if the param
     * is in GET and POST only the GET parameter value will be returned.
     *
     * @param mixed $param
     * @return mixed. Return string, array, or null if there is no parameter.
     * */
    public function getParam($param)
    {
        if(isset($_GET[$param]))
            return $this->simpleClean($_GET[$param]);

        if(isset($_POST[$param]))
            return $this->simpleClean($_POST[$param]);

        return null;
    }

    /**
     * Strip tags from variable. If the variable is an array call function recursively.
     *
     * @param mixed $var
     * @return mixed
     * */
    private function simpleClean($var)
    {
        if(is_array($var))
        {
            foreach($var as $key=>$value)
                $var[$key] = $this->simpleClean($value);

            return $var;
        }
        return strip_tags($var);
    }

    /**
     * Load a view template file with associative data array. $keys will be the variable names for the view with
     * values being the values.
     *
     * @param string $viewFile
     * @param array $variables
     * */
    public function loadView($viewFile, $variables = array())
    {
        $this->view->loadView($viewFile, $variables);
    }
}