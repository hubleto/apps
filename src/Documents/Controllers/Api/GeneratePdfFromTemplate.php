<?php declare(strict_types=1);

namespace HubletoApp\Community\Documents\Controllers\Api;

use HubletoApp\Community\Documents\Generator;
use HubletoApp\Community\Documents\Models\Template;

class GeneratePdfFromTemplate extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idTemplate = $this->main->urlParamAsInteger('idTemplate');
    $outpuFilename = $this->main->urlParamAsString('outpuFilename');
    $vars = $this->main->urlParamAsArray('vars');

    $mTemplate = $this->main->load(Template::class);
    $template = $mTemplate->record->prepareReadQuery()->where('id', $idTemplate)->get();

    $generator = $this->main->load(Generator::class);
    $idDocument = $generator->generatePdfFromTemplate(
      $template->id,
      $outpuFilename,
      $vars
    );

    return $idDocument;
  }
}
