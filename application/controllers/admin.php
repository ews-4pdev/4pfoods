<?php

class Admin extends MY_Controller
{
    private static $_skipAuth = array(

        'login', 'setpass', 'newpass', 'logout'

    );

    private static $_driverAccess = array(

        'deliveries', 'logout'

    );

    private static $_navData = array(

        'dashboardNav' => array(

            'title' => 'Dashboard',

            'url' => '/admin',

            'driverAccess' => false

        ),

        'categoryNav' => array(

            'title' => 'Categories',

            'url' => '/admin/categories',

            'driverAccess' => false

        ),

        'bagNav' => array(

            'title' => 'Bags',

            'url' => '/admin/bags',

            'driverAccess' => false,
            'id' => 'bags_nav'

        ),

        'productsNav' => array(

            'title' => 'Products',

            'url' => '/admin/products',

            'driverAccess' => false

        ),

        'itemNav' => array(

            'title' => 'Food Items',

            'url' => '/admin/items',

            'driverAccess' => false

        ),

        'supplierNav' => array(

            'title' => 'Suppliers',

            'url' => '/admin/suppliers',

            'driverAccess' => false

        ),

        'customersNav' => array(

            'title' => 'Customers',

            'url' => '/admin/customers',

            'driverAccess' => false

        ),

        'paymentsNav' => array(

            'title' => 'Payments',

            'url' => '/admin/payments',

            'driverAccess' => false

        ),

        'discountsNav' => array(

            'title' => 'Discounts',

            'url' => '/admin/discounts',

            'driverAccess' => false

        ),

        'approvalNav' => array(

            'title' => 'Approval',

            'url' => '/admin/approval',

            'driverAccess' => false

        ),

        'reportsNav' => array(

            'title' => 'Reports',

            'url' => '/admin/reports',

            'driverAccess' => false

        ),

        'exportNav' => array(

            'title' => 'Export Data',

            'url' => '/admin/exportdata',

            'driverAccess' => false

        ),

        'deliveryNav' => array(

            'title' => 'Delivery Management',

            'url' => '/admin/deliveries',

            'driverAccess' => true

        ),

        'cronNav' => array(

            'title' => 'Cron Log',

            'url' => '/admin/cron',

            'driverAccess' => false

        )

    );

    function __construct()
    {

        parent::__construct();

        //  Require login unless URL falls into specific category
        if (!SKIP_LOGIN && !in_array($this->uri->segment(2), self::$_skipAuth)) {
            //  Not logged in
            if (!$this->isAdminLoggedIn())
                redirect('/admin/login');
            //  Driver-only pages
            if ($this->hasAdminAccess() == 'Driver' && !in_array($this->uri->segment(2), self::$_driverAccess))
                redirect('/admin/deliveries');
        }
    }

    function index(){

        if (!$this->isAdminLoggedIn()){
            redirect('/admin/login');
        }

        $recentSignups = UserQuery::getRecentSignups();


        $data = array(

            'currentPage' => 'dashboardNav',

            'totalDonatedOrders' => OrderQuery::getTotalDonated(),

            'donatedOrdersThisWeek' => OrderQuery::getDonatedLastWeek(),

            'totalCustomers' => UserQuery::getTotalCustomers(),

          'totalRevenue' => PaymentQuery::getTotalEarned(),

            'nOrders' => OrderQuery::getFullCount(),

            'newSubscriptions' => SubscriptionQuery::getRecent(),

            'recentSignups' => $recentSignups

        );

        $this->_loadWrappedPage('dashboard', $data);


    }

    private function _loadWrappedPage($view, $data)
    {


        $data['altCSS'] = 'admin';

        $data['error'] = ($this->session->flashdata('error')) ? $this->session->flashdata('error') : '';

        $data['navData'] = self::$_navData;

        $data['_get_build'] = 'admin';


        //  Change nav data for driver

        if ($this->hasAdminAccess() == 'Driver') {

            foreach ($data['navData'] as $key => $info) {

                if (!$info['driverAccess'])

                    unset($data['navData'][$key]);

            }

        }


        $this->load->view('component/header', $data);

        $this->load->view('component/admin-header-inner', $data);

        $this->load->view('admin/' . $view, $data);

        $this->load->view('component/admin-footer-inner', $data);

        $this->load->view('component/footer', $data);


    }

