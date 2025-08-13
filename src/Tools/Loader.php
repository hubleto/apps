<?php

namespace HubletoApp\Community\Tools;

class Loader extends \HubletoMain\App
{
  public bool $canBeDisabled = false;

  public function init(): void
  {
    parent::init();
    $this->main->router->httpGet([
      '/^tools\/?$/' => Controllers\Dashboard::class,
    ]);
  }

}
