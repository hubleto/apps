<?php

namespace HubletoApp\Community\Campaigns;

class Loader extends \HubletoMain\App
{
  public bool $hasCustomSettings = true;

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^campaigns\/?$/' => Controllers\Campaigns::class,
    ]);

    $externalModels = $this->main->di->create(\HubletoApp\Community\Tasks\ExternalModels::class);
    $externalModels->registerExternalModel($this, Models\Campaign::class);

  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->main->di->create(Models\Campaign::class)->dropTableIfExists()->install();
    }
  }

}
