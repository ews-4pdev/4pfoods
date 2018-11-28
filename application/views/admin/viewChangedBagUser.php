<?php  $bags = [];  ?>
<div class="margin">
    <div class="row">
        <div class="col-lg-12">
            <section class="panel light">
                <div class="wrapper">
                    <p class="pull-right">Joined <?= $oUser->getCreatedAt('M j, Y'); ?></p>
                    <div class="heading m-b"><?= $oUser->getFirstName().' '.$oUser->getLastName(); ?></div>
                    <p><strong>Email</strong> <?= $oUser->getEmail(); ?></p>
                    <p><strong>Phone</strong> <?= $oUser->getPhone(); ?></p>
                    <p><strong>Default Delivery Site</strong> <?= $oUser->getSiteNickname(); ?> </td></p>
                    <p><strong>Stripe ID</strong> <?= $oUser->getStripeId(); ?></p>
                    <p><strong>Dietary Restrictions:</strong> <?= $oUser->getDietaryRestrictions(); ?></p>
                    <?php if (!$oUser->isConfirmed()) : ?>
                        <p><strong>Confirm Link:</strong> <?= $oUser->getConfirmLink(); ?></p>
                    <?php endif; ?>

                </div><!-- /.wrapper -->
            </section>
            <section class="panel">
                <!-- Nav tabs -->

                <ul class="nav nav-tabs">
                    <?php $count = 0;  foreach($aBags as $uBag){  ?>
                        <li class="<?=(++$count == 1) ? 'active' : '' ?>"><a data-toggle="tab" href="#<?=$uBag['customer']->getId();?>"><?=$uBag['customer']->getProduct()->getTitle();?></a></li>
                    <?php }//endforeach  ?>
                </ul>

                <div class="tab-content">
                    <!-- Start tab pane -->
                    <?php $count = 0;  foreach($aBags as $aBag){   ?>
                        <?php $bags[] = [
                            'user'  => $aBag['customer']->getId(),
                            'admin' => $aBag['admin']->getId()
                        ]  ?>
                        <div role='tabpanel' style="padding: 0;"  class="tab-content col-lg-12 tab-pane <?=(++$count == 1) ? 'active' : '' ?>" id="<?=$aBag['customer']->getId();?>">
                            <div class="flex">
                                <div>
                                    <h2 style="padding: 1em; background: tomato" class="text-center">User Bags Items</h2>
                                    <table style="border-right: 2px groove gray" id="user_<?=$aBag['customer']->getId();?>" class="table table-hover table-responsive">
                                        <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Points</th>
                                            <th>Quantity</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <?php foreach($aBag['customer']->getUserBagItems() as $bagItems){  ?>
                                            <?php if($bagItems->getIsDeleted() == false){ ?>
                                            <tr>
                                                <td><?=$bagItems->getItemsPoint()->getItems()->getName();?></td>
                                                <td><?=$bagItems->getPoints();?></td>
                                                <td><?=$bagItems->getQuantity();?></td>
                                            </tr>
                                            <?php } ?>
                                        <?php }//endforeach  ?>
                                        </tbody>
                                    </table>
                                </div>

                                <div>
                                    <h2 style="padding: 1em; background: gray" class="text-center">Admin Bags Items</h2>
                                    <table id="admin_<?=$aBag['admin']->getId();?>" class="table table-hover table-responsive">
                                <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Points</th>
                                    <th>Quantity</th>
                                </tr>
                                </thead>

                                <tbody>
                                <?php  foreach($aBag['admin']->getBagsItems() as $bagItems){   ?>
                                    <?php if($bagItems->getStatus() == 'Primary'){ ?>
                                    <tr>
                                        <td><?=$bagItems->getItemsPoint()->getItems()->getName();?></td>
                                        <td><?=$bagItems->getPoints();?></td>
                                        <td>1</td>
                                    </tr>
                                    <?php } ?>
                                <?php }//endforeach  ?>
                                </tbody>
                            </table>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div><!-- End tabbed content -->

            </section><!-- /.panel -->
        </div>
    </div>
</div>

<script type="text/javascript">
var bags = <?=json_encode($bags) ?>;

for( var i in bags ){
    if(bags.hasOwnProperty(i)){
        var user = 'user_'+bags[i].user;
        var admin = 'admin_'+bags[i].admin;
        sortTable( user );
        sortTable( admin );
    }
}

function sortTable(id){
    var tr = $('#'+id+' tbody tr');

    tr.sort(function(a,b){
        var first = $(a).find('td:eq(0)').text().toLowerCase();
        var second = $(b).find('td:eq(0)').text().toLowerCase();

        if(first < second){
            return -1;
        }else if(second < first){
            return 1;
        }else{
            return 0;
        }
    }).appendTo($('#'+id+' tbody'));
}



</script>