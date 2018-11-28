<?php


/**
 * Skeleton subclass for performing query and update operations on the 'deliveries' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class DeliveryQuery extends BaseDeliveryQuery
{

    /**
     *  Get list of Delivery sites associated with pending deliveries
     */
    public static function getSiteList( $today = NULL )
    {

        $today = ( $today ) ? $today : date( 'Y-m-d' );

        $aDeliveries = self::create()
            ->filterByDeliveryDate( $today )
            ->filterByStatus( 'Pending' )
            ->find();

        $list = array();
        foreach ( $aDeliveries as $oDelivery ) {
            if ( $oDelivery->getDeliverySite() ) {
                $list[] = [
                    'name' => ( $oDelivery->getDoorstep() ) ?
                        UserQuery::create()->findPk( $oDelivery->getUserId() )->getFullAddress()
                        : $oDelivery->getDeliverySite()->getNickname(),

                    'doorstep' => $oDelivery->getDoorStep(),
                    'id' => $oDelivery->getId()
                ];
            }
        }
        return $list;
    }

    public static function getPendingForSite( $iSite, $today = NULL )
    {

        $today = ( $today ) ? $today : date( 'Y-m-d' );

        return self::create()
            ->filterByStatus( 'Pending' )
            ->filterByDeliveryDate( $today )
            ->filterByDeliverySiteId( $iSite )
            ->find();
    }

    public static function getDeliveriesCSVForDate( DateTime $date )
    {
        $deliveries = self::create()
            ->filterByStatus( 'Pending' )
            ->filterByDeliveryDate( $date )
            ->find();

        $retArray = [ ];
        $totalArray = [ ];

        foreach ( $deliveries as $delivery ) {
            $deliverySite = null;
            $deliverySite = DeliverySiteQuery::create()->findPk( $delivery->getDeliverySiteId() );

            if ( $delivery->getDoorstep() == false ) {
                $retArray[ $delivery->getId() ] = [
                    'Labels' => $delivery->getId(),
                    'Stop#' => '',
                    'Delivery Sites' => $deliverySite->getNickName(),
                    'Route - ' . $delivery->getDeliveryDay() => $deliverySite->getBothAddress(),

                    'City' => $deliverySite->getCity(),
                    'State' => $deliverySite->getStateAbbrev(),
                    'Zip' => $deliverySite->getZip()
                ];

            } else {

                $oUser = UserQuery::create()->findPk( $delivery->getUserId() );

                $retArray[ $delivery->getId() ] = [
                    'Labels' => $delivery->getId(),
                    'Stop#' => '',
                    'Delivery Sites' => 'DOORSTEP',
                    'Route - ' . $delivery->getDeliveryDay() => $deliverySite->getBothAddress( $delivery->getUserId() ),

                    'City' => $oUser->getCity(),
                    'State' => $oUser->getStateAbbrev(),
                    'Zip' => $oUser->getZip()
                ];

            }

            $sum = 0;
            foreach ( ProductQuery::getAllNames() as $product ) {
                $retArray[ $delivery->getId() ][ $product->getTitle() ] =
                    OrderQuery::getProductCount( $product->getId(), $delivery->getId(), $date );

                $sum += $retArray[ $delivery->getId() ][ $product->getTitle() ];

                $string = str_replace( ' ', '_', $product->getProductCategory()->getTitle() );
                if ( !isset( $totalArray[ $string ] ) ) {
                    $totalArray[ $string ] = 0;
                }

                $totalArray[ $string ] += $retArray[ $delivery->getId() ][ $product->getTitle() ];
            }
            $retArray[ $delivery->getId() ][ 'Total' ] = $sum;
        }

        $filename = $date->format( 'd-F Y' ) . ' DriverManifest.csv';
        $file = fopen( 'temp/' . $filename, 'w' ) or die( 'Cant create file' . $filename );
        fputcsv( $file, array_keys( reset( $retArray ) ) );
        foreach ( $retArray as $delivery ) {
            fputcsv( $file, $delivery );
        }
//        fputcsv( $file, [ ] );
//        fputcsv( $file, array_keys( $totalArray ) );
//        fputcsv( $file, $totalArray );
        fclose( $file );

        return $filename;
    }

    public static function getList( $today = NULL )
    {

        $today = ( $today ) ? $today : date( 'Y-m-d' );

        $aDeliveries = self::create()
            ->filterByDeliveryDate( $today )
            ->filterByStatus( 'Pending' )
            ->find();

        $list = array();
        foreach ( $aDeliveries as $oDelivery ) {
            if ( $oDelivery->getDeliverySite() ) {
                $list[] = [
                    'name' => ( $oDelivery->getDoorstep() ) ?
                        UserQuery::create()->findPk( $oDelivery->getUserId() )->getFullAddress()
                        : $oDelivery->getDeliverySite()->getNickname(),

                    'doorstep' => $oDelivery->getDoorStep(),
                    'id' => $oDelivery->getId()
                ];
            }
        }
        return $list;
    }

}