    function logout()
    {


        $this->session->unset_userdata('iAdmin');

        redirect('/admin/login');


    }

    function login()
    {
        if ($this->isAdminLoggedIn())

            redirect('/admin');


        $data = array(

            '_get_build' => 'admin'

        );

        $this->_loadSimplePage('admin/login', $data);


    }

    function setpass()
    {


        $data = array();

        $this->_loadSimplePage('admin/setpass', $data);


    }

    function forgotpass()
    {


        $data = array();

        $this->_loadSimplePage('admin/forgotpass', $data);


    }

    function cron()
    {


        $aLogs = CronLogQuery::create()
            ->orderByCreatedAt('DESC')
            ->find();

        $data = array(

            'aLogs' => $aLogs,

            'currentPage' => 'cronNav'

        );

        $this->_loadWrappedPage('cronlog', $data);


    }

    function loginto($id)
    {


        $oUser = UserQuery::create()->findPk($id);


        if ($oUser->getAdminAccess() != NULL)

            $this->redirectWithError('InvalidUserStatus', '');


        $this->session->set_userdata('uid', $id);

        $this->redirectWithMessage('You are logged in as ' . $oUser->getFullName(), '/gateway/login');


    }

    function customers($id = NULL)
    {
        if ($id) {
            $oUser = UserQuery::create()->findPk($id);

            $aTax = StateQuery::getTaxList();

            if (!$oUser)
                $this->redirectWithError('Invalid user.', '/admin/customers/');


            $data = array(

                'aSites' => DeliverySiteQuery::getValidSitesList(),

                'aDoorSteps' => DoorStepQuery::getValidSitesList(),

                'oUser' => $oUser,

                'atax' => $aTax,

                'orderExist' => $oUser->hasPendingOrders(),

                'aOrders' => $oUser->getPaidOrders(),

                'unPaidOrders' => $oUser->getUnpaidOrders(),

                'aSubs' => $oUser->getSubscriptions(),

                'currentPage' => 'customersNav',

                'jsApp' => $this->jsAppEncode('Admin', 'viewcustomer', array(

                    'iUser' => $oUser->getId()

                ))

            );
        
            $this->_loadWrappedPage('viewcustomer', $data);


            //  Customer list

        } else {

            $aUsers = UserQuery::create()
                ->filterByAdminAccess(NULL)
                ->filterByIsArchived(0)
                ->filterByCustomerStatus('Active')
                ->orderByLastName('ASC')
                ->find();

            $aArchived = UserQuery::create()
                ->filterByAdminAccess(NULL)
                ->filterByIsArchived(1)
                ->orderByLastName('ASC')
                ->find();

            $data = array(

                'currentPage' => 'customersNav',

                'jsApp' => $this->jsAppEncode('Admin', 'customers'),

                'aUsers' => $aUsers,

                'aArchived' => $aArchived

            );

            $this->_loadWrappedPage('customers', $data);

        }


    }

    function getEmailList()
    {


        $fromDate = date('Y-m-d', strtotime('+2 Days'));

        $toDate = date('Y-m-d', strtotime('+9 Days'));

        $data = OrderQuery::getTheoretical($fromDate, $toDate);

        $list = array();

        foreach ($data['contactList'] as $id => $info)

            $list[] = implode(',', array_values($info));

        $fileData = implode("\n", $list);


        header('Content-Disposition: attachment; filename=customerlist.csv');

        header('Content-type: text/csv');

        echo $fileData;

        exit();


    }

    function discounts($id = NULL)
    {


        $aDiscounts = DiscountQuery::create()
            ->orderByCode('ASC')
            ->find();

        $data = array(

            'aDiscounts' => $aDiscounts,

            'currentPage' => 'discountsNav'

        );

        $this->_loadWrappedPage('discounts', $data);


    }

