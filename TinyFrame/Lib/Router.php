<?php

/**
 * TinyFrame
 *
 * @copyright Copyright (c) 2018 Michael Gutierrez
 * @license   MIT License
 */

namespace TinyFrame\Lib;

/**
 * Class Router
 *
 * Abstract class for simple router. All this class does is parse the URI and assume the default controller. By leaving
 * the loadController function as an abstract method the details of how to load controllers is left to the implementation
 * of this class allowing different behaviors per project
 * */
abstract class Router
{
    /**
     * HTTP Request method. Useful for restful services
     *
     * @var string
     */
    protected $method;

    /**
     * URI parameters
     *
     * @var array
     */

    protected $uriParams;

    /**
     * Name of Controller to load
     *
     * @var string
     */
    protected $controller;

    /**
     * Create router object
     *
     * parse URI and extract controller method. Allow the assumption of an Index controller if nothing is there to parse
     * */
    public function __construct()
    {
        //Get request method and uri without get parameters
        $this->method = $_SERVER['REQUEST_METHOD'];
        $temp = explode('?',$_SERVER['REQUEST_URI']);
        $uri = $temp[0];

        if($uri && $uri != '/')
        {
            $this->uriParams = explode('/', $uri);
            $this->cleanUriParts();
            $tempController = array_shift($this->uriParams); //probably empty space
            $tempController = $tempController == '' ? array_shift($this->uriParams) : $tempController; //just in case
            $this->controller = ucwords(strtolower($tempController)); //Should be our params
        }
        else
        {
            $this->uriParams = array();
            $this->controller = "Index";
        }
    }

    /**
     * Clean URI segments
     * */
    protected function cleanUriParts()
    {
        foreach($this->uriParams as $key => $value)
            $this->uriParams[$key] = preg_replace("/[^a-zA-Z0-9]/", '', $value);
    }

    /**
     * Implement to load controller
     * */
    abstract function loadController();
}