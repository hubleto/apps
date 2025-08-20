<?php

namespace HubletoApp\Community\Deals\Controllers\Api;

use Exception;
use HubletoApp\Community\Deals\Models\Deal;
use HubletoApp\Community\Deals\Models\DealDocument;
use HubletoApp\Community\Deals\Models\DealHistory;
use HubletoApp\Community\Deals\Models\DealProduct;
use HubletoApp\Community\Leads\Models\Lead;
use HubletoApp\Community\Leads\Models\LeadDocument;
use HubletoApp\Community\Leads\Models\LeadHistory;
use HubletoApp\Community\Pipeline\Models\Pipeline;

class CreateFromLead extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {

    $idLead = $this->main->urlParamAsInteger("idLead");

    if ($idLead <= 0) {
      return [
        "status" => "failed",
        "error" => "The lead for converting was not set"
      ];
    }

    $mLead = $this->main->load(Lead::class);
    $mLeadHistory = $this->main->load(LeadHistory::class);
    $mLeadDocument = $this->main->load(LeadDocument::class);

    $mDeal = $this->main->load(Deal::class);
    $mDealHistory = $this->main->load(DealHistory::class);
    $mDealDocument = $this->main->load(DealDocument::class);
    $deal = null;

    $mPipeline = $this->main->load(Pipeline::class);
    list($defaultPipeline, $idPipeline, $idPipelineStep) = $mPipeline->getDefaultPipelineInfo(Pipeline::TYPE_DEAL_MANAGEMENT);

    try {
      $lead = $mLead->record->where("id", $idLead)->first();

      $deal = $mDeal->record->recordCreate([
        "identifier" => $lead->identifier,
        "title" => $lead->title,
        "id_customer" => $lead->id_customer,
        "id_contact" => $lead->id_contact,
        "price_excl_vat" => $lead->price,
        "id_currency" => $lead->id_currency,
        "date_expected_close" => $lead->date_expected_close,
        "date_created" => date("Y-m-d H:i:s"),
        "id_owner" => $lead->id_owner,
        "shared_folder" => $lead->shared_folder,
        "source_channel" => $lead->source_channel,
        "is_archived" => false,
        "id_lead" => $lead->id,
        "deal_result" => $mDeal::RESULT_UNKNOWN,
        "id_pipeline" => $idPipeline,
        "id_pipeline_step" => $idPipelineStep,
      ]);

      $lead->status = $mLead::STATUS_CONVERTED_TO_DEAL;
      $lead->save();

      $leadDocuments = $mLeadDocument->record->where("id_lead", $idLead)->get();

      foreach ($leadDocuments as $leadDocument) { //@phpstan-ignore-line
        $mDealDocument->record->recordCreate([
          "id_document" => $leadDocument->id_document,
          "id_deal" => $deal['id']
        ]);
      }

      $leadHistories = $mLeadHistory->record->where("id_lead", $idLead)->get();

      foreach ($leadHistories as $leadHistory) { //@phpstan-ignore-line
        $mDealHistory->record->recordCreate([
          "description" => $leadHistory->description,
          "change_date" => $leadHistory->change_date,
          "id_deal" => $deal['id']
        ]);
      }

      $mLeadHistory->record->recordCreate([
        "description" => "Converted to a Deal",
        "change_date" => date("Y-m-d"),
        "id_lead" => $idLead
      ]);

      $mDealHistory->record->recordCreate([
        "description" => "Converted to a Deal",
        "change_date" => date("Y-m-d"),
        "id_deal" => $deal['id']
      ]);

      $lead->save();
    } catch (Exception $e) {
      return [
        "status" => "failed",
        "error" => $e
      ];
    }

    return [
      "status" => "success",
      "idDeal" => $deal['id'],
      "title" => str_replace(" ", "+", (string) $deal['title'])
    ];
  }

}
