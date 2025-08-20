<?php

namespace HubletoApp\Community\Orders;

use HubletoApp\Community\Documents\Models\Template;

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
      '/^orders\/api\/create-from-deal\/?$/' => Controllers\Api\CreateFromDeal::class,
      '/^orders\/?$/' => Controllers\Orders::class,
      '/^orders\/(?<recordId>\d+)\/?$/' => Controllers\Orders::class,
      '/^orders\/api\/generate-pdf\/?$/' => Controllers\Api\GeneratePdf::class,
      '/^orders\/api\/generate-invoice\/?$/' => Controllers\Api\GenerateInvoice::class,
      '/^settings\/order-states\/?$/' => Controllers\States::class,
    ]);

    $this->main->apps->community('Settings')->addSetting($this, [
      'title' => $this->translate('Order states'),
      'icon' => 'fas fa-file-lines',
      'url' => 'settings/order-states',
    ]);
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->main->load(Models\State::class)->dropTableIfExists()->install();
      $this->main->load(Models\Order::class)->dropTableIfExists()->install();
      $this->main->load(Models\OrderProduct::class)->dropTableIfExists()->install();
      $this->main->load(Models\OrderDeal::class)->dropTableIfExists()->install();
      $this->main->load(Models\OrderInvoice::class)->dropTableIfExists()->install();
      $this->main->load(Models\OrderDocument::class)->dropTableIfExists()->install();
      $this->main->load(Models\History::class)->dropTableIfExists()->install();
    }

    if ($round == 2) {
      $mState = $this->main->load(Models\State::class);
      $mState->record->recordCreate(['title' => 'New', 'code' => 'N', 'color' => '#444444']);
      $mState->record->recordCreate(['title' => 'Sent to customer', 'code' => 'S', 'color' => '#444444']);
      $mState->record->recordCreate(['title' => 'Requires modification', 'code' => 'M', 'color' => '#444444']);
      $mState->record->recordCreate(['title' => 'Accepted', 'code' => 'A', 'color' => '#444444']);
      $mState->record->recordCreate(['title' => 'Modified', 'code' => 'M', 'color' => '#444444']);
      $mState->record->recordCreate(['title' => 'Order created', 'code' => 'O', 'color' => '#444444']);
      $mState->record->recordCreate(['title' => 'Rejected', 'code' => 'R', 'color' => '#444444']);
    }
  }

  public function generateDemoData(): void
  {
    $mUser = $this->main->load(\HubletoApp\Community\Settings\Models\User::class);
    $userCount = $mUser->record->count();

    $mCustomer = $this->main->load(\HubletoApp\Community\Customers\Models\Customer::class);
    $customerCount = $mCustomer->record->count();

    $mState = $this->main->load(Models\State::class);
    $stateCount = $mState->record->count();

    $mOrder = $this->main->load(Models\Order::class);
    $mHistory = $this->main->load(Models\History::class);
    $mOrderProduct = $this->main->load(Models\OrderProduct::class);
    $mTemplate = $this->main->load(Template::class);

    $idTemplate = $mTemplate->record->recordCreate([
      'name' => 'Demo template for order PDF',
      'content' => '
        <div>1 {{ identifier }}</div>
        <div>2 {{ title }}</div>
        <div>3 {{ CUSTOMER.first_name }}</div>
      '
    ])['id'];

    for ($i = 1; $i <= 9; $i++) {

      $idOrder = $mOrder->record->recordCreate([
        'id_customer' => rand(1, $customerCount),
        'id_state' => rand(1, $stateCount),
        'order_number' => 'O' . date('Y') . '-00' . $i,
        'title' => 'This is a test bid #' . $i,
        'price' => rand(1000, 2000) / rand(3, 5),
        'id_currency' => 1,
        'date_order' => date('Y-m-d', strtotime('-' . rand(0, 10) . ' days')),
        'id_template' => $idTemplate,
      ])['id'];

      $mHistory->record->recordCreate([ 'id_order' => $idOrder, 'short_description' => 'Order created', 'date_time' => date('Y-m-d H:i:s') ]);

      for ($j = 1; $j <= 5; $j++) {
        $mOrderProduct->record->recordCreate([
          'id_order' => $idOrder,
          'id_product' => rand(1, 5),
          'title' => 'Item #' . $i . '.' . $j,
          'amount' => rand(100, 200) / rand(3, 7),
          'sales_price' => rand(50, 80) / rand(2, 5),
        ]);
      }
    }

  }

}
