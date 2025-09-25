<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
 <meta name="robots" content="noindex, nofollow">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" -->
    <meta content='width=device-width, initial-scale=1, user-scalable=1, minimum-scale=1, maximum-scale=1' name='viewport'/> 
    <meta name="description" content="">
    <meta name="author" content="">

    <title>PA Transport</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <!-- Bootstrap core CSS-->
    <script type="text/javascript" src="<?php echo base_url().'admin/js/ajex.js';?>"></script>
    

    <!-- Custom fonts for this template-->
    <link href="<?php echo base_url().'admin/vendor/fontawesome-free/css/all.min.css'; ?>" rel="stylesheet" type="text/css">

    <!-- Page level plugin CSS-->
    <link href="<?php echo base_url().'admin/vendor/datatables/dataTables.bootstrap4.css'; ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
     
    <!-- Custom styles for this template-->
    <script src="<?php echo base_url().'admin/vendor/jquery/jquery.min.js'; ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
    <script src="<?php echo base_url().'admin/vendor/datatables/dataTables.bootstrap4.js'; ?>"></script>
    <script src="/assets/js/chart.js"></script>

    <link rel="stylesheet" href="/assets/js/jquery/dataTables.min.css">
    <script src="/assets/js/jquery/jquery.min.js"></script>
    <script src="/assets/js/jquery/dataTables.min.js"></script>
    <script src="/assets/js/jquery/jquery-ui.js"></script>

    <link href="<?php echo base_url().'admin/vendor/bootstrap/css/bootstrap.min.css'; ?>" rel="stylesheet">

<!--script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
 <link href="https://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" rel="Stylesheet"
        type="text/css" /-->
        
    <!--script type="text/javascript" src="https://code.jquery.com/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script-->
    <link href="<?php echo base_url().'admin/css/sb-admin.css'; ?>" rel="stylesheet">
    <link href="<?php echo base_url().'admin/css/jquery.tagsinput-revisited.css'; ?>" rel="stylesheet">
    <!--script type="text/javascript" src="<?php echo base_url().'admin/js/nicEdit.js';?>"></script>

      <script type="text/javascript">
       bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
      </script-->

    <link href="<?php echo base_url().'admin/css/custom-style.css'; ?>" rel="stylesheet" />
      
  </head>

  <body id="page-top">
    <main class="pt-dashboard-wrapper">

      <header class="pt-dashboard-header">
        <div class="pt-logo-site d-flex align-items-center justify-content-between">
          <div class="pt-logo-sitediv">
            <a href="<?php echo base_url('AdminDashboard/');?>" class="desktop-logo">
              <img src="<?php echo base_url().'admin/images/logo-icon.png'; ?>" alt="PA Transport" />
            </a>
            <a href="<?php echo base_url('AdminDashboard/');?>" class="toggle-logo">
              <img src="<?php echo base_url().'admin/images/logo-icon.png'; ?>" alt="PA Transport" />
            </a>
          </div>
          <a href="javascript: void(0);" class="pt-humburger d-block">
              <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M1.27075 15.7324H16.882V13.726H1.27075V15.7324ZM1.27075 10.6988H16.882V8.67481H1.27075V10.6988ZM1.27075 3.64121V5.66521H16.882V3.64121H1.27075Z" fill="#071021"/>
              </svg>
          </a>
        </div>
        <nav class="navbar navbar-expand navbar-dark bg-darks static-top">

          <!-- a class="navbar-brand mr-1" href="<?php //echo base_url('AdminDashboard/');?>"> PA Transport V3</a -->

          <!-- Navbar user logout -->
          <form class="d-none d-md-inline-block form-inline ml-auto mr-0 mr-md-3 my-2 my-md-0">
          
          </form>

          <!-- Navbar -->
          <ul class="navbar-nav ml-auto ml-md-0 pt-userlogout-ul">           
            
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-user-circle fa-fw"></i>
              </a>
              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
             
                <!-- div class="dropdown-divider"></div -->
                <a class="dropdown-item" href="<?php  echo base_url().'AdminLogin/logout/';  ?>" data-toggle="modal" data-target="#logoutModal">Logout</a>
              </div>
            </li>
          </ul>

        </nav>
      </header>