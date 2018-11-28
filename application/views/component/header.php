<!DOCTYPE html>
<html>
<head>
  <title>4P Foods</title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="/images/favicons/favicon-196x196.png" sizes="196x196">
  <link rel="icon" type="image/png" href="/images/favicons/favicon-160x160.png" sizes="160x160">
  <link rel="icon" type="image/png" href="/images/favicons/favicon-96x96.png" sizes="96x96">
  <link rel="icon" type="image/png" href="/images/favicons/favicon-16x16.png" sizes="16x16">
  <link rel="icon" type="image/png" href="/images/favicons/favicon-32x32.png" sizes="32x32">
  
  <!--[if IE]>
  	<link href="/ie.css" media="screen, projection" rel="stylesheet" type="text/css" />
  <![endif]-->
  
  <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--
	    	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        -->
			<script src="/js/respond.min.js"></script>
	    <!--[if lt IE 9]>
	      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->

  <script type="text/javascript" src="/js/moment.min.js"></script>


  <link href="/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
  <link href="/css/font-awesome.css" rel="stylesheet">
  <link href="/css/animate.min.css" rel="stylesheet" type="text/css" />

  <link href="/css/alertify.core.css" rel="stylesheet" type="text/css" />
  <link href="/css/alertify.theme.css" rel="stylesheet" type="text/css" />

  <link href="/css/screen.css" rel="stylesheet" type="text/css" />
  <link href="/css/style.css" rel="stylesheet" type="text/css" />
  <link href="/css/print.css" rel="stylesheet" type="text/css" />
  <link href="/css/animate.min.css" rel="stylesheet" type="text/css" />
  <link href="/css/datepicker.css" rel="stylesheet" type="text/css" />
  <link href="/css/liquid-slider.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" type="text/css" href="/css/dataTables.bootstrap.css"/>
  <link rel="stylesheet" type="text/css" href="/css/bootstrap-datetimepicker-standalone.css"/>

<!--  // By EWS-->
<!--  <link href="/css/select2.css" rel="stylesheet" type="text/css" />-->
<!--  <link href="/css/select2-bootstrap.css" rel="stylesheet" type="text/css" />-->
<!--  <script type="text/javascript" src="/js/select2.js"></script>-->
<!--  // End EWS-->
  <script type="text/javascript" src="/js/<?= _JS_DIR; ?>/jquery-1.10.2.<?= (_JS_DIR == 'dev') ? '' : 'min.'; ?>js"></script>
  <script type="text/javascript" src="/js/<?= _JS_DIR; ?>/bootstrap.<?= (_JS_DIR == 'dev') ? '' : 'min.'; ?>js"></script>
  <script type="text/javascript" src="/js/<?= _JS_DIR; ?>/jquery.loadTemplate-1.3.2.<?= (_JS_DIR == 'dev') ? '' : 'min.'; ?>js"></script>
  <script type="text/javascript" src="/js/<?= _JS_DIR; ?>/class.js"></script>
  <script type="text/javascript" src="/js/dataTables.bootstrap.js"></script>
  <script type="text/javascript" src="/js/front.js"></script>
  <script type="text/javascript" src="/js/jquery.bootstrap.wizard.min.js"></script>
  <script type="text/javascript" src="/js/datepicker/bootstrap-datepicker.js"></script>
  <script type="text/javascript" src="/js/alertify.js"></script>
  <?php if (JS_BUILD) : ?>
  <script type="text/javascript" src="/js/build-<?= (isset($_get_build)) ? $_get_build : 'main'; ?>.js?3"></script>
<?php else : ?>
  <script type="text/javascript" src="/assets/js/<?= (isset($_get_build)) ? $_get_build : 'main'; ?>"></script>
<?php endif; ?>
  <script type="text/javascript">
    $(document).ready(function()
    {
      this._engine = new Engine('<?= buildHash(10); ?>');

      $('.modal').on('hidden.bs.modal', function(e)
      {
        $('.error-notify').text('').hide();
      }) ;
    });
  </script>
  
  <script type="text/javascript">
	  try {
	  document.execCommand('BackgroundImageCache', false, true);
	  }
	  catch(e) {};
  </script> 
</head>
<body>
<?php if (isset($error) && (bool)($error)) : ?>
<div class="error-block hide"><?= $error; ?></div>
<?php endif; ?>
