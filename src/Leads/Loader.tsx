import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableLeads from "./Components/TableLeads"
import LeadFormActivity from "./Components/LeadFormActivity"
import TableLevels from './Components/TableLevels'

class LeadsApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.main.registerReactComponent('LeadsTableLeads', TableLeads);
    globalThis.main.registerReactComponent('LeadsFormActivity', LeadFormActivity);
    globalThis.main.registerReactComponent('LeadsTableLevels', TableLevels);
  }
}

// register app
globalThis.main.registerApp('HubletoApp/Community/Leads', new LeadsApp());
