<?php

namespace HubletoApp\Community\Campaigns;

class Loader extends \HubletoMain\App
{
  public bool $hasCustomSettings = true;

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
      '/^campaigns\/?$/' => Controllers\Campaigns::class,
    ]);

    $externalModels = $this->main->load(\HubletoApp\Community\Tasks\ExternalModels::class);
    $externalModels->registerExternalModel($this, Models\Campaign::class);

  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->main->load(Models\Campaign::class)->dropTableIfExists()->install();
    }
  }

}
