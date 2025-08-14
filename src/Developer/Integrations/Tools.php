<?php

namespace HubletoApp\Community\Developer\Integrations;

class Tools extends \HubletoMain\Integration
{
  public function getItems(): array
  {
    return [
      [
        'title' => $this->app->translate('Developer tools'),
        'icon' => 'fas fa-screwdriver-wrench',
        'url' => 'developer',
      ]
    ];
  }

}