    function products($id = NULL)
    {
        if ($id) {

            $oProduct = ProductQuery::create()
                ->joinWith('Product.ProductCategory')
                ->setFormatter('PropelArrayFormatter')
                ->findPk($id);


            $aCategories = ProductCategoryQuery::getList();

            if (!$oProduct)

                $this->redirectWithError('/admin/products', 'Invalid product.');

            $data = array(

                'oProduct' => $oProduct,

                'aCategories' => $aCategories,

                'currentPage' => 'productsNav'

            );

            $this->_loadWrappedPage('viewproduct', $data);

        } else {

            $aCategories = ProductCategoryQuery::getList();

            $aSizes = ProductPeer::getValueSets()['products.size'];

            $aProducts = ProductQuery::create()
                ->filterByIsPublished(true)
                ->joinWith('Product.ProductCategory')
                ->orderBy('title', 'ASC')
                ->setFormatter('PropelArrayFormatter')
                ->find();


            $data = array(

                'aProducts' => $aProducts,

                'aCategories' => $aCategories,

                'aSizes' => $aSizes,

                'currentPage' => 'productsNav'

            );

            $this->_loadWrappedPage('products', $data);

        }
    }

    function bags($date = null)
    {

        $data = [
            'date' => (!empty($date)) ? DateTime::createFromFormat('m-d-Y', $date)->format('m-d-Y') : 0,
            'jsApp' => $this->jsAppEncode('Bag', 'bags'),
            'currentPage' => 'bagNav',
            'alreadyCreatedBags' => BagsQuery::getCurrentMonthBagsDates(),
            'expectedDates' => DeliverySiteQuery::getDays()
        ];

        $temp = null;
        if ($date) {
            $date1 = DateTime::createFromFormat('m-d-Y', $date);
            $data['aItems'] = ItemsQuery::getJsonItems();
            $data['allCats'] = ProductCategoryQuery::getAllCatWithProducts(  );
            $temp = BagsQuery::arePublished($date1);

            if (($temp['total'] - $temp['published']) > 0) {
                $data['published'] = 1;
            } elseif (($temp['total'] - $temp['sync']) > 0) {
                $data['published'] = 0;
            }
        }

        if ( isset($temp['lock']) && $temp['lock'] > 0 ) {
            $this->_loadWrappedPage('lockedbags', $data);
        } else {
            $this->_loadWrappedPage('bags', $data);
        }
    }

    function changeItemStatus($input)
    {

        if (empty($input)) {
            throwSingleError('InvalidInput');
        }

        $item = ItemsQuery::create()->findPk($input);

        if (!$item) {
            throwSingleError('InvalidInputID');
        }

        ($item->getactive() == 0) ? $item->setactive(1) : $item->setactive(0);

        $item->save();

        $this->items();


    }

    function items()
    {
        $data = array(
            'aItems' => ItemsQuery::getItems(),
            'aCategories' => ProductCategoryQuery::getList(),
            'aSuppliers' => SuppliersQuery::getList(),
            'jsApp' => $this->jsAppEncode('Admin', 'items'),
            'currentPage' => 'itemNav'
        );
        $this->_loadWrappedPage('items', $data);
    }

    function categories($id = null)
    {

        if ($id) {

            $aCategory = ProductCategoryQuery::create()->findPk($id);

            if (!$aCategory) {
                $this->redirectWithError('/admin/categories', 'Invalid category.');
            }


            $data = [

                'aCategory' => $aCategory,

                'currentPage' => 'categoryNav',

                'jsApp' => $this->jsAppEncode('Admin', 'viewcategory', array(

                    'iCategory' => $aCategory->getId()

                ))

            ];

            $this->_loadWrappedPage('viewcategory', $data);

        } else {

            $aCategories = ProductCategoryQuery::create()->find();

            $data = array(
                'aCategories' => $aCategories,
                'jsApp' => $this->jsAppEncode('Admin', 'categories'),
                'currentPage' => 'categoryNav'
            );

            $this->_loadWrappedPage('categories', $data);

        }


    }

