<?php

/**
 * TinyFrame
 *
 * @copyright Copyright (c) 2018 Michael Gutierrez
 * @license   MIT License
 */

namespace TinyFrame\Utils;

/**
 * Utility to scale images and resize them. This also allows for a background fill if the target aspect ratio is
 * different from the source.
 * */
class CopyFillHelper
{
    private $targetHeight;
    private $targetWidth;
    private $backgroundRed = 255;
    private $backgroundGreen = 255;
    private $backgroundBlue = 255;

    /**
     * Set Height for target image
     * */
    public function setHeight($height)
    {
        $height = (int) $height;
        if($height < 1)
        {
            throw new \Exception('Height must be an integer greater 0');
        }
        $this->targetHeight = $height;
    }

    /**
     * Set Width for target image
     * */
    public function setWidth($width)
    {
        $width = (int) $width;
        if($width < 1)
        {
            throw new \Exception('Height must be an integer greater 0');
        }
        $this->targetWidth = $width;
    }

    /**
     * Set background for scaling images. Default is white
     *
     * @param integer $red.
     * @param integer $green.
     * @param integer $blue.
     * */
    public function setBackground($red, $green, $blue)
    {
        $this->backgroundRed   = (int) $red;
        $this->backgroundGreen = (int) $green;
        $this->backgroundBlue  = (int) $blue;
    }

    /**
     * Resize source file to target file with previously set dimensions
     * @param string $source_file. File to resize
     * @param string $target_file.
     * @throws \Exception. Throws when dir of target file does not exist and function can not create it.
     * */
    public function copy_fill($source_file, $target_file)
    {
        //Make directory for target file if it does not exist
        $basedir = dirname($target_file);
        if (!is_dir($basedir))
        {
            // Create the directory recursively
            if (!mkdir($basedir, 0777, true))
            {
                throw new \Exception("Could not create the folder : {$basedir}");
            }
        }

        //Values for scaling
        $offset_x = 0;
        $offset_y = 0;

        // Get source dimensions
        list($source_width, $source_height) = getimagesize($source_file);

        //Get ratios
        $ratio = $source_width/$source_height;
        $target_ratio = $this->targetWidth/$this->targetHeight;

        //Set image size and offsets

        //Is Old image proportionally wider than target?
        if($ratio > $target_ratio)
        {
            $new_width = $this->targetWidth;
            $new_height = $this->targetHeight / $ratio;
            $offset_x = 0;
            $offset_y = floor(($this->targetHeight - $new_height) / 2);
        }
        elseif($ratio < $target_ratio) //is it proportionally longer?
        {
            $new_height = $this->targetHeight;
            $new_width = $this->targetHeight * $ratio;
            $offset_y = 0;
            $offset_x = floor(($this->targetWidth - $new_width) / 2);
        }
        else //same ratio
        {
            $new_height = $this->targetHeight;
            $new_width  = $this->targetWidth;
        }


        //Make background image
        $background = imagecreatetruecolor($this->targetWidth, $this->targetHeight);
        $white = imagecolorallocate($background, $this->backgroundRed, $this->backgroundGreen, $this->backgroundBlue);
        imagefill($background, 0, 0, $white);

        //Get Extension
        $temp = explode ( '.', $source_file);
        $exten = strtolower($temp[count($temp)-1]);

        //Get Target Extension
        $temp = explode ( '.', $target_file);
        $target_exten = strtolower($temp[count($temp)-1]);

        switch($exten)
        {
            case 'jpg':
            case 'jpeg':
                //make image from jpeg
                $image = imagecreatefromjpeg($source_file);
                break;
            case 'png':
                //Make image from png
                $image = imagecreatefrompng($source_file);
                break;

            case 'gif':
                //make image from gif
                $image = imagecreatefromgif($source_file);
                break;
        }

        //Create new resized image with white background
        imagecopyresampled($background, $image, $offset_x, $offset_y, 0, 0, $new_width, $new_height, $source_width, $source_height);

        switch($target_exten)
        {
            case 'jpg':
                // Output
                imagejpeg($background, $target_file, 100);
                break;
            case 'png':
                // Output
                imagepng($background, $target_file);
                break;
            case 'gif':
                // Output
                imagegif($background, $target_file);
                break;
        }
    }
}