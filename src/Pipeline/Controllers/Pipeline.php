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

    $mPipeline = $this->main->load(ModelPipeline::class);

    $fOwner = $this->main->urlParamAsInteger('fOwner');
    $pipelineType = $this->main->urlParamAsString('pipelineType');
    $idPipeline = $this->main->urlParamAsInteger('idPipeline');
    $fPipelineType = [
      'deals' => $mPipeline::TYPE_DEAL_MANAGEMENT,
      'projects' => $mPipeline::TYPE_PROJECT_MANAGEMENT,
      'tasks' => $mPipeline::TYPE_TASK_MANAGEMENT,
      'orders' => $mPipeline::TYPE_ORDER_MANAGEMENT,
    ][$pipelineType] ?? 0;

    $pipelines = $mPipeline->record->where('type', $fPipelineType)->get()?->toArray();
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

    $itemDetailsUrl = '';

    switch ($pipelineType) {
      case 'deals':
        $mDeal = $this->main->load(Deal::class);
        $items = $mDeal->record->prepareReadQuery()
          ->where($mDeal->table . ".id_pipeline", $idPipeline)
          ->where($mDeal->table . ".is_closed", false)
        ;
        $itemDetailsUrl = $this->main->projectUrl . '/deals';
      break;
      case 'projects':
        $mProject = $this->main->load(Project::class);
        $items = $mProject->record->prepareReadQuery()
          ->where($mProject->table . ".id_pipeline", $idPipeline)
          ->where($mProject->table . ".is_closed", false)
        ;
        $itemDetailsUrl = $this->main->projectUrl . '/projects';
      break;
      case 'tasks':
        $mTask = $this->main->load(Task::class);
        $items = $mTask->record->prepareReadQuery()
          ->where($mTask->table . ".id_pipeline", $idPipeline)
          ->where($mTask->table . ".is_closed", false)
        ;
        $itemDetailsUrl = $this->main->projectUrl . '/tasks';
      break;
      case 'orders':
        $mOrder = $this->main->load(Order::class);
        $items = $mOrder->record->prepareReadQuery()
          ->where($mOrder->table . ".id_pipeline", $idPipeline)
          ->where($mOrder->table . ".is_closed", false)
        ;
        $itemDetailsUrl = $this->main->projectUrl . '/orders';
      break;
    }

    if ($fOwner > 0) {
      $items = $items->where('id_owner', $fOwner);
    }

    $items = $items->get()?->toArray();

    $this->viewParams["pipelines"] = $pipelines;
    $this->viewParams["pipeline"] = $pipeline;
    $this->viewParams["pipelineTypeReadable"] = [
      'deals' => 'Deals',
      'projects' => 'Projects',
      'tasks' => 'Tasks',
      'orders' => 'Orders',
    ][$pipelineType] ?? '';
    $this->viewParams["items"] = $items;
    $this->viewParams["itemDetailsUrl"] = $itemDetailsUrl;

    $this->setView('@HubletoApp:Community:Pipeline/Pipeline.twig');
  }

}
