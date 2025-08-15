<?php declare(strict_types=1);

namespace HubletoApp\Community\Orders\Controllers\Api;

use HubletoApp\Community\Orders\Models\Order;

class GeneratePdf extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idOrder = $this->main->urlParamAsInteger('idOrder');
    $mOrder = $this->main->load(Order::class);
    $idDocument = $mOrder->generatePdf($idOrder);
    return [
      'idDocument' => $idDocument
    ];
  }
}
