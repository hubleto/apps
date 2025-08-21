<?php

namespace HubletoApp\Community\Projects;

class Loader extends \HubletoMain\App
{

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
      // '/^projects\/api\/save-junction\/?$/' => Controllers\Api\SaveJunction::class,

      '/^projects\/api\/convert-deal-to-project\/?$/' => Controllers\Api\ConvertDealToProject::class,
      '/^projects\/api\/create-from-order\/?$/' => Controllers\Api\CreateFromOrder::class,

      '/^projects(\/(?<recordId>\d+))?\/?$/' => Controllers\Projects::class,
      '/^projects\/phases\/?$/' => Controllers\Phases::class,
    ]);

    $this->addSearchSwitch('p');

    $this->main->apps->community('Settings')->addSetting($this, [
      'title' => 'Projects', // or $this->translate('Projects')
      'icon' => 'fas fa-table',
      'url' => 'settings/projects',
    ]);

    $calendarManager = $this->main->load(\HubletoApp\Community\Calendar\Manager::class);
    $calendarManager->addCalendar(
      $this,
      'Projects-calendar', // UID of your app's calendar. Will be referenced as "source" when fetching app's events.
      '#008000', // your app's calendar color
      Calendar::class // your app's Calendar class
    );

    $appMenu = $this->main->load(\HubletoApp\Community\Desktop\AppMenuManager::class);
    $appMenu->addItem($this, 'projects', $this->translate('Projects'), 'fas fa-diagram-project');
    $appMenu->addItem($this, 'projects/phases', $this->translate('Phases'), 'fas fa-list');

    $externalModels = $this->main->load(\HubletoApp\Community\Tasks\ExternalModels::class);
    $externalModels->registerExternalModel($this, Models\Project::class);

  }

  // installTables
  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->main->load(Models\Phase::class)->dropTableIfExists()->install();
      $this->main->load(Models\Project::class)->dropTableIfExists()->install();
      $this->main->load(Models\ProjectDeal::class)->dropTableIfExists()->install();
      $this->main->load(Models\ProjectOrder::class)->dropTableIfExists()->install();
    }
    if ($round == 2) {

    }
    if ($round == 3) {
      $mPhase = $this->main->load(Models\Phase::class);
      $mPhase->record->recordCreate(['name' => 'Early preparation', 'order' => 1, 'color' => '#344556']);
      $mPhase->record->recordCreate(['name' => 'Advanced preparation', 'order' => 2, 'color' => '#6830a5']);
      $mPhase->record->recordCreate(['name' => 'Final preparation', 'order' => 3, 'color' => '#3068a5']);
      $mPhase->record->recordCreate(['name' => 'Early implementation', 'order' => 4, 'color' => '#ae459f']);
      $mPhase->record->recordCreate(['name' => 'Advanced implementation', 'order' => 5, 'color' => '#a38f9a']);
      $mPhase->record->recordCreate(['name' => 'Final implementation', 'order' => 6, 'color' => '#44879a']);
      $mPhase->record->recordCreate(['name' => 'Delivery', 'order' => 7, 'color' => '#74809a']);
    }
  }

  // generateDemoData
  public function generateDemoData(): void
  {
    $mProject = $this->main->load(Models\Project::class);

    $mProject->record->recordCreate([
      'id_deal' => 1,
      'title' => 'Sample project #1',
      'description' => 'Sample project #1 for demonstration purposes.',
      'id_main_developer' => 1,
      'id_account_manager' => 1,
      'id_phase' => 3,
      'color' => '#008000',
    ]);

    $mProject->record->recordCreate([
      'id_deal' => 1,
      'title' => 'Sample project #2',
      'description' => 'Sample project #2 for demonstration purposes.',
      'id_main_developer' => 1,
      'id_account_manager' => 1,
      'id_phase' => 1,
      'color' => '#008000',
    ]);
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
    $mProject = $this->main->load(Models\Project::class);
    $qProjects = $mProject->record->prepareReadQuery();
    
    foreach ($expressions as $e) {
      $qProjects = $qProjects->where(function($q) use ($e) {
        $q->orWhere('projects.identifier', 'like', '%' . $e . '%');
        $q->orWhere('projects.title', 'like', '%' . $e . '%');
      });
    }

    $projects = $qProjects->get()->toArray();

    $results = [];

    foreach ($projects as $project) {
      $results[] = [
        "id" => $project['id'],
        "label" => $project['identifier'] . ' ' . $project['title'],
        "url" => 'projects/' . $project['id'],
      ];
    }

    return $results;
  }

}
