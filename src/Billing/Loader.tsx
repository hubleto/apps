import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import BillingTableBillingAccountService from "./Components/TableBillingAccountServices"

// register app
globalThis.main.registerApp('HubletoApp/Community/Billing', new HubletoApp());

// register react components
globalThis.main.registerReactComponent('BillingTableBillingAccountService', BillingTableBillingAccountService);
