<?php

namespace HubletoApp\Community\Desktop;

use \Hubleto\Framework\Interfaces\AppMenuManagerInterface;

class Loader extends \HubletoMain\App
{
  public const DEFAULT_INSTALLATION_CONFIG = [
    'sidebarOrder' => 0,
  ];

  public bool $canBeDisabled = false;
  public bool $permittedForAllUsers = true;

  public SidebarManager $sidebar;
  public DashboardManager $dashboard;

  public function __construct(\HubletoMain\Loader $main)
  {
    parent::__construct($main);
    $this->sidebar = $main->di->create(SidebarManager::class);
    $this->dashboard = $main->di->create(DashboardManager::class);
  }

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^$/' => Controllers\Home::class,
    ]);

    $this->setConfigAsInteger('sidebarOrder', 0);

    $help = $this->main->load(\HubletoApp\Community\Help\Manager::class);
    $help->addContextHelpUrls('/^\/?$/', [
      'en' => '',
    ]);
  }

}
