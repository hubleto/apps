<?php

namespace HubletoApp\Community\Deals;

class Loader extends \HubletoMain\App
{
  public bool $hasCustomSettings = true;

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
      '/^deals\/api\/log-activity\/?$/' => Controllers\Api\LogActivity::class,
      '/^deals\/api\/create-from-lead\/?$/' => Controllers\Api\CreateFromLead::class,
      '/^deals\/api\/generate-quotation-pdf\/?$/' => Controllers\Api\GenerateQuotationPdf::class,
      '/^deals\/api\/generate-invoice\/?$/' => Controllers\Api\GenerateInvoice::class,

      '/^deals(\/(?<recordId>\d+))?\/?$/' => Controllers\Deals::class,
      '/^deals\/add\/?$/' => ['controller' => Controllers\Deals::class, 'vars' => ['recordId' => -1]],
      '/^deals\/settings\/?$/' => Controllers\Settings::class,
      '/^deals\/archive\/?$/' => Controllers\DealsArchive::class,
      '/^deals\/change-pipeline\/?$/' => Controllers\Api\ChangePipeline::class,
      '/^settings\/deal-tags\/?$/' => Controllers\Tags::class,
      '/^settings\/deal-lost-reasons\/?$/' => Controllers\LostReasons::class,
      '/^deals\/boards\/deal-warnings\/?$/' => Controllers\Boards\DealWarnings::class,
      '/^deals\/boards\/most-valuable-deals\/?$/' => Controllers\Boards\MostValuableDeals::class,
      '/^deals\/boards\/deal-value-by-result\/?$/' => Controllers\Boards\DealValueByResult::class,
    ]);
    
    $this->addSearchSwitch('dl');
    $this->addSearchSwitch('deal');

    $this->main->apps->community('Settings')?->addSetting($this, [
      'title' => $this->translate('Deal Tags'),
      'icon' => 'fas fa-tags',
      'url' => 'settings/deal-tags',
    ]);
    $this->main->apps->community('Settings')?->addSetting($this, [
      'title' => $this->translate('Deal Lost Reasons'),
      'icon' => 'fas fa-tags',
      'url' => 'settings/deal-lost-reasons',
    ]);

    $calendarManager = $this->main->load(\HubletoApp\Community\Calendar\Manager::class);
    $calendarManager->addCalendar($this, 'deals', $this->configAsString('calendarColor'), Calendar::class);

    $this->main->apps->community('Reports')?->reportManager?->addReport($this, Reports\MonthlyRevenue::class);

    $externalModels = $this->main->load(\HubletoApp\Community\Tasks\ExternalModels::class);
    $externalModels->registerExternalModel($this, Models\Deal::class);

    $boards = $this->main->load(\HubletoApp\Community\Dashboards\Manager::class);
    $boards->addBoard( $this, $this->translate('Deal warnings'), 'deals/boards/deal-warnings');
    $boards->addBoard( $this, $this->translate('Most valuable deals'), 'deals/boards/most-valuable-deals');
    $boards->addBoard( $this, $this->translate('Deal value by result'), 'deals/boards/deal-value-by-result');
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mDeal = $this->main->load(\HubletoApp\Community\Deals\Models\Deal::class);
      $mDealHistory = $this->main->load(\HubletoApp\Community\Deals\Models\DealHistory::class);
      $mDealTag = $this->main->load(\HubletoApp\Community\Deals\Models\Tag::class);
      $mCrossDealTag = $this->main->load(\HubletoApp\Community\Deals\Models\DealTag::class);
      $mDealOrder = $this->main->load(\HubletoApp\Community\Deals\Models\DealOrder::class);
      $mDealProduct = $this->main->load(\HubletoApp\Community\Deals\Models\DealProduct::class);
      $mDealActivity = $this->main->load(\HubletoApp\Community\Deals\Models\DealActivity::class);
      $mDealDocument = $this->main->load(\HubletoApp\Community\Deals\Models\DealDocument::class);
      $mLostReasons = $this->main->load(\HubletoApp\Community\Deals\Models\LostReason::class);

      $mLostReasons->dropTableIfExists()->install();
      $mDeal->dropTableIfExists()->install();
      $mDealHistory->dropTableIfExists()->install();
      $mDealTag->dropTableIfExists()->install();
      $mDealOrder->dropTableIfExists()->install();
      $mCrossDealTag->dropTableIfExists()->install();
      $mDealProduct->dropTableIfExists()->install();
      $mDealActivity->dropTableIfExists()->install();
      $mDealDocument->dropTableIfExists()->install();

      $mDealTag->record->recordCreate([ 'name' => "Important", 'color' => '#fc2c03' ]);
      $mDealTag->record->recordCreate([ 'name' => "ASAP", 'color' => '#62fc03' ]);
      $mDealTag->record->recordCreate([ 'name' => "Extenstion", 'color' => '#033dfc' ]);
      $mDealTag->record->recordCreate([ 'name' => "New Customer", 'color' => '#fcdb03' ]);
      $mDealTag->record->recordCreate([ 'name' => "Existing Customer", 'color' => '#5203fc' ]);

      $mLostReasons->record->recordCreate(["reason" => "Price"]);
      $mLostReasons->record->recordCreate(["reason" => "Solution"]);
      $mLostReasons->record->recordCreate(["reason" => "Demand canceled by customer"]);
      $mLostReasons->record->recordCreate(["reason" => "Other"]);
    }
  }

  /**
   * Implements fulltext search functionality for tasks
   *
   * @param array $expressions List of expressions to be searched and glued with logical 'or'.
   * 
   * @return array
   * 
   */
  public function search(array $expressions): array
  {
    $mDeal = $this->main->load(Models\Deal::class);
    $qDeals = $mDeal->record->prepareReadQuery();
    
    foreach ($expressions as $e) {
      $qDeals = $qDeals->where(function($q) use ($e) {
        $q->orWhere('deals.identifier', 'like', '%' . $e . '%');
        $q->orWhere('deals.title', 'like', '%' . $e . '%');
      });
    }

    $deals = $qDeals->get()->toArray();

    $results = [];

    foreach ($deals as $deal) {
      $results[] = [
        "id" => $deal['id'],
        "label" => $deal['identifier'] . ' ' . $deal['title'],
        "url" => 'deals/' . $deal['id'],
        // "description" => $task[''],
      ];
    }

    return $results;
  }

}
