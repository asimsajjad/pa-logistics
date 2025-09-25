<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'Front';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
$route['AdminLogin'] = 'AdminLogin/index';
// $route['AdminDashboard'] = 'AdminDashboard/index';

$route['FleetDashboard'] = 'FleetDashboard/fleetDashboard';
$route['AdminDashboard'] = 'LogisticsDashboard/logisticsDashboard';


$route['admin/products'] = 'AdminProduct/index';
$route['admin/add/products'] = 'AdminProduct/add_products';
$route['admin/product/delete/(:any)'] = 'AdminProduct/product_delete';
$route['admin/product/edit/(:any)'] = 'AdminProduct/product_edit';
$route['admin/category'] = 'AdminProduct/allcategory';
$route['admin/add/category'] = 'AdminProduct/addcategory';
$route['admin/update/category/(:any)'] = 'AdminProduct/updatecategory';
$route['admin/category/delete/(:any)'] = 'AdminProduct/deletecategory';
$route['admin/add/subcategory/(:any)'] = 'AdminProduct/subcategory';
$route['admin/all/subcategory/(:any)'] = 'AdminProduct/allsubcategory';
$route['admin/update/subcategory/(:any)'] = 'AdminProduct/updatesubcategory';
$route['admin/subcategory/delete/(:any)'] = 'AdminProduct/deletesubcategory';
$route['admin/createpages'] = 'CreatePages/index';
$route['admin/AllPages'] = 'AllPages/index';
$route['admin/alluser']='AdminDashboard/alluser';
$route['admin/adduser']='AdminDashboard/adduser';
$route['admin/user/view/(:num)'] = 'AdminDashboard/view/$1';
//$route['admin/user/delete/(:num)'] = 'AdminDashboard/userdelete/$1';
$route['admin/blog'] = 'AllPages/blog';
$route['admin/blog/add'] = 'CreatePages/addblog';
$route['admin/blog/update'] = 'CreatePages/updateblog';
$route['admin/alltags'] = 'AllPages/tags';
$route['admin/alltags/add'] = 'CreatePages/addtags';
$route['admin/alltags/update'] = 'CreatePages/updatetags';

$route['admin/vehicles'] = 'Comancontroler/vehicles';
$route['admin/vehicle/add'] = 'Comancontroler/vehicleadd';
$route['admin/vehicle/update/(:num)'] = 'Comancontroler/vehicleupdate';
$route['admin/vehicle/delete/(:num)'] = 'Comancontroler/vehicledelete';
 
$route['admin/companies'] = 'Comancontroler/companies';
$route['admin/company/add'] = 'Comancontroler/companyadd';
$route['admin/company/update/(:num)'] = 'Comancontroler/companyupdate';
$route['admin/company/delete/(:num)'] = 'Comancontroler/companydelete';
 $route['admin/company/removefile/(:num)/(:num)'] = 'Comancontroler/removefile';

$route['admin/company-address'] = 'Comancontroler/address';
$route['admin/address/add'] = 'Comancontroler/addressadd';
$route['admin/address/update/(:num)'] = 'Comancontroler/addressupdate';
$route['admin/address/delete/(:num)'] = 'Comancontroler/addressdelete';

$route['admin/cities'] = 'Comancontroler/cities';
$route['admin/city/add'] = 'Comancontroler/cityadd';
$route['admin/city/update/(:num)'] = 'Comancontroler/cityupdate';
$route['admin/city/delete/(:num)'] = 'Comancontroler/citydelete';

$route['admin/shipment-status'] = 'Comancontroler/shipmentStatus';
$route['admin/shipment-status/add'] = 'Comancontroler/shipmentStatusAdd';
$route['admin/shipment-status/update/(:num)'] = 'Comancontroler/shipmentStatusUpdate';
$route['admin/shipment-status/delete/(:num)'] = 'Comancontroler/shipmentStatusDelete';

$route['admin/locations'] = 'Comancontroler/locations';
$route['admin/location/add'] = 'Comancontroler/locationadd';
$route['admin/location/update/(:num)'] = 'Comancontroler/locationupdate';
$route['admin/location/delete/(:num)'] = 'Comancontroler/locationdelete';

