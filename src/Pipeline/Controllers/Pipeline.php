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

    $pipelines = $mPipeline->record->get()?->toArray();
    if (!is_array($pipelines)) $pipelines = [];

    $idPipeline = $this->main->urlParamAsInteger('idPipeline');

    $pipeline = $mPipeline->record
      ->where("id", $idPipeline)
      ->with("STEPS")
      ->first()
    ;

    if ($pipeline) {
      $pipelineLoader = $pipelineManager->getPipelineLoaderForGroup($pipeline->group);

      $this->viewParams["pipeline"] = $pipeline;
      $this->viewParams["items"] = $pipelineLoader->loadItems($idPipeline, ['fOwner' => $fOwner]);
    }

    $this->viewParams["pipelines"] = $pipelines;

    $this->setView('@HubletoApp:Community:Pipeline/Pipeline.twig');
  }

}