    function payments($id = NULL)
    {


        if ($id) {

            $oPayment = PaymentQuery::create()->findPk($id);

            if (!$oPayment)

                $this->redirectWithError('/admin/payments', 'Invalid payment.');

            $data = array(

                'oPayment' => $oPayment,

                'oUser' => $oPayment->getUser(),

                'currentPage' => 'paymentsNav',

                'jsApp' => $this->jsAppEncode('Admin', 'viewpayment', array(

                    'iPayment' => $oPayment->getId()

                ))

            );
            $this->_loadWrappedPage('viewpayment', $data);

        } else {

            $aPayments = PaymentQuery::create()
                ->filterByCreatedAt(array('min' => date('Y-m-d', strtotime('-3 Months'))))
                ->orderByCreatedAt('DESC')
                ->useUserQuery()
                    ->orderByLastName('ASC')
                ->endUse()
                ->find();

            $newPaymentArray = [];

            foreach ( $aPayments as $oPayment ) {
                $newPaymentArray[] = [
                    'Payment_ID' => $oPayment->getId(),
                    'Customer_ID' => $oPayment->getUser()->getId(),
                    'Customer' => $oPayment->getCustomerLastName().', '.$oPayment->getCustomerFirstName(),
                    'Amount_Paid' => money($oPayment->getAmountPaid()),
                    'Tax_Paid' => money($oPayment->getTax()),
                    'Deliver_Charge' => money( $oPayment->getDeliverycharge() ),
                    'Amount_Refunded' => money( $oPayment->getRefundedTotal() ),
                    'Payment_Status' => $oPayment->getStatus(),
                    'Processed' => $oPayment->getCreatedAt('M, d, Y'),
                    'Refund' => json_encode( array('iPayment' => $oPayment->getId(), 'maxAmount' => $oPayment->getRefundableAmount()) )
                ];
            }

            $data = array(
                'aPayments' => $newPaymentArray,
                'jsApp' => $this->jsAppEncode('Admin', 'payments'),
                'currentPage' => 'paymentsNav'
            );
            $this->_loadWrappedPage('payments', $data);
        }


    }

    function exportdata()
    {
        require_once(APPPATH . 'helpers/StatHelper.php');

        $statuses = ['Active', 'AddressPending', 'Deferred', 'Suspended'];

        $aSites = DeliverySiteQuery::create()->orderByNickname('ASC')->find();

        $aProducts = ProductQuery::create()->filterByIsPublished(true)->orderByTitle('ASC')->find();

        $data = array(

            'statuses' => $statuses,

            'aSites' => $aSites,

            'aProducts' => $aProducts,

//            'orderStatus' => OrderPeer::getValueSet('orders.status'),

            'currentPage' => 'reportsNav'

        );

        $this->_loadWrappedPage('newreports', $data);


    }

    function reports($id = NULL)
    {
        $fromDate = date('Y-m-d', strtotime('+2 Days'));
        $toDate = date('Y-m-d', strtotime('+9 Days'));
        $order = new OrderQuery();
        $aSkipped = $order->getSkippedOrders(date('Y-m-d'), date('Y-m-d', strtotime('+3 Months')));

        $data = array(

            'currentPage' => 'reportsNav',

            'aSkipped' => $aSkipped,

            'outForDelivery' => $order->getOutForDeliveryOnDate(),

            'queued' => $order->getQueued(),

            'theoretical' => $order->getTheoretical($fromDate, $toDate)

        );


        $this->_loadWrappedPage('reports', $data);


    }

    function deliveries($id = NULL)
    {


        $aStates = StateQuery::getList();

        $aDrivers = UserQuery::getDrivers();

        $aSites = DeliverySiteQuery::create()
            ->filterByIsPublished(1)
            ->orderById('ASC')
            ->find();

        $aDoorSteps = DoorStepQuery::create()
                        ->filterByIsPublished(1)
                        ->orderById('ASC')
                        ->find();

        $aDeliveries = DeliveryQuery::create()
            ->filterByDeliveryDate(array('min'=> time() - 90 *24 * 60 *60 ))
            ->filterByStatus(array('Pending', 'Delivered'))
            ->find();

        $data = array(

            'aDrivers' => $aDrivers,

            'aDeliveries' => $aDeliveries,

            'aStates' => $aStates,

            'isDriver' => ($this->hasAdminAccess() == 'Driver'),

            'currentPage' => 'deliveryNav',

            'aSites' => $aSites,

            'aDoorSteps' => $aDoorSteps

        );

        $this->_loadWrappedPage('deliveries', $data);


    }

