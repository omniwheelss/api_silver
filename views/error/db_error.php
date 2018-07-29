<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?=Config::DB_ERROR_TITLE;?></title>

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
              <h1 class="error-number">DB ERROR</h1>
              <h2>Please check your database connection</h2>
              <p>Error Details below</p>
              <div class="mid_center">
                <form>
                  <div class="col-xs-12 form-group pull-right top_search">
                    <div class="input-group">
											<div class="alert alert-danger alert-dismissible fade in" role="alert">
											<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
											</button>
											<strong>Database : <?=$db_check[0]?></strong> <?=$db_check[1]?>
											</div>
                    </div>
                  </div>
                </form>
              </div>
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