
<?php
$bag_points = [];
$temp = 0;
?>
<div id="content" class="m-t m-b-md">
    <div class="container">
        <!-- Button trigger modal -->
        <a href="#" data-toggle="modal" data-target="#newSubscription" class="pull-right m-b-md"> Start new subscription
            <img src="/images/add-admin.svg" alt="Add Subscription" height="50" width="50" id="addGraphic"/> </a>

        <div class="modal fade front" id="newSubscription" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content margin">
                    <button class="close closeGraphic" data-dismiss="modal" type="button"><img src="/images/close.png"
                                                                                               alt="close" width="40"
                                                                                               height="40"/></button>
                    <div class="modalTop p-lg">
                        <div class="row no-margin">
                            <div class="title">Select a bag to add</div>
                            <div class="form-group">
                                <select id="selectAddSubscription" name="addSubscription" class="form-control"
                                        style="width:300px;">
                                    <?php foreach ($aCategories as $oCat) : ?>
                                        <option value="<?= $oCat->getId(); ?>">
                                            <?= $oCat->getTitle(); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <div class="error-notify"></div>
                            </div>
                            <!-- End group -->
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.modalTop -->

                    <div class="no-border p-lg no-shadow">
                        <div class="title productTitle">Please select a bag size</div>
                        <form class="form" role="form" id="addSubscriptionForm">
                            <script type="text/html" id="tmpl-product">
                                <div class="radio">
                                    <label>
                                        <input type="radio" class="productCheck" name="iProduct" data-alt="id"/>
                                        <span data-content="title">No thanks</span> - <span data-content="price"></span>
                                    </label>
                                </div><!-- /.radio -->
                            </script>
                            <div id="radioContainer"></div>
                            <button class="btn btn-default m-t-md " id="submitAddSubscription">Add Subscription</button>
                            <input type="hidden" name="iUser" value="<?= $oUser->getId(); ?>"/>
                        </form>
                        <!-- / form -->
                    </div>
                    <!-- /.p-lg -->
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
        <!-- /.container -->
    </div>

    <!--    No Subscription-->
    <?php if (count($aSubscriptions) == 0 ) { ?>
        <div class="row">
            <div class="container">
                <img src="/images/no-subscriptions.jpg" alt="No subscription" width="128"
                     height="250" class="center-block img-responsive"/>

                <div class="heading m-b m-t text-center">Goose Egg. No Subscriptions.</div>
                <p class="text-center">Go ahead and start one, your tummy will thank you.</p>
            </div>
            <!-- /.container -->
        </div>
        <!-- /.row -->
        <!--  No Subscription End-->
    <?php }else{ ?>

        <!-- Active Subscription-->
        <?php foreach ($aSubscriptions as $oSub) : ?>

            <div class="modal fade front" id="manageSub_<?= $oSub->getId(); ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content margin">
                        <button class="close" data-dismiss="modal" type="button"><img src="/images/close.png"
                                                                                      alt="close" width="40"
                                                                                      height="40"/></button>
                        <div class="modalTop p-lg">
                            <form id="changeSubscription<?= $oSub->getId(); ?>">
                                <div class="row no-margin">
                                    <div class="title">I would like to change my subscription to</div>
                                    <div class="col-sm-6 col-offset-sm-6 no-padding">
                                        <div class="form-group">
                                            <select id="changeProductTo<?= $oSub->getId(); ?>" name="iProduct"
                                                    class="form-control">
                                                <?php foreach ($oSub->getAllInCategory() as $oProduct) : ?>
                                                    <?php if( $oSub->getProductId() != $oProduct->getId() ){ ?>
                                                    <option
                                                        value="<?= $oProduct->getId(); ?>">
                                                        <?= $oProduct->getTitle(); ?>
                                                    </option>
                                                <?php } ?>
                                                <?php endforeach; ?>
                                            </select>

                                            <div class="error-notify"></div>
                                        </div>
                                        <!-- End group -->
                                    </div>
                                    <!-- /.half -->
                                </div>
                                <!-- /.row -->
                                <div class="row no-margin">
                                    <div class="form-group">
                                        <label for="weekly">
                                            <input type="checkbox" name="weekly" id="weekly_<?=$oSub->getId();?>"> Change Subscription just for this week
                                        </label>
                                    </div>
                                </div>
                                <input type="hidden" name="iUser" value="<?= $oSub->getUserId(); ?>"/>
                                <input type="hidden" name="iSub" value="<?= $oSub->getId(); ?>"/>
                            </form>
                            <!-- /.form -->

                            <button class="btn btn-default m-t-md submitChange" rel="<?= $oSub->getId(); ?>">Update
                                subscription
                            </button>
                        </div>
                        <!-- /.panel -->
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>

            <div class="modal fade front" id="tripModal_<?= $oSub->getId(); ?>" tabindex="-1">
                <form id="tripForm_<?= $oSub->getId(); ?>">
                    <div class="modal-dialog">
                        <div class="modal-content margin">
                            <button class="close" data-dismiss="modal" type="button"><img src="/images/close.png"
                                                                                          alt="close" width="40"
                                                                                          height="40"/></button>
                            <div class="modalTop p-lg">
                                <div class="row no-margin">
                                    <div class="row">
                                        <div class="title">1. When does your trip start?</div>
                                        <div class="col-sm-6 col-offset-sm-6 no-padding">
                                            <div class="form-group">
                                                <input type="text" class="datepickerInput"
                                                       id="startDate_<?= $oSub->getId(); ?>" name="startDate"
                                                       placeholder="Choose date"/>

                                                <div class="error-notify"></div>
                                            </div>
                                            <!-- End group -->
                                        </div>
                                        <!-- /.half -->
                                    </div>
                                    <!-- /.row -->

                                    <div class="row">
                                        <div class="title">2. When does your trip end?</div>
                                        <div class="col-sm-6 col-offset-sm-6 no-padding">
                                            <div class="form-group">
                                                <input type="text" class="datepickerInput"
                                                       id="endDate_<?= $oSub->getId(); ?>" name="endDate"
                                                       placeholder="Choose date"/>

                                                <div class="error-notify"></div>
                                            </div>
                                            <!-- End group -->
                                        </div>
                                        <!-- /.half -->
                                    </div>
                                    <!-- /.row -->

                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.modalTop -->

                            <div class="panel margin padding border-strong no-shadow"><img src="/images/donate.png"
                                                                                           alt="donate" width="197"
                                                                                           height="75"
                                                                                           class="center-block m-b"/>

                                <div class="title text-center">Would you like to donate this?</div>
                                <div class="checkbox" id="donateMe">
                                    <label>
                                        <input type="checkbox" name="isDonate">
                                        Yes please.<a href="http://4pfoods.com/how-it-works/" target="_blank"><i
                                                class="fa fa-question-circle"></i></a></label>
                                </div>
                            </div>
                            <!-- /.panel -->

                            <div class="panel no-shadow b-n text-center">
                                <button class="btn btn-default m-t-md confirmSkip" rel="<?= $oSub->getId(); ?>">
                                    Confirm
                                </button>
                            </div>
                            <!-- /.panel -->
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                    <input type="hidden" name="iUser" value="<?= $oUser->getId(); ?>"/>
                    <input type="hidden" name="iSub" value="<?= $oSub->getId(); ?>"/>
                </form>
                <!-- /.form -->
            </div>

            <div class="modal fade front" id="stopSubscriptionModal_<?= $oSub->getId(); ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content margin">
                        <button class="close" data-dismiss="modal" type="button"><img src="/images/close.png"
                                                                                      alt="close" width="40"
                                                                                      height="40"/></button>
                        <div class="modalTop p-lg">
                            <form id="stopSubscription<?= $oSub->getId(); ?>">
                                <div class="row no-margin">
                                    <div class="title">I would like to stop this subscription</div>
                                    <a href="javascript:;" class="stopSubscription btn btn-default m-t-md"
                                       data-msg="Are you sure you want to stop this subscription? It will be removed from your account and no more deliveries will be made."
                                       data-uid="<?= $oSub->getUserId(); ?>" data-sid="<?= $oSub->getId(); ?>">Stop
                                        now</a></div>
                                <!-- /.row -->
                            </form>
                            <!-- /.form -->
                        </div>
                        <!-- /.panel -->
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>

            <div class="modal fade" id="futureSkips_<?= $oSub->getId(); ?>">
                <div class="modal-dialog">
                    <div class="modal-content margin">
                        <button class="close" data-dismiss="modal" type="button"><img src="/images/close.png"
                                                                                      alt="close" width="40"
                                                                                      height="40"/></button>
                        <div class="modalTop p-lg left">
                            <div class="row no-margin">
                                <div class="title">Schedule overview</div>
                            </div>
                            <!-- /.row -->

                            <?php if (count($oSub->getSkippedOrders(NULL, true)) == 0) : ?>
                                <div class="row">
                                    <div class="container"><img src="/images/nothing-scheduled.jpg"
                                                                alt="Nothing Scheduled" width="220" height="20"
                                                                class="img-responsive"/>

                                        <div class="heading m-b m-t">Nothing planned</div>
                                        <p>Enjoy your regular delivery schedule.</p>
                                    </div>
                                    <!-- /.container -->
                                </div>
                                <!-- /.row -->
                            <?php else : ?>
                                <div class="row">
                                    <table class="table table-hover table-responsive m-t">
                                        <thead>
                                        <tr>
                                            <th>Bag skipped</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($oSub->getSkippedOrders(NULL, true) as $oOrder) : ?>
                                            <tr>
                                                <td>1
                                                    <?= $oSub->getProduct()->getTitle(); ?></td>
                                                <td><?= $oOrder->getDeliveryScheduledFor('M d, Y'); ?></td>
                                                <td><?= $oOrder->getStatus(); ?>
                                                    (<a href="#" class="reactivateLink"
                                                        rel="<?= $oOrder->getId(); ?>">Reactivate</a>)
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.row -->
                            <?php endif; ?>
                        </div>
                        <!-- /.modalTop -->
                    </div>
                    <!-- /.content -->
                </div>
                <!-- /.dialog -->
            </div>

            <div class="modal fade del-modal modal-hide" id="skipModal_<?= $oSub->getId(); ?>" tabindex="-1" role="dialog"
                 aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <!-- <div class="modal-header">

                      </div> -->
                        <button type="button" class="close" data-dismiss="modal"><img src="/images/close.png"
                                                                                      alt="close" width="40"
                                                                                      height="40"/></button>
                        <div class="modal-body no-js row">
                            <div class="del-sched-area row liquid-slider makeSlider"
                                 id="skipSlider_<?= $oSub->getId(); ?>">
                                <?php $k = 0; ?>
                                <?php for ($i = 1; $i <= 6; $i++) : ?>
                                    <div id="week_<?= $oSub->getId(); ?>_<?= $i; ?>">
                                        <?php if ($i == 1) : ?>
                                            <h2 class="title">Now</h2>
                                        <?php else : ?>
                                            <h2 class="title">+
                                                <?= ($i * 4) - 4; ?>
                                                Weeks</h2>
                                        <?php endif; ?>
                                        <ul class="list">
                                            <?php for ($j = 1; $j <= 4; $j++) : ?>
                                                <?php $tDate = $skipDays[$oSub->getId()][$k++]; ?>
                                                <?php $isSkip = ($oOrder = $oSub->hasOrderOnDate('Skipped', $tDate)); ?>
                                                <?php $isDonate = (!$isSkip && $oSub->hasOrderOnDate('Donated', $tDate)); ?>
                                                <?php $status = ($isDonate) ? 'donated' : ($isSkip ? 'skipped' : 'active'); ?>
                                                <li class="del-<?= $status; ?> col-xs-3 col-md-3 infoEl"
                                                    data-date="<?= $tDate; ?>"
                                                    data-isub="<?= $oSub->getId(); ?>"<?= ($oOrder) ? ' data-iorder="' . $oOrder->getId() . '"' : ''; ?>>
                                                    <h3 class="del-ttle">Delivery
                                                        <?= ($isDonate) ? 'Donated' : ($isSkip ? '<br/>Skipped' : 'Scheduled'); ?>
                                                    </h3>
                  <span class="del-date">
                  <?= date('d', strtotime($tDate)); ?>
                  </span> <span class="del-month">
                  <?= date('F', strtotime($tDate)); ?>
                  </span>

                                                    <div class="row"><a href="#"
                                                                        class="del-tog<?= ($isDonate) ? ' hide' : ''; ?>">Delivery
                                                            Options</a>
                                                        <ul class="delOptions col-xs-12 col-md-12 hide for-active<?= ($status == 'active') ? ' active' : ''; ?>">
                                                            <li><a href="#" class="skipAllLink" title="Skip all bags for this week">Skip the Week</a></li>
                                                            <li><a href="#" class="skipLink" title="Skip only '<?php echo $oSub->getProduct()->getTitle(); ?>'">Skip the Bag</a></li>
                                                            <li><a href="#" class="donateLink">Donate</a></li>
                                                        </ul>
                                                        <ul class="delOptions col-xs-12 col-md-12 hide for-skipped<?= ($status == 'skipped') ? ' active' : ''; ?>">
                                                            <li><a href="#" class="reactivatePopupLink">Reactivate
                                                                    Order</a></li>
                                                        </ul>
                                                    </div>
                                                    <div class="clear"></div>
                                                </li>
                                            <?php endfor; ?>
                                        </ul>
                                        <div class="clear"></div>
                                        <div class="msg_error">
                                            <p><span class="msg_msg"></span><br/>
                                                <br/>
                                                <a href="#">Close</a></p>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <!-- <div class="modal-footer">
                          <i class="fa fa-angle-left fa-2x"></i>
                        <i class="fa fa-angle-right fa-2x"></i>
                      </div> -->
                    </div>
                </div>
            </div>
            <!-- $oCurrent is order object   -->
            <?php $oCurrent = $oSub->getCurrentOrder(); ?>
            <div class="container">
                <?php if ($oCurrent) { ?>
                    <input type="hidden" name="noweekly_<?=$oSub->getId();?>" value="false">
                    <div class="panel border-strong p-lg no-shadow">
                        <div class="col-lg-5 pull-left">
                            <div class="m-b-sm">
                                <span class="title"><?= $oCurrent->getSubscription()->getProduct()->getTitle(); ?> </span> <br>
                                <span class="m-b">Your Order is Ready</span>
                            </div>


                            <div class="">
                                <small class="muted"><img src="/images/scheduled.png" alt="clock" width="31" height="25"
                                                          id="scheduled"/> <?= $oCurrent->getStatus() ?> •
                                    <?= $oSub->getNextDeliveryDate('M d, Y'); ?>
                                </small>
                            </div>
                        </div>
                        <div style="clear: both;"></div>
                        <!--   Current Bag Items-->
                        <?php  if ( count($oCurrent->getUserBag()) > 0 && $oCurrent->getStatus() == 'Pending' ) { ?>
                            <?php $bag = $oCurrent->getUserBag(); ?>
                            <div class="clearfix">
                                <table style="" id="primary-<?= $oSub->getId(); ?>"
                                       class="table table-hover table-responsive m-t">
                                    <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Points</th>
                                        <th>Quantity</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($bag->getUserBagItems() as $items) : $supplier_name = '';
                                        if ($items->getStatus() == 'Active') { ?>
                                            <tr>
                                                <td><?= $items->getItemsPoint()->getItems()->getName(); ?></td>
                                                <td width="120"><?php $temp += $items->getPoints() * $items->getQuantity();
                                                    echo $items->getPoints(); ?></td>
                                                <td width="120"><?= $items->getQuantity(); ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php }else{ ?>
                            <div class="alert alert-info text-center padding">
                                You have <?=$oCurrent->getStatus() ?> your bag.
                            </div>
                        <?php } ?>
                        <!--  End Current Bag Items-->

                        <div class="b-t m-t-md p-t-md">
                            <span class="subscriptionOptions changeSub"><a href="javascript:;"
                                        data-target="manageSub_<?= $oSub->getId(); ?>"><img
                                        src="/images/change.jpg" alt="Change" width="24" height="25"
                                        class="animated swing"/> Change</a></span>

                            <span class="subscriptionOptions"><a
                                    href="#" data-toggle="modal" data-target="#skipModal_<?= $oSub->getId(); ?>"><img
                                        src="/images/skip.jpg" alt="Skip" width="19" height="25"
                                        class="animated swing"/> Skip</a></span>

                            <span class="subscriptionOptions"><a
                                    href="#" data-toggle="modal"
                                    data-target="#stopSubscriptionModal_<?= $oSub->getId(); ?>"><img
                                        src="/images/stop.jpg" alt="Stop" width="23" height="25"
                                        class="animated swing"/> Stop</a></span>
                            <span class="subscriptionOptions"><a
                                    href="#" data-toggle="modal" data-target="#futureSkips_<?= $oSub->getId(); ?>"><img
                                        src="/images/truck.jpg" alt="Delivery Schedule" width="46" height="25"
                                        class="animated swing"/> View schedule</a></span>
                        </div>
                        <!-- / footer-panel -->
                    </div>

                    <!-- /.panel -->
                <?php } else { $bag = null; ?>
                    <?php if (!is_null( $oSub->getUserBagOnDate() ) ) { ?>
                        <?php $bag = $oSub->getUserBagOnDate(); ?>
                    <?php } ?>
                    <div class="panel panel-default border-strong p-lg no-shadow">

                        <div class="col-lg-5 pull-left">
                            <div class="m-b-sm">
                                <span class="title"><?=( !is_null($bag) ) ? $bag->getProduct()->getTitle()
                                                                            : $oSub->getProduct()->getTitle() ; ?> </span> <br>
                                <span class="m-b">Upcoming Order</span>
                            </div>


                            <div class="">
                                <small class="muted"><img src="/images/scheduled.png" alt="clock" width="31" height="25"
                                                          id="scheduled"/> <?= $oSub->getStatus() ?> •
                                    <?= ( $bag != null ) ? DateTime::createFromFormat('m/d/Y', $bag->getDate())->format('M d, Y')
                                                                : $oSub->getNextDeliveryDate('M d, Y') ; ?>
                                </small>
                            </div>

                            <div class=""> Available points
                                <span id="available_<?= $oSub->getId(); ?>" class="badge"
                                      style="background-color: tomato;color: white;"></span>
                            </div>
                        </div>
                        <div style="clear: both;"></div>
                        <?php if( isset($bag) ){ ?>
                            <!--   Current Bag Items-->
                            <?php if (!is_null( $bag )) : ?>
                                <div class="clearfix">
                                    <table style="" id="primary-<?= $oSub->getId(); ?>"
                                           class="table table-hover table-responsive m-t">
                                        <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th class="no-sort">Points</th>
                                            <th class="no-sort">Quantity</th>
                                            <th class="no-sort">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $bag_points[$oSub->getId()] = [
                                            'total_points' => $bag->getTotalPoints(),
                                            'available_points' => 0
                                        ]; ?>
                                        <?php foreach ($bag->getUserBagItems() as $items) : $supplier_name = '';
                                            if ($items->getStatus() == 'Active') { ?>
                                                <tr id="primary-tr-<?= $items->getId(); ?>">
                                                    <td><?= $items->getItemsPoint()->getItems()->getName(); ?></td>
                                                    <td width="120"><?php $temp += $items->getPoints() * $items->getQuantity();
                                                        echo $items->getPoints(); ?></td>
                                                    <td width="120"><input data-bag="<?= $items->getId(); ?>"
                                                                           data-uid="<?= $oUser->getId(); ?>"
                                                                           data-default="<?= $items->getQuantity(); ?>"
                                                                           data-subid="<?= $oSub->getId(); ?>" class="Qty"
                                                                           style="width:50px;" type="text"
                                                                           value="<?= $items->getQuantity(); ?>"></td>
                                                    <td>
                                                        <button style="margin: 0;" id="btn-<?= $items->getId(); ?>"
                                                                data-subid="<?= $oSub->getId(); ?>"
                                                                data-bag="<?= $items->getId(); ?>"
                                                                data-uid="<?= $oUser->getId(); ?>"
                                                                class="item-list btn btn-danger">Remove
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php } endforeach;
                                        $bag_points[$oSub->getId()]['available_points'] = $bag_points[$oSub->getId()]['total_points'] - $temp;
                                        $temp = null; ?>

                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                            <!--  End Current Bag Items-->

                            <!--   Start Secondary Bag Items-->
                            <?php  if ( $bag->getId() && !is_null($swapsItems = $bag->getAdminBag()->getSecondaryItems($bag->getId()))) : ?>
                                <div>
                                    <h2 style="text-align:center; margin-bottom:0 !important; font-size:24px;">Swappable Items</h2>
                                    <table style="" id="secondary-<?= $oSub->getId(); ?>"
                                           class="table table-hover table-responsive m-t">
                                        <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th class="no-sort">Points</th>
                                            <th class="no-sort">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($swapsItems as $items) : $supplier_name = '';
                                            $supplier_name = ''; ?>
                                            <tr id="secondary-tr-<?= $items->getId(); ?>">
                                                <td><?= $items->getItemsPoint()->getItems()->getName(); ?></td>
                                                <td width="120"><?= $items->getPoints(); ?></td>
                                                <td>
                                                    <button class="margin0 addProduct btn btn-info no-margin"
                                                            data-item-point-id="<?= $items->getItemsPoint()->getId(); ?>"
                                                            data-uid="<?= $oUser->getId(); ?>"
                                                            data-bagid="<?= $bag->getId(); ?>"
                                                            data-subid="<?= $oSub->getId(); ?>">Add
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (!is_null($bag->getUserBagItems())) : ?>
                                            <?php foreach ($bag->getUserBagItems() as $items) : $supplier_name = '';
                                                if ($items->getStatus() == 'Deleted') { ?>
                                                    <tr id="delete-tr-<?= $items->getId(); ?>">
                                                        <td><?= $items->getItemsPoint()->getItems()->getName(); ?></td>
                                                        <td width="120"><?= $items->getPoints(); ?></td>
                                                        <td>
                                                            <button data-pointid="tr-<?= $items->getId(); ?>"
                                                                    class="margin0 addProduct btn btn-info" style="margin:0;"
                                                                    data-bag="<?= $items->getId(); ?>"
                                                                    data-uid="<?= $oUser->getId(); ?>"
                                                                    data-subid="<?= $oSub->getId(); ?>">Add
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php } endforeach; ?>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                            <!--    End Secondary Bag Items-->

                            <!--   Start Deleted Bag Items-->
                            <?php if ( $bag->getIsChanged() == 1 && count($bag->getDeletedItems()) > 0) : ?>
                                <hr>
                                <div id="bag_<?= $oSub->getId(); ?>">
                                    <h2 onclick="hide_products('table_<?= $oSub->getId(); ?>')" class="update m-b-none clearfix"
                                        style="text-align: center;color:white; padding:10px; background:gray; cursor:pointer; font-size: 15px;">
                                        Previously Customized Bag<i class="pull-right fa fa-angle-down fa-2x"></i></h2>
                                    <table style="" id="table_<?= $oSub->getId(); ?>"
                                           class="table table-hover table-responsive m-t">
                                        <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Points</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($bag->getDeletedItems() as $items) : ?>
                                            <?php
//                                                    if ($items[0]['status'] == 'Active') {
                                            ?>
                                            <tr id="tr">
                                                <td><?= $items[0]['item_name']; ?></td>
                                                <td width="120"><?= $items[0]['points']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                            <!--   End Deleted Bag Items-->
                        <?php }else{ ?>
                            <div class="alert alert-info">
                                Bag is not ready at the moment. You will receive email notification once it is ready.
                            </div>
                        <?php } ?>
                        <div class="b-t m-t-md p-t-md">
                            <span class="subscriptionOptions changeSub"><a href="javascript:;"
                                        data-target="manageSub_<?= $oSub->getId(); ?>"><img
                                        src="/images/change.jpg" alt="Change" width="24" height="25"
                                        class="animated swing"/> Change</a>
                            </span>

                            <span class="subscriptionOptions"><a
                                    href="#" data-toggle="modal" data-target="#skipModal_<?= $oSub->getId(); ?>"><img
                                        src="/images/skip.jpg" alt="Skip" width="19" height="25"
                                        class="animated swing"/> Skip</a>
                            </span>

                            <span class="subscriptionOptions"><a
                                    href="#" data-toggle="modal"
                                    data-target="#stopSubscriptionModal_<?= $oSub->getId(); ?>"><img
                                        src="/images/stop.jpg" alt="Stop" width="23" height="25"
                                        class="animated swing"/> Stop</a></span> <span class="subscriptionOptions"><a
                                    href="#" data-toggle="modal" data-target="#futureSkips_<?= $oSub->getId(); ?>"><img
                                        src="/images/truck.jpg" alt="Delivery Schedule" width="46" height="25"
                                        class="animated swing"/> View schedule</a></span></div>
                        <!-- / footer-panel -->
                    </div>
                    <!-- /.panel -->

                <?php } ?>
            </div>
        <?php endforeach; ?>
        <!-- Active Subscription-->
    <?php } ?>
    <div class="row clearboth">
        <div class="container"><a data-toggle="collapse" class="btn btn-default" href="#history">View subscription
                history</a>

            <div class="collapse" id="history">
                <ul>
                    <?php if (count($aCanceled) > 0) : ?>
                        <?php foreach ($aCanceled as $oSub) : ?>
                            <li>Stopped a
                                <?= $oSub->getProduct()->getTitle(); ?>
                                subscription for
                                <?= money($oSub->getPricePaid()); ?>
                                on
                                <?= $oSub->getCanceledAt('M. d, Y'); ?>
                            </li>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <li>There are 0 subscriptions in your history.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <!-- /.container -->
    </div>
</div>

<!-- /#content -->

<?php if (isset($message) && $message) : ?>
    <div class="auto-message">
        <?= $message; ?>
    </div>
<?php endif; ?>
<div class="app-trigger" id="_Account"
     rel='<?= json_encode(array('p' => 'subscriptions', 'sessionID' => session_id(), 'iUser' => $oUser->getId())); ?>'>

</div>

<script type="text/javascript" src="/js/jquery.sliderTabs.min.js"></script>
<script type="text/javascript" src="/js/jquery.touchSwipe.min.js"></script>
<script type="text/javascript" src="/js/jquery.easing.min.js"></script>
<script src="/js/jquery.liquid-slider.min.js"></script>

<script>
    var bags = <?=json_encode($bag_points)?>;
    var extra_points = <?=BagsPeer::EXTRA_POINTS; ?>;
</script>
