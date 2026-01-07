<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="font/inter/inter.min.css">
	<link href="plugins/material-design-icons-iconfont/material-design-icons.min.css" rel="stylesheet">
	<link rel="stylesheet" href="plugins/simplebar/simplebar.min.css">
	<link rel="stylesheet" href="css/style.min.css" id="main-css">
	<link rel="stylesheet" href="css/sidebar-gray.min.css" id="theme-css"> <!-- options: blue,cyan,dark,gray,green,pink,purple,red,royal,ash,crimson,namn,frost -->
	<title>Bowin Silver System</title>
</head>

<body class="login-page">

	<div class="container pt-5">
		<div class="row justify-content-center">
			<div class="col-md-auto d-flex justify-content-center">
				<div class="card shadow">
					<div class="card-header bg-primary text-white flex-column">
						<h4 class="text-center mb-0">เข้าสู่ระบบ</h4>
						<div class="text-center opacity-50 font-italic">Bowin Silver</div>
					</div>
					<div class="card-body p-4">

						<!-- LOG IN FORM -->
						<form id="login-form" onsubmit="fn.login();return false;">
							<div class="form-group">
								<div class="floating-label input-icon">
									<i class="material-icons">person_outline</i>
									<input type="text" class="form-control" placeholder="Username" name="username" autocomplete="off">
									<label for="username">Username</label>
								</div>
							</div>
							<div class="form-group">
								<div class="floating-label input-icon">
									<i class="material-icons">lock_open</i>
									<input type="password" class="form-control" placeholder="Password" name="password">
									<label for="password">Password</label>
								</div>
							</div>
							<div class="form-group d-flex justify-content-between align-items-center">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="remember">
									<label class="custom-control-label" for="remember">Remember me</label>
								</div>
								<a href="forgot-password.html" class="text-primary text-decoration-underline small">Forgot password ?</a>
							</div>
							<button type="submit" class="btn btn-primary btn-block">LOG IN</button>
						</form>
						<!-- /LOG IN FORM -->

					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Plugins -->
	<!-- JS plugins goes here -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.bundle.min.js"></script>
	<script src="plugins/bootbox/bootbox.min.js"></script>
	<script src="js/app.nebulaos.js"></script>
</body>

</html>

