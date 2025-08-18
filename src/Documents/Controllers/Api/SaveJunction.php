<?php declare(strict_types=1);

namespace HubletoApp\Community\Documents\Controllers\Api;

class SaveJunction extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idDocument = $this->main->urlParamAsInteger('idDocument');
    $junctionModel = $this->main->urlParamAsString('junctionModel');
    $junctionColumn = $this->main->urlParamAsString('junctionColumn');
    $junctionId = $this->main->urlParamAsInteger('junctionId');

    $jModel = $this->main->load($junctionModel);

    $tmp = $jModel
      ->record
      ->where($junctionColumn, $junctionId)
      ->where('id_document', $idDocument)
      ->get()?->toArray()
    ;

    // var_dump($tmp);
    // var_dump($junctionColumn);
    // var_dump($junctionId);
    // var_dump($idDocument);

    if (is_array($tmp) && count($tmp) == 0) {
      $jModel->record->recordCreate([
        $junctionColumn => $junctionId,
        'id_document' => $idDocument,
      ]);
    }

    return [];
  }
}
