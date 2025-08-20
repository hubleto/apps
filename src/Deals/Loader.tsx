import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableDeals from "./Components/TableDeals"
import DealFormActivity from "./Components/DealFormActivity"
import FormCustomerTopMenu from './Components/FormCustomerTopMenu'
import FormLeadTopMenu from './Components/FormLeadTopMenu'

// register app
globalThis.main.registerApp('HubletoApp/Community/Deals', new HubletoApp());

// register react components
globalThis.main.registerReactComponent('DealsTableDeals', TableDeals);
globalThis.main.registerReactComponent('DealsFormActivity', DealFormActivity);

globalThis.main.registerDynamicContent('FormCustomer:TopMenu', FormCustomerTopMenu);
globalThis.main.registerDynamicContent('FormLead:TopMenu', FormLeadTopMenu);
