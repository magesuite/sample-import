<?php

namespace MageSuite\SampleImport\Import;

class Configuration  implements \MageSuite\Importer\Repository\ImportConfiguration
{
    public function getById($id)
    {
        $jsonConfiguration = file_get_contents(__DIR__. '/import.json');
        $importConfiguration = json_decode($jsonConfiguration, true);
        return $importConfiguration[$id];
    }
}