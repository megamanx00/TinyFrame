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
     * Create view object and set base directory
     * @param string $baseDir
     */
    public function __construct($baseDir)
    {
        $this->__viewDir = $baseDir;
    }

    /**
     * Load a view file and set variables passed to the view. View files can load more views and pass variables to those
     * partial files.
     *
     * @param string $baseDir
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
            //Create variables for our view
            foreach($variables as $key=>$var)
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
}