$route['admin/drivers'] = 'Comancontroler/drivers';
$route['admin/driver/add'] = 'Comancontroler/driveradd';
$route['admin/driver/update/(:num)'] = 'Comancontroler/driverupdate';
$route['admin/driver/gps-location/(:num)'] = 'Comancontroler/drivergpslocation';
$route['admin/driver/delete/(:num)'] = 'Comancontroler/driverdelete';
$route['admin/dispatch/removedriverfile/(:num)/(:num)'] = 'Dispatch/removedriverfile';

$route['admin/fuel'] = 'Comancontroler/fuel';
$route['admin/fuel/add'] = 'Comancontroler/fueladd';
$route['admin/fuel/update/(:num)'] = 'Comancontroler/fuelupdate';
$route['admin/fuel/delete/(:num)'] = 'Comancontroler/fueldelete';

$route['admin/(:any)/removeotherfile/(:num)/(:num)'] = 'Comancontroler/removeOtherFile';

$route['admin/reimbursement'] = 'Comancontroler/reimbursement';
$route['admin/reimbursement/add'] = 'Comancontroler/reimbursementadd';
$route['admin/reimbursement/update/(:num)'] = 'Comancontroler/reimbursementupdate';
$route['admin/reimbursement/checkUpdate/(:num)'] = 'Comancontroler/reimbursementCheckboxUpdate';
$route['admin/reimbursement/delete/(:num)'] = 'Comancontroler/reimbursementdelete';

$route['admin/truck_supplies_request'] = 'Comancontroler/truck_supplies_request';
$route['admin/truck_supplies_request/add'] = 'Comancontroler/truck_supplies_requestadd';
$route['admin/truck_supplies_request/update/(:num)'] = 'Comancontroler/truck_supplies_requestupdate';
$route['admin/truck_supplies_request/delete/(:num)'] = 'Comancontroler/truck_supplies_requestdelete';


$route['admin/driver_trip'] = 'Billingcontroler/driver_trip';
$route['admin/driver_trip/add'] = 'Billingcontroler/drivertripadd';
$route['admin/driver_trip/update/(:num)'] = 'Billingcontroler/drivertripupdate';
$route['admin/driver_trip/delete/(:num)'] = 'Billingcontroler/drivertripdelete';

$route['admin/finance'] = 'Billingcontroler/finance';
$route['admin/finance/update/(:num)'] = 'Billingcontroler/financeupdate';
$route['admin/finance/delete/(:num)'] = 'Billingcontroler/financedelete';
$route['admin/finance/multiple_view'] = 'Billingcontroler/multiple_view';

$route['admin/get-address'] = 'Dispatch/getAddress';
$route['admin/get-companies'] = 'Dispatch/getCompanies';

$route['admin/driver_shift'] = 'Dispatch/driver_shift';
$route['admin/driver_shift/add'] = 'Dispatch/driver_shift_add';
$route['admin/driver_shift/update/(:num)'] = 'Dispatch/driver_shift_update';
$route['admin/driver_shift/delete/(:num)'] = 'Dispatch/driver_shift_delete';

$route['admin/invoice'] = 'Invoice/index';
$route['admin/invoice-pending'] = 'Invoice/invoicePending';
$route['admin/statement-of-account'] = 'Invoice/statementOfAccount';
$route['admin/dbPAFleet'] = 'AllInvoices/dbPAFleetInvoices';
$route['admin/dbPALogistics'] = 'AllInvoices/dbPALogisticsInvoices';
$route['admin/qpPAFleet'] = 'AllInvoices/qpPAFleetInvoices';
$route['admin/rtsPAFleet'] = 'AllInvoices/rtsPAFleetInvoices';
$route['admin/dbPAWarehousing'] = 'AllInvoices/dbPAWarehousingInvoices';

$route['admin/invoice/add'] = 'Invoice/invoiceAdd';
$route['admin/invoice/add/(:num)'] = 'Invoice/invoiceAdd/$1';
$route['admin/invoice/update/(:num)'] = 'Invoice/invoiceUpdate';
$route['admin/invoice/removefile/(:any)/(:num)/(:num)'] = 'Invoice/removefile';
$route['admin/invoice/ajaxdelete'] = 'Invoice/ajaxdelete';
$route['admin/invoiceEmailHistory'] = 'Invoice/invoiceEmailHistory';
$route['admin/carrierEmailHistory'] = 'Invoice/carrierEmailHistory';
//$route['admin/invoice/ajaxedit'] = 'Invoice/ajaxedit';

