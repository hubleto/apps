<?php

namespace HubletoApp\Community\Deals\Models;

use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use HubletoApp\Community\Orders\Models\Order;

class DealOrder extends \Hubleto\Framework\Models\Model
{
  public string $table = 'deals_orders';
  public string $recordManagerClass = RecordManagers\DealOrder::class;
  public ?string $lookupSqlValue = '{%TABLE%}.id';

  public array $relations = [
    'ORDER'   => [ self::BELONGS_TO, Order::class, 'id_order', 'id'],
    'DEAL' => [ self::BELONGS_TO, Deal::class, 'id_deal', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_order' => (new Lookup($this, $this->translate('Order'), Order::class))->setRequired(),
      'id_deal' => (new Lookup($this, $this->translate('Deal'), Deal::class))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Order Deals';
    $description->ui["addButtonText"] = $this->translate("Add deal");

    if ($this->main->urlParamAsInteger('idOrder') > 0) {
      $description->columns = [];
      $description->inputs = [];
      $description->ui = [];
    }

    return $description;
  }
}
