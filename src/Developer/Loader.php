<?php

namespace HubletoApp\Community\Developer;

class Loader extends \HubletoMain\App
{
  // init
  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^developer\/?$/' => Controllers\Dashboard::class,
      '/^developer\/db-updates\/?$/' => Controllers\DbUpdates::class,
      '/^developer\/form-designer\/?$/' => Controllers\FormDesigner::class,
    ]);

    $tools = $this->main->load(\HubletoApp\Community\Tools\Manager::class);
    $tools->addTool($this, [
      'title' => $this->translate('Developer tools'),
      'icon' => 'fas fa-screwdriver-wrench',
      'url' => 'developer',
    ]);

  }

}
