<div id="content">
    <div class="container">

        <h2>Credit card information</h2>

        <!-- Button trigger modal -->
        <button class="btn btn-default" data-toggle="modal" data-target="#cc">
            Update credit card
        </button>

        <!-- Modal -->
        <div class="modal fade" id="cc" tabindex="-1" role="dialog" aria-labelledby="Update credit card" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close resetClose" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <div class="title">Update credit card</div>
                    </div><!-- End modal header -->
                    <div class="wrapper margin">
                        <form id="billing" name="billing">
                            <div class="form-group m-b">
                                <label for="Card">Card number</label> <input class="form-control" id="ccNum" name="Card" placeholder="Card number" type="text" />

                                <div class="error-notify"></div>
                            </div><!-- End group -->

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="Month">Expiration Month</label>
                                        <select class="form-control" id="ccExpMonth" name="Month">
                                            <?php for ($i = 1; $i <= 12; $i++) : ?>
                                                <option value="<?= $i; ?>"><?= date('F', strtotime($i.'/1/2014')); ?></option>
                                            <?php endfor; ?>
                                        </select>
                                        <div class="error-notify"></div>
                                    </div><!-- End group -->
                                </div><!-- /. half -->

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="Year">Expiration year</label>
                                        <select class="form-control" id="ccExpYear" name="Year">
                                            <?php for ($i = date('Y'); $i <= date('Y') + 10; $i++) : ?>
                                                <option value="<?= $i; ?>"><?= $i; ?></option>
                                            <?php endfor; ?>
                                        </select>

                                        <div class="error-notify"></div>
                                    </div><!-- End group -->
                                </div><!-- /.half -->
                            </div><!-- /.row -->

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="Cvv">Security code</label> <input class="form-control" id="ccSec" name="Cvv" placeholder="Security code" type="text" />

                                        <div class="error-notify"></div>
                                    </div><!-- End group -->
                                </div><!-- /. half -->

                                <div class="col-sm-6">
                                    <img src="/images/cvv.png" alt="cvv" width="197" height="45" id="cvvImg" />
                                </div><!-- /.half -->
                            </div><!-- /.row -->

                            <button class="btn btn-default" type="button" id="update">Update Billing</button>
                        </form><!-- / end form -->
                    </div><!-- /.wrapper -->
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <h2>Billing history</h2>

        <section class="panel">
            <table class="table table-hover table-responsive">
                <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Payment Status</th>
                    <th style="width: 50%;">Details</th>
                    <th style="width: 30%; text-align: right">Total Price</th>
                    <th>Date processed</th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($aPayments as $oPayment) : ?>
                    <tr>
                        <td>#<?= $oPayment->getId(); ?></td>
                        <td>Paid</td>
                        <td>
                            <ul >
                                <?php foreach ($oPayment->getOrders() as $oOrder) : ?>
                                    <li>
                                        <?= $oOrder->getProductName(); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                        <td>
                            <ul class="text-right">
                                <?php foreach ($oPayment->getOrders() as $oOrder) : ?>
                                    <li>
                                        <?=( $oOrder->getDiscountApplied()  ) ? 'Discount ('.money($oOrder->getDiscountApplied()->getAmount()).')  '. money($oOrder->getPrice())  : money($oOrder->getPrice())  ; ?>
                                    </li>
                                <?php endforeach; ?>
                                <?php
                                $tax =  $oPayment->getTax();
                                $charges = $oPayment->getDeliverycharge();
                                ?>
                                <li>Tax - <?php   echo money( $tax );?></li>
                                <li>Delivery Charge - <?=money( $charges ); ?></li>
                                <li style="border-top: 1px solid;"><?=money( ( $oPayment->getTotalPrice() + $tax + $charges ) ); ?></li>
                            </ul>
                        </td>
                        <td><?= $oPayment->getCreatedAt('M d, Y'); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </section><!-- /.panel -->
    </div><!-- /.container -->
</div><!-- /#content -->
<div class="app-trigger" id="_Account" rel='<?= json_encode(array('p' => 'billing', 'sessionID' => session_id(), 'stripeKey' => STRIPE_PK, 'iUser' => $oUser->getId())); ?>'></div>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