    function approval()
    {

        $aUsers = UserQuery::getPendingAddresses();

        $aStates = StateQuery::getList();

        $aSites = DeliverySiteQuery::create()->find();

        $data = array(

            'aStates' => $aStates,

            'aSites' => $aSites,

            'aUsers' => $aUsers,

            'currentPage' => 'approvalNav'

        );

        $this->_loadWrappedPage('approval', $data);


    }

    function suppliers()
    {

        $aSuppliers = new SuppliersQuery;

        $data = array(

            'activeSuppliers' => $aSuppliers->getSuppliersByStatus(true),
            'inActiveSuppliers' => $aSuppliers->getSuppliersByStatus(false),
            //Revision end By EWS

            'jsApp' => $this->jsAppEncode('Admin', 'suppliers'),

            'currentPage' => 'supplierNav'

        );

        $this->_loadWrappedPage('suppliers', $data);


    }

    function preport()
    {
        $input = $this->input->post();

        if (isset($input['start']) && isset($input['end'])) {
            $input['start'] = DateTime::createFromFormat('m-d-Y', $input['start']);
            $input['end'] = DateTime::createFromFormat('m-d-Y', $input['end']);

            $reports = (new UserBagQuery())->getOrderedItemsFromRange($input, null);

            $filename = $input['start']->format('d-F').' to '.$input['end']->format('d-F Y').' POHelper.csv';
            $file = fopen('temp/'.$filename, 'w') or die('Cant create file' . $filename);
            fputcsv($file, ['Item Name', 'Points']);
            foreach ($reports as $report) {
                fputcsv($file, $report);
            }

            fclose($file);
            $this->getCsvFile($filename);

        }
    }

    function changedBag()
    {
        $date = $this->input->post('date');
        if(!empty($date)){
            $filename = UserQuery::createCsvOfChangedBag($date);
            $this->getCsvFile($filename);
        }
    }

