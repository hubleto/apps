<?php

namespace HubletoApp\Community\Invoices\Models\RecordManagers;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

use \HubletoApp\Community\Customers\Models\RecordManagers\Customer;
use \HubletoApp\Community\Settings\Models\RecordManagers\User;
use \HubletoApp\Community\Settings\Models\RecordManagers\InvoiceProfile;

class Invoice extends \HubletoMain\RecordManager {
  public $table = 'invoices';

  /** @return BelongsTo<Customer, covariant Invoice> */
  public function CUSTOMER(): BelongsTo {
    return $this->BelongsTo(Customer::class, 'id_customer');
  }

  /** @return BelongsTo<InvoiceProfile, covariant Invoice> */
  public function PROFILE(): BelongsTo {
    return $this->BelongsTo(InvoiceProfile::class, 'id_profile');
  }

  /** @return BelongsTo<User, covariant Invoice> */
  public function ISSUED_BY(): BelongsTo {
    return $this->BelongsTo(User::class, 'id_issued_by');
  }

  /** @return HasMany<InvoiceItem, covariant Invoice> */
  public function ITEMS(): HasMany {
    return $this->HasMany(InvoiceItem::class, 'id_invoice');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $main = \HubletoMain\Loader::getGlobalApp();

    $idCustomer = $main->urlParamAsInteger('idCustomer');
    if ($idCustomer > 0) $query->where('id_customer', $idCustomer);

    $idProfile = $this->main->urlParamAsInteger('idProfile');
    if ($idProfile > 0) $query->where('id_profil', $idProfile);

    if ($this->main->isUrlParam('number')) $query->where('number', 'like', '%' . $this->main->urlParamAsString('number') . '%');
    if ($this->main->isUrlParam('vs')) $query->where('vs', 'like', '%' . $this->main->urlParamAsString('vs') . '%');

    if ($this->main->isUrlParam('dateIssueFrom')) $query->whereDate('date_issue', '>=', $this->main->urlParamAsString('dateIssueFrom'));
    if ($this->main->isUrlParam('dateIssueTo')) $query->whereDate('date_issue', '<=', $this->main->urlParamAsString('dateIssueTo'));
    if ($this->main->isUrlParam('dateDeliveryFrom')) $query->whereDate('date_delivery', '>=', $this->main->urlParamAsString('dateDeliveryFrom'));
    if ($this->main->isUrlParam('dateDeliveryTo')) $query->whereDate('date_delivery', '<=', $this->main->urlParamAsString('dateDeliveryTo'));
    if ($this->main->isUrlParam('dateTueFrom')) $query->whereDate('date_due', '>=', $this->main->urlParamAsString('dateTueFrom'));
    if ($this->main->isUrlParam('dateTueTo')) $query->whereDate('date_due', '<=', $this->main->urlParamAsString('dateTueTo'));
    if ($this->main->isUrlParam('datePaymentFrom')) $query->whereDate('date_payment', '>=', $this->main->urlParamAsString('datePaymentFrom'));
    if ($this->main->isUrlParam('datePaymentTo')) $query->whereDate('date_payment', '<=', $this->main->urlParamAsString('datePaymentTo'));

    $query
      ->first()
      ?->toArray()
    ;

    return $query;
  }

}
