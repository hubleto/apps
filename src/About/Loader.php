<?php

namespace HubletoApp\Community\About;

class Loader extends \HubletoMain\App
{
  public bool $canBeDisabled = false;
  public bool $permittedForAllUsers = true;

  /**
   * Inits the app: adds routes, settings, calendars, hooks, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();
    $this->main->router->httpGet([ '/^about\/?$/' => Controllers\About::class ]);
  }

}
