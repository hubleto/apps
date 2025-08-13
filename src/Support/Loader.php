<?php

namespace HubletoApp\Community\Support;

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
      '/^support\/?$/' => Controllers\Dashboard::class,
    ]);

  }

}
