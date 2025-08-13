<?php

namespace HubletoApp\Community\Help;

class Loader extends \HubletoMain\App
{
  public bool $canBeDisabled = false;
  public bool $permittedForAllUsers = true;

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^help\/?$/' => Controllers\Help::class,
    ]);
  }


}
