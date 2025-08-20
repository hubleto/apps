import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import request from "@hubleto/react-ui/core/Request";
import OrdersTableOrders from "./Components/TableOrders";

// register app
globalThis.main.registerApp('HubletoApp/Community/Orders', new HubletoApp());

// register react components
globalThis.main.registerReactComponent('OrdersTableOrders', OrdersTableOrders);

// miscellaneous
globalThis.main.getApp('HubletoApp/Community/Deals').addFormHeaderButton(
  'Create order',
  (form: any) => {
    request.get(
      'orders/api/create-from-deal',
      {idDeal: form.state.record.id},
      (data: any) => {
        if (data.status == "success") {
          globalThis.window.open(globalThis.main.config.projectUrl + '/orders/' + data.idOrder);
        }
      }
    );
  }
)