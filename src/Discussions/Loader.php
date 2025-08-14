<?php

namespace HubletoApp\Community\Discussions;

class Loader extends \HubletoMain\App
{
  public array $externalModels = [];

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
      '/^discussions(\/(?<recordId>\d+))?\/?$/' => Controllers\Discussions::class,
      '/^discussions\/members(\/(?<recordId>\d+))?\/?$/' => Controllers\Members::class,
      '/^discussions\/messages(\/(?<recordId>\d+))?\/?$/' => Controllers\Messages::class,

      '/^discussions\/api\/send-message\/?$/' => Controllers\Api\SendMessage::class,
    ]);

  }

  // installTables
  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->main->load(Models\Discussion::class)->dropTableIfExists()->install();
      $this->main->load(Models\Member::class)->dropTableIfExists()->install();
      $this->main->load(Models\Message::class)->dropTableIfExists()->install();
    }
    if ($round == 2) {
      // do something in the 2nd round, if required
    }
    if ($round == 3) {
      // do something in the 3rd round, if required
    }
  }

  public function registerExternalModel(\Hubleto\Framework\Interfaces\AppInterface $app, string $modelClass)
  {
    $this->externalModels[$modelClass] = $app;
  }

  public function getRegisteredExternalModels(): array
  {
    return $this->externalModels;
  }

}
