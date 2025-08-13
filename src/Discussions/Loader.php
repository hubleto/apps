<?php

namespace HubletoApp\Community\Discussions;

class Loader extends \HubletoMain\App
{
  public array $externalModels = [];

  // init
  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^discussions(\/(?<recordId>\d+))?\/?$/' => Controllers\Discussions::class,
      '/^discussions\/members(\/(?<recordId>\d+))?\/?$/' => Controllers\Members::class,
      '/^discussions\/messages(\/(?<recordId>\d+))?\/?$/' => Controllers\Messages::class,

      '/^discussions\/api\/send-message\/?$/' => Controllers\Api\SendMessage::class,
    ]);

    $appMenu = $this->main->di->create(\HubletoApp\Community\Desktop\AppMenuManager::class);
    $appMenu->addItem($this, 'discussions', $this->translate('Discussions'), 'fas fa-user');
    $appMenu->addItem($this, 'discussions/members', $this->translate('Members'), 'fas fa-file-import');
    $appMenu->addItem($this, 'discussions/messages', $this->translate('Messages'), 'fas fa-file-import');
  }

  // installTables
  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->main->di->create(Models\Discussion::class)->dropTableIfExists()->install();
      $this->main->di->create(Models\Member::class)->dropTableIfExists()->install();
      $this->main->di->create(Models\Message::class)->dropTableIfExists()->install();
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
