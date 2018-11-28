<?php


class StatHelper
{


    //  Customers

    const TYPE_SAMPLE = 1;

    const TYPE_ALL = 2;

    const TYPE_STATUS = 3;

    const TYPE_SITE = 4;


    //  Orders

    const TYPE_ALL_ORDERS = 5;

    const TYPE_QUEUED_ORDERS = 6;

    const TYPE_ORDER_FORECAST = 7;


    //  Report types

    const REPORT_CUSTOMER = 1;

    const REPORT_ORDER = 2;


    //  Settings

    const FORECAST_LENGTH = 7;


    /**
     * Private storage for report data
     *
     * @var array
     */

    private $_data = [ ];


    /**
     * Constructor
     *
     * @param int $type Use static class constants
     * @param array Options to use for filters
     * @return $this
     */

    public function __construct( $report, $type, $options = [ ] )
    {


        $this->setReport( $report, $type, $options );


    }


    /**
     * Choose type and pass filter params to generate report
     *
     * @param int $report Type of result set
     * @param int $type Use static class constants
     * @param array Options to use for filters
     * @return void
     */

    private function setReport( $report, $type, $options = [ ] )
    {


        switch ( $type ) {

            case self::TYPE_ALL:

                $fn = 'getAll';

                break;

            case self::TYPE_STATUS:

                $fn = 'getByStatus';

                break;

            case self::TYPE_SAMPLE:

                $fn = 'getSampleReport';

                break;

            case self::TYPE_SITE:

                $fn = 'getSiteFilter';

                break;

            case self::TYPE_ALL_ORDERS:

                $fn = 'getAllOrders';

                break;

            case self::TYPE_QUEUED_ORDERS:

                $fn = 'getQueuedOrders';

                break;

            case self::TYPE_ORDER_FORECAST;

                $fn = 'getOrderForecast';

                break;

        }


        if ( $report == self::REPORT_CUSTOMER ) {

            $customers = $this->$fn( $options );

            $this->_data = self::transform( $customers );

        } else if ( $report == self::REPORT_ORDER ) {

            $orders = $this->$fn( $options );

            $this->_data = self::transformOrders( $orders );

        }


    }

    /**
     * Transform an array of customer objects into printable data
     *
     * @param array <User> $customers
     * @return array
     */

