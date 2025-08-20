<?php declare(strict_types=1);

namespace HubletoApp\Community\Projects\Controllers\Api;

class SaveJunction extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): array
  {
    $junctionModel = $this->main->urlParamAsString('junctionModel');
    $junctionSourceColumn = $this->main->urlParamAsString('junctionSourceColumn');
    $junctionDestinationColumn = $this->main->urlParamAsString('junctionSourceColumn');
    $junctionId = $this->main->urlParamAsInteger('junctionId');
    $junctionSourceRecordId = $this->main->urlParamAsInteger('junctionSourceRecordId');

    $jModel = $this->main->load($junctionModel);

    $tmp = $jModel
      ->record
      ->where($junctionSourceColumn, $junctionId)
      ->where($junctionDestinationColumn, $junctionSourceRecordId)
      ->get()?->toArray()
    ;

    if (is_array($tmp) && count($tmp) == 0) {
      $jModel->record->recordCreate([
        $junctionSourceColumn => $junctionId,
        $junctionDestinationColumn => $junctionSourceRecordId,
      ]);
    }

    return [];
  }
}
