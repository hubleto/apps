<?php

namespace HubletoApp\Community\Pipeline\Controllers;

use HubletoApp\Community\Deals\Models\Deal;
use HubletoApp\Community\Projects\Models\Project;
use HubletoApp\Community\Tasks\Models\Task;
use HubletoApp\Community\Orders\Models\Order;
use HubletoApp\Community\Pipeline\Models\Pipeline as ModelPipeline;

class Pipeline extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => '', 'content' => $this->translate('Pipeline') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $fOwner = $this->main->urlParamAsInteger('fOwner');

    $pipelineManager = $this->main->load(\HubletoApp\Community\Pipeline\Manager::class);
    $mPipeline = $this->main->load(ModelPipeline::class);

    $pipelineGroup = $this->main->urlParamAsString('pipelineGroup');
    $idPipeline = $this->main->urlParamAsInteger('idPipeline');

    $pipelineLoader = $pipelineManager->getPipelineLoaderForGroup($pipelineGroup);

    $pipelines = $mPipeline->record->where('group', $pipelineGroup)->get()?->toArray();
    if (!is_array($pipelines)) $pipelines = [];

    if ($idPipeline <= 0) {
      $tmp = reset($pipelines);
      $idPipeline = (int) ($tmp['id'] ?? 0);
    }

    $pipeline = (array) $mPipeline->record
      ->where("id", $idPipeline)
      ->with("STEPS")
      ->first()
      ->toArray()
    ;

    $this->viewParams["pipelines"] = $pipelines;
    $this->viewParams["pipeline"] = $pipeline;
    $this->viewParams["items"] = $pipelineLoader->loadItems($idPipeline, ['fOwner' => $fOwner]);

    $this->setView('@HubletoApp:Community:Pipeline/Pipeline.twig');
  }

}
