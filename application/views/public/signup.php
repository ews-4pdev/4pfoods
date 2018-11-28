<div id="wrap">
    <div id="signup" class="main">
        <!-- Start header -->
        <header class="break">
            <div class="container edit" id="headerBox">
                

<a href="/"><img src="/logo2.png" alt="logo" height="100" id="logo" class="pull-left hidden-xs"/></a>

                <div id="logoText" class="pull-left muted hidden-xs">
                    <a href="/"><img src="/logoTextblank.png" alt="logo"/></a>
                </div><!-- /#logoText -->

                <div id="access" class="pull-right hidden-xs">
                    Already a member? <a href="/gateway/login">Login</a>
                </div><!-- /#access -->
            </div><!-- /.container -->

            <!-- Start Nav -->
            <div class="navbar" role="navigation">
                <div class="container">
                    <div id="navWrap">
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle" data-toggle="collapse"
                                    data-target=".navbar-collapse">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                            <a href="/" class="navbar-brand visible-xs"><img src="/logoText2.png" alt="logo"
                                                                             height="20" width="120"/></a>
                        </div>
                        <div class="collapse navbar-collapse">
                            <ul class="nav navbar-nav">
                                <li><a href="http://4pfoods.com/get-a-bag/">Get a bag</a></li>
                                <li><a href="http://4pfoods.com/how-it-works/">How it works</a></li>
                                <li><a href="http://4pfoods.com/category/impact/">Impact</a></li>
                                <li><a href="http://4pfoods.com/about-us/">About 4p</a></li>
                            </ul>
                        </div><!-- /.nav-collapse -->
                    </div><!-- /#navWrap -->
                </div><!-- /.container -->
            </div><!-- /.navbar -->
            <!-- End Nav -->
        </header><!-- End header -->

        <div class="break m-b">
            <div class="container"></div><!-- /.container -->
        </div><!-- /break -->

        <div class="edit container">
            <div class="panel-group" id="accordion">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title"><a class="title" data-parent="#accordion" data-toggle="collapse"
                                                    href="#collapseThree"><span class="stepsNumber">1</span>
                                Delivery</a></div>
                    </div>
                    <div class="panel-collapse active " id="collapseThree">
                        <div class="panel-body">
                            <div class="stepInnerWrapper" id="pinWrapper">
                                <div class="heading m-b m-t text-center">
                                    Enter 5 Digit Zip Code
                                </div>
                                <form class="m-b text-center clearfix" role="form" id="pin">
                                    <div class="form-group">
                                        <input autocomplete="off" class="form-control pinBox center-block" id="pinBox" name="Pin"
                                               type="text" maxLength="5"/>

                                        <div class="error-notify"></div>
                                    </div><!-- End group -->

                                </form><!-- /#pin -->
                                <p class="error-notify text-center">Invalid Site ID. Please make sure you entered it
                                    correctly!</p>

                                <div id="doorstep" class=" text-center hidden">
                                    <div class="text-left" style="display: flex; justify-content: center; align-content: center">
                                        <div style="width: 75%">
                                            Great news! Based on where you live, we can deliver right to your doorstep.
                                            We’ll need as much detail from you as possible to help our driver
                                            make sure they leave your bags of great food in the best place possible – even
                                            if you’re not home. Please fill out the form below adding any details that you think will help us out.
                                            Once complete, finish the sign up process and we’ll send you an email to confirm that you’re all set.
                                            Please note that doorstep delivery takes us some extra time and energy.
                                            There is a flat <?=money(DELIVERY_CHARGE) ?> fee that helps to cover some of that.
                                            Since we are awesome humans (and not drones) delivering your food with care, hopefully you’ll agree that it’s totally worth it.
                                        </div>

                                    </div>

                                    <div class="text-center text-primary">OR</div>
                                    <br>
                                    <button id="pickup" class="hidden btn btn-info">Pick a Delivery Location Instead
                                    </button>
                                </div>
                                <div id="doorstep_access_code" class="hidden  text-center">
                                    You are choosing a delivery location instead of door-step delivery which is
                                    available in your zip code.
                                    <br>
                                    OR
                                    <br>
                                    <button id="doorstep_pickup" class="hidden btn btn-info">Choose DoorStep Delivery Instead
                                    </button>
                                </div>
                                <div id="access_code" class="hidden text-left" style="display: flex; justify-content: center;">
                                    <div style="width: 75%;">
                                        We don’t have direct home delivery in your neighborhood just yet.
                                        The good news is we do have many sites within a short radius from your location that you can use to pick up your bags each week.
                                        Select from the list below that is most convenient for you.
                                        <br><br>We’ll email you as soon as your bags are dropped off so you know when you can stop by every week.
                                        Email us with any questions!  community@4pfoods.com
                                    </div>

                                </div>

                                <div id="zipcode-panel" class="show site-picker panel m-t padding hidden">
                                    <ul id="site-feed" class="site-feed">

                                    </ul><!-- /.site-feed -->
                                </div><!-- /.site-picker -->

                                <div class="col-lg-12">
                                    <div id="notify_user" style="width:50%; margin: 0 auto;" class="hidden clearfix">
                                        <div id="no_address"  class="text-left m-t m-b">
                                            Sorry, we don’t deliver in or around your zip code just yet – but we are expanding.
                                            <br> <br>
                                            Just leave us your name and e-mail address and we’ll let you know once we do start delivering in your vicinity.
                                        </div>
                                        <div>
                                            <form id="notify_user_form" action="">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label for="FName">First name</label> <input
                                                                class="form-control" id="fname" name="FName"
                                                                type="text">

                                                            <div class="error-notify"></div>
                                                        </div><!-- End group -->
                                                    </div><!-- /. half -->
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label for="LName">Last name</label> <input
                                                                class="form-control" id="lname" name="LName"
                                                                type="text">
                                                            <div class="error-notify"></div>
                                                        </div><!-- End group -->
                                                    </div><!-- /. half -->
                                                </div><!-- /.row -->

                                                <div class="form-group">
                                                    <label for="email">Email</label> <input
                                                        class="form-control" id="email" name="email"
                                                        type="text">
                                                    <div class="error-notify"></div>
                                                </div>

                                                <div class="col-lg-12 text-center">
                                                    <button id="subscribe" class="btn btn-primary">Subscribe</button>
                                                </div>

                                            </form>
                                        </div>
                                    </div>
                                </div>


                                <div id="customAddress" class="m-t collapse">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <form id="cAddress">
                                                <input id="doorstepInput" type="hidden" name="doorstep" value="0">

                                                <div class="form-group">
                                                    <label for="Address1">Address</label> <input class="form-control"
                                                                                                 id="cAddress1"
                                                                                                 name="Address1"
                                                                                                 type="text">

                                                    <div class="error-notify"></div>
                                                </div><!-- End group -->
                                                <div class="form-group">
                                                    <label for="Address2">Address 2</label> <input class="form-control"
                                                                                                   id="cAddress2"
                                                                                                   name="Address2"
                                                                                                   type="text">

                                                    <div class="error-notify"></div>
                                                </div><!-- End group -->

                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label for="City">City</label> <input class="form-control"
                                                                                                  id="cCity" name="City"
                                                                                                  type="text">

                                                            <div class="error-notify"></div>
                                                        </div><!-- End group -->
                                                    </div><!-- /. half -->

                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label for="State">State</label>
                                                            <select class="form-control" id="cStateId" name="StateId">
                                                                <?php $i = 1; ?>
                                                                <?php foreach ($aStates as $id => $name) : ?>
                                                                    <option value="<?= $id; ?>"><?= $name; ?></option>
                                                                    <?php $i = $i + 1; endforeach; ?>
                                                            </select>

                                                            <div class="error-notify"></div>
                                                        </div><!-- End group -->
                                                    </div><!-- /. half -->
                                                </div><!-- /.row -->
                                                <div class="row">
                                                    <div class="col-sm-6 col-offset-sm-6">
                                                        <div class="form-group">
                                                            <label for="cZip">Zip/Postal code</label> <input
                                                                class="form-control" id="cZip" name="Zip" type="text">

                                                            <div class="error-notify"></div>
                                                        </div><!-- End group -->
                                                    </div><!-- /. half -->
                                                </div><!-- /. row -->
                                                <input type="hidden" id="cAccessCode" name="AccessCode">
                                            </form><!-- / end form -->
                                        </div><!-- /.half -->
                                        <div class="col-sm-6 hidden-sm hidden-xs">
                                            <img src="/images/map.jpg" alt="map" width="280" height="375" id="map"/>
                                        </div><!-- /.half -->
                                    </div><!-- /.row -->
                                </div><!-- /#customAddress -->
                                <button class="btn btn-primary btn-lg center-block collapseButton hide" type="button"
                                        data-this="collapseThree" data-next="collapseFour">Continue
                                </button>
                            </div><!-- /.StepInnerWrapper -->
                        </div><!-- /.panel-body -->
                    </div><!-- /.panel-collapse -->
                </div><!-- /.panel -->
                <div class="hidden panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <a class="title" data-parent="#accordion" data-toggle="collapse" href="#collapseOne"><span
                                    class="stepsNumber">2</span> Confirm order</a></div>
                    </div>

                    <div class="panel-collapse active " id="collapseOne">
                        <div class="panel-body">
                            <div class="stepInnerWrapper clearfix">
                                <div class="row clearfix">
                                    <?php foreach ($aProducts as $oCat) : ?>
                                        <?php if ($oCat->countProducts() == 0) continue; ?>
                                        <div class="col-sm-4">
                                            <div class="productBox panel border-strong p-lg no-shadow">
                                                <img class="productImg img-responsive" src="<?= $oCat->getImage(); ?>"
                                                     alt="bag"/>

                                                <div class="title productTitle">The <?= $oCat->getTitle(); ?> bag</div>
                                                <div class="productDescription">
                                                    <p><?= $oCat->getDescription(); ?></p>

                                                    <div class="title muted">Bag Size</div>
                                                    <form class="form" role="form">
                                                        <div class="radio">
                                                            <label>
                                                                <input class="catnone" type="radio" name="Products[]"
                                                                       id="cat<?= $oCat->getId(); ?>none" value=""
                                                                       data-cat="<?= $oCat->getId(); ?>"
                                                                       checked/>
                                                                No thanks
                                                            </label>
                                                        </div><!-- /.radio -->

                                                        <?php foreach ($oCat->getProducts() as $oProduct) : ?>
                                                            <?php if($oProduct->getIsPublished(true)){ ?>
                                                            <div class="radio">
                                                                <label>
                                                                    <input type="radio" class="productCheck"
                                                                           name="Products[]" data-cat="<?= $oCat->getId(); ?>"
                                                                           id="p<?= $oProduct->getId(); ?>"
                                                                           value="<?= $oProduct->getId(); ?>"/>
                                                                    <span class="productInfo"
                                                                          id="pi_<?= $oProduct->getId(); ?>"
                                                                          rel="<?= $oProduct->getPrice(); ?>"><?= $oProduct->getTitle(); ?>
                                                                        - <?= '$'.$oProduct->getPrice(); ?>
                                                                        / delivery
                                                                    </span>
                                                                </label>
                                                            </div><!-- /.radio -->
                                                            <?php } ?>
                                                        <?php endforeach; ?>
                                                    </form><!-- / form -->
                                                </div><!-- /.productDescription -->
                                            </div><!-- /#productBox -->
                                        </div><!-- / 6 col -->
                                    <?php endforeach; ?>
                                </div><!--. row -->
                                <button class="btn btn-primary btn-lg center-block collapseButton hide" type="button"
                                        data-this="collapseOne" data-next="collapseTwo">Continue
                                </button>
                            </div><!-- /.StepInnerWrapper -->
                        </div><!-- /.panel-body -->
                    </div><!-- /.panel-collapse -->
                </div><!-- /.panel -->
                <div class="hidden panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <a class="title" data-parent="#accordion" data-toggle="collapse" href="#collapseTwo"><span
                                    class="stepsNumber">3</span> Register</a>
                        </div>
                    </div>
                    <div class="panel-collapse active " id="collapseTwo">
                        <div class="panel-body">
                            <div class="stepInnerWrapper clearfix">
                                <div class="row clearfix">
                                    <div class="col-sm-6">
                                        <div class="title">Create your account:</div>
                                        <form id="fAccount" name="Account">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="FirstName">First name</label> <input
                                                            class="form-control" id="cFirstName" name="FirstName"
                                                            type="text">

                                                        <div class="error-notify"></div>
                                                    </div><!-- End group -->
                                                </div><!-- /. half -->
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="LastName">Last name</label> <input
                                                            class="form-control" id="cLastName" name="LastName"
                                                            type="text">

                                                        <div class="error-notify"></div>
                                                    </div><!-- End group -->
                                                </div><!-- /. half -->
                                            </div><!-- /.row -->

                                            <div class="form-group m-b">
                                                <label for="Phone">Phone</label> <input class="form-control" id="cPhone"
                                                                                        name="Phone" type="text">

                                                <div class="error-notify"></div>
                                            </div><!-- End group -->

                                            <div class="form-group m-b">
                                                <label for="Email">Email</label> <input class="form-control" id="cEmail"
                                                                                        name="Email" type="email">

                                                <div class="error-notify"></div>
                                            </div><!-- End group -->
                                            <div class="form-group m-b">
                                                <label for="Password">Password</label> <input class="form-control"
                                                                                              id="cPassword"
                                                                                              name="Password"
                                                                                              type="password">

                                                <div class="error-notify"></div>
                                            </div><!-- End group -->
                                            <div class="form-group m-b">
                                                <label for="Confirm Password">Confirm Password</label> <input
                                                    class="form-control" id="cConfirmPassword" name="ConfirmPassword"
                                                    type="password">

                                                <div class="error-notify"></div>
                                            </div><!-- End group -->

                                            <div class="row doorstep">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="Mobile">Cell Phone</label> <input
                                                            class="form-control" id="Mobile" name="Mobile"
                                                            type="text">

                                                        <div class="error-notify"></div>
                                                    </div><!-- End group -->
                                                </div>
                                                <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label for="AMobile">Alternate Cell Phone</label> <input
                                                                class="form-control" id="AMobile" name="AMobile"
                                                                type="text">

                                                            <div class="error-notify"></div>
                                                        </div><!-- End group -->
                                                    </div>
                                            </div>

                                            <div class="form-group doorstep">
                                                    <div>Is this building a:</div>

                                                    <div class="col-lg-6">
                                                        <div class="checkbox ">
                                                            <label for="SingleFamilyHome">
                                                                <input id="SingleFamilyHome" class="BuildingType" name="SingleFamilyHome" value=""
                                                                       type="checkbox">Single Family Home
                                                            </label>
                                                        </div>
                                                        <div class="checkbox">
                                                            <label for="ApartmentCondo">
                                                                <input id="ApartmentCondo" class="BuildingType" name="ApartmentCondo" value=""
                                                                       type="checkbox">Apartment/Condo
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <div class="checkbox">
                                                            <label for="OfficeBuilding">
                                                                <input id="OfficeBuilding" class="BuildingType" name="OfficeBuilding" value=""
                                                                       type="checkbox">Office Building
                                                            </label>
                                                        </div>

                                                        <div class="checkbox">
                                                            <label for="TownHouse">
                                                                <input id="TownHouse" class="BuildingType" name="TownHouse" value=""
                                                                       type="checkbox">TownHouse
                                                            </label>
                                                        </div>
                                                    </div>
                                            </div>
                                            <div style="clear: both; height: 1px;"></div>

                                            <div class=" m-t-md doorstep">
                                                <div class="form-group">
                                                    <div class="checkbox">
                                                        <label for="concierge">Is there a concierge we can leave the
                                                            package with?</label>
                                                        <input class="" placeholder="Concierge"
                                                               id="concierge" name="Concierge" type="checkbox">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="m-t-md doorstep">
                                                <div class="form-group">
                                                    <label for="concierge_number">Enter Concierge Number</label>
                                                    <input class="form-control" placeholder="Concierge Number"
                                                           id="concierge_number" name="ConciergeNumber" type="text">
                                                </div>
                                            </div>

                                            <div class=" m-t-md doorstep">
                                                <div class="form-group">
                                                    <div>If a key fob is needed to access the
                                                        building, can you provide us with a fob?</div>
                                                    <div class="pull-left m-r-sm">
                                                        <label for="KeyFob">Yes</label>
                                                            <input id="KeyFob" name="KeyFob" data-value="true"
                                                                   type="radio">
                                                    </div>

                                                    <div>
                                                        <label for="KeyFob1">No</label>
                                                        <input class="" id="KeyFob1" name="KeyFob" data-value="false"
                                                               type="radio">
                                                    </div>


                                                </div>
                                            </div>

                                            <div class=" m-t-md doorstep">
                                                <div class="form-group">
                                                    <label for="Parking">Where can we easily park while making the
                                                        delivery?</label>
                                                        <textarea class="form-control" id="Parking" name="Parking"
                                                                  rows="3"></textarea>

                                                    <div class="error-notify"></div>
                                                </div><!-- End group -->
                                            </div>

                                            <!--  Ended   -->

                                            <div class="form-group">
                                                <label for="DietaryRestrictions">Dietary Restrictions</label> <textarea
                                                    class="form-control" id="cDietaryRestrictions"
                                                    name="DietaryRestrictions" rows="3"></textarea>

                                                <div class="error-notify"></div>
                                            </div><!-- End group -->


                                            <div class="form-group m-b">
                                                    <label for="DeliverySiteNotes">Any other special instructions?
                                                        Pretend like you are telling a 1st grader where to deliver your
                                                        bag of goodies.The more detail the better!</label> <textarea
                                                        class="form-control" id="cDeliverySiteNotes"
                                                        name="DeliverySiteNotes" rows="3"></textarea>

                                                    <div class="error-notify"></div>
                                                </div><!-- End group -->

                                            <!-- Revision Start By EWS ------ Add Checkbox for Terms and Service -->
                                            <div class="form-group m-b">
                                                <label class="checkbox">
                                                    <input type="checkbox" id="cterms" name="cterms">
                                                    <small>By registering, you agree to our <a
                                                            href="http://4pfoods.com/terms-of-service" target="_blank"
                                                            id="termsofservices">Terms of Service</a>.
                                                    </small>
                                                </label>

                                                <div class="error-notify"></div>
                                            </div>
                                            <!-- Revision Start By EWS ------ Add Checkbox for Terms and Service -->
                                        </form><!-- / end form -->
                                    </div><!-- /.half -->
                                    <div class="col-sm-6 hidden-md hidden-sm hidden-xs">
                                        <img src="/images/avocado.jpg" alt="basket" width="290" height="387"
                                             id="registerBasket"/>
                                    </div><!-- /.half -->
                                </div><!--. row -->
                                <button class="btn btn-primary btn-lg center-block collapseButton hide" type="button"
                                        data-this="collapseTwo" data-next="collapseThree">Continue
                                </button>
                            </div><!-- /.Ste#nerWrapper -->
                        </div><!-- /.panel-body -->
                    </div><!-- /.panel-collapse -->
                </div><!-- /.panel -->
                <div class="hidden panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <a class="title" data-parent="#accordion" data-toggle="collapse" href="#collapseFour"><span
                                    class="stepsNumber">4</span> Finish up</a>
                        </div>
                    </div>
                    <div class="panel-collapse" id="collapseFour">
                        <div class="panel-body">
                            <div class="stepInnerWrapper clearfix">
                                <div class="row clearfix">
                                    <div class="col-sm-6">
                                        <div class="title m-b"><i class="fa fa-lock accent2"></i> Enter your billing
                                            information:
                                        </div>
                                        <form id="billing" name="billing">
                                            <a data-toggle="collapse" href="#promotion" class="accent2">Have a promo
                                                code?</a>

                                            <div class="collapse" id="promotion">
                                                <div class="form-group m-b">
                                                    <label for="DiscountCode"></label> <input class="addDiscount"
                                                                                              id="DiscountCode"
                                                                                              name="DiscountCode"
                                                                                              placeholder="Enter code"
                                                                                              type="text">

                                                    <div class="error-notify"></div>
                                                    <button class="btn btn-primary" type="button"
                                                            id="applyDiscountButton">Apply
                                                    </button>
                                                </div><!-- End group -->
                                            </div><!-- /#promotion -->
                                            <div class="form-group m-b">
                                                <label for="Card">Card number</label> <input class="form-control"
                                                                                             id="ccNum" name="Card"
                                                                                             type="text">

                                                <div class="error-notify"></div>
                                            </div><!-- End group -->
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="Month">Expiration Month</label>
                                                        <select class="form-control" id="ccExpMonth" name="Month">
                                                            <?php for ($i = 1; $i <= 12; $i++) : ?>
                                                                <option
                                                                    value="<?= $i; ?>"><?= date('F', strtotime($i . '/1/2014')); ?></option>
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
                                                        <label for="CVV">Security code</label> <input
                                                            class="form-control" id="ccSec" name="CVV" type="text">

                                                        <div class="error-notify"></div>
                                                    </div><!-- End group -->
                                                </div><!-- /. half -->
                                                <div class="col-sm-6">
                                                    <img src="/images/cvv.png" alt="cvv" width="197" height="45"
                                                         id="cvvImg"/>
                                                </div><!-- /.half -->
                                            </div><!-- /.row -->
                                        </form><!-- / end form -->
                                    </div><!-- /.half -->
                                    <div class="col-sm-6">
                                        <div id="recieptWrap" class="clearfix">
                                            <div id="recieptBody">
                                                <div class="title">Order Summary</div>
                                                <p><strong>Item(s):</strong></p>

                                                <div id="itemList"></div>
                                            </div><!-- /#recieptBody -->
                                            <!-- Revision Start By EWS ------ Add Tax in Order Summary and Update Summary Block -->
                                            <div id="recieptFooter" class="m-t clearfix">
                                                <div id="discountInfo" class="hide">
                                                    <small><span class="accent2">Discount applied:</span><br>
                                                        -$<span id="discountAmount">19</span> per box (<span
                                                            id="discountOrders">4</span> boxes).
                                                    </small>
                                                </div>
                                                <div class="pull-left">
                                                    <div id="deliveryCharge" class="title hide">Delivery Cost</div>
                                                    <div id="Tax" class="title hide">Tax</div>
                                                    <div id="title" class="title">Total</div>
                                                </div>
                                                <div class="pull-right">
                                                    <div class="title hide" id="deliveryChargeAmount"></div>
                                                    <div class="title hide" id="TaxAmmount"></div>
                                                    <div class="title" id="productPrice"></div>
                                                </div>
                                            </div>
                                            <!-- Revision End By EWS ------ Add Tax in Order Summary and Update Summary Block -->
                                            <!-- /#recieptFooter -->
                                        </div><!--/#recieptWrap -->
                                    </div><!--/.half -->
                                </div><!--/.row-->
                                <div class="clearfix"></div>
                                <button class="btn btn-primary btn-lg center-block m-t-lg" type="button"
                                        id="completeOrder" data-loading-text="Please wait...">Complete order
                                </button>
                            </div><!--/.stepInnerWrapper -->
                        </div><!-- /.panel-body -->
                    </div><!--/.panel-collapse -->
                </div><!-- /.panel -->
            </div><!--/ .Panel Group -->
            <div class="expectBox m-t m-b hidden-xs">
                <img src="/images/banner-footer.jpg" alt="banner-footer" width="430" height="50" class="center-block"/>
            </div>
        </div><!--/ .container -->
    </div><!--/ #singup -->
</div><!-- /.wrap -->
<footer class="footer simple">
    <div class="container edit">
        <div class="pull-right muted">
            <small>Copyright <?= date('Y') ?> 4P Foods. All rights reserved. Food is power.</small>
        </div>
    </div><!-- /.container -->
</footer><!-- /.simple-footer -->
<div class="hide" id="customText">
    <h2>It looks like you are signing up from a new address... Awesome!</h2>

    <p class="left m-b-sm">Ah, bummer. It looks like we don’t yet deliver directly to doorsteps in your neighborhood.
        Enter your address below and we will let you know once we DO deliver in your area.</p>

    <p class="left m-b-sm">Or, check out our list of other drop sites nearby. Remember that we also deliver to many
        offices in and around DC – if we can’t deliver to your home, maybe your office will work as a good plan B?</p>

    <p class="left m-b-sm">Questions? Please reach out: community@4pfoods.com</p>
</div>

<div class="app-trigger" id="_SignupForm"
     rel='<?= json_encode(array('deliveryCharges' => $deliveryCharges,   'p' => 'signup', 'sessionID' => session_id(), 'stripeKey' => STRIPE_PK, 'iProduct' => $iProduct)); ?>'></div>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>

