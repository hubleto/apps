<?php

namespace HubletoApp\Community\Contacts\Integrations;

class AppMenu extends \HubletoMain\Integration
{
  public function getItems(): array
  {
    return [
      [
        'app' => $this->app,
        'url' => 'contacts',
        'title' => $this->app->translate('Contacts'),
        'icon' => 'fas fa-user',
      ],
      [
        'app' => $this->app,
        'url' => 'contacts/import',
        'title' => $this->app->translate('Import contacts'),
        'icon' => 'fas fa-file-import',
      ],
    ];
  }

}