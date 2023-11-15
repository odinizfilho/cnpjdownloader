# CNPJDownloader -  Downloader de dados de CNPJ 

Este é um pacote PHP que permite baixar e extrair arquivos de dados do Cadastro Nacional da Pessoa Jurídica (CNPJ) disponíveis na API pública do dados.gov.br.

## Requisitos
PHP 8.1 ou superior
Composer para gerenciar dependências (se você ainda não tiver instalado, clique aqui para obter instruções de instalação)

## Instalação

Instale CNPJDownloader com composer

```bash
  composer require odinizfilho/cnpjdownloader:dev-master
```
## Uso/Exemplos
Aqui está como você pode usar o CNPJDownloader em seu projeto PHP:

```php

use Cnpj\Downloader\RFBDownloader;

$downloader = new RFBDownloader(__DIR__.'/download');
$downloader->fetchFilesAndGenerateJSON();

```

## Licença

[MIT](https://choosealicense.com/licenses/mit/)
