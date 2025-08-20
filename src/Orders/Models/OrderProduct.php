<?php

namespace HubletoApp\Community\Orders\Models;

use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use HubletoApp\Community\Products\Models\Product;

class OrderProduct extends \Hubleto\Framework\Models\Model
{
  public string $table = 'orders_products';
  public string $recordManagerClass = RecordManagers\OrderProduct::class;
  public ?string $lookupSqlValue = '{%TABLE%}.id';

  public array $relations = [
    'ORDER' => [ self::BELONGS_TO, Order::class, 'id_order', 'id'],
    'PRODUCT' => [ self::BELONGS_TO, Product::class, 'id_product', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_order' => (new Lookup($this, $this->translate('Order'), Order::class))->setRequired(),
      'title' => (new Varchar($this, $this->translate('Title')))->setRequired()->setProperty('defaultVisibility', true),
      'id_product' => (new Lookup($this, $this->translate('Product'), Product::class))->setProperty('defaultVisibility', true),
      'sales_price' => (new Decimal($this, $this->translate('Sales price')))->setRequired()->setProperty('defaultVisibility', true),
      'amount' => (new Integer($this, $this->translate('Amount')))->setRequired()->setProperty('defaultVisibility', true),
      'discount' => (new Integer($this, $this->translate('Discount')))->setUnit('%')->setProperty('defaultVisibility', true),
      'vat' => (new Integer($this, $this->translate('Vat')))->setUnit('%')->setProperty('defaultVisibility', true),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Order Products';
    $description->ui["addButtonText"] = $this->translate("Add product");

    return $description;
  }
}