$route['admin/dispatch'] = 'Dispatch/index';
$route['admin/dispatch/add'] = 'Dispatch/dispatchadd';
$route['admin/dispatch/add/(:num)'] = 'Dispatch/dispatchadd/$1';
$route['admin/dispatch/upload-csv'] = 'Dispatch/uploadcsv';
$route['admin/dispatch/update/(:num)'] = 'Dispatch/dispatchupdate';
$route['admin/dispatch/delete/(:num)'] = 'Dispatch/dispatchdelete';
$route['admin/dispatch-extra/delete/(:num)'] = 'Dispatch/extradispatchdelete';
$route['admin/dispatch/ajaxdelete'] = 'Dispatch/ajaxdelete';
$route['admin/dispatch/ajaxedit'] = 'Dispatch/ajaxedit';
$route['admin/dispatch/removefile/(:num)/(:num)'] = 'Dispatch/removefile';
$route['admin/paysheet'] = 'Dispatch/paysheet';
$route['admin/download_pdf/(:any)/(:any)'] = 'Dispatch/download_pdf/$1/$2';

$route['admin/outside-dispatch'] = 'OutSideDispatch/index';
$route['admin/outside-dispatch/add'] = 'OutSideDispatch/outsideDispatchAdd';
$route['admin/outside-dispatch/add/(:num)'] = 'OutSideDispatch/outsideDispatchAdd/$1';
$route['admin/outside-dispatch/upload-csv'] = 'OutSideDispatch/uploadcsv';
$route['admin/outside-dispatch/update/(:num)'] = 'OutSideDispatch/outsideDispatchUpdate';
$route['admin/outside-dispatch-extra/delete/(:num)'] = 'OutSideDispatch/extradispatchdelete';
$route['admin/outside-dispatch/removefile/(:any)/(:num)/(:num)'] = 'OutSideDispatch/removefile';
$route['admin/outside-dispatch/ajaxdelete'] = 'OutSideDispatch/ajaxdelete';
$route['admin/outside-dispatch/ajaxedit'] = 'OutSideDispatch/ajaxedit';

$route['admin/trucking-companies'] = 'OutSideDispatch/truckingCompanies';
$route['admin/trucking-company/add'] = 'OutSideDispatch/truckingCompaniesAdd';
$route['admin/trucking-company/update/(:num)'] = 'OutSideDispatch/truckingCompaniesUpdate';
$route['admin/trucking-company/remove-file/(:num)/(:num)'] = 'OutSideDispatch/truckingCompaniesRemoveFile';
$route['admin/trucking-company/delete/(:num)'] = 'OutSideDispatch/truckingCompaniesDelete';

// Booked Under Routes
$route['admin/booked-under'] = 'BookedUnder/bookedUnder';
$route['admin/booked-under/add'] = 'BookedUnder/bookedUnderAdd';
$route['admin/booked-under/update/(:num)'] = 'BookedUnder/bookedUnderUpdate';
$route['admin/booked-under/delete/(:num)'] = 'BookedUnder/bookedUnderDelete';


$route['admin/pre_made_trips'] = 'Dispatch/pre_made_trips';
$route['admin/pre_made_trips/add'] = 'Dispatch/pre_made_tripsadd';
$route['admin/pre_made_trips/update/(:num)'] = 'Dispatch/pre_made_tripsupdate';
$route['admin/pre_made_trips/delete/(:num)'] = 'Dispatch/pre_made_tripsdelete';

$route['admin/events'] = 'Dispatch/events';
$route['admin/event/add'] = 'Dispatch/eventadd';
$route['admin/event/update/(:num)'] = 'Dispatch/eventupdate';
$route['admin/event/delete/(:num)'] = 'Dispatch/eventdelete';

$route['admin/calendar/(:num)/(:num)'] = 'Dispatch/calendar/$1/$2';
$route['admin/calendar'] = 'Dispatch/calendar'; 
$route['admin/calendar_weekly'] = 'Dispatch/calendar_weekly';
$route['admin/calendar_weekly/(:num)/(:num)/(:num)'] = 'Dispatch/calendar_weekly/$1/$2/$3';
$route['admin/calendar_day_view'] = 'Dispatch/calendar_day_view';
$route['admin/calendar_day_view/(:num)/(:num)/(:num)'] = 'Dispatch/calendar_day_view/$1/$2/$3';

