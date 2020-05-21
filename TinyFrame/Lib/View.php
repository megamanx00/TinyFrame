<?php

/**
 * TinyFrame
 *
 * @copyright Copyright (c) 2018 Michael Gutierrez
 * @license   MIT License
 */

namespace TinyFrame\Lib;

/**
 * Class View
 *
 * The view object exists to display output. The view takes an array of data and sets it as variables within the scope
 * of the view. The view should not have access to any data outside the view object.
 * */
class View
{
    /**
     * Directory for view files
     *
     * @var string
     */
    private $__viewDir;

    /**
     * Base URL for webViews
     *
     * @var string
     */
    private $__baseURL;

    /**
     * Container for view variables
     *
     * @var array
     */
    private static $__container;



    /**
     * Create view object and set base directory
     * @param string $baseDir
     * @param string $baseURL
     */
    public function __construct($baseDir, $baseURL)
    {
        $this->__viewDir = $baseDir;
        $this->__baseURL = $baseURL;

        if(is_null(self::$__container))
        {
            self::$__container = array();
        }
    }

    /**
     * Load a view file and set variables passed to the view. View files can load more views and pass variables to those
     * partial files.
     *
     * @param string $__view
     * @param array $variables
     */
    public function loadView($__view, $variables = array())
    {
        //append php extension if not present
        $__view.= $this->checkForPHPExtension($__view) ? '' : '.php';

        //Set full path to view
        $__view  = $this->__viewDir . '/' . $__view;

        if(!empty($variables))
        {
            self::$__container = array_merge(self::$__container, $variables);
        }

        if(!empty(self::$__container))
        {
            //Create variables for our view
            //@todo: move to array access interface and overide get rather than wasting memory due to scope concerns in sub views.
            foreach(self::$__container as $key=>$var)
            {
                $$key = $key == '__view' ? : $var;
            }
        }

        //show view
        include(realpath($__view));
    }

    /**
     * check if file has php extension
     *
     * @param string $fileName
     * @return bool
     * */
    private function checkForPHPExtension($fileName)
    {
        //.php takes up 4 characters
        if(strlen($fileName < 4))
        {
            //we don't have .php here
            return false;
        }

        $temp = strtolower(substr("$fileName", -1, 4));
        return $temp == '.php';
    }

    /**
     * Return baseurl. Useful for web views
     *
     * @param $url string
     * @return string
     * */
    public function getBaseUrl($url = null)
    {
        if(!is_null($url))
        {
            if($url[0] !== '/')
            {
                $url = '/' . $url;
            }
            return $this->__baseURL . $url;
        }
        return $this->__baseURL;
    }

    public function selectBox(array $options, $id="",$name="", $cssClass="", $selectedValue="")
    {
        $output = ' ';


        $output.="<select name='$name' id='$id' class='$cssClass'>\n";



        foreach($options as $k=>$v)
        {
            $selected = ($selectedValue == $k) ? ' selected="selected"' : '';
            $output.='  <option value="' . $k . '"' . $selected . '>' . $v . '</option>';
        }

        $output.="</select>\n";

        echo $output;
    }
}