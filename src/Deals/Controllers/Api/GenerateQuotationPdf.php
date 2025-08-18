<?php declare(strict_types=1);

namespace HubletoApp\Community\Deals\Controllers\Api;

use HubletoApp\Community\Deals\Models\Deal;

class GenerateQuotationPdf extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idDeal = $this->main->urlParamAsInteger('idDeal');
    $mDeal = $this->main->load(Deal::class);
    $idDocument = $mDeal->generateQuotationPdf($idDeal);
    return [
      'idDocument' => $idDocument
    ];
  }
}
