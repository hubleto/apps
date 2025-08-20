import React, { Component } from 'react';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import TableLeads from '@hubleto/apps/Leads/Components/TableLeads';

export interface FormCampaignProps extends HubletoFormProps {}
export interface FormCampaignState extends HubletoFormState {}

export default class FormCampaign<P, S> extends HubletoForm<FormCampaignProps, FormCampaignState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Campaigns/Models/Campaign',
  };

  props: FormCampaignProps;
  state: FormCampaignState;

  translationContext: string = 'HubletoApp\\Community\\Campaigns\\Loader::Components\\FormCampaign';

  parentApp: string = 'HubletoApp/Community/Campaigns';

  constructor(props: FormCampaignProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormCampaignProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Campaign')}</b> },
        ...(this.getParentApp()?.getFormTabs() ?? [])
      ]
    };
  }

  renderTitle(): JSX.Element {
    return <>
      <h2>{this.state.record.subject ? this.state.record.subject : ''}</h2>
      <small>{this.translate("Campaign")}</small>
    </>;
  }

  renderTab(tab: string) {
    const R = this.state.record;

    switch (tab) {
      case 'default':
        return <>
          <div className='w-full flex gap-2'>
            <div className='flex-1 border-r border-gray-100'>
              {this.inputWrapper('name')}
              {this.inputWrapper('target_audience')}
              {this.inputWrapper('goal')}
              {this.inputWrapper('id_mail_template')}
              {this.inputWrapper('mail_body')}
            </div>
            <div className='flex-1'>
              {this.inputWrapper('utm_source')}
              {this.inputWrapper('utm_campaign')}
              {this.inputWrapper('utm_term')}
              {this.inputWrapper('utm_content')}
              {this.inputWrapper('id_manager')}
              {this.inputWrapper('notes')}
              {this.inputWrapper('color')}
              {this.inputWrapper('datetime_created')}
            </div>
          </div>
        </>;
      break

      default:
        super.renderTab(tab);
      break;
    }
  }
}

