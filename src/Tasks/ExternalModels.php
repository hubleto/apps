<?php

namespace HubletoApp\Community\Tasks;

class ExternalModels extends \HubletoMain\CoreClass
{

  public array $externalModels = [];

  public function registerExternalModel(\Hubleto\Framework\Interfaces\AppInterface $app, string $modelClass)
  {
    $this->externalModels[$modelClass] = $app;
  }

  public function getRegisteredExternalModels(): array
  {
    return $this->externalModels;
  }

}