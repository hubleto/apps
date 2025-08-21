<?php

namespace HubletoApp\Community\Pipeline;

use HubletoApp\Community\Deals\Models\Deal;

class Loader extends \HubletoMain\App
{

  /**
   * Inits the app: adds routes, settings, calendars, hooks, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^pipeline\/?$/' => Controllers\Home::class,
      '/^pipeline\/api\/get-pipelines\/?$/' => Controllers\Api\GetPipelines::class,
      '/^settings\/pipelines\/?$/' => Controllers\Pipelines::class,
    ]);

    $this->main->apps->community('Settings')->addSetting($this, [
      'title' => $this->translate('Pipelines'),
      'icon' => 'fas fa-bars-progress',
      'url' => 'settings/pipelines'
    ]);
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mPipeline = $this->main->load(Models\Pipeline::class);
      $mPipelineStep = $this->main->load(Models\PipelineStep::class);

      $mPipeline->dropTableIfExists()->install();
      $mPipelineStep->dropTableIfExists()->install();

      $idPipeline = $mPipeline->record->recordCreate([ "name" => "Deal stage", "type" => $mPipeline::TYPE_DEAL_MANAGEMENT ])['id'];
      $mPipelineStep->record->recordCreate([ 'name' => 'Prospecting', 'order' => 1, 'color' => '#838383', 'id_pipeline' => $idPipeline , "set_result" => Deal::RESULT_UNKNOWN, "probability" => 1]);
      $mPipelineStep->record->recordCreate([ 'name' => 'Qualified', 'order' => 2, 'color' => '#d8a082', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_UNKNOWN, "probability" => 10]);
      $mPipelineStep->record->recordCreate([ 'name' => 'Quote Sent', 'order' => 3, 'color' => '#d1cf79', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_UNKNOWN , "probability" => 30]);
      $mPipelineStep->record->recordCreate([ 'name' => 'Under Review', 'order' => 5, 'color' => '#82b3d8', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_UNKNOWN , "probability" => 70]);
      $mPipelineStep->record->recordCreate([ 'name' => 'Contracting', 'order' => 6, 'color' => '#82d88b', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_UNKNOWN , "probability" => 85]);
      $mPipelineStep->record->recordCreate([ 'name' => 'WON', 'order' => 7, 'color' => '#008000', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_WON , "probability" => 100]);
      $mPipelineStep->record->recordCreate([ 'name' => 'LOST', 'order' => 8, 'color' => '#f50c0c', 'id_pipeline' => $idPipeline, "set_result" => Deal::RESULT_LOST , "probability" => 0]);

      $idPipeline = $mPipeline->record->recordCreate([ "name" => "Project phase", "type" => $mPipeline::TYPE_PROJECT_MANAGEMENT ])['id'];
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Preparation', 'order' => 1, 'color' => '#344556']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Implementation', 'order' => 2, 'color' => '#d8a082']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Testing', 'order' => 3, 'color' => '#6830a5']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Delivered', 'order' => 4, 'color' => '#008000']);

      $idPipeline = $mPipeline->record->recordCreate([ "name" => "Task status", "type" => $mPipeline::TYPE_TASK_MANAGEMENT ])['id'];
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'In progress', 'order' => 1, 'color' => '#344556']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Ready to test', 'order' => 2, 'color' => '#6830a5']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Test passed', 'order' => 3, 'color' => '#3068a5']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Test failed', 'order' => 4, 'color' => '#ae459f']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Ready to deploy', 'order' => 5, 'color' => '#a38f9a']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Deployed', 'order' => 6, 'color' => '#44879a']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Accepted', 'order' => 7, 'color' => '#74809a']);

      $idPipeline = $mPipeline->record->recordCreate([ "name" => "Order stage", "type" => $mPipeline::TYPE_ORDER_MANAGEMENT ])['id'];
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'In progress', 'order' => 1, 'color' => '#344556']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Delivered', 'order' => 2, 'color' => '#6830a5']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Ready to invoice', 'order' => 3, 'color' => '#3068a5']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Invoiced', 'order' => 4, 'color' => '#ae459f']);
      $mPipelineStep->record->recordCreate(['id_pipeline' => $idPipeline, 'name' => 'Paid', 'order' => 5, 'color' => '#a38f9a']);
    }
  }

}
