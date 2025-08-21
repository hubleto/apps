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
      '/^campaigns\/api\/save-contacts\/?$/' => Controllers\Api\SaveContacts::class,
      '/^campaigns(\/(?<recordId>\d+))?\/?$/' => Controllers\Campaigns::class,
    ]);

  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->main->load(Models\Campaign::class)->dropTableIfExists()->install();
      $this->main->load(Models\CampaignContact::class)->dropTableIfExists()->install();
      $this->main->load(Models\CampaignTask::class)->dropTableIfExists()->install();
    }
  }

}
