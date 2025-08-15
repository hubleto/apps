<?php

namespace HubletoApp\Community\Tasks;

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
      '/^tasks(\/(?<recordId>\d+))?\/?$/' => Controllers\Tasks::class,
    ]);

  }

  // installTables
  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->main->load(Models\Task::class)->dropTableIfExists()->install();
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
    $mTask = $this->main->load(Models\Task::class);
    $qTasks = $mTask->record->prepareReadQuery();
    
    foreach ($expressions as $e) {
      $qTasks = $qTasks
        ->orWhere('identifier', 'like', '%' . $e . '%')
        ->orWhere('title', 'like', '%' . $e . '%')
      ;
    }

    $tasks = $qTasks->get()->toArray();

    $results = [];

    foreach ($tasks as $task) {
      $results[] = [
        "id" => $task['id'],
        "label" => $task['identifier'] . ' ' . $task['title'],
        "url" => 'tasks/' . $task['id'],
        // "description" => $task[''],
      ];
    }

    return $results;
  }

}
