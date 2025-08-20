<?php

namespace HubletoApp\Community\Projects\Controllers\Api;

use Exception;
use HubletoApp\Community\Projects\Models\Project;
use HubletoApp\Community\Projects\Models\ProjectOrder;
use HubletoApp\Community\Orders\Models\Order;

class CreateFromOrder extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idOrder = $this->main->urlParamAsInteger("idOrder");

    if ($idOrder <= 0) {
      return [
        "status" => "failed",
        "error" => "The order for converting was not set"
      ];
    }

    $mOrder = $this->main->load(Order::class);
    $mProject = $this->main->load(Project::class);
    $mProjectOrder = $this->main->load(ProjectOrder::class);

    try {
      $order = $mOrder->record->prepareReadQuery()->where($mOrder->table . ".id", $idOrder)->first();
      if (!$order) {
        throw new Exception("Order was not found.");
      }

      $project = $mProject->record->recordCreate([
        "id_customer" => $order->id_customer,
        "title" => $order->title,
        "identifier" => $order->identifier,
      ]);

      $mProjectOrder->record->recordCreate([
        "id_project" => $project['id'],
        "id_order" => $order->id,
      ]);
    } catch (Exception $e) {
      return [
        "status" => "failed",
        "error" => $e
      ];
    }

    return [
      "status" => "success",
      "idProject" => $project['id'],
      "title" => str_replace(" ", "+", (string) $project['title'])
    ];
  }

}
