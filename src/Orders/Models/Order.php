<?php

namespace HubletoApp\Community\Orders\Models;

use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use HubletoApp\Community\Customers\Models\Customer;
use HubletoApp\Community\Products\Models\Product;
use HubletoApp\Community\Settings\Models\Currency;
use HubletoApp\Community\Settings\Models\Setting;

use HubletoApp\Community\Pipeline\Models\Pipeline;
use HubletoApp\Community\Pipeline\Models\PipelineStep;
use HubletoApp\Community\Invoices\Models\Invoice;
use HubletoApp\Community\Invoices\Models\Dto\Invoice as InvoiceDto;

class Order extends \Hubleto\Framework\Models\Model
{
  public string $table = 'orders';
  public string $recordManagerClass = RecordManagers\Order::class;
  public ?string $lookupSqlValue = '{%TABLE%}.order_number';

  public array $relations = [
    'PRODUCTS' => [ self::HAS_MANY, OrderProduct::class, 'id_order', 'id' ],
    'INVOICES' => [ self::HAS_MANY, OrderInvoice::class, 'id_order', 'id' ],
    'HISTORY' => [ self::HAS_MANY, History::class, 'id_order', 'id' ],
    'CUSTOMER' => [ self::HAS_ONE, Customer::class, 'id','id_customer'],
    'CURRENCY' => [ self::HAS_ONE, Currency::class, 'id', 'id_currency'],
    'PIPELINE' => [ self::HAS_ONE, Pipeline::class, 'id', 'id_pipeline'],
    'PIPELINE_STEP' => [ self::HAS_ONE, PipelineStep::class, 'id', 'id_pipeline_step'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'order_number' => (new Varchar($this, $this->translate('Order number')))->setCssClass('badge badge-info')->setProperty('defaultVisibility', true),
      'id_customer' => (new Lookup($this, $this->translate('Customer'), Customer::class))->setRequired()->setProperty('defaultVisibility', true),
      'title' => (new Varchar($this, $this->translate('Title')))->setCssClass('font-bold')->setProperty('defaultVisibility', true),
      'id_pipeline' => (new Lookup($this, $this->translate('Pipeline'), Pipeline::class))->setDefaultValue(1),
      'id_pipeline_step' => (new Lookup($this, $this->translate('Pipeline step'), PipelineStep::class))->setDefaultValue(null),
      'price' => (new Decimal($this, $this->translate('Price')))->setReadonly()->setRequired()->setDefaultValue(0)->setProperty('defaultVisibility', true),
      'id_currency' => (new Lookup($this, $this->translate('Currency'), Currency::class))->setReadonly(),
      'date_order' => (new Date($this, $this->translate('Order date')))->setRequired()->setDefaultValue(date("Y-m-d")),
      'required_delivery_date' => (new Date($this, $this->translate('Required delivery date'))),
      'shipping_info' => (new Varchar($this, $this->translate('Shipping information'))),
      'note' => (new Text($this, $this->translate('Notes'))),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = ''; // 'Orders';
    $description->ui['addButtonText'] = $this->translate("Add order");

    unset($description->columns["shipping_info"]);
    unset($description->columns["note"]);

    return $description;
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $mSettings = $this->main->load(Setting::class);
    $defaultCurrency = (int) $mSettings->record
      ->where("key", "Apps\Community\Settings\Currency\DefaultCurrency")
      ->first()
      ->value
    ;

    $description = parent::describeForm();
    $description->defaultValues["id_currency"] = $defaultCurrency;

    return $description;
  }

  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    $savedRecord = parent::onAfterUpdate($originalRecord, $savedRecord);

    $mProduct = $this->main->load(Product::class);
    $longDescription = "";

    if (isset($savedRecord["PRODUCTS"])) {
      foreach ($savedRecord["PRODUCTS"] as $product) {
        if (isset($product["_toBeDeleted_"])) {
          continue;
        }
        $productTitle = (string) $mProduct->record->find((int) $product["id_product"])->title;
        $longDescription .=  "{$productTitle} - Amount: ".(string) $product["amount"]." - Unit Price: ".(string) $product["unit_price"]." - Vat: ".(string) $product["vat"]." - Discount: ".(string) $product["discount"]." \n\n";
      }
    }

    if ($longDescription == "") {
      $longDescription = "The order had no products or all products were deleted";
    }

    $mHistory = $this->main->load(History::class);
    $mHistory->record->recordCreate([
      "id_order" => $savedRecord["id"],
      "short_description" => "Order has been updated",
      "long_description" => $longDescription,
      "date_time" => date("Y-m-d H:i:s"),
    ]);

    return $savedRecord;
  }

  public function onAfterCreate(array $savedRecord): array
  {
    $mPipeline = $this->main->load(Pipeline::class);

    list($defaultPipeline, $idPipeline, $idPipelineStep) = $mPipeline->getDefaultPipelineInfo(Pipeline::TYPE_ORDER_MANAGEMENT);

    $savedRecord['id_pipeline'] = $idPipeline;
    $savedRecord['id_pipeline_step'] = $idPipelineStep;

    $this->record->recordUpdate($savedRecord);

    $savedRecord = parent::onAfterCreate($savedRecord);

    $order = $this->record->find($savedRecord["id"]);
    $order->order_number = $order->id;
    $order->save();

    $mHistory = $this->main->load(History::class);
    $mHistory->record->recordCreate([
      "id_order" => $order->id,
      "short_description" => "Order created",
      "date_time" => date("Y-m-d H:i:s"),
    ]);

    return $savedRecord;
  }

  public function generateInvoice(int $idOrder): void
  {
    $mInvoice = $this->main->load(Invoice::class);

    $order = $this->record->prepareReadQuery()->where('id', $idOrder)->first();

    if ($order) {
      $mInvoice->generateInvoice(new InvoiceDto(
        1, // $idProfile
        $this->main->auth->getUserId(), // $idIssuedBy
        (int) $order['id_customer'], // $idCustomer
        'ORD/' . $order->number, // $number
        null, // $vs
        '', // $cs
        '', // $ss
        null, // $dateIssue
        new \DateTimeImmutable()->add(new \DateInterval('P14D')), // $dateDelivery
        new \DateTimeImmutable()->add(new \DateInterval('P14D')), // $dateDue
        null, // $datePayment
        '', // $note
      ));
    }
  }

}
