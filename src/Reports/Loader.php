<?php

namespace HubletoApp\Community\Reports;

class Loader extends \HubletoMain\App
{
  public ReportManager $reportManager;

  public function __construct(\HubletoMain\Loader $main)
  {
    parent::__construct($main);
    $this->reportManager = $main->di->create(ReportManager::class);
  }

  /**
   * Inits the app: adds routes, settings, calendars, hooks, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^reports\/?$/' => Controllers\Reports::class,
      '/^reports\/(?<reportUrlSlug>.*?)\/?$/' => Controllers\Report::class,
    ]);

  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->main->load(Models\Report::class)->dropTableIfExists()->install();
    }
  }

  public function generateDemoData(): void
  {
    $mReport = $this->main->load(Models\Report::class);

    $mReport->record->recordCreate([
      'title' => 'Test report for Customers',
      'model' => \HubletoApp\Community\Customers\Models\Customer::class,
      'query' => '{}',
    ]);
  }

}
