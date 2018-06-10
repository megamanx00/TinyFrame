<?php

/**
 * TinyFrame
 *
 * @copyright Copyright (c) 2018 Michael Gutierrez
 * @license   MIT License
 */

namespace TinyFrame\Utils;

/**
 * Utility for uploading files. This object will also save error text and error codes if there are any problems
 * uploading the file
 *
 * @todo Refactor old snake case code to camel case
 * */
class FileUploader
{
    private $_file_size;
    private $_file_name;
    private $_file_type;
    private $_error_code;
    private $_error_text;
    private $_temp_file;

    //--Constructor
    public function __construct()
    {
        $this->_error_code = 0;
        $this->_error_text = "";
    }

    //Function to get file extension
    public function getExtension($file_name)
    {
        $temp = explode ( '.', $file_name);
        return $temp[count($temp)-1];
    }

    public function getError()
    {
        return $this->_error_text;
    }

    public function doFileUpload($file, $path="", $max_size=8000000, $ext_allow=array(), $over_write_ok=true, $force_file_name="")
    {
        $this->_temp_file = $_FILES[$file]['tmp_name'];
        $this->_file_size = $_FILES[$file]['size'];
        $this->_file_name = $_FILES[$file]['name'];
        $this->_file_type = $_FILES[$file]['type'];

        if (strlen($force_file_name))
        {
            $this->_file_name = $force_file_name;
        }

        if($this->_temp_file == 'none' || $this->_temp_file == '')
        {
            $this->_error_text = "This File was unspecified.";
            $this->_error_code = 1;
            return $this->_error_code;
        }

        //Attach prevention
        if(!is_uploaded_file($this->_temp_file))
        {
            $this->_error_text =  'Possible File Upload Attack, file_name: "' . $this->_temp_file. '"';
            $this->_error_code = 2;
            return $this->_error_code;
        }

        //empty file
        if($this->_file_size == 0)
        {
            $this->_error_text = 'The file you attempted to upload is zero length!';
            $this->_error_code = 3;
            return $this->_error_code;
        }

        //Get File Extension
        $extension = strtolower($this->getExtension($this->_file_name));

        //valid extension?
        if(!((count($ext_allow)==0)||(in_array($extension, $ext_allow))))
        {
            $this->_error_text = 'You attempted to upload a file with an invalid extension';
            $this->_error_code = 4;
            return $this->_error_code;
        }

        //Is the file too big?
        if($this->_file_size > $max_size)
        {
            $this->_error_text = 'File is too big. The maximum file size is' . ($max_size / 1048576) . 'MB.';
            $this->_error_code = 5;
            return $this->_error_code;
        }

        //Does the File already exist?
        if(file_exists($path . $this->_file_name) && !($over_write_ok))
        {
            $this->_error_text = 'File is Already Uploaded';
            $this->_error_code = 6;
            return $this->_error_code;
        }

        //Create directory if it doesn't exist
        if(!(is_dir($path)))
        {
            mkdir($path);
        }

        //Everything is good, return the upload file
        if(!move_uploaded_file($this->_temp_file, $path . $this->_file_name))
        {
            //Moving the file failed for some reason. Make sure file permissions are right.
            $this->_error_text = 'File upload failed';
            $this->_error_code = 10;
            return $this->_error_code;
        }
        if (!chmod ($path . $this->_file_name, 0777))//Remove if your webserver hates you :D
        {
            $this->_error_text = 'Permissions error';
            $this->_error_code = 11;
            return $this->_error_code;
        }

        $this->_error_text = '';
        $this->_error_code = 0;
        return $this->_error_code;
        //yay we made it no errors
    }

}
