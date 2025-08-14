<?php

namespace HubletoApp\Community\Deals\Integrations;

class AppMenu extends \HubletoMain\Integration
{
  public function getItems(): array
  {
    return [
      [
        'app' => $this->app,
        'url' => 'deals',
        'title' => $this->app->translate('Active deals'),
        'icon' => 'fas fa-handshake',
      ],
      [
        'app' => $this->app,
        'url' => 'deals/archive',
        'title' => $this->app->translate('Archived deals'),
        'icon' => 'fas fa-box-archive',
      ],
    ];
  }

}