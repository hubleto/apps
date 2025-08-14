<?php

namespace HubletoApp\Community\Help;

class Loader extends \HubletoMain\App
{
  public bool $canBeDisabled = false;
  public bool $permittedForAllUsers = true;

  public array $contextHelp = [];

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
      '/^help\/?$/' => Controllers\Help::class,
    ]);

    $this->contextHelp = $this->collectIntegrationItems('ContextHelp');
  }

}
