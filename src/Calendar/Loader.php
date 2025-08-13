<?php

namespace HubletoApp\Community\Calendar;

use HubletoApp\Community\Calendar\Models\Activity;
use HubletoApp\Community\Calendar\Models\SharedCalendar;

class Loader extends \HubletoMain\App
{
  public CalendarManager $calendarManager;

  public bool $hasCustomSettings = true;

  public function __construct(\HubletoMain\Loader $main)
  {
    parent::__construct($main);
    $this->calendarManager = $main->di->create(CalendarManager::class);
  }

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^calendar\/?$/' => Controllers\Calendar::class,
      '/^calendar(\/(?<key>\w+))?\/ics\/?$/' => Controllers\IcsCalendar::class,
      '/^calendar\/settings\/?$/' => Controllers\Settings::class,
      '/^calendar\/boards\/reminders\/?$/' => Controllers\Boards\Reminders::class,
      '/^calendar\/api\/get-calendar-events\/?$/' => Controllers\Api\GetCalendarEvents::class,
      '/^calendar\/api\/get-shared-calendars\/?$/' => Controllers\Api\GetSharedCalendars::class,
      '/^calendar\/api\/stop-sharing-calendar\/?$/' => Controllers\Api\StopSharingCalendar::class,
    ]);

    $help = $this->main->di->create(\HubletoApp\Community\Help\Manager::class);
    $help->addContextHelpUrls('/^calendar\/?$/', [
      'en' => 'en/apps/community/calendar',
    ]);

    $boards = $this->main->di->create(\HubletoApp\Community\Dashboards\Manager::class);
    $boards->addBoard(
      $this,
      $this->translate('Reminders'),
      'calendar/boards/reminders'
    );

    $this->main->apps->community('Calendar')?->calendarManager?->addCalendar(
      'calendar',
      'blue',
      Calendar::class
    );

  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mActivity = $this->main->di->create(Activity::class);
      $mActivity->install();
      $mSharedCalendar = $this->main->di->create(SharedCalendar::class);
      $mSharedCalendar->install();
    }
  }

}
