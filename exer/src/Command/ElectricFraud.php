<?php
/**
 * Created by PhpStorm.
 * User: manel
 * Date: 2019-01-27
 * Time: 10:13
 */

namespace App\Command;

use App\Classes\DetectorFraud;
use App\Classes\FileReadings;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ElectricFraud extends Command
{
    protected function configure()
    {
        $this->addArgument('pathFile', InputArgument::REQUIRED, 'path where there is the file')
            ->setName('app:electric-fraud')
            ->setDescription('Analize a file with suspicious readings');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pathFile = $input->getArgument('pathFile');

        if (file_exists($pathFile)) {
            try {
                $file = new FileReadings($pathFile);
                $arrayReadings = $file->convertFileToArray();

                //partial array at n subarrays, so every subarray have all client's periods
                $arrayReadings = $file->partitionFile($arrayReadings, 12);

                $fraud = new DetectorFraud();
                $listReadingsFraud = $fraud->getListReadingsFraud($arrayReadings);
                $output->writeln($listReadingsFraud);

            } catch (Exception $e) {

                throw new Exception($e);
            }
        } else {
            $output->writeln('ERROR: file not exist');
        }
    }
}