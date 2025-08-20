// How to add any React Component to be usable in Twig templates as '<app-*></app-*>' HTML tag.
// -> Replace 'MyModel' with the name of your model in the examples below

// 1. import the component
// import TableMyModel from "./Components/TableMyModel"

// 2. Register the React Component into Hubleto framework
// globalThis.main.registerReactComponent('WarehousesTableMyModel', TableMyModel);

// 3. Use the component in any of your Twig views:
// <app-warehouses-table-my-model string:some-property="some-value"></app-warehouses-table-my-model>

import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableWarehouses from "./Components/TableWarehouses"

// register app
globalThis.main.registerApp('HubletoApp/Community/Suppliers', new HubletoApp());

// register react components
globalThis.main.registerReactComponent('WarehousesTableWarehouses', TableWarehouses);