<?php

namespace HubletoApp\Community\Crypto;

class Loader extends \HubletoMain\App
{

  public function init(): void
  {
    parent::init();

    $this->setConfigAsInteger('sidebarOrder', 0);

    $this->main->router->httpGet([
      '/^crypto\/?$/' => Controllers\Dashboard::class,
    ]);

    $this->main->apps->community('Settings')?->addSetting($this, [
      'title' => $this->translate('Crypto'),
      'icon' => 'fas fa-key',
      'url' => 'crypto',
    ]);
  }

}
