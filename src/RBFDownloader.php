<?php

namespace Cnpj\Downloader;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class RBFDownloader {
    private $downloadFolder;

    public function __construct($downloadFolder) {
        $this->downloadFolder = $downloadFolder;
    }

    public function fetchFilesAndGenerateJSON() {
        $client = new Client();
        $downloadedFiles = [];
        $downloadedUrls = [];

        try {
            // Faz a requisição para obter o JSON com as informações dos recursos
            $response = $client->request('GET', 'https://dados.gov.br/api/publico/conjuntos-dados/cadastro-nacional-da-pessoa-juridica---cnpj');
            $jsonData = json_decode($response->getBody(), true);

            // Verifica se existem recursos e se são arquivos .zip
            if (isset($jsonData['resources']) && is_array($jsonData['resources'])) {
                foreach ($jsonData['resources'] as $resource) {
                    // Verifica se o formato é zip+csv (assumindo que são os arquivos que deseja baixar)
                    if (isset($resource['format']) && $resource['format'] === 'zip+csv' && isset($resource['url'])) {
                        $fileUrl = $resource['url'];

                        // Verifica se a URL já foi baixada
                        if (!in_array($fileUrl, $downloadedUrls)) {
                            $response = $client->request('GET', $fileUrl, [
                                'progress' => function ($downloadTotal, $downloadedBytes) use ($fileUrl) {
                                    echo "Baixando $fileUrl... $downloadedBytes/$downloadTotal bytes\n";
                                }
                            ]);

                            // Salva o arquivo na pasta de download
                            $fileName = basename($fileUrl);
                            $filePath = $this->downloadFolder . DIRECTORY_SEPARATOR . $fileName;

                            file_put_contents($filePath, $response->getBody());

                            if (file_exists($filePath)) {
                                $downloadedFiles[] = [
                                    'url' => $fileUrl,
                                    'file' => $fileName,
                                    'downloaded_at' => date('Y-m-d H:i:s')
                                ];

                                $downloadedUrls[] = $fileUrl; // Registra a URL como baixada

                                // Verifica se o arquivo é um arquivo ZIP
                                if (pathinfo($filePath, PATHINFO_EXTENSION) === 'zip') {
                                    // Cria uma instância da classe ZipArchive
                                    $zip = new \ZipArchive;

                                    // Abre o arquivo ZIP
                                    if ($zip->open($filePath) === true) {
                                        $extractPath = $this->downloadFolder . DIRECTORY_SEPARATOR . pathinfo($fileName, PATHINFO_FILENAME);

                                        // Extrai o conteúdo do arquivo ZIP para o diretório de destino
                                        $zip->extractTo($extractPath);

                                        // Fecha o arquivo ZIP
                                        $zip->close();

                                        echo "Arquivo descompactado: " . $fileName . "\n";
                                    } else {
                                        echo "Falha ao abrir o arquivo ZIP: " . $fileName . "\n";
                                    }
                                }
                            } else {
                                echo "Falha ao salvar o arquivo: $fileName\n";
                            }
                        }
                    }
                }

                // Salva informações dos arquivos baixados em um arquivo JSON, se necessário
                // file_put_contents('downloaded_files.json', json_encode($downloadedFiles, JSON_PRETTY_PRINT));

                return $downloadedFiles; // Retorna a lista de arquivos baixados, se necessário
            }
        } catch (RequestException $e) {
            echo "Erro ao obter os recursos: " . $e->getMessage();
        }

        return null;
    }
}
