<?php
/**
 * Created by PhpStorm.
 * User: manel
 * Date: 2019-01-27
 * Time: 10:22
 */


namespace App\Classes;


class DetectorFraud
{

    public function getListReadingsFraud($arrReadings)
    {
        $header = $this->getHeaderList();
        $body = $this->getBodyList($arrReadings);

        return $header . $body;
    }

    private function getHeaderList()
    {
        $header = "| Client | Month | Suspicious | Median" . PHP_EOL;
        $header .= "--------------------------------------" . PHP_EOL;
        return $header;
    }

    private function getBodyList($arrReadings)
    {
        $readingsFraud = '';

        for ($i = 0; $i < count($arrReadings); $i++) {
            $average = $this->calculateAnnualAverage($arrReadings[$i]);
            $readingsFraud .= $this->getReadingsFrauds($arrReadings[$i], $average);
        }

        return $readingsFraud;
    }

    private function calculateAnnualAverage($anualReadings)
    {
        usort($anualReadings, function ($param1, $param2) {
            return $param1[2] > $param2[2];
        });

        $reading1 = $anualReadings[5];
        $reading2 = $anualReadings[6];
        $average = ($reading1[2] + $reading2[2]) / 2;

        return $average;
    }

    private function getReadingsFrauds($anualReadings, $average)
    {
        $readingsFraud = '';
        $minLimit = $average - ($average * 0.5);
        $maxLimit = $average + ($average * 0.5);

        for ($i = 0; $i < count($anualReadings); $i++) {
            if (!$this->isCorrectReading($anualReadings[$i][2], $maxLimit, $minLimit)) {
                //concatenate readgins frauds
                $readingsFraud .= '| ' . $anualReadings[$i][0] . ' | ' .
                    $anualReadings[$i][1] . ' | ' .
                    $anualReadings[$i][2] . ' | ' .
                    $average .
                    PHP_EOL;
            }
        }

        return $readingsFraud;
    }

    private function isCorrectReading($reading, $maxLimit, $minLimit)
    {
        if ($minLimit < $reading && $reading < $maxLimit) {
            return true;
        } else {
            return false;
        }
    }
}