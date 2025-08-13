<?php

namespace HubletoApp\Community\Tasks;

class Loader extends \HubletoMain\App
{
  // init
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
      $this->main->di->create(Models\Task::class)->dropTableIfExists()->install();
    }
    if ($round == 2) {
      // do something in the 2nd round, if required
    }
    if ($round == 3) {
      // do something in the 3rd round, if required
    }
  }

}
