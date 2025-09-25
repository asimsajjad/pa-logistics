<html lang="en">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>PA Transport</title>
<meta name="viewport" content="width=device-width, initial-scale=1"/>


<meta name="description" content="PA Transport">
      
   
  <link href="/assets/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
  <link href="/assets/css/style.css" rel="stylesheet" type="text/css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://use.fontawesome.com/releases/v5.12.1/js/all.js" crossorigin="anonymous"></script>
        <!-- Google fonts-->
        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
</head>

<body>


<div id="wrapper">

<!--Header-->


<header>

<div class="header_bar col-sm-12 col-md-12 col-lg-12 col-xs-12">
<div class="container">
    <ul>
	<li><a class="btn btn-primary" href="<?php echo base_url(); if($this->session->userdata('role')==2)
            { echo 'profile';}else{ echo 'login';}?>">Account</a></li>
	<li><a href="https://www.facebook.com/allbigmanual/" target="_blank"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
	<li><a href="https://twitter.com/ManualBig" target="_blank"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
	<li><a class="btn btn-primary" href="/cart"><i class="fa fa-shopping-basket"></i>Cart <span class="counted_cart"><?php echo $this->cart->total_items();?></span></a></li>
	
	</ul>
</div>
</div>

<div class="top_header col-sm-12 col-md-12 col-lg-12 col-xs-12">
     <div class="container">
        <div class="row">

           <div class="logo col-md-3 col-sm-3 col-xs-12">
              <a href="<?php base_url();?>/"><img class="img-responsive" src="/assets/images/logo.png" alt="logo"></a>
           </div>

                <div class="address_navbar col-md-9 col-sm-9 col-xs-12">
				  <nav class="navbar navbar-expand-md navbar-dark ">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
                  <span class="navbar-toggler-icon"></span>
                </button>
				                                
             <div class="collapse navbar-collapse" id="collapsibleNavbar">
               <ul class="navbar-nav">
                 <li class="nav-item">
                 <a class="nav-link" href="/">Home</a>
                   </li>
                   <li class="nav-item">
                      <a class="nav-link" href="<?php echo base_url(); ?>about">About Us</a>
                       </li>
                      
                        <li class="nav-item dropdown">
                        <a class="nav-link" href="<?php echo base_url();?>categories">Categories</a>
						 <ul class="dropdown-content">
                         <?php $user_details = get_user_details(); 
                         $i=0;
                        
                         foreach($user_details as $user_details_view){
                              if($i<5){
                             echo '<li class="nav-item"><a class="nav-link" href="'.base_url().'category/'.$user_details_view['id'].'">'.$user_details_view['name'].'</a></li>';
                             
                          $i++;}}
                         ?>
                         </ul>
                         </li>
                        
                        <!--li class="nav-item">
                         <a class="nav-link" href="#">Site Map</a>
                        </li> 
                        <li class="nav-item">
                         <a class="nav-link" href="<?php //echo base_url();?>product">Products</a>
                        </li--> 
                        <li class="nav-item">
                         <a class="nav-link" href="<?php echo base_url();?>blog">Blog</a>
                        </li>
                        <li class="nav-item">
                    <a class="nav-link" href="<?php base_url();?>/contact">Contact Us</a>
                  </li> 
                          		 <?php  if($this->session->userdata('role')==2)
            {?>
		
		<li class="nav-item dropdown">
            <a class="nav-link" href="<?php echo base_url(); ?>profile">Profile</a> 
            <div class="dropdown-content">
       <a href="<?php echo base_url();?>Login/logout"  class="btn btn-primary logoutdiv">Logout</a>  
      
      </div>   
		 </li>
            
     
					
			    		
              </ul>
           </div>  
        </nav>
<?php }else{ ?>

				<li class="nav-item">
                <a class="nav-link" href="<?php echo base_url();?>login">Sign in</a>
                </li>
 

<?php } ?>
                      
				     
      	    
               </ul>
             </div>  
           </nav>
                </div>

          </div>
      </div>
</div>


</header>
<!--Headerclosed-->
