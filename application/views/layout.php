<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <base href="<?php echo base_url(); ?>">
    <title>AngularJS and CodeIgniter</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <link href="static/css/bootstrap.css" rel="stylesheet">
    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>
    <link href="static/css/bootstrap-responsive.css" rel="stylesheet">
  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="<?php echo site_url('projects#/'); ?>">AngularJS and CodeIgniter</a>
        </div>
      </div>
    </div>

    <div class="container">

      <div ng-app="project">
        
        <div class="page-header">
          <h1>Projects</h1>
        </div>

        <div ng-view></div>

      </div>

    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="http://code.jquery.com/jquery-2.1.4.js"></script>
    <script src="https://code.angularjs.org/1.4.8/angular.js"></script>
    <script src="https://code.angularjs.org/1.4.8/angular-resource.js"></script>
    <script src="https://code.angularjs.org/1.4.8/angular-route.js"></script>
    <script src="static/js/bootstrap.min.js"></script>
    <script src="static/js/app.js"></script>
	<script src="static/js/controllers.js"></script>
	<script src="static/js/factorys.js"></script>

  </body>
</html>
