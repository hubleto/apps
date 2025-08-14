<?php

namespace HubletoApp\Community\Tools;

class Loader extends \HubletoMain\App
{
  public bool $canBeDisabled = false;

  public array $tools = [];

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
      '/^tools\/?$/' => Controllers\Dashboard::class,
    ]);

    $this->tools = $this->collectIntegrationItems('Tools');
  }

}
