import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import InvoicesTableInvoices from "./Components/TableInvoices"
import InvoicesTableInvoiceItems from "./Components/TableInvoiceItems"

// register app
globalThis.main.registerApp('HubletoApp/Community/Invoices', new HubletoApp());

// register react components
globalThis.main.registerReactComponent('InvoicesTableInvoices', InvoicesTableInvoices);
globalThis.main.registerReactComponent('InvoicesTableInvoiceItems', InvoicesTableInvoiceItems);
