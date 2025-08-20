import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableDocuments from "./Components/TableDocuments"
import TableTemplates from "./Components/TableTemplates"
import Browser from "./Components/Browser"

// register app
globalThis.main.registerApp('HubletoApp/Community/Documents', new HubletoApp());

// register react components
globalThis.main.registerReactComponent('DocumentsTableDocuments', TableDocuments);
globalThis.main.registerReactComponent('DocumentsBrowser', Browser);
globalThis.main.registerReactComponent('DocumentsTableTemplates', TableTemplates);
