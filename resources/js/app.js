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



//import tippy from 'tippy.js';
require('./global.js');

require('./resources/form_elements/form_elements.js');
require('./resources/config/config_variables.js');

require('./nav/nav.js');
require('./dashboard/dashboard.js');
require('./admin/monitor.js');


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

require('./marketing/data/agent_database.js');


require('./tests/test');
