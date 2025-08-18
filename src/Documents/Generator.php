<?php

namespace HubletoApp\Community\Documents;

use Dompdf\Dompdf;
use Dompdf\Options;

class Generator extends \HubletoMain\CoreClass
{

  /**
   * Creates a document in upload folder with a given content and returns ID of created document.
   *
   * @param string $content Content of the document
   * @param string $outputFilename Name of the output file
   * 
   * @return int ID of generated document
   * 
   */
  public function saveFromString(string $content, string $outputFilename): int
  {
    @file_put_contents($this->main->uploadFolder . '/' . $outputFilename, $content);
    
    $mDocument = $this->main->load(Models\Document::class);
    $document = $mDocument->record->recordCreate([
      'id_folder' => 0,
      'name' => $outputFilename,
      'file' => $outputFilename,
    ]);

    return (int) $document['id'];
  }

  /**
   * Generates PDF document from template and returns ID of the generated document.
   *
   * @param int $idTemplate ID of template to be used for generating the document
   * @param string $outputFilename Name of the file to be generated.
   * @param array $vars Variable values to be replaced in template.
   * 
   * @return int ID of generated document
   * 
   */
  public function generatePdfFromTemplate(int $idTemplate, string $outputFilename, array $vars): int
  {
    $mTemplate = $this->main->load(Models\Template::class);
    $template = $mTemplate->record->prepareReadQuery()->where('id', $idTemplate)->first();

    $vars['defaultStyle'] = "
      <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
      <head>
        <style>
          @font-face {
            font-family: 'Gabarito';
            font-style: normal;
            font-weight: normal;
            src: url('{$this->main->assetsUrl}/fonts/Gabarito/Gabarito-VariableFont_wght.ttf') format('truetype');
          }

          @font-face {
            font-family: 'Gabarito';
            font-style: normal;
            font-weight: bold;
            src: url('{$this->main->assetsUrl}/fonts/Gabarito/Gabarito-VariableFont_wght.ttf') format('truetype');
          }

          * { font-family: 'Gabarito'; font-size: '10pt' }
        </style>
      </head>
    ";

    $twigTemplate = $this->main->twig->createTemplate($template->content);
    $documentHtmlContent = $twigTemplate->render($vars);

    $options = new Options();
    $options->set('isRemoteEnabled', true);

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($documentHtmlContent, 'UTF-8');
    $dompdf->setPaper('A4');
    $dompdf->render();

    $idDocument = $this->saveFromString($dompdf->output(), $outputFilename);

    return $idDocument;

  }

}