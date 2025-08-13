<?php

namespace HubletoApp\Community\EventRegistrations;

class Loader extends \HubletoMain\App
{
  // Uncomment following if you want a button for app's settings
  // to be rendered next in sidebar, right next to your app's button.
  // public bool $hasCustomSettings = true;

  // init
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

    $calendarManager = $this->main->apps->community('Calendar')->calendarManager;
    $calendarManager->addCalendar(
      'EventRegistrations-calendar', // UID of your app's calendar. Will be referenced as "source" when fetching app's events.
      '#008000', // your app's calendar color
      Calendar::class // your app's Calendar class
    );
  }

  // installTables
  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->main->di->create(Models\Contact::class)->dropTableIfExists()->install();
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
