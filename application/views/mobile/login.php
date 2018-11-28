<div id="driverLogin" class="margin">
    <div class="loginBox">
    
        <img src="/images/logo-login.png" alt="logo-login" width="150" height="111" class="center-block img-responsive m-b" />
                   
            
        <h2>Driver login</h2>
    	<form id="formLogin">
            <div class="form-group m-b">
                <label for="Email">Email</label> <input class="form-control" id="cEmail" name="Email" placeholder="Email" type="email" />

                <div class="error-notify"></div>
            </div><!-- End group -->
            
            <div class="form-group m-b">
                <label for="Password">Password</label> <input class="form-control" id="cPassword" name="Password" placeholder="Password" type="password" />

                <div class="error-notify"></div>
            </div><!-- End group -->
            
            <button class="btn btn-default" id="cSubmit" type="submit">Sign in</button>
        </form><!-- / end form -->
	</div><!-- /.loginBox -->
</div><!-- /#driverLogin -->
<div class="app-trigger" id="_Driver" rel='<?= json_encode(array('p' => 'login', 'sessionID' => session_id())); ?>'></div>
