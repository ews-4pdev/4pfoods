<div id="confirm">
    <div class="loginBox">
    
        <img src="/images/confirm.jpg" alt="confirm" width="300" height="276" class="center-block img-responsive" />
                   
            
			<div class="heading m-b m-t">Thanks for joining.</div>
			
			<p>Only one step left. Please check your inbox for a message from us with instructions on how to confirm your email address.</p>
    	
    	<p class="m-t">Don't see the email? Click below, give us a <a href="http://4pfoods.com/contact/">shout</a> or call us at (703) 732.6664.</p>
    	
    	<button class="btn btn-default btn-sm m-t-md" id="resend">Resend email</button>
        
 </div><!-- /.loginBox -->
</div><!-- /#confirm -->
<div class="app-trigger" id="_Gateway" rel='<?= json_encode(array('p' => 'confirm', 'sessionID' => session_id(), 'hash' => $hash)); ?>'></div>