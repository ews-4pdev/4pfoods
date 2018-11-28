<?php
$bag_points = [];
$temp = 0;
$primary = $secondary = 0;
?>
<div class="margin">
    <div class="row">
        <div class="pull-right col-lg-4 " id="sandbox-container">
            <div>Select Date (Month, Day, Year)</div>
            <div class="input-group date">
                <input id="date" placeholder="Select Date" style="background-color: whitesmoke;" type="text"
                       class="form-control">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <?php if( isset($allCats) && !empty($allCats) ){ ?>
                <ul class='nav nav-tabs' role='tablist'>
                    <?php $count = 0;
                    foreach ($allCats as $cat) { ?>
                        <li role='presentation' class="<?= ($count++ < 1) ? 'active' : '' ?>">
                            <a aria-controls='<?= $cat->getTitle() ?>' role='tab' data-toggle='tab' href="#<?= $cat->getTitle() ?>">
                                <?= $cat->getTitle() ?>
                            </a>
                        </li>
                    <?php } ?>

                </ul>

                <div class='tab-content'>
                    <?php $count = 0; $count1 = 0;
                    foreach ($allCats as $cat) { ?>
                        <div role='tabpanel' class='tab-pane <?= ($count1++ < 1) ? 'active' : '' ?> tab-content col-lg-12' id='<?=$cat->getTitle()?>'>
                            <?php foreach ($cat->getBags($date) as $product) { ?>
                                <?php

                                $bag_points[$product->getId()] = [
                                    'total_points' => $product->getProduct()->getPoints(),
                                    'available_points' => 0,
                                    'title' => $product->getProduct()->getTitle()
                                ];
                                ?>
                                <div style="clear: both; margin-top: 10px;"></div>
                                <h2 style="text-align: center;color:#006699; padding:10px; background:#9E9E9E; cursor:pointer; font-size: 15px;"
                                    class="update m-b-none clearfix"
                                    onClick="hide_products('<?= 'bag_' . $product->getId(); ?>')">
                                    <i class="fa <?= ($product->getLocked()) ? 'fa-lock' : 'fa-unlock-alt' ?>"></i>
                                    <?= $product->getProduct()->getTitle(); ?>
                                    <span title="Customer Bag has all published items" class=" <?=( $product->getSync() ) ? 'sync' : 'no-sync' ?>">
                                        <i class="fa fa-refresh fa-2x"></i>
                                    </span>
                                </h2>
                                <div class="col-lg-12" id="<?= 'bag_' . $product->getId(); ?>"
                                     style="display:none; padding: 0;">
                                    <div class="panel border-gray p-lg no-shadow">
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <h2 class="m-b-none" style="margin-top:0;">List of Items</h2>

                                                <div class="m-b">Total points
                                                            <span style="background-color: tomato;color: white;"
                                                                  class="badge"
                                                                  id="total_<?= $product->getId() ?>"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-8 padding">
                                                <h2
                                                    data-bag-id="<?= $product->getId() ?>"
                                                    id="date_<?= $product->getId() ?>"
                                                    class="c-pointer p-sm no-margin pull-right text-center dateSync  <?= (!$product->getBagsItems() && empty($product->getBagsItems())) ? 'hidden' : 'visible'; ?>"
                                                    data-toggle="modal" data-target="">
                                                    <i class="fa fa-files-o fa-2x"></i> Sync To
                                                </h2>

                                            </div>
                                        </div>

                                        <table style="" id="table_<?= $product->getId(); ?>"
                                               class="table table-hover table-responsive m-t">

                                            <thead>
                                            <tr>
                                                <th style="width: 30%;">Item</th>
                                                <th style="width: 40%;">Supplier</th>
                                                <th>Points</th>
                                            </tr>
                                            </thead>

                                            <tbody>
                                            <?php  if ( isset( $product->getBagsItems()[0] ) ) : $temp = 0; ?>
                                                <?php foreach ($product->getBagsItems() as $itemProduct) : $supplier_name = null; ?>

                                                    <?php if ( $itemProduct->getStatus()   && $itemProduct->getStatus() === 'Primary') : ?>
                                                        <?php if ($primary == 0) : $primary = 1; ?>
                                                            <tr class="primary">
                                                                <td id="primary-td-<?= $product->getId() ?>"
                                                                    data-bag-id="<?= $product->getId() ?>"
                                                                    style="text-align: left;" colspan="5">Items

                                                                    <i class="glyphicon glyphicon-minus pull-right"></i>
                                                                </td>
                                                            </tr>
                                                        <?php endif ?>
                                                        <tr class="primary-<?= $product->getId() ?>"
                                                            id="item-list-<?= $itemProduct->getId(); ?>">
                                                            <td><?= $itemProduct->getItemsPoint()->getItems()->getName() ?></td>
                                                            <td>
                                                                <?php
                                                                foreach ($itemProduct->getItemsPoint()->getItems()->getItemSuppliers() as $supplier) {
                                                                    $supplier_name = (empty($supplier_name)) ? $supplier->getSuppliers()->getName() : $supplier->getSuppliers()->getName() . ',' . $supplier_name;
                                                                }
                                                                echo $supplier_name;
                                                                ?>
                                                            </td>
                                                            <td width="120">
                                                                <?php $temp += $itemProduct->getPoints();
                                                                echo $itemProduct->getPoints(); ?>
                                                            </td>
                                                        </tr>
                                                    <?php endif; ?>

                                                    <?php if ( $itemProduct->getStatus() && $itemProduct->getStatus() === 'Secondary') : ?>
                                                        <?php if ($secondary == 0) : $secondary = 1; ?>
                                                            <tr class="secondary">
                                                                <td id="secondary-td-<?= $product->getId() ?>"
                                                                    data-bag-id="<?= $product->getId() ?>"
                                                                    style="text-align: left;" colspan="5">Swappable Items
                                                                    <i class="glyphicon glyphicon-minus pull-right"></i>
                                                                </td>
                                                            </tr>
                                                        <?php endif ?>
                                                        <tr class="secondary-<?= $product->getId() ?>"
                                                            id="item-list-<?= $itemProduct->getId(); ?>">
                                                            <td><?= $itemProduct->getItemsPoint()->getItems()->getName() ?></td>
                                                            <td>
                                                                <?php
                                                                foreach ($itemProduct->getItemsPoint()->getItems()->getItemSuppliers() as $supplier) {
                                                                    $supplier_name = (empty($supplier_name)) ? $supplier->getSuppliers()->getName() : $supplier->getSuppliers()->getName() . ',' . $supplier_name;
                                                                }
                                                                echo $supplier_name;
                                                                ?>
                                                            </td>
                                                            <td width="120">
                                                                <?= $itemProduct->getPoints(); ?>
                                                            </td>
                                                        </tr>
                                                    <?php endif; ?>
                                                <?php endforeach;
                                                $bag_points[$product->getId()]['available_points'] = $bag_points[$product->getId()]['total_points'] - $temp;
                                                $temp = null; ?>

                                            <?php else : ?>
                                                <?php $bag_points[$product->getId()]['available_points'] = $bag_points[$product->getId()]['total_points'] - $temp;
                                                $temp = null; ?>
                                                <tr>
                                                    <td id="alert_<?= $product->getId(); ?>" colspan="5">
                                                        <div class="alert alert-danger text-center">No Items In this
                                                            Bag.
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endif;
                                            $secondary = $primary = 0; ?>
                                            </tbody>
                                        </table>

                                    </div>
                                    <!-- / footer-panel -->
                                </div>
                            <?php  } ?>
                        </div>
                    <?php } ?>

                </div>
            <?php } ?>

        </div>
        <!-- End tabbed content -->
    </div>
</div>

<script type="text/javascript">
    var items = <?=(isset($aItems) && !empty($aItems)) ? json_encode($aItems) : 0?>;
    var futureDates = <?=(isset($expectedDates) && !empty($expectedDates)) ? json_encode($expectedDates) : 0?>;
    var aProducts = <?=( isset($aProducts) && !empty($aProducts)) ? 1 : 0 ?>;
    var bags = <?=json_encode($bag_points)?>;
    var phpDate = '<?=$date?>';
    var bagsCreatedDates = <?=json_encode($alreadyCreatedBags)?>;

    bagsCreatedDates = sliceObject(bagsCreatedDates);

    function sliceObject(obj){
        for(var i in obj){
            if( obj.hasOwnProperty(i) ){
                if(typeof obj[i] == 'object'){
                    sliceObject(obj[i]);
                }else{
                    obj[i] =  createDate(obj[i]);
                }
            }
        }
        return obj;
    }

    function createDate(date){
        var nd = new Date( date );
        var ndd = nd.getDate();
        var ndm = nd.getMonth()+1;
        var ndy = nd.getFullYear();
        var ndg = [parseInt(ndy), parseInt(ndm), parseInt(ndd)];
        return new Date(ndg);
    }

</script>