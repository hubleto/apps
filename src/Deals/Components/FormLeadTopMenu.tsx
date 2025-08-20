import React, { Component } from 'react';
import FormLead, { FormLeadProps, FormLeadState } from '@hubleto/apps/Leads/Components/FormLead'
import TableDeals from './TableDeals';
import ModalSimple from "@hubleto/react-ui/core/ModalSimple";
import TranslatedComponent from "@hubleto/react-ui/core/TranslatedComponent";
import request from '@hubleto/react-ui/core/Request';

interface P {
  form: FormLead<FormLeadProps, FormLeadState>
}

interface S {
  showDeals: boolean;
}

export default class FormLeadTopMenu extends TranslatedComponent<P, S> {
  props: P;
  state: S;

  translationContext: string = 'HubletoApp\\Community\\Deals\\Loader::Components\\FormDeal';

  constructor(props: P) {
    super(props);
    this.state = { showDeals: false };
  }

  convertToDeal(recordId: number) {
    request.get(
      'deals/api/convert-lead-to-deal',
      {recordId: recordId},
      (data: any) => {
        if (data.status == "success") {
          location.assign(globalThis.main.config.projectUrl + `/deals?recordId=${data.idDeal}&recordTitle=${data.title}`)
        }
      }
    );
  }

  convertDealWarning(recordId: number) {
    globalThis.main.showDialogDanger(
      <>
        <div>
          Are you sure you want to convert this Lead to a Deal?<br/>
        </div>
      </>,
      {
        headerClassName: "dialog-warning-header",
        header: "Convert to a Deal",
        footer: <>
          <button
            className="btn btn-yellow"
            onClick={() => {this.convertToDeal(recordId)}}
          >
            <span className="icon"><i className="fas fa-forward"></i></span>
            <span className="text">Yes, convert to a Deal</span>
          </button>
          <button
            className="btn btn-transparent"
            onClick={() => {
              globalThis.main.lastShownDialogRef.current.hide();
            }}
          >
            <span className="icon"><i className="fas fa-times"></i></span>
            <span className="text">No, do not convert to a Deal</span>
          </button>
        </>
      }
    );
  }

  render() {
    const form = this.props.form;
    const R = form.state.record;

    return (R.DEAL != null ?
      <a
        className='btn btn-transparent'
        href={`${globalThis.main.config.projectUrl}/deals/${R.DEAL.id}`}
        target='_blank'
      >
        <span className='text'>{this.translate('Deal')}</span>
      </a>
      :
      <a className='btn btn-transparent' onClick={() => this.convertDealWarning(R.id)}>
        <span className='text'>Convert to Deal</span>
      </a>
    );
  }
}

