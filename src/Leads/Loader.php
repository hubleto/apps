<?php

namespace HubletoApp\Community\Leads;

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
      '/^leads(\/(?<recordId>\d+))?\/?$/' => Controllers\Leads::class,
      '/^leads\/settings\/?$/' => Controllers\Settings::class,
      '/^leads\/archive\/?$/' => Controllers\LeadsArchive::class,
      '/^leads\/api\/move-to-archive\/?$/' => Controllers\Api\MoveToArchive::class,
      '/^leads\/api\/log-activity\/?$/' => Controllers\Api\LogActivity::class,
      '/^settings\/lead-tags\/?$/' => Controllers\Tags::class,
      '/^settings\/lead-levels\/?$/' => Controllers\Levels::class,
      '/^settings\/lead-lost-reasons\/?$/' => Controllers\LostReasons::class,
      '/^leads\/boards\/lead-value-by-score\/?$/' => Controllers\Boards\LeadValueByScore::class,
      '/^leads\/boards\/lead-warnings\/?$/' => Controllers\Boards\LeadWarnings::class,
      '/^leads\/save-bulk-status-change\/?$/' => Controllers\Api\SaveBulkStatusChange::class,
    ]);

    $this->addSearchSwitch('L');
    $this->addSearchSwitch('lead');

    $this->main->apps->community('Settings')->addSetting($this, [
      'title' => $this->translate('Lead Levels'),
      'icon' => 'fas fa-layer-group',
      'url' => 'settings/lead-levels',
    ]);
    $this->main->apps->community('Settings')->addSetting($this, [
      'title' => $this->translate('Lead Tags'),
      'icon' => 'fas fa-tags',
      'url' => 'settings/lead-levels',
    ]);
    $this->main->apps->community('Settings')->addSetting($this, [
      'title' => $this->translate('Lead Lost Reasons'),
      'icon' => 'fas fa-tags',
      'url' => 'settings/lead-lost-reasons',
    ]);

    $externalModels = $this->main->load(\HubletoApp\Community\Tasks\ExternalModels::class);
    $externalModels->registerExternalModel($this, Models\Lead::class);

    $calendarManager = $this->main->load(\HubletoApp\Community\Calendar\Manager::class);
    $calendarManager->addCalendar($this, 'leads', $this->configAsString('calendarColor'), Calendar::class);

    $boards = $this->main->load(\HubletoApp\Community\Dashboards\Manager::class);
    $boards->addBoard( $this, 'Lead value by score', 'leads/boards/lead-value-by-score');
    $boards->addBoard( $this, 'Lead warnings', 'leads/boards/lead-warnings');

    $appMenu = $this->main->load(\HubletoApp\Community\Desktop\AppMenuManager::class);
    $appMenu->addItem($this, 'leads', $this->translate('Active leads'), 'fas fa-people-arrows');
    $appMenu->addItem($this, 'leads/archive', $this->translate('Archived leads'), 'fas fa-box-archive');
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mLevel = $this->main->load(\HubletoApp\Community\Leads\Models\Level::class);
      $mLead = $this->main->load(\HubletoApp\Community\Leads\Models\Lead::class);
      $mLeadHistory = $this->main->load(\HubletoApp\Community\Leads\Models\LeadHistory::class);
      $mLeadTag = $this->main->load(\HubletoApp\Community\Leads\Models\Tag::class);
      $mCrossLeadTag = $this->main->load(\HubletoApp\Community\Leads\Models\LeadTag::class);
      $mLeadActivity = $this->main->load(\HubletoApp\Community\Leads\Models\LeadActivity::class);
      $mLeadDocument = $this->main->load(\HubletoApp\Community\Leads\Models\LeadDocument::class);
      $mLostReasons = $this->main->load(\HubletoApp\Community\Leads\Models\LostReason::class);

      $mLevel->dropTableIfExists()->install();
      $mLostReasons->dropTableIfExists()->install();
      $mLead->dropTableIfExists()->install();
      $mLeadHistory->dropTableIfExists()->install();
      $mLeadTag->dropTableIfExists()->install();
      $mCrossLeadTag->dropTableIfExists()->install();
      $mLeadActivity->dropTableIfExists()->install();
      $mLeadDocument->dropTableIfExists()->install();

      $mLeadTag->record->recordCreate([ 'name' => "Complex", 'color' => '#2196f3' ]);
      $mLeadTag->record->recordCreate([ 'name' => "Great opportunity", 'color' => '#4caf50' ]);
      $mLeadTag->record->recordCreate([ 'name' => "Duplicate", 'color' => '#9e9e9e' ]);
      $mLeadTag->record->recordCreate([ 'name' => "Needs attention", 'color' => '#795548' ]);

      $mLevel->record->recordCreate([ 'name' => "Cold", 'color' => '#2196f3' ]);
      $mLevel->record->recordCreate([ 'name' => "Warm", 'color' => '#4caf50' ]);
      $mLevel->record->recordCreate([ 'name' => "Hot", 'color' => '#9e9e9e' ]);
      $mLevel->record->recordCreate([ 'name' => "Marketing qualified", 'color' => '#795548' ]);
      $mLevel->record->recordCreate([ 'name' => "Sales qualified", 'color' => '#795548' ]);

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
    $mLead = $this->main->load(Models\Lead::class);
    $qLeads = $mLead->record->prepareReadQuery();
    
    foreach ($expressions as $e) {
      $qLeads = $qLeads->where(function($q) use ($e) {
        $q->orWhere('leads.identifier', 'like', '%' . $e . '%');
        $q->orWhere('leads.title', 'like', '%' . $e . '%');
      });
    }

    $leads = $qLeads->get()->toArray();

    $results = [];

    foreach ($leads as $lead) {
      $results[] = [
        "id" => $lead['id'],
        "label" => $lead['identifier'] . ' ' . $lead['title'],
        "url" => 'leads/' . $lead['id'],
        // "description" => $task[''],
      ];
    }

    return $results;
  }

}
