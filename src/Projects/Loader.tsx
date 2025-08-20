import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableProjects from "./Components/TableProjects"
import TablePhases from './Components/TablePhases'
import request from "@hubleto/react-ui/core/Request";

// register app
globalThis.main.registerApp('HubletoApp/Community/Projects', new HubletoApp());

// register react components
globalThis.main.registerReactComponent('ProjectsTableProjects', TableProjects);
globalThis.main.registerReactComponent('ProjectsTablePhases', TablePhases);

// miscellaneous
globalThis.main.getApp('HubletoApp/Community/Deals').addFormHeaderButton(
  'Create project',
  (form: any) => {
    request.get(
      'projects/api/create-from-deal',
      {idDeal: form.state.record.id},
      (data: any) => {
        if (data.status == "success") {
          globalThis.window.open(globalThis.main.config.projectUrl + '/projects/' + data.idProject);
        }
      }
    );
  }
)