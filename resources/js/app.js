import * as FilePond from "filepond";
window.FilePond = FilePond;

import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import FilePondPluginFileEncode from 'filepond-plugin-file-encode';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';

FilePond.registerPlugin(FilePondPluginImagePreview);
FilePond.registerPlugin(FilePondPluginFileEncode);
FilePond.registerPlugin(FilePondPluginFileValidateType);

import Cropper from 'cropperjs';
window.Cropper = Cropper;



require('./bootstrap');


require('./global.js');

require('./resources/form_elements/form_elements.js');
require('./resources/config/config_variables.js');

require('./nav/nav.js');
require('./dashboard/dashboard.js');
require('./admin/queue_monitor.js');

require('./tables/tables.js');

require('./reports/reports');

require('./email/email_list');
require('./doc_management/admin/forms/forms.js');
require('./doc_management/admin/forms/form_fields.js');

require('./doc_management/admin/checklists/checklists.js');

require('./employees/agents.js');

require('./employees/employees.js');

require('./employees/loan_officers.js');
require('./employees/in_house.js');

require('./users/users.js');


require('./doc_management/transactions/create.js');
require('./doc_management/transactions/archives.js');
require('./doc_management/transactions/escrow.js');

require('./heritage_financial/loans.js');
require('./heritage_financial/view_loan.js');
require('./heritage_financial/commission_reports');
require('./heritage_financial/manager_bonuses');
require('./heritage_financial/lenders');
require('./heritage_financial/view_lender');
require('./heritage_financial/agent_database');

require('./marketing/data/address_database.js');
require('./marketing/data/upload_list.js');
require('./marketing/data/address_database.js');
require('./marketing/schedule/schedule.js');
require('./marketing/schedule/schedule_settings.js');
require('./marketing/schedule/checklist.js');
require('./admin/system_monitor.js');
require('./notes/notes.js');
require('./tools/tools.js');



