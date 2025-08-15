<?php

namespace HubletoApp\Community\Developer\Extendibles;

class Tools extends \HubletoMain\Extendible
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