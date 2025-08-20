import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableCampaigns from "./Components/TableCampaigns"

// register app
globalThis.main.registerApp('HubletoApp/Community/Campaigns', new HubletoApp());

// register react components
globalThis.main.registerReactComponent('CampaignsTableCampaigns', TableCampaigns);
