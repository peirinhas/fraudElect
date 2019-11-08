<?php
/**
 * Created by PhpStorm.
 * User: manel
 * Date: 2019-01-27
 * Time: 10:20
 */


namespace App\Classes;


use Exception;

class FileReadings
{
    private $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function getFile()
    {
        return $this->file;
    }


    public function convertFileToArray()
    {
        $path_parts = pathinfo($this->getFile());
        $extension = strtoupper($path_parts['extension']);

        switch ($extension) {
            case 'XML':
                return $this->convertXmlToArray();
                break;
            case 'CSV':
                return $this->convertCsvToArray();
                break;
        }
    }

    private function convertXmlToArray()
    {
        $xml = simplexml_load_file($this->getFile());
        $readings = [];

        foreach ($xml->reading as $line) {
            $attributes = $line->attributes();
            $reading = array();
            $reading[0] = (string)$attributes['clientID'][0];
            $reading[1] = (string)$attributes['period'][0];
            $reading[2] = (int)$line[0];
            array_push($readings, $reading);
        }

        return $readings;
    }

    private function convertCsvToArray()
    {
        $readings = [];

        if (($file = fopen($this->getFile(), 'r')) === false) {
            throw new Exception('There was an error loading the CSV file.');

        } else {
            while (($reading = fgetcsv($file, 1000)) !== false) {
                $readings[] = $reading;
            }
        }

        //remove header csv file
        unset($readings[0]);

        return $readings;
    }

    public static function partitionFile($readings, $numRows = 12)
    {
        return array_chunk($readings, $numRows);
    }
}