<?php

namespace HubletoApp\Community\Orders\Models;

use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use HubletoApp\Community\Projects\Models\Project;

class OrderProject extends \Hubleto\Framework\Models\Model
{
  public string $table = 'orders_projects';
  public string $recordManagerClass = RecordManagers\OrderProject::class;
  public ?string $lookupSqlValue = '{%TABLE%}.id';

  public array $relations = [
    'ORDER'   => [ self::BELONGS_TO, Order::class, 'id_order', 'id'],
    'PROJECT' => [ self::BELONGS_TO, Project::class, 'id_project', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_order' => (new Lookup($this, $this->translate('Order'), Order::class))->setRequired(),
      'id_project' => (new Lookup($this, $this->translate('Project'), Project::class))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Order Projects';
    $description->ui["addButtonText"] = $this->translate("Add project");

    if ($this->main->urlParamAsInteger('idOrder') > 0) {
      $description->columns = [];
      $description->inputs = [];
      $description->ui = [];
    }

    return $description;
  }
}
