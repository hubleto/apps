<?php

namespace HubletoApp\Community\Tasks;

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
      '/^tasks(\/(?<recordId>\d+))?\/?$/' => Controllers\Tasks::class,
    ]);

  }

  // installTables
  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->main->load(Models\Task::class)->dropTableIfExists()->install();
    }
  }

}