    private static function transform( $customers, $skipGraph = false )
    {


        $remove = [

            'Password', 'Hash', 'IsConfirmed', 'IsArchived',

            'StateId', 'CountryId', 'DefaultDeliverySiteId'

        ];

        $return = [ ];

        foreach ( $customers as $oUser ) {


            //  Start with basic info

            if( $oUser->getId() == 1572 )
            {
                echo 'here';
            }
            $base = $oUser->toArray();
            $base['Address1'] = str_replace( ',', ' ', $base['Address1'] );
            $base['Address2'] = str_replace( ',', ' ', $base['Address2'] );
            $base['Parking'] = str_replace( ',', ' ', $base['Parking'] );
            $base['DeliverySiteNotes'] = str_replace( ',', ' ', $base['DeliverySiteNotes'] );
            $base['DietaryRestrictions'] = str_replace( ',', ' ', $base['DietaryRestrictions'] );

            //  Add calculated info

            $base[ 'State' ] = $oUser->getStateAbbrev();

            $base[ 'Country' ] = $oUser->getCountry()->getName();

            $base[ 'Revenue' ] = money( $oUser->getTotalRevenue() );

            $base[ 'MostRecentPaymentDate' ] = $oUser->getMostRecentPaymentDate();


            //  Cancelation information - this alg is a doesy...

            $base[ 'CanceledAt' ] = $oUser->getCanceledAt();


            //  Add delivery site info
           

            if( $oUser->getDoorstep() ){
                $oSite = [
                    'Address1' => $oUser->getAddress1(),
                    'Address2' => $oUser->getAddress2(),
                    'City' => $oUser->getCity(),
                    'Zip' => $oUser->getZip(),
                    'DefaultDeliveryDay' => DoorStepQuery::create()->findOneByZip( $oUser->getZip() )->getDefaultDay()
                ];
            }else{
                $oSite = $oUser->getDefaultDeliverySite();
                if( $oSite )
                    $oSite = $oSite->toArray();
            }

            $siteFields = [

                'Address1', 'Address2', 'City', 'Zip',

                'DefaultDeliveryDay'

            ];

            foreach ( $siteFields as $field )

                $base[ 'Site' . $field ] = ( $oSite ) ? $oSite[ $field ] : NULL;


            if ( !$skipGraph ) {
                $days = [ 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' ];

                $subs = [ ];

                foreach ( $oUser->getSubscriptions() as $oSub ) {

                    if ( $oSub->getStatus() == 'Active' )

                        $subs[] = $oSub->getDeliveryDay();
                }

                foreach ( $days as $day )

                    $base[ 'DeliveryOn' . $day ] = ( in_array( $day, $subs ) ) ? 'Y' : 'N';
            }


            //  Remove omitted fields

            foreach ( $remove as $field )

                unset( $base[ $field ] );


            $id = $base[ 'Id' ];


            //  Make sure commas are escaped by adding quotes around everything

            foreach ( $base as $key => $value )

                $base[ $key ] = qq( $value );


            $return[ $id ] = $base;


        }


        return $return;


    }

    /**
     * Transform orders into readable data, not including adding customers
     */

    private static function transformOrders( $orders )
    {


        $remove = [

            'DeliverySiteId', 'SubscriptionId', 'DeliveredAt', 'SkippedAt',

            'DonatedAt', 'PaymentId', 'DeliveryId',

        ];

        $return = [ ];

        $aiUser = [ ];

        foreach ( $orders as $order ) {


            //  If this is a real order

            if ( is_object( $order ) ) {


                //  Start with basics

                $item = $order->toArray();


                //  Info from other tables

                $item[ 'Product' ] = $order->getSubscription()->getProduct()->getTitle();

                $item[ 'ProductCurrentPrice' ] = $order->getSubscription()->getProduct()->getPrice();

                $item[ 'DeliverySite' ] = ( $order->getSubscription()->getUser()->getDoorstep() ) ? $order->getSubscription()->getUser()->getFullAddressWithOutComma() : $order->getDeliverySite()->getNickname();


                $item[ 'UserId' ] = $order->getSubscription()->getUserId();


                //  If the order is theoretical

            } else

                $item = $order;


            //  Remove any unwanted fields

            foreach ( $remove as $field )

                unset( $item[ $field ] );


            //  Index and pack

            $aiUser[] = $item[ 'UserId' ];

            $return[] = $item;


        }


        $aiUser = array_unique( $aiUser );


        return self::addCustomers( $return, $aiUser );


    }

    /**
     * Add customer information to set of orders
     *
     * @param array $orders Indexed array of Orders
     * @param array <int> $aiUser Array of user IDs for pulling users
     */

    private static function addCustomers( $orders, $aiUser )
    {


        $aUsers = UserQuery::create()
            ->filterById( $aiUser )
            ->find();

        $userTable = self::transform( $aUsers, $skipGraph = true );


        foreach ( $orders as &$order ) {

            if ( !isset( $userTable[ $order[ 'UserId' ] ] ) )

                continue;

            $userData = $userTable[ $order[ 'UserId' ] ];

            foreach ( $userData as $key => $value ) {

                if ( $key != 'Id' )

                    $order[ $key ] = $value;

            }

        }


        return $orders;


    }

    /**
     * REPORT: Get all orders
     *
     * @return array
     */

    private static function getAllOrders( $options = [ ] )
    {


        $q = OrderQuery::create();

        return self::addOrderFilters( $q, $options )->find();


    }

    /**
     * REPORT: Add filters to order query
     *
     * @return array
     */

    private static function addOrderFilters( $q, $options = [ ] )
    {


        if ( isset( $options[ 'SiteId' ] ) )

            $q = $q->filterByDeliverySiteId( $options[ 'SiteId' ] );


        if ( isset( $options[ 'ProductId' ] ) ) {

            if ( get_class( $q ) == 'OrderQuery' ) {

                $q = $q->useSubscriptionQuery()
                    ->filterByProductId( $options[ 'ProductId' ] )
                    ->endUse();

            } else

                $q = $q->filterByProductId( $options[ 'ProductId' ] );

        }


        if ( isset( $options[ 'Allergies' ] ) ) {

            $criteria = ( $options[ 'Allergies' ] == 'Has' ) ? Criteria::NOT_EQUAL : Criteria::EQUAL;

            if ( get_class( $q ) == 'OrderQuery' ) {

                $q = $q->useSubscriptionQuery()
                    ->useUserQuery()
                    ->filterByDietaryRestrictions( '', $criteria )
                    ->endUse()
                    ->endUse();

            } else

                $q = $q->useUserQuery()
                    ->filterByDietaryRestrictions( '', $criteria )
                    ->endUse();

        }


        return $q;


    }

    /**
     * REPORT: Get Order Forecast
     *
     * @return array
     */

    private static function getOrderForecast( $options = [ ] )
    {


        //  Delivery date is within

        $fromDate = date( 'Y-m-d' );

        $toDate = date( 'Y-m-d', strtotime( sprintf( '+%d Days', self::FORECAST_LENGTH - 1 ) ) );


        $return = [ ];

        $current = $fromDate;


        while ( strtotime( $current ) <= strtotime( $toDate ) ) {


            //  What day of the week is this?

            $weekday = date( 'D', strtotime( $current ) );


            //  Get all active subscriptions with a delivery day on this

            //  day of the week

            $q = SubscriptionQuery::create()
                ->filterByStatus( 'Active' )
                ->filterByDeliveryDay( $weekday )
                ->useUserQuery()
                ->orderByLastName( 'ASC' )
                ->endUse();

            $aSubscriptions = self::addOrderFilters( $q, $options )->find();


            foreach ( $aSubscriptions as $oSub ) {


                $item = array(

                    'Status' => 'Theoretical Paid',

                    'DeliveryScheduledFor' => $current,

                    'Price' => $oSub->getProduct()->getPrice(),

                    'Product' => $oSub->getProduct()->getTitle(),

                    'DeliverySite' => ( $oSub->getUser()->getDoorstep() ) ? $oSub->getUser()->getFullAddressWithOutComma() : $oSub->getDeliverySite()->getNickname(),

                    'UserId' => $oSub->getUserId()

                );


                //  Exclude if there is already a non-donated order on this date

                if ( $oSub->hasOrderOnDate( array( 'Skipped', 'Pending', 'Failed', 'Delivered' ), $current ) )

                    continue;


                //  If the user signed up within 48 hours before the date, don't include them

                $exp = '%s -%d Days';

                $maxSubStamp = date( 'Y-m-d 00:00:00', strtotime( sprintf( $exp, $current, OrderPeer::CHARGE_DAYS_BEFORE ) ) );

                if ( strtotime( $oSub->getCreatedAt() ) > strtotime( $maxSubStamp ) )

                    continue;


                //  If there is a donated order, include it

                if ( $oOrder = $oSub->hasOrderOnDate( 'Donated', $current ) )
                                 $item[ 'Status' ] = 'Donated';


                $return[] = $item;


            }


            $current = date( 'Y-m-d', strtotime( $current . ' +1 Day' ) );


        }


        return $return;


    }

    /**
     *
     *
     *
     * /**
     * REPORT: Get queued orders
     *
     * @return array
     */

    private static function getQueuedOrders( $options = [ ] )
    {


        $fromDate = date( 'Y-m-d', strtotime( 'Tomorrow' ) );

        $days = 7;

        $untilDate = date( 'Y-m-d', strtotime( $fromDate . ' +' . $days . ' Days' ) );

        $q = OrderQuery::create()
            ->filterByStatus( 'Pending' )
            ->filterByDeliveryScheduledFor( array( 'min' => $fromDate, 'max' => $untilDate ) );

        return self::addOrderFilters( $q, $options )->find();


    }

    /**
     * REPORT: Get Customers with specific status
     *
     * @param array $options
     * @return array
     */

    private static function getByStatus( $options )
    {
        $status = $options[ 'status' ];

        return UserQuery::create()
            ->filterByCustomerStatus( $options[ 'status' ] )
            ->filterByIsArchived( 0 )
            ->filterByAdminAccess(null)
            ->find();
    }

    /**
     * REPORT: Get Customers from single delivery site
     *
     * @param array $options
     * @return array
     */

    private static function getSiteFilter( $options )
    {


        $iSite = $options[ 'SiteId' ];

        return UserQuery::create()
            ->useSubscriptionQuery()
            ->filterByDeliverySiteId( $iSite )
            ->endUse()
            ->find();


    }

    /**
     * REPORT: Get All Customers
     *
     * @return array
     */

    private static function getAll()
    {


        return UserQuery::create()->find();


    }

    /**
     * Sample data for testing CSV suite
     *
     * @return array
     */

    private static function getSampleReport()
    {


        return [

            [

                'FirstName' => 'Robin',

                'LastName' => 'Arenson'

            ],

            [

                'FirstName' => 'Baya',

                'LastName' => 'Voce'

            ]

        ];


    }

    /**
     * Convert privately stored data to CSV string and return
     *
     * @return string CSV data
     */

    public function getCSV()
    {


        return static::parseCSV( $this->_data );


    }

    /**
     * CSV parser
     *
     * @param array Data to be parsed
     * @return string
     */

    private static function parseCSV( $data )
    {


        $buffer = '';

        $rows = [ ];

        $i = 0;

        foreach ( $data as $row ) {

            if ( $i == 0 )

                $rows[] = implode( ',', array_keys( $row ) );

            $rows[] = implode( ',', array_values( $row ) );

            $i++;

        }


        return implode( "\n", $rows );


    }


}
