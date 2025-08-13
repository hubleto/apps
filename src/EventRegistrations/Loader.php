<?php

namespace HubletoApp\Community\EventRegistrations;

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
      '/^eventregistrations\/?$/' => Controllers\Dashboard::class,
      '/^eventregistrations\/contacts\/?$/' => Controllers\Contacts::class,
      '/^settings\/eventregistrations\/?$/' => Controllers\Settings::class,
    ]);

    $this->main->apps->community('Settings')->addSetting($this, [
      'title' => 'EventRegistrations', // or $this->translate('EventRegistrations')
      'icon' => 'fas fa-table',
      'url' => 'settings/eventregistrations',
    ]);

    $calendarManager = $this->main->load(\HubletoApp\Community\Calendar\Manager::class);
    $calendarManager->addCalendar(
      $this,
      'EventRegistrations-calendar', // UID of your app's calendar. Will be referenced as "source" when fetching app's events.
      '#008000', // your app's calendar color
      Calendar::class // your app's Calendar class
    );
  }

  // installTables
  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->main->load(Models\Contact::class)->dropTableIfExists()->install();
    }
    if ($round == 2) {
      // do something in the 2nd round, if required
    }
    if ($round == 3) {
      // do something in the 3rd round, if required
    }
  }

  // generateDemoData
  public function generateDemoData(): void
  {
    // Create any demo data to promote your app.
  }

}
