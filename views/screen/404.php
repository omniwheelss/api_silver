<!DOCTYPE html>
<html lang="en">
  <head>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="<?=$seo_array[1]?>">
	<meta name="keywords" content="<?=$seo_array[0]?>">
	<meta name="copyright" content="<?=date('Y')?> <?=CONFIG::WEBSITE_NAME?>">
	<meta name="author" content="<?=CONFIG::WEBSITE_NAME?>">
	<meta name="email" content="webmaster@<?=CONFIG::WEBSITE_NAME?>">
	<meta name="Charset" content="ISO-8859-1">
	<meta name="Distribution" content="Global">
	<meta name="Rating" content="General">
	<meta name="Robots" content="INDEX,FOLLOW">
	<meta name="Revisit-after" content="1 Day">	
    <title><?=CONFIG::HOME_TITLE?></title>

    <!-- Bootstrap -->
    <link href="./follow/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="./follow/font-awesome/css/font-awesome.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="./build/css/custom.min.css" rel="stylesheet">
  </head>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
        <!-- page content -->
        <div class="col-md-12">
          <div class="col-middle">
            <div class="text-center text-center">
              <h1 class="error-number">404</h1>
              <h2>Sorry but we couldn't find this page</h2>
              <p>This page you are looking for does not exist? Go to <a href="index.php?do=home">Home</a></p>
			  <!--
              <div class="mid_center">
                <h3>Search</h3>
                <form>
                  <div class="col-xs-12 form-group pull-right top_search">
                    <div class="input-group">
                      <input type="text" class="form-control" placeholder="Search for...">
                      <span class="input-group-btn">
                              <button class="btn btn-default" type="button">Go!</button>
                          </span>
                    </div>
                  </div>
                </form>
              </div>
			  -->
            </div>
          </div>
        </div>
        <!-- /page content -->
      </div>
    </div>

    <!-- jQuery -->
    <script src="./follow/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="./follow/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script src="./follow/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="./follow/nprogress/nprogress.js"></script>

    <!-- Custom Theme Scripts -->
    <script src="./build/js/custom.min.js"></script>
  </body>
</html>