    function getCsvFile($file = null)
    {
        if(!$file){ $file = $this->input->post('file'); }
        $file = 'temp/'.$file;
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            unlink($file);
            exit;
        }
    }

    function deliveryManifest(){
        $input = $this->input->post();

        if (isset($input['start']) && isset($input['end'])) {
            $reports = (new OrderQuery())->getDeliveryManifestReport($input);

            // Create total items for header
            $csvKeys = [
                'Delivery Schedule', 'Product',  'Stop #', 'Delivery Site', 'First Name', 'Last Name',
                'Email', 'Phone', 'Delivery Site Notes', 'SiteAddress1'
            ];

            for( $i = 1; $i <= $reports['max']['biggestValue']; $i++ ){
                $csvKeys[] = 'item '.$i;
            }
            unset($reports['max']);
            $filename = 'deliveryManifest.csv';

            $file = fopen('temp/'.$filename, 'w') or die('Cant create file' . $filename);
            fputcsv($file, $csvKeys);
            foreach ($reports as $report) {
                foreach ($report as $item) {
                    fputcsv($file, $item);
                }
            }

            fclose($file);
            $this->getCsvFile($filename);

        }
    }

    function generatesaletaxreport() {

        $from_date = $this->input->post('fromdate');
        $to_date = $this->input->post('todate');

        $from_date = substr($from_date,6,4)."-".substr($from_date,0,2)."-".substr($from_date,3,2);
        $to_date = substr($to_date,6,4)."-".substr($to_date,0,2)."-".substr($to_date,3,2);


        $download_filename ="4pFoodsSalesTaxReportDownloadedOn_".date("Ymd").".csv";
        $aPayments = PaymentQuery::create()
            ->filterByTax(0, Criteria::GREATER_THAN)
            ->useOrderQuery()
                ->filterByPaidAt(['min' => $from_date, 'max' => $to_date])
            ->endUse()
            ->distinct()
            ->find();

        $header =['Payment ID','Status','Created On', 'Tax', 'Delivery Charge', 'Amount Paid', 'Products Names', 'FirstName','LastName','Email'];

        $file = fopen('temp/'.$download_filename, 'w') or die('Cant create file' . $download_filename);
        fputcsv( $file, $header );
        foreach ($aPayments as $oPayment ) {
            $items = [
                $oPayment->getId(),
                $oPayment->getStatus(),
                $oPayment->getCreatedAt(),
                $oPayment->getTax(),
                $oPayment->getDeliverycharge(),
                $oPayment->getAmountPaid(),
                $oPayment->getProductsName(),
                $oPayment->getUser()->getFirstName(),
                $oPayment->getUser()->getLastName(),
                $oPayment->getUser()->getEmail()
            ];
            fputcsv( $file, $items );
        }
        fclose($file);
        $this->getCsvFile($download_filename);
    }

    function driverManifest(){
        $input = $this->input->post();
        $date = DateTime::createFromFormat('m-d-Y', $input['date']);
        if(!$date)
            throwSingleError('InvalidDate');

        $this->getCsvFile( DeliveryQuery::getDeliveriesCSVForDate( $date ) );
    }

    function generatereport()
    {


        if (!($type = $this->input->post('type')))

            die('Invalid post submission.');


        if (!($report = $this->input->post('report')))

            die('Invalid post submission.');


        require_once(APPPATH . 'helpers/StatHelper.php');


        switch ($type) {

            case StatHelper::TYPE_STATUS:

                if ($this->input->post('status') == 'All') {

                    $statType = StatHelper::TYPE_ALL;

                    $options = [];

                } else {

                    $statType = StatHelper::TYPE_STATUS;

                    $options = ['status' => $this->input->post('status')];

                }

                break;

            case StatHelper::TYPE_ALL_ORDERS:
            case StatHelper::TYPE_QUEUED_ORDERS:

            case StatHelper::TYPE_ORDER_FORECAST:

                $statType = $type;

                $options = [];

                if ($this->input->post('SiteId') != 'All')

                    $options['SiteId'] = $this->input->post('SiteId');

                if ($this->input->post('ProductId') != 'All')

                    $options['ProductId'] = $this->input->post('ProductId');

                if ($this->input->post('Allergies') != 'All')

                    $options['Allergies'] = $this->input->post('Allergies');

                break;

        }


        $helper = new StatHelper($report, $statType, $options);

        $file = $helper->getCSV();


        header('Content-Disposition: attachment; filename=report.csv');

        header('Content-type: text/csv');

        echo $file;

        exit();


    }

    function migrateDeliveryChargeFromOrderToPayment(){
        $sql = "SELECT * FROM orders
                WHERE payment_id IS NOT NULL  AND deliveryCharge > 0
                GROUP BY payment_id
                ORDER  BY payment_id ASC;";

        $con = Propel::getConnection();
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $con->prepare($sql);
        $stmt->execute();

        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ( $orders as $order ) {
            $oPayment = PaymentQuery::create()->findPk( $order['payment_id'] );
            $oPayment->setDeliverycharge( $order['deliveryCharge'] )->save();
        }
    }

    function changeStateId(){
        $aUsers = UserQuery::create()
                            ->filterByCustomerStatus('Active')
                            ->filterByIsConfirmed(true)
                            ->filterByDefaultDeliverySiteId(null, Criteria::ISNOTNULL)
                            ->filterByIsArchived(false)
                            ->find();

        foreach ( $aUsers as $oUser ) {

            $oUser->setStateId(
                $oUser->getDefaultDeliverySite()->getState()->getId()
            )->save();
        }
    }

    function createPayments($date){
        if(ENVIRONMENT != 'development')
            return false;
        $cron = CRON.'cron.php';
        exec("php  $cron executeCharges $date");
    }
    
    function createOrders($date){
        $path = CRON."testCron.php";
        //$helper = new CronHelper('createOrders', $date);
        //$helper->createOrders();
        exec("php  $path createOrders $date > /dev/null 2>&1 &");
    }

    function createDeliveries($date){
        $path = CRON."testCron.php";
        exec("php  $path createDeliveries $date > /dev/null 2>&1 &");

    }

}
