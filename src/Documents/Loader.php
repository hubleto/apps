<?php

namespace HubletoApp\Community\Documents;

class Loader extends \HubletoMain\App
{
  
  /**
   * Inits the app: adds routes, settings, calendars, hooks, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^documents\/?$/' => Controllers\Browse::class,
      '/^documents\/browse\/?$/' => Controllers\Browse::class,
      '/^documents\/list\/?$/' => Controllers\Documents::class,
      '/^documents\/(?<recordId>\d+)\/?$/' => Controllers\Documents::class,
      '/^documents\/templates\/?$/' => Controllers\Templates::class,
      '/^documents\/api\/get-folder-content\/?$/' => Controllers\Api\GetFolderContent::class,
    ]);

  }

  public function getRootFolderId(): int|null
  {
    $mFolder = $this->main->load(Models\Folder::class);
    $rootFolder = $mFolder->record->where('uid', '_ROOT_')->first()->toArray();
    if (!isset($rootFolder['id'])) {
      return null;
    } else {
      return (int) $rootFolder['id'];
    }
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mFolder = $this->main->load(Models\Folder::class);
      $mFolder->dropTableIfExists()->install();

      $mFolder->record->recordCreate([
        'id_parent_folder' => null,
        'uid' => '_ROOT_',
        'name' => '_ROOT_',
      ]);

      $this->main->load(Models\Document::class)->dropTableIfExists()->install();
      $this->main->load(Models\Template::class)->dropTableIfExists()->install();
    }

  }

  public function generateDemoData(): void
  {
    $mFolder = $this->main->load(Models\Folder::class);
    $mDocument = $this->main->load(Models\Document::class);
    $mTemplate = $this->main->load(Models\Template::class);

    $mDocument->record->recordCreate([
      'id_folder' => $this->getRootFolderId(),
      'name' => 'bid_template.docx',
      'hyperlink' => 'https://www.google.com',
    ]);

    $idFolderMM = $mFolder->record->recordCreate([ 'id_parent_folder' => $this->getRootFolderId(), 'name' => 'Marketing materials' ])['id'];
    $idFolderMM1 = $mFolder->record->recordCreate(['id_parent_folder' => $idFolderMM, 'name' => 'LinkedIn'])['id'];
    $idFolderMM2 = $mFolder->record->recordCreate(['id_parent_folder' => $idFolderMM, 'name' => 'GoogleAds'])['id'];

    $idFolderCU = $mFolder->record->recordCreate([ 'id_parent_folder' => $this->getRootFolderId(), 'name' => 'Customer profiles' ])['id'];

    $mDocument->record->recordCreate([ 'id_folder' => $idFolderMM, 'name' => 'logo.png', 'hyperlink' => 'https://www.google.com' ]);
    $mDocument->record->recordCreate([ 'id_folder' => $idFolderMM1, 'name' => 'post_image_1.png', 'hyperlink' => 'https://www.google.com' ]);
    $mDocument->record->recordCreate([ 'id_folder' => $idFolderMM1, 'name' => 'post_image_2.png', 'hyperlink' => 'https://www.google.com' ]);
    $mDocument->record->recordCreate([ 'id_folder' => $idFolderMM2, 'name' => 'analytics_report_1.pdf', 'hyperlink' => 'https://www.google.com' ]);
    $mDocument->record->recordCreate([ 'id_folder' => $idFolderMM2, 'name' => 'analytics_report_2.pdf', 'hyperlink' => 'https://www.google.com' ]);

    $mDocument->record->recordCreate([ 'id_folder' => $idFolderCU, 'name' => 'customer_profile_1.pdf', 'hyperlink' => 'https://www.google.com' ]);
    $mDocument->record->recordCreate([ 'id_folder' => $idFolderCU, 'name' => 'customer_profile_2.pdf', 'hyperlink' => 'https://www.google.com' ]);

    $idTemplate = $mTemplate->record->recordCreate([
      'name' => 'PDF template for quotation',
      'content' => '
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
  * { font-family: "Helvetica"; font-size: 12px; }
</style>

<div>
  <div class="dtop">
    <div style="font-size:2em"><b>Quotation</b></div>
    {{ identifier }}
  </div>
  <br/>
  <div style="padding:1em;border:1px solid black;width:100%">
    <div style="width:50%;float:left;">
      <b>Supplier</b>
      <table style="width:100%">
        <tr>
          <td>Order number</td>
          <td>{{ identifier }}</td>
        </tr>
        <tr>
          <td>Generated on</td>
          <td>{{ now }}</td>
        </tr>
      </table>
    </div>
    <div style="width:50%; float:left;">
      <b>Quotation for</b>
      <table style="width:100%">
        <tr>
          <td>Customer</td>
          <td>{{ CUSTOMER.name }}</td>
        </tr>
        <tr>
          <td>Contact person</td>
          <td>{{ CONTACT.first_name }} {{ CONTACT.last_name }}</td>
        </tr>
      </table>
    </div>
    <div style="clear:both;"></div>
  </div>

  <table style="width:100%">
    <tr>
      <td style="width:40%">Product</td>
      <td style="width:10%">Unit price</td>
      <td style="width:10%">Amount</td>
      <td style="width:10%">Discount</td>
      <td style="width:10%">VAT</td>
      <td style="width:10%">Subtotal</td>
      <td style="width:10%">Subtotal incl. VAT</td>
    </tr>
    {% for product in PRODUCTS %}
      <tr>
        <td style="width:40%">{{ product.PRODUCT.name }}</td>
        <td style="width:10%">{{ product.sales_price }} €</td>
        <td style="width:10%">{{ product.amount}} </td>
        <td style="width:10%">{{ product.discount }} %</td>
        <td style="width:10%">{{ product.vat }} %</td>
        <td style="width:10%">{{ product.price_excl_vat }} €</td>
        <td style="width:10%">{{ product.price_incl_vat }} €</td>
      </tr>
    {% endfor %}
  </table>
      '
    ])['id'];


  }

}
