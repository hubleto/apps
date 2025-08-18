import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormDocument, { FormDocumentProps } from './FormDocument';

interface TableDocumentsProps extends HubletoTableProps {
  junctionModel?: string
  junctionColumn?: string
  junctionId?: number
}
interface TableDocumentsState extends HubletoTableState {}

export default class TableDocuments extends HubletoTable<TableDocumentsProps, TableDocumentsState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    orderBy: {
      field: "id",
      direction: "desc"
    },
    model: 'HubletoApp/Community/Documents/Models/Document',
  }

  props: TableDocumentsProps;
  state: TableDocumentsState;

  translationContext: string = 'HubletoApp\\Community\\Documents\\Loader::Components\\TableDocuments';

  constructor(props: TableDocumentsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableDocumentsProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      junctionModel: this.props.junctionModel,
      junctionColumn: this.props.junctionColumn,
      junctionId: this.props.junctionId,
    }
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'centered small';
    return params;
  }

  renderForm(): JSX.Element {
    let formProps: FormDocumentProps = this.getFormProps();
    formProps.junctionModel = this.props.junctionModel;
    formProps.junctionColumn = this.props.junctionColumn;
    formProps.junctionId = this.props.junctionId;
    return <FormDocument {...formProps}/>;
  }
}