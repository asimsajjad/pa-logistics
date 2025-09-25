<html>
	<head>
		<meta name="robots" content="noindex, nofollow">
		<meta content='width=device-width, initial-scale=1, user-scalable=1, minimum-scale=1, maximum-scale=1' name='viewport'/> 
		<title>Admin page Login</title>
		<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
		<link href="<?php echo base_url().'admin/vendor/bootstrap/css/bootstrap.min.css'; ?>" rel="stylesheet" />
		<link href="<?php echo base_url().'admin/css/sb-admin.css'; ?>" rel="stylesheet" />
		<link href="<?php echo base_url().'admin/css/custom-style.css'; ?>" rel="stylesheet" />
	</head>

	<body>
		<div class="wapper pt-admin-wrapper">
			<!--
			<div class="pt-admin-title">
				<a href="#">
					<img src="<?php //echo base_url().'admin/images/logo.png'; ?>" alt="PA Trans" />
				</a>
			</div>
			-->
			<div class="main admin_login pt-adminlogin-box">
				<div class="pt-logo-admin">
					<img src="<?php echo base_url().'admin/images/logo-icon.png'; ?>" alt="PA Transport" />
				</div>
				<h1>Admin Login</h1>
				<?php if($error!='') { echo $error; } ?>
				<form method="POST" action="<?php echo base_url('AdminLogin');?>">
				  
					<div class="input form-group pt-username">
						<label>Username or Email Address</label>
						<input type="text" name="username" placeholder="Enter user name" class="text form-control" />
					</div>
					<div class="input form-group pt-password">
						<label>Password</label>
						<input type="password" name="pass" placeholder="********" class="text form-control" />
					</div>
					<div class="input">
						<input type="submit" name="submit" onclick="myFunction();" value="Login" class="btun btn" />
					</div>
					<!-- a href="<?php echo base_url().'AdminLogin/recover/'?>">Forgot Password</a -->
			    </form>
			</div>
		</div>

		<script>
			function myFunction() {
			    //alert('Login successfully');
			}
		</script>
	</body>
</html>


