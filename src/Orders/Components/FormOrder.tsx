import React, { Component, createRef, useRef } from 'react';
import { getUrlParam } from '@hubleto/react-ui/core/Helper';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import TableOrderProducts from '@hubleto/apps/Orders/Components/TableOrderProducts';
import TableDocuments from '@hubleto/apps/Documents/Components/TableDocuments';
import TableDeals from '@hubleto/apps/Deals/Components/TableDeals';
import TableProjects from '@hubleto/apps/Projects/Components/TableProjects';
import TableInvoices from '@hubleto/apps/Invoices/Components/TableInvoices';
import request from "@hubleto/react-ui/core/Request";
import TableHistories from './TableHistories';
import PipelineSelector from '../../Pipeline/Components/PipelineSelector';
import FormInput from '@hubleto/react-ui/core/FormInput';

export interface FormOrderProps extends HubletoFormProps {
}

export interface FormOrderState extends HubletoFormState {
  newEntryId: number,
}

export default class FormOrder<P, S> extends HubletoForm<FormOrderProps,FormOrderState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Orders/Models/Order',
  };

  props: FormOrderProps;
  state: FormOrderState;

  translationContext: string = 'HubletoApp\\Community\\Orders\\Loader::Components\\FormOrder';

  constructor(props: FormOrderProps) {
    super(props);
    this.state = {
      ...this.getStateFromProps(props),
      newEntryId: -1,
    };
  }

  getStateFromProps(props: FormOrderProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: this.translate('Order') },
        { uid: 'products', title: this.translate('Products'), showCountFor: 'PRODUCTS' },
        { uid: 'documents', title: this.translate('Documents'), showCountFor: 'DOCUMENTS' },
        { uid: 'projects', title: this.translate('Projects'), showCountFor: 'PROJECTS' },
        { uid: 'invoices', title: this.translate('Invoices'), showCountFor: 'INVOICES' },
        { uid: 'history', title: this.translate('History') },
      ]
    };
  }

  renderTitle(): JSX.Element {
    if (getUrlParam('recordId') == -1) {
      return <h2>{globalThis.main.translate('New Order')}</h2>;
    } else {
      return <h2>{this.state.record.id_customer ? this.state.record.order_number : '[Undefined Order]'}</h2>
    }
  }

  getSumPrice(recordProducts: any) {
    var sumPrice = 0;
    recordProducts.map((product, index) => {
      if (product.unit_price && product.amount && product._toBeDeleted_ != true) {
        var sum = product.unit_price * product.amount;
        if (product.vat) sum = sum + (sum * (product.vat / 100));
        if (product.discount) sum = sum - (sum * (product.discount / 100));
        sumPrice += sum;
      }
    });
    return Number(sumPrice.toFixed(2));
  }

  renderHeaderLeft(): null|JSX.Element {
    return <>
      {super.renderHeaderLeft()}
      <button className="btn btn-transparent"
        onClick={() => {
          request.post(
            'orders/api/generate-pdf',
            {idOrder: this.state.record.id},
            {},
            (result: any) => {
              console.log(result);
              if (result.idDocument) {
                window.open(globalThis.main.config.projectUrl + '/documents/' + result.idDocument);
              }
            }
          );
        }}
      >
        <span className="text">Generate PDF</span>
      </button>
    </>;
  }

  renderTab(tab: string) {
    const R = this.state.record;
    const showAdditional = R.id > 0 ? true : false;

    switch (tab) {
      case 'default':

        const pipeline = <PipelineSelector
          idPipeline={R.id_pipeline}
          idPipelineStep={R.id_pipeline_step}
          onPipelineChange={(idPipeline: number, idPipelineStep: number) => {
            //
          }}
          onPipelineStepChange={(idPipelineStep: number, step: any) => {
            if (!R.is_archived) {
              if (this.state.isInlineEditing == false) this.setState({isInlineEditing: true});
              R.id_pipeline_step = idPipelineStep;
              R.deal_result = step.set_result;
              R.PIPELINE_STEP = step;
              this.updateRecord(R);
            }
          }}
        ></PipelineSelector>;

        return <>
          <div className='card'>
            <div className='card-body flex flex-row gap-2'>
              <div className='grow'>
                <FormInput title={"Deals"}>
                  {R.DEALS ? R.DEALS.map((item, key) => {
                    return <div key={key} className='badge'>{item.DEAL.identifier}</div>;
                  }) : null}
                </FormInput>
                {showAdditional ? this.inputWrapper('order_number') : <></>}
                {showAdditional ?
                  <div className='flex flex-row *:w-1/2'>
                    {this.inputWrapper('price')}
                    {this.inputWrapper('id_currency')}
                  </div>
                : <></>}
                {this.inputWrapper('date_order')}
                {this.inputWrapper('required_delivery_date')}
                {this.inputWrapper('shipping_info')}
              </div>
              <div className='border-l border-gray-200'></div>
              <div className='grow'>
                {this.inputWrapper('id_customer')}
                {this.inputWrapper('note')}
                {this.inputWrapper('id_template')}
              </div>
            </div>

            {pipeline}
          </div>
        </>;
      break;

      case 'products':
        return <TableOrderProducts
          tag={"table_order_product"}
          parentForm={this}
          uid={this.props.uid + "_table_order_product"}
          idOrder={R.id}
          // junctionTitle='Order'
          // junctionModel='HubletoApp/Community/Orders/Models/OrderProduct'
          // junctionSourceColumn='id_order'
          // junctionSourceRecordId={R.id}
          // junctionDestinationColumn='id_product'
          readonly={R.is_archived == true ? false : !this.state.isInlineEditing}
        />;

      break;

      case 'documents':
        return <TableDocuments
          key={"table_order_document"}
          parentForm={this}
          uid={this.props.uid + "_table_order_document"}
          junctionTitle='Order'
          junctionModel='HubletoApp/Community/Orders/Models/OrderDocument'
          junctionSourceColumn='id_order'
          junctionSourceRecordId={R.id}
          junctionDestinationColumn='id_document'
          readonly={R.is_archived == true ? false : !this.state.isInlineEditing}
        />;

      break;

      case 'projects':
        return <TableProjects
          tag={"table_order_project"}
          parentForm={this}
          uid={this.props.uid + "_table_order_project"}
          junctionTitle='Order'
          junctionModel='HubletoApp/Community/Orders/Models/OrderProject'
          junctionSourceColumn='id_order'
          junctionSourceRecordId={R.id}
          junctionDestinationColumn='id_project'
        />;
      break;

      case 'history':
        return <>
          <TableHistories
            uid={this.props.uid + "_table_order_history"}
            data={{ data: R.HISTORY }}
            descriptionSource='props'
            description={{
              ui: {
                showHeader: false,
                showFooter: false,
              },
              permissions: {
                canCreate: false,
                canUpdate: false,
                canDelete: false,
                canRead: true,
              },
              columns: {
                short_description: { type: "text", title: "Short Description" },
                date: { type: "datetime", title: "Date Time"},
              },
              inputs: {
                short_description: { type: "text", title: "Short Description" },
                date: { type: "datetime", title: "Date Time"},
              }
            }}
            isUsedAsInput={true}
            isInlineEditing={false}
            onRowClick={(table: TableHistories, row: any) => table.openForm(row.id)}
          />
        </>;
      break;
    }
  }
}