$route['admin/permits'] = 'AdminControler/permits';
$route['admin/permits/add'] = 'AdminControler/permitsAdd';
$route['admin/permits/update/(:num)'] = 'AdminControler/permitsUpdate';
$route['admin/permits/delete/(:num)'] = 'AdminControler/permitsDelete';

$route['admin/insurance'] = 'AdminControler/insurance';
$route['admin/insurance/add'] = 'AdminControler/insuranceAdd';
$route['admin/insurance/update/(:num)'] = 'AdminControler/insuranceUpdate';
$route['admin/insurance/delete/(:num)'] = 'AdminControler/insuranceDelete';

$route['admin/dispatch-info'] = 'AdminControler/dispatchInfo';
$route['admin/dispatch-info/add'] = 'AdminControler/dispatchInfoAdd';
$route['admin/dispatch-info/update/(:num)'] = 'AdminControler/dispatchInfoUpdate';
$route['admin/dispatch-info/delete/(:num)'] = 'AdminControler/dispatchInfoDelete';

$route['admin/expenses'] = 'AdminControler/expenses';
$route['admin/expense/add'] = 'AdminControler/expenseAdd';
$route['admin/expense/update/(:num)'] = 'AdminControler/expenseUpdate';
$route['admin/expense/delete/(:num)'] = 'AdminControler/expenseDelete';

$route['admin/admin-user'] = 'AdminControler/adminUser';
$route['admin/admin-user/add'] = 'AdminControler/adminUserAdd';
$route['admin/admin-user/update/(:num)'] = 'AdminControler/adminUserUpdate';
$route['admin/admin-user/delete/(:num)'] = 'AdminControler/adminUserDelete';

$route['admin/equipment'] = 'AdminControler/equipment';
$route['admin/equipment/add'] = 'AdminControler/equipmentAdd';
$route['admin/equipment/update/(:num)'] = 'AdminControler/equipmentUpdate';
$route['admin/equipment/delete/(:num)'] = 'AdminControler/equipmentDelete';

$route['admin/services'] = 'AdminControler/services';
$route['admin/service/add'] = 'AdminControler/serviceAdd';
$route['admin/service/update/(:num)'] = 'AdminControler/serviceUpdate';
$route['admin/service/delete/(:num)'] = 'AdminControler/serviceDelete';

$route['admin/pre-services'] = 'AdminControler/preService';
$route['admin/pre-service/add'] = 'AdminControler/preServiceAdd';
$route['admin/pre-service/update/(:num)'] = 'AdminControler/preServiceUpdate';
$route['admin/pre-service/delete/(:num)'] = 'AdminControler/preServiceDelete';

$route['admin/accountPayable'] = 'AccountPayableController/accountPayable';
$route['admin/payableBatches'] = 'AccountPayableController/payableBatches';
$route['admin/accountReceivable'] = 'AccountReceivableController/accountReceivable';
$route['admin/receivableBatches'] = 'AccountReceivableController/receivableBatches';

$route['admin/pick-up-Information'] = 'PickUpInformation/index';
$route['admin/pick-up-Information/add'] = 'PickUpInformation/add';
$route['admin/pick-up-Information/update/(:num)'] = 'PickUpInformation/update';
$route['admin/pick-up-Information/delete/(:num)'] = 'PickUpInformation/delete';

$route['admin/drayage-equipments'] = 'DrayageEquipments/index';
$route['admin/drayage-equipments/add'] = 'DrayageEquipments/add';
$route['admin/drayage-equipments/update/(:num)'] = 'DrayageEquipments/update';
$route['admin/drayage-equipments/delete/(:num)'] = 'DrayageEquipments/delete';

$route['admin/trucking-equipments'] = 'TruckingEquipments/index';
$route['admin/trucking-equipments/add'] = 'TruckingEquipments/add';
$route['admin/trucking-equipments/update/(:num)'] = 'TruckingEquipments/update';
$route['admin/trucking-equipments/delete/(:num)'] = 'TruckingEquipments/delete';

$route['admin/factoringCompany'] = 'FactoringCompany/companies';
$route['admin/factoringCompany/add'] = 'FactoringCompany/companyadd';
$route['admin/factoringCompany/update/(:num)'] = 'FactoringCompany/companyupdate';
$route['admin/factoringCompany/delete/(:num)'] = 'FactoringCompany/companydelete';

