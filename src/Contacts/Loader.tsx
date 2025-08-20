import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableContacts from "./Components/TableContacts"

// register app
globalThis.main.registerApp('HubletoApp/Community/Contacts', new HubletoApp());

// register react components
globalThis.main.registerReactComponent('ContactsTableContacts', TableContacts);
