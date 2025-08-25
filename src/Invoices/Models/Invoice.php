<?php

namespace HubletoApp\Community\Invoices\Models;

use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use HubletoApp\Community\Customers\Models\Customer;
use HubletoApp\Community\Settings\Models\InvoiceProfile;
use HubletoApp\Community\Settings\Models\User;

class Invoice extends \HubletoMain\Model {
  public string $table = 'invoices';
  public ?string $lookupSqlValue = '{%TABLE%}.number';
  public string $recordManagerClass = RecordManagers\Invoice::class;

  public array $relations = [
    'CUSTOMER' => [ self::BELONGS_TO, Customer::class, "id_customer" ],
    'PROFILE' => [ self::BELONGS_TO, InvoiceProfile::class, "id_profile" ],
    'ISSUED_BY' => [ self::BELONGS_TO, User::class, "id_issued_by" ],
    'ITEMS' => [ self::HAS_MANY, InvoiceItem::class, "id_invoice", "id" ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_profile' => (new Lookup($this, $this->translate('Profile'), InvoiceProfile::class)),
      'id_issued_by' => (new Lookup($this, $this->translate('Issued by'), User::class)),
      'id_customer' => (new Lookup($this, $this->translate('Customer'), Customer::class)),
      'number' => (new Varchar($this, $this->translate('Number'))),
      'vs' => (new Varchar($this, $this->translate('Variable symbol'))),
      'cs' => (new Varchar($this, $this->translate('Constant symbol'))),
      'ss' => (new Varchar($this, $this->translate('Specific symbol'))),
      'date_issue' => (new Date($this, $this->translate('Issued'))),
      'date_delivery' => (new Date($this, $this->translate('Delivered'))),
      'date_due' => (new Date($this, $this->translate('Due'))),
      'date_payment' => (new Date($this, $this->translate('Payment'))),
      'notes' => (new Text($this, $this->translate('Notes'))),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = $this->translate("Add invoice");
    return $description;
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();
    $description->defaultValues = [
      'id_issued_by' => $this->main->auth->getUserId(),
      'issued' => date('Y-m-d H:i:s'),
    ];
    return $description;
  }

  public function onBeforeCreate(array $record): array
  {

    $mInvoiceProfile = new InvoiceProfile($this->main);

    $invoicesThisYear = (array) $this->record->whereYear('date_delivery', date('Y'))->get()->toArray();
    $profil = $mInvoiceProfile->record->where('id', $record['id_profile'])->first()->toArray();

    $record['number'] = (string) ($profil['numbering_pattern'] ?? '{YYYY}{NNNN}');
    $record['number'] = str_replace('{YY}', date('y'), $record['number']);
    $record['number'] = str_replace('{YYYY}', date('Y'), $record['number']);
    $record['number'] = str_replace('{NN}', str_pad((string) (count($invoicesThisYear) + 1), 2, '0', STR_PAD_LEFT), $record['number']);
    $record['number'] = str_replace('{NNN}', str_pad((string) (count($invoicesThisYear) + 1), 3, '0', STR_PAD_LEFT), $record['number']);
    $record['number'] = str_replace('{NNNN}', str_pad((string) (count($invoicesThisYear) + 1), 4, '0', STR_PAD_LEFT), $record['number']);

    $record['vs'] = $record['number'];
    $record['cs'] = "0308";
    $record['date_issue'] = date("Y-m-d");
    $record['date_delivery'] = date("Y-m-d");
    $record['date_due'] = date("Y-m-d", strtotime("+14 days"));

    return $record;
  }

  public function onAfterLoadRecord(array $record): array {
    $vatPercent = 20;

    $total = 0;
    foreach ($record['ITEMS'] as $key => $item) { //@phpstan-ignore-line
      $unitPrice = (float) ($item['unit_price'] ?? 0);
      $amount = (float) ($item['amount'] ?? 0);
      $itemPrice = $unitPrice * $amount;
      $itemVat = $itemPrice*$vatPercent/100;
      $total += $itemPrice;

      $record['ITEMS'][$key]['SUMMARY'] = [
        'totalExcludingVat' => $itemPrice,
        'vat' => $itemVat,
        'totalIncludingVat' => $itemPrice + $itemVat,
      ];
    }

    $totalExclVat = $total;
    $vat = $totalExclVat*$vatPercent/100;

    $record['SUMMARY'] = [
      'totalExcludingVat' => $totalExclVat,
      'vat' => $vat,
      'totalIncludingVat' => $totalExclVat + $vat,
    ];

    return $record;

  }

  /**
   * Generates invoice and return ID of generated invoice
   *
   * @param InvoiceInterface $invoice
   * 
   * @return int ID of generated invoice
   * 
   */
  public function generateInvoice(Dto\Invoice $invoice): int
  {
    $idInvoice = $this->record->recordCreate([
      'id_profile' => $invoice->idProfile,
      'id_issued_by' => $invoice->idIssuedBy,
      'id_customer' => $invoice->idCustomer,
      'number' => $invoice->number,
      'vs' => $invoice->vs,
      'cs' => $invoice->cs,
      'ss' => $invoice->ss,
      'date_issue' => $invoice->date_issue,
      'date_delivery' => $invoice->date_delivery,
      'date_due' => $invoice->date_due,
      'date_payment' => $invoice->date_payment,
      'notes' => $invoice->notes,
    ])['id'];

    return $idInvoice;
  }

}