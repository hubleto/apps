import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableProjects from "./Components/TableProjects"
import TablePhases from './Components/TablePhases'
import FormDealTopMenu from './Components/FormDealTopMenu'

// register app
globalThis.main.registerApp('HubletoApp/Community/Projects', new HubletoApp());

// register react components
globalThis.main.registerReactComponent('ProjectsTableProjects', TableProjects);
globalThis.main.registerReactComponent('ProjectsTablePhases', TablePhases);

globalThis.main.registerDynamicContent('FormDeal:TopMenu', FormDealTopMenu);
