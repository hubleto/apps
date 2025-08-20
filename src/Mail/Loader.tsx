import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableMails from "./Components/TableMails"

// register app
globalThis.main.registerApp('HubletoApp/Community/Mail', new HubletoApp());

// register react components
globalThis.main.registerReactComponent('MailTableMails', TableMails);
