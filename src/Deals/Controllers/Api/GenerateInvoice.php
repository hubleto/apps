<?php declare(strict_types=1);

namespace HubletoApp\Community\Deals\Controllers\Api;

use HubletoApp\Community\Deals\Models\Deal;

class GenerateInvoice extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idDeal = $this->main->urlParamAsInteger('idDeal');

    $mDeal = $this->main->load(Deal::class);
    $idInvoice = $mDeal->generateInvoice($idDeal);

    return $idInvoice;
  }
}
