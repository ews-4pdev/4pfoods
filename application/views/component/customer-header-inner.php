<div id="wrap">
	<div class="main">

<!-- FOR ROBIN UNIVERSAL FOR ALL FRONT FACING PAGES TO STICK THE FOOTER AT THE BOTTOM -->

		<!-- Start header -->
		
		<header class="break">
		
			<div class="container" id="headerBox">
				<a href="http://4pfoods.com/"><img src="/images/logo.png" alt="logo" height="100" id="logo" class="pull-left hidden-xs" /></a>
				<div id="logoText" class="pull-left muted hidden-xs">
					<a href="http://4pfoods.com/"><img src="/images/logoText.png" alt="logo"  /></a>
				</div><!-- /#logoText -->
				
				<div id="access" class="pull-right hidden-xs">
					<a href="/account/logout">Logout</a>
				</div><!-- /#access -->
			</div><!-- /.container -->
			
			<!-- Start Nav -->
			
			<div class="navbar" role="navigation">
		    	<div class="container">
		      		<div id="navWrap">
				        <div class="navbar-header">
				          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				            <span class="sr-only">Toggle navigation</span>
				            <span class="icon-bar"></span>
				            <span class="icon-bar"></span>
				            <span class="icon-bar"></span>
				          </button>
				        <a href="/" class="navbar-brand visible-xs"><img src="/images/logoText.png" alt="logo" height="20" width="120" /></a> 
				        </div>
				        <div class="collapse navbar-collapse">
				          <ul class="nav navbar-nav">
                  <?php foreach ($navData as $id => $item) : ?>
                    <li<?= ($id == $currentPage) ? ' class="active"' : ''; ?>><a  href="<?= $item['url']; ?>"><?= $item['title']; ?></a></li>
                  <?php endforeach; ?>
				          </ul>
				        </div><!-- /.nav-collapse -->
					</div><!-- /#navWrap -->
				</div><!-- /.container -->
		    </div><!-- /.navbar -->
		    
			<!-- End Nav -->
		
		</header>
		
		<!-- End header -->

		<blockquote class="error">
			  <!-- Adding error bar across the top -->
			  <div class="row">
			  	<div class="col-sm-12">
				  <div class="alert alert-error">  
				  	<a class="close" data-dismiss="alert">×</a>  
				  		Error message goes here. 
				  </div><!-- /.alert -->
			  	</div><!-- /.col -->
			  </div><!-- /.row -->
		 </blockquote>
