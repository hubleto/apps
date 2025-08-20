import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import ProductsTableProducts from "./Components/TableProducts";

// register app
globalThis.main.registerApp('HubletoApp/Community/Products', new HubletoApp());

// register react components
globalThis.main.registerReactComponent('ProductsTableProducts', ProductsTableProducts);
