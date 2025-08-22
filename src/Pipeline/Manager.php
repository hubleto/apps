<?php

namespace HubletoApp\Community\Pipeline;

class Manager extends \HubletoMain\CoreClass
{

  /** @var array<string, Pipeline> */
  protected array $pipelineLoaders = [];

  public function addPipeline(\Hubleto\Framework\Interfaces\AppInterface $app, string $group, string $pipelineClass): void
  {
    $pipeline = $this->main->load($pipelineClass);
    if ($pipeline instanceof Pipeline) {
      $pipeline->app = $app;
      if (!isset($this->pipelineLoaders[$group])) $this->pipelineLoaders[$group] = [];
      $this->pipelineLoaders[$group] = $pipeline;
    }
  }

  /** @return Pipeline */
  public function getPipelineLoaderForGroup(string $group): Pipeline
  {
    return $this->pipelineLoaders[$group];
  }

  public function getPipeline(string $pipelineClass): Pipeline
  {
    return $this->pipelineLoaders[$pipelineClass];
  }

}
