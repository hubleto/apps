<?php

namespace HubletoApp\Community\Usage\Integrations;

class Tools extends \HubletoMain\Integration
{
  public function getItems(): array
  {
    return [
      [
        'title' => $this->app->translate('Usage log'),
        'icon' => 'fas fa-chart-bar',
        'url' => 'usage/log',
      ]
    ];
  }

}