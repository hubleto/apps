<?php

namespace HubletoApp\Community\Deals\Models;

use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use HubletoApp\Community\Products\Controllers\Api\CalculatePrice;
use HubletoApp\Community\Products\Models\Product;

class DealProduct extends \Hubleto\Framework\Models\Model
{
  public string $table = 'deal_products';
  public string $recordManagerClass = RecordManagers\DealProduct::class;
  public ?string $lookupSqlValue = '{%TABLE%}.id_product';

  public array $relations = [
    'PRODUCT' => [ self::BELONGS_TO, Product::class, 'id_product', 'id' ],
    'DEAL' => [ self::BELONGS_TO, Deal::class, 'id_deal', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_deal' => (new Lookup($this, $this->translate('Deal'), Deal::class))->setRequired(),
      'id_product' => (new Lookup($this, $this->translate('Product'), Product::class))->setFkOnUpdate("CASCADE")->setFkOnDelete("SET NULL")->setRequired()->setProperty('defaultVisibility', true),
      'order' => (new Integer($this, $this->translate('Order')))->setRequired()->setProperty('defaultVisibility', true),
      'description' => (new Text($this, $this->translate('Description')))->setProperty('defaultVisibility', true),
      'unit_price' => (new Decimal($this, $this->translate('Unit Price')))->setRequired()->setProperty('defaultVisibility', true),
      'amount' => (new Decimal($this, $this->translate('Amount')))->setRequired()->setProperty('defaultVisibility', true),
      'vat' => (new Decimal($this, $this->translate('Vat')))->setUnit("%")->setProperty('defaultVisibility', true),
      'discount' => (new Decimal($this, $this->translate('Discount')))->setUnit("%")->setProperty('defaultVisibility', true),
      'price_excl_vat' => new Decimal($this, $this->translate('Price excl. VAT'))->setProperty('defaultVisibility', true),
      'price_incl_vat' => new Decimal($this, $this->translate('Price incl. VAT'))->setProperty('defaultVisibility', true),
    ]);
  }

  // public function describeTable(): \Hubleto\Framework\Description\Table
  // {
  //   $description = parent::describeTable();
  //   if ($this->main->urlParamAsInteger('idDeal') > 0) {
  //     // $description->permissions = [
  //     //   'canRead' => $this->main->permissions->granted($this->fullName . ':Read'),
  //     //   'canCreate' => $this->main->permissions->granted($this->fullName . ':Create'),
  //     //   'canUpdate' => $this->main->permissions->granted($this->fullName . ':Update'),
  //     //   'canDelete' => $this->main->permissions->granted($this->fullName . ':Delete'),
  //     // ];
  //     $description->columns = [];
  //     $description->inputs = [];
  //     $description->ui = [];
  //   }

  //   return $description;
  // }

  public function onBeforeCreate(array $record): array
  {
    $record["price_excl_vat"] = (new CalculatePrice($this->main))->calculatePriceExcludingVat(
      (float) ($record["unit_price"] ?? 0),
      (float) ($record["amount"] ?? 0),
      (float) ($record["discount"] ?? 0)
    );
    $record["price_incl_vat"] = (new CalculatePrice($this->main))->calculatePriceIncludingVat(
      (float) ($record["unit_price"] ?? 0),
      (float) ($record["amount"] ?? 0),
      (float) ($record["vat"] ?? 0),
      (float) ($record["discount"] ?? 0)
    );
    return $record;
  }

  public function onBeforeUpdate(array $record): array
  {
    $record["price_excl_vat"] = (new CalculatePrice($this->main))->calculatePriceExcludingVat(
      (float) ($record["unit_price"] ?? 0),
      (float) ($record["amount"] ?? 0),
      (float) ($record["discount"] ?? 0)
    );
    $record["price_incl_vat"] = (new CalculatePrice($this->main))->calculatePriceIncludingVat(
      (float) ($record["unit_price"] ?? 0),
      (float) ($record["amount"] ?? 0),
      (float) ($record["vat"] ?? 0),
      (float) ($record["discount"] ?? 0)
    );
    return $record;
  }
}
