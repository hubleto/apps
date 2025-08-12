<?php

namespace HubletoApp\Community\Desktop;

class AppMenuManager implements \Hubleto\Framework\Interfaces\AppMenuManagerInterface
{

  /** @var array<int, array<string, bool|string>> */
  public array $items = [];

  public function __construct(public \Hubleto\Framework\Loader $main)
  {
  }

  public function addItem(\Hubleto\Framework\App $app, string $url, string $title, string $icon): void
  {
    if ($app->isActivated) {
      $this->items[] = [
        'url' => $url,
        'title' => $title,
        'icon' => $icon,
      ];
    }
  }

  public function getItems(): array
  {
    return $this->items;
  }

}
