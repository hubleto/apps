<?php

namespace HubletoApp\Community\Customers;

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
      '/^customers(\/(?<recordId>\d+))?\/?$/' => Controllers\Customers::class,
      '/^customers\/add\/?$/' => ['controller' => Controllers\Customers::class, 'vars' => ['recordId' => -1]],
      '/^customers\/settings\/?$/' => Controllers\Settings::class,
      '/^customers\/activities\/?$/' => Controllers\Activity::class,
      '/^settings\/customer-tags\/?$/' => Controllers\Tags::class,

      '/^customers\/api\/get-customer\/?$/' => Controllers\Api\GetCustomer::class,
      // '/^customers\/api\/get-calendar-events\/?$/' => Controllers\Api\GetCalendarEvents::class,
      '/^customers\/api\/log-activity\/?$/' => Controllers\Api\LogActivity::class,
    ]);

    $this->addSearchSwitch('c', 'customers');

    $calendarManager = $this->main->load(\HubletoApp\Community\Calendar\Manager::class);
    $calendarManager->addCalendar($this, 'customers', $this->configAsString('calendarColor'), Calendar::class);

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

  /**
   * Implements fulltext search functionality for customers
   *
   * @param array $expressions List of expressions to be searched and glued with logical 'or'.
   * 
   * @return array
   * 
   */
  public function search(array $expressions): array
  {
    $mCustomer = $this->main->load(Models\Customer::class);
    $qCustomers = $mCustomer->record->prepareReadQuery();
    
    foreach ($expressions as $e) {
      $qCustomers = $qCustomers->where(function($q) use ($e) {
        $q->orWhere('customers.name', 'like', '%' . $e . '%');
        $q->orWhere('customers.city', 'like', '%' . $e . '%');
        $q->orWhere('customers.vat_id', 'like', '%' . $e . '%');
        $q->orWhere('customers.tax_id', 'like', '%' . $e . '%');
        $q->orWhere('customers.customer_id', 'like', '%' . $e . '%');
      });
    }

    $customers = $qCustomers->get()->toArray();

    $results = [];

    foreach ($customers as $customer) {
      $results[] = [
        "id" => $customer['id'],
        "label" => $customer['name'],
        "url" => 'customers/' . $customer['id'],
        "description" => $customer['customer_id'] . ' ' . $customer['city'],
      ];
    }

    return $results;
  }

}
