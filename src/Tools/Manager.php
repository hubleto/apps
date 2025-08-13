<?php

namespace HubletoApp\Community\Tools;

class Manager extends \HubletoMain\CoreClass
{

  /** @var array<int, array<\Hubleto\Framework\App, array>> */
  private array $tools = [];

  public function addTool(\Hubleto\Framework\Interfaces\AppInterface $app, array $tool): void
  {
    $this->tools[] = [$app, $tool];
  }

  public function getTools(): array
  {
    $tools = [];
    foreach ($this->tools as $tool) {
      $tools[] = $tool[1];
    }

    $titles = array_column($tools, 'title');
    array_multisort($titles, SORT_ASC, $tools);
    return $tools;
  }

}