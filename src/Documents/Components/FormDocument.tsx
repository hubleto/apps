import React, { Component } from 'react';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import { getUrlParam } from '@hubleto/react-ui/core/Helper';
import request from '@hubleto/react-ui/core/Request';

export interface FormDocumentProps extends HubletoFormProps {
  junctionModel?: string
  junctionColumn?: string
  junctionId?: number
}
export interface FormDocumentState extends HubletoFormState {}

export default class FormDocument<P, S> extends HubletoForm<FormDocumentProps,FormDocumentState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Documents/Models/Document',
  };

  props: FormDocumentProps;
  state: FormDocumentState;

  translationContext: string = 'HubletoApp\\Community\\Documents\\Loader::Components\\FormDocument';

  constructor(props: FormDocumentProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormDocumentProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  onAfterSaveRecord(saveResponse) {
    console.log('onar', this.props);
    super.onAfterSaveRecord(saveResponse);
    if (this.props.junctionModel) {
      console.log('onar', 'req', saveResponse);
      request.post(
        'documents/api/save-junction',
        {
          junctionModel: this.props.junctionModel,
          junctionColumn: this.props.junctionColumn,
          junctionId: this.props.junctionId,
          idDocument: saveResponse.savedRecord['id'],
        },
        {},
        (data: any) => { /* */ }
      );
    }
  }

  renderTitle(): JSX.Element {
    if (getUrlParam('recordId') == -1) {
      return <h2>{this.translate('New Document')}</h2>;
    } else {
      return <>
        <h2>{this.state.record.name ? this.state.record.name : ''}</h2>
        <small>{this.translate("Document")}</small>
      </>;
    }
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    // const showAdditional: boolean = R.id > 0 ? true : false;

    // const linkExists = this.state.description.defaultValues?.creatingForModel ? false : true

    return <>
      <div>{this.props.junctionModel}</div>
      <div>{this.props.junctionColumn}</div>
      <div>{this.props.junctionId}</div>
      <div className='card mt-4'>
        <div className='card-body'>
          {this.inputWrapper('id_folder')}
          {this.inputWrapper('name', {cssClass: 'text-2xl text-primary'})}
          {this.inputWrapper('file')}
          {this.inputWrapper('hyperlink')}
          {/* {R.origin_link && linkExists ?
            <a href={this.state.record.origin_link} className='btn brn-primary mt-2'>
              <span className='icon'><i className='fas fa-link'></i></span>
              <span className='text'>{this.translate("Go to origin entry")}</span>
            </a>
          : <></>
          } */}
        </div>
      </div>
    </>;
  }
}

