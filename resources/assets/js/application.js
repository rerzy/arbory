import FieldRegistry from "./Admin/FieldRegistry";
import AdminPanel from "./Admin/AdminPanel";
import Navigator from "./Admin/Navigator/Navigator";

const adminPanel = new AdminPanel(FieldRegistry, new Navigator());

adminPanel.initialize();

export default adminPanel;

