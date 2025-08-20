<?php

namespace HubletoApp\Community\Orders\Controllers\Api;

use Exception;
use HubletoApp\Community\Orders\Models\Order;
use HubletoApp\Community\Deals\Models\DealOrder;
use HubletoApp\Community\Deals\Models\Deal;

class CreateFromDeal extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    if (!$this->main->isUrlParam("idDeal")) {
      return [
        "status" => "failed",
        "error" => "The deal for converting was not set"
      ];
    }

    $idDeal = $this->main->urlParamAsInteger("idDeal");

    $mDeal = $this->main->load(Deal::class);
    $mOrder = $this->main->load(Order::class);
    $mDealOrder = $this->main->load(DealOrder::class);
    $deal = null;

    try {
      $deal = $mDeal->record->prepareReadQuery()->where($mDeal->table . ".id", $idDeal)->first();
      if (!$deal) {
        throw new Exception("Deal was not found.");
      }

      $order = $mOrder->record->recordCreate([
        "id_customer" => $deal->id_customer,
        "title" => $deal->title,
        "identifier" => $deal->identifier,
      ]);

      $mDealOrder->record->recordCreate([
        "id_deal" => $deal->id,
        "id_order" => $order['id'],
      ]);
    } catch (Exception $e) {
      return [
        "status" => "failed",
        "error" => $e
      ];
    }

    return [
      "status" => "success",
      "idOrder" => $order['id'],
      "title" => str_replace(" ", "+", (string) $order['title'])
    ];
  }

}
