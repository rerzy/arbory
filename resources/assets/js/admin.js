import FieldRegistry from "./Admin/FieldRegistry";
import AdminPanel from "./Admin/AdminPanel";
import Navigator from './Admin/Navigator/Navigator'

export default new AdminPanel(FieldRegistry, new Navigator());
