<?php declare(strict_types=1);

namespace HubletoApp\Community\Pipeline\Hooks;

use HubletoApp\Community\Pipeline\Models\PipelineHistory;

class SavePipelineHistory extends \HubletoMain\Hook
{

  public function run(string $event, array $args): void
  {
    if ($event == 'model:on-after-update') {
      $model = $args['model'];
      $savedRecord = $args['savedRecord'];

      if ($model->hasColumn('id_pipeline') && $model->hasColumn('id_pipeline_step')) {
        $mPipelineHistory = new PipelineHistory($this->main);

        $lastState = $mPipelineHistory->record
          ->where('model', get_class($model))
          ->where('record_id', $savedRecord['id'])
          ->first()
        ;
        var_dump($savedRecord['id_pipeline_step']);
        var_dump($lastState->id_pipeline_step);
        if (
          !$lastState
          || $lastState->id_pipeline != $savedRecord['id_pipeline']
          || $lastState->id_pipeline_step != $savedRecord['id_pipeline_step']
        ) {
          $mPipelineHistory->record->recordCreate([
            'model' => get_class($model),
            'record_id' => $savedRecord['id'],
            'datetime_change' => date('Y-m-d H:i:s'),
            'id_pipeline' => $savedRecord['id_pipeline'],
            'id_pipeline_step' => $savedRecord['id_pipeline_step'],
          ]);
        }
      }
    }
  }

}