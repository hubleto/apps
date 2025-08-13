<?php

namespace HubletoApp\Community\Customers;

class Loader extends \HubletoMain\App
{
  public bool $hasCustomSettings = true;

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^customers(\/(?<recordId>\d+))?\/?$/' => Controllers\Customers::class,
      '/^customers\/add\/?$/' => ['controller' => Controllers\Customers::class, 'vars' => ['recordId' => -1]],
      '/^customers\/settings\/?$/' => Controllers\Settings::class,
      '/^customers\/activities\/?$/' => Controllers\Activity::class,
      '/^settings\/customer-tags\/?$/' => Controllers\Tags::class,

      '/^customers\/api\/get-customer\/?$/' => Controllers\Api\GetCustomer::class,
      // '/^customers\/api\/get-calendar-events\/?$/' => Controllers\Api\GetCalendarEvents::class,
      '/^customers\/api\/log-activity\/?$/' => Controllers\Api\LogActivity::class,
    ]);

    $calendarManager = $this->main->load(\HubletoApp\Community\Calendar\Manager::class);
    $calendarManager->addCalendar($this, 'customers', $this->configAsString('calendarColor'), Calendar::class);

    $help = $this->main->load(\HubletoApp\Community\Help\Manager::class);
    $help->addContextHelpUrls('/^customers\/?$/', [
      'en' => 'en/apps/community/customers',
    ]);

    $this->main->apps->community('Settings')?->addSetting($this, [
      'title' => $this->translate('Customer Tags'),
      'icon' => 'fas fa-tags',
      'url' => 'settings/customer-tags',
    ]);
  }

  public function installTables(int $round): void
  {

    if ($round == 1) {
      $mCustomer = $this->main->load(\HubletoApp\Community\Customers\Models\Customer::class);
      $mCustomerDocument = $this->main->load(\HubletoApp\Community\Customers\Models\CustomerDocument::class);
      $mCustomerTag = $this->main->load(\HubletoApp\Community\Customers\Models\Tag::class);
      $mCrossCustomerTag = $this->main->load(\HubletoApp\Community\Customers\Models\CustomerTag::class);

      $mCustomer->dropTableIfExists()->install();
      $mCustomerTag->dropTableIfExists()->install();
      $mCrossCustomerTag->dropTableIfExists()->install();
      $mCustomerDocument->dropTableIfExists()->install();

      $mCustomerTag->record->recordCreate([ 'name' => "VIP", 'color' => '#D33115' ]);
      $mCustomerTag->record->recordCreate([ 'name' => "Partner", 'color' => '#4caf50' ]);
      $mCustomerTag->record->recordCreate([ 'name' => "Public", 'color' => '#2196f3' ]);
    }

    if ($round == 2) {
      $mCustomerActivity = $this->main->load(\HubletoApp\Community\Customers\Models\CustomerActivity::class);
      $mCustomerActivity->dropTableIfExists()->install();
    }
  }

}
