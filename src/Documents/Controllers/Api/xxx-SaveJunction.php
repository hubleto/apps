<?php declare(strict_types=1);

namespace HubletoApp\Community\Documents\Controllers\Api;

class SaveJunction extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): array
  {
    $junctionModel = $this->main->urlParamAsString('junctionModel');
    $junctionColumn = $this->main->urlParamAsString('junctionColumn');
    $junctionDestinationRecordId = $this->main->urlParamAsInteger('junctionDestinationRecordId');
    $junctionSourceRecordId = $this->main->urlParamAsInteger('junctionSourceRecordId');

    $jModel = $this->main->load($junctionModel);

    $tmp = $jModel
      ->record
      ->where($junctionColumn, $junctionId)
      ->where('id_document', $junctionSourceRecordId)
      ->get()?->toArray()
    ;

    if (is_array($tmp) && count($tmp) == 0) {
      $jModel->record->recordCreate([
        $junctionColumn => $junctionId,
        'id_document' => $junctionSourceRecordId,
      ]);
    }

    return [];
  }
}