$route['admin/warehouse'] = 'Warehouse/index';
$route['admin/materialHistory'] = 'Warehouse/materialHistory';
// $route['admin/warehouse/update/(:num)'] = 'Warehouse/update';
// $route['admin/warehouse/delete/(:num)'] = 'Warehouse/delete';

$route['admin/warehouseMaterials'] = 'Warehouse/materials';
$route['admin/warehouse/addMaterials'] = 'Warehouse/addMaterials';
$route['admin/warehouse/updateMaterials/(:num)'] = 'Warehouse/updateMaterials';
$route['admin/warehouse/deleteMaterials/(:num)'] = 'Warehouse/deleteMaterials';
$route['admin/warehouse/uploadMaterials'] = 'Warehouse/uploadMaterials';

$route['admin/warehouseInbounds'] = 'Warehouse/inbounds';
$route['admin/warehouse/addInbounds'] = 'Warehouse/addInbounds';
$route['admin/warehouse/updateInbounds/(:num)'] = 'Warehouse/updateInbounds';
$route['admin/warehouse/deleteInbounds/(:num)'] = 'Warehouse/deleteInbounds';
$route['admin/warehouse/uploadInbounds'] = 'Warehouse/uploadInbounds';
$route['admin/warehouse/internalTransfer'] = 'Warehouse/internalTransfer';


$route['admin/warehouseOutbounds'] = 'Warehouse/outbounds';
$route['admin/warehouse/addOutbounds'] = 'Warehouse/addOutbounds';
$route['admin/warehouse/updateOutbounds/(:num)'] = 'Warehouse/updateOutbounds';
$route['admin/warehouse/deleteOutbounds/(:num)'] = 'Warehouse/deleteOutbounds';
$route['admin/warehouse/uploadOutbounds'] = 'Warehouse/uploadOutbounds';

$route['admin/warehouseLog'] = 'Warehouse/warehouseLogs';

$route['admin/paWarehouse'] = 'WarehouseDispatch/index';
$route['admin/paWarehouseAdd'] = 'WarehouseDispatch/paWarehouseAdd';
$route['admin/paWarehouseAdd/(:num)'] = 'WarehouseDispatch/paWarehouseAdd/$1';
$route['admin/paWarehouse/update/(:num)'] = 'WarehouseDispatch/paWarehouseUpdate';
$route['admin/paWarehouseUpdate/deletePAWarehouse/(:num)'] = 'WarehouseDispatch/deletePAWarehouse';
$route['admin/paWarehouse/removefile/(:any)/(:num)/(:num)'] = 'WarehouseDispatch/removefile';
$route['admin/paWarehouse/ajaxedit'] = 'WarehouseDispatch/ajaxedit';
$route['admin/paWarehouse-extra/delete/(:num)'] = 'WarehouseDispatch/extradispatchdelete';
$route['admin/paWarehouse/ajaxdelete'] = 'WarehouseDispatch/ajaxdelete';
$route['admin/paWarehouse/upload-csv'] = 'WarehouseDispatch/uploadcsv';

$route['admin/paWarehouseAddDemo'] = 'WarehouseDispatch/paWarehouseAddDemo';
$route['admin/paWarehouseUpdateDemo'] = 'WarehouseDispatch/paWarehouseUpdateDemo';

$route['admin/warehouseServices'] = 'Comancontroler/warehouseServices';
$route['admin/warehouseServices/add'] = 'Comancontroler/warehouseServicesAdd';
$route['admin/warehouseServices/update/(:num)'] = 'Comancontroler/warehouseServicesUpdate';
$route['admin/warehouseServices/delete/(:num)'] = 'Comancontroler/warehouseServicesDelete';

$route['admin/warehouseAddress'] = 'Comancontroler/warehouseAddress';
$route['admin/address/warehouseAdd'] = 'Comancontroler/warehouseAddressAdd';
$route['admin/address/warehouseUpdate/(:num)'] = 'Comancontroler/warehouseAddressUpdate';
$route['admin/address/warehouseDelete/(:num)'] = 'Comancontroler/warehouseAddressDelete';
$route['admin/address/warehouseAddSublocation/(:num)'] = 'Comancontroler/warehouseAddSublocation';

$route['admin/(:any)/removeSingleDocument/(:num)/(:num)'] = 'AdminControler/removeSingleDocument';

$route['api/driver-login'] = 'Appapi/driverLogin';
