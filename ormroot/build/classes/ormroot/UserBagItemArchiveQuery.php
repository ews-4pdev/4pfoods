<?php



/**
 * Skeleton subclass for performing query and update operations on the 'user_bag_item_archive' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class UserBagItemArchiveQuery extends BaseUserBagItemArchiveQuery
{
    static function getDeletedItems($bag_id){
        $sql = "SELECT user_bag_item_archive.id, user_bag_item_archive.points, user_bag_item_archive.status, user_bag_item_archive.quantity,

items.name as item_name, suppliers.name as supplier_name

FROM user_bag_item_archive

LEFT JOIN items_point
ON user_bag_item_archive.point_id = items_point.id

LEFT JOIN items ON items.id = items_point.item_id

LEFT JOIN item_supplier ON items.id = item_supplier.item_id

LEFT JOIN suppliers  ON item_supplier.supplier_id = suppliers.id

WHERE user_bag_item_archive.bag_id = :bag_id AND user_bag_item_archive.status = :status
ORDER by id;";
        $status = 'Active';
        $con = Propel::getConnection();
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':bag_id', $bag_id, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
    }
}
