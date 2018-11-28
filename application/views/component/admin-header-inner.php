<div id="admin">
    <section class="screenBox fullHeight" id="customers">
        <!-- FOR ROBIN - the main navigation starts here -->

        <aside class="aside-lg leftPanel dark" id="nav">
            <section class="vbox">
                <header class="nav-bar navbar-inverse nav-bar-fixed-top">
                    <a class="btn btn-link visible-xs" data-toggle="class:nav-off-screen" data-target="#nav">
						<i class="fa fa-bars"></i>
					</a>
					<a href="#" class="nav-brand" data-toggle="fullscreen">4P Foods</a>
					<a href="/admin/logout" class="btn btn-link visible-xs" data-toggle="collapse" data-target=".navbar-collapse">
						<i class="fa fa-power-off"></i>
					</a>
                </header><!-- end header -->

                <section>
                    <div class="nav-primary hidden-xs">
                        <ul class="nav">
                          <?php foreach ($navData as $id => $item) : ?>
                            <li<?= ($id == $currentPage) ? ' class="active"' : ''; ?>><a href="<?= $item['url']; ?>"><span><?= $item['title']; ?></span></a></li>
                          <?php endforeach; ?>
                        </ul>
                    </div><!-- /.nav-primary -->
                </section><!-- end left nav section -->
            </section><!-- /.vbox -->
        </aside><!-- FOR ROBIN - the main navigation ends here -->
        <!-- FOR ROBIN - the main content section starts here -->

        <section id="adminContent">
        	<section class="screenBox fullHeight">
            <section class="vbox">
                <header class="header border">
                    <div class="title pull-left">
                        <?= $navData[$currentPage]['title']; ?>
                    </div>

                    <ul class="nav navbar-nav navbar-right hidden-xs">
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="/admin/logout" id="profile">4P Foods</a>

                            <ul class="dropdown-menu animated fadeInRight">
                                <li><a href="/admin/logout">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </header><!-- end header -->
                
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
							 
							 <section class="scrollable">

