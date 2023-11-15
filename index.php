<?php

set_time_limit(1800); // Define o tempo limite para 1800 segundos (30 minutos)
ini_set('memory_limit', '3024M'); // Define o limite de memória para 1GB (ou outro valor desejado)



require_once __DIR__ . '/vendor/autoload.php';

use Cnpj\Downloader\ZipDownloader;

$downloader = new ZipDownloader(__DIR__.'/download');
$downloader->fetchFilesAndGenerateJSON();
