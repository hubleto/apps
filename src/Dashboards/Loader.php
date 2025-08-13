<?php

namespace HubletoApp\Community\Dashboards;

class Loader extends \HubletoMain\App
{

  public function __construct(\HubletoMain\Loader $main)
  {
    parent::__construct($main);
  }

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^dashboards(\/(?<dashboardSlug>[^\/]+))?\/?$/' => Controllers\Dashboards::class,
      '/^settings\/dashboards\/?$/' => Controllers\Settings::class,
    ]);

    $this->main->apps->community('Settings')?->addSetting($this, [
      'title' => $this->translate('Dashboards'),
      'icon' => 'fas fa-table',
      'url' => 'settings/dashboards',
    ]);

  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->main->load(Models\Dashboard::class)->dropTableIfExists()->install();
      $this->main->load(Models\Panel::class)->dropTableIfExists()->install();
    }
  }

  public function generateDemoData(): void
  {
    $mDashboard = $this->main->load(Models\Dashboard::class);
    $mPanel = $this->main->load(Models\Panel::class);

    $dashboard = $mDashboard->record->recordCreate([
      'id_owner' => 1,
      'title' => 'Default dashboard',
      'slug' => 'default',
      'is_default' => true,
    ]);

    $boards = $this->main->load(Manager::class);
    foreach ($boards->getBoards() as $board) {
      $mPanel->record->recordCreate([
        'id_dashboard' => $dashboard['id'],
        'title' => $board['title'],
        'board_url_slug' => $board['boardUrlSlug'],
        'configuration' => '',
      ]);
    }
  }

}
