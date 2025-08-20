<?php

namespace HubletoApp\Community\Projects\Controllers\Api;

use Exception;
use HubletoApp\Community\Projects\Models\Project;
use HubletoApp\Community\Projects\Models\ProjectDeal;
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
    $mProject = $this->main->load(Project::class);
    $mProjectDeal = $this->main->load(ProjectDeal::class);
    $deal = null;

    try {
      $deal = $mDeal->record->prepareReadQuery()->where($mDeal->table . ".id", $idDeal)->first();
      if (!$deal) {
        throw new Exception("Deal was not found.");
      }

      $project = $mProject->record->recordCreate([
        "id_customer" => $deal->id_customer,
        "title" => $deal->title,
        "identifier" => $deal->identifier,
      ]);

      $mProjectDeal->record->recordCreate([
        "id_deal" => $deal->id,
        "id_project" => $project['id'],
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
