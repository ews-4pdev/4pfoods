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
        <!--        --><?php //if(isset($published) && $published > 0) : ?>
        <div id="syncButton" class=" pull-left hidden">
            <button id="createCustomerBags" data-date="<?= $date; ?>" class="btn btn-info">Create Customer Bags</button>
        </div>
        <!--        --><?php //endif; ?>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <?php if( isset($allCats) && !empty($allCats) ){ ?>
                <ul class='nav nav-tabs' role='tablist'>
                    <?php $count = 0;
                    foreach ($allCats as $cat) { ?>
                        <li role='presentation' class="<?= ($count++ < 1) ? 'active' : '' ?>">
                            <a aria-controls='aria_<?= $cat->getId() ?>' role='tab' data-toggle='tab' href="#aria_<?= $cat->getId() ?>">
                                <?= $cat->getTitle() ?>
                            </a>
                        </li>
                    <?php } ?>

                </ul>

                <div class='tab-content'>
                        <?php $count = 0; $count1 = 0;
                        foreach ($allCats as $cat) { ?>
                            <div role='tabpanel' class='tab-pane <?= ($count1++ < 1) ? 'active' : '' ?> tab-content col-lg-12' id='aria_<?=$cat->getId()?>'>
                                <?php foreach ($cat->getBags($date) as $product) { ?>
                                        <?php

                                        $bag_points[$product->getId()] = [
                                            'total_points' => $product->getTotalPoints(),
                                            'title' => $product->getProduct()->getTitle()
                                        ];
                                        ?>
                                        <div style="clear: both; margin-top: 10px;"></div>
                                        <h2 style="text-align: center;color:#006699; padding:10px; background:#9E9E9E; cursor:pointer; font-size: 15px;"
                                            class="update m-b-none clearfix"
                                            onClick="hide_products('<?= 'bag_' . $product->getId(); ?>')">
                                            <i class="fa <?= ($product->getLocked()) ? 'fa-lock' : 'fa-unlock-alt' ?>"></i>
                                            <?= $product->getProduct()->getTitle(); ?>
                                            <span title="Customer Bag has all published items" class="sync_<?= $product->getId(); ?>  <?=( $product->getSync() ) ? 'sync' : 'no-sync' ?>">
                                                <i class="fa fa-refresh fa-2x"></i>
                                            </span>
                                        </h2>
                                        <div class="col-lg-12" id="<?= 'bag_' . $product->getId(); ?>"
                                             style="display:none; padding: 0;">
                                            <div class="panel border-gray p-lg no-shadow">
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <h2 class="m-b-none" style="margin-top:0;">List of Items</h2>

                                                        <div class="m-b">Total Points
                                                            <span style="background-color: tomato;color: white;" id="total_<?= $product->getId() ?>"
                                                                  class="badge "><?= $product->getTotalPoints() ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-8 padding">
                                                        <!-- Button trigger modal -->
                                                        <h2
                                                            data-product-type="<?= $product->getProduct()->getTitle(); ?>"
                                                            class="c-pointer p-sm no-margin pull-right text-center modalProducts"
                                                            data-toggle="modal"
                                                            data-category="<?= $product->getProduct()->getProductCategory()->getTitle(); ?>"
                                                            data-target="#addProducts" data-bag-id="<?= $product->getId() ?>"
                                                            data-product-id="<?= $product->getProductId() ?>">
                                                            <img class="addBag" src='/images/add-admin.svg'
                                                                 alt='Add new item'/>
                                                        </h2>

                                                        <h2
                                                            data-bag-id="<?= $product->getId() ?>"
                                                            id="date_<?= $product->getId() ?>"
                                                            class="c-pointer p-sm no-margin pull-right text-center dateSync  <?= (!$product->getBagsItems() && !$product->getBagsItems() ) ? 'hidden' : 'visible'; ?>"
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
                                                        <th>Action</th>
                                                    </tr>
                                                    </thead>

                                                    <tbody>
                                                    <?php  if ( isset( $product->getBagsItems()[0] ) && !empty( $product->getBagsItems()[0] ) ) : $temp = 0; ?>
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
                                                                    <td width="120"><input
                                                                            id="input-<?= $itemProduct->getId(); ?>"
                                                                            class="form-control updatePoints"
                                                                            type="text"
                                                                            value="<?php $temp += $itemProduct->getPoints();
                                                                            echo $itemProduct->getPoints(); ?>"
                                                                            data-default="<?= $itemProduct->getPoints(); ?>"
                                                                            name="">
                                                                    </td>
                                                                    <td width="1">
                                                                        <div class="input-group col-lg-1 pull-right">
                                                                            <button style="margin:0;"
                                                                                    class="item-list btn btn-danger">Delete
                                                                            </button>
                                                                        </div>
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
                                                                        <input id="input-<?= $itemProduct->getId(); ?>"
                                                                               class="form-control updatePoints" type="text"
                                                                               data-default="<?= $itemProduct->getPoints(); ?>"
                                                                               value="<?= $itemProduct->getPoints(); ?>"
                                                                               name="">
                                                                    </td>
                                                                    <td width="1">
                                                                        <div class="">
                                                                            <button style="margin:0;"
                                                                                    class="item-list btn btn-danger">Delete
                                                                            </button>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>

                                                    <?php else : ?>
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

<!-- Start change modal -->
<?php if (isset($allCats) && !empty($allCats)) : ?>
    <div aria-hidden="true" aria-labelledby="new code" role="dialog" tabindex="-1"
         id="addProducts"
         class="modal fade" style="display: none;">
        <div style="width: 100%;" class=" modal-dialog ">
            <div class="modal-content margin ">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                    </button>
                    <div class="title">Add Items</div>
                    <p class="m-b">Total Points
                        <span id="totalPoints" class="badge" style="background-color: tomato;color: white;"></span></p>
                </div>
                <div class="modalTop p-lg">
                    <table style="" id="extraProduct" class="table table-hover table-responsive m-t">
                        <thead>
                        <tr>
                            <th>Item</th>
                            <th>Supplier</th>
                            <th>Product</th>
                            <th>Points</th>
                            <th>Swappable Items</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>

                </div>
                <!-- /.panel -->
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
<?php endif; ?>

<!-- /.modal -->
<script type="text/javascript">
    var items = <?=(isset($aItems) && !empty($aItems)) ? json_encode($aItems) : 0?>;
    var futureDates = <?=(isset($expectedDates) && !empty($expectedDates)) ? json_encode($expectedDates) : 0?>;
    var aProducts = <?=( isset($aProducts) && !empty($aProducts)) ? 1 : 0 ?>;
    var bags = <?=json_encode($bag_points)?>;
    var phpDate = '<?=$date?>';
    var bagsCreatedDates = <?=json_encode($alreadyCreatedBags)?>;
    var published = '<?php if(isset($published)) {echo $published; }  ?>';
    if (published && published == 0) {
        $('#syncButton').removeClass('hidden');
        $('#createCustomerBags').text('Update Customer Bags');
    } else if (published && published == 1) {
        $('#syncButton').removeClass('hidden');
        $('#createCustomerBags').text('Create Customer Bags');
    }

bagsCreatedDates = sliceObject(bagsCreatedDates);

</script>