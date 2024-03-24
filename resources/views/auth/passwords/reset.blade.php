<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width,initial-scale=1">
		
		<title>Mercedes Homes</title>
		
		<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
		
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700&display=swap">
		<link rel="stylesheet" href="http://fonts.cdnfonts.com/css/ds-digital">
		<link rel="stylesheet" href="{{ asset('front/css/style.css') }}" type="text/css" />
	</head>
	<style>
		.btn-primary {
			background-color: #CF5F47 !important;
			border-color: #CF5F47;
		}
		
		.btn-primary:hover {
			opacity: 90%;
			border-color: #CF5F47;
		}
	</style>
	<body>
		<div id="hero-bussiness-river">
			<div class="container">
				<div class="row">
					<div class="col-lg-3">
						<!-- -->
					</div>
					<div class="col-lg-6">
						<h1 class="text-center">RESET PASSWORD</h1>
							@if (isset($td_error))
								<div class="alert alert-danger" role="alert">{{ $td_error }}</div>
							@endif
							
							@if (\Session::has('error'))
								<div class="alert alert-danger" role="alert">{{ \Session::get('error') }}</div>
							@endif

							@if (\Session::has('success'))
								<div class="alert alert-success" role="alert">{{ \Session::get('success') }}</div>
							@endif	
							<div class="panel panel-default" style="border-radius: 8px; padding: 8px 13px;">
								<div class="panel-body">
								<form method="POST" action="{{ route('password.update') }}">
									@csrf

									<input type="hidden" name="token" value="{{ $token }}">
									<input id="email" type="hidden" class="form-control" name="email" value="{{ $email }}" required autocomplete="email" autofocus>

									<div class="form-group row">
										<div class="col-md-12">
											<input id="password" type="password" class="form-control" name="password" required autocomplete="new-password" placeholder="Password">
											<div class="w-100 text-center">
												<div id="password-error" class="text-danger"></div>
											</div>
										</div>
									</div>

									<div class="form-group row">
										<div class="col-md-12">
											<input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm Password">
											<div class="w-100 text-center">
												<div id="confirm-password-error" class="text-danger"></div>
											</div>
										</div>
									</div>

									<div class="form-group row">
										<div class="col-md-12">
											<input type="checkbox" id="show-password" >
											<label for="show-password" style="font-weight: normal;">Show Password</label>
										</div>
									</div>

									<div class="form-group row mb-0">
										<div class="col-md-12">
											<button type="submit" class="btn btn-block btn-primary">
												{{ __('Reset Password') }}
											</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
					<div class="col-lg-3">
						<!-- -->
					</div>					
				</div>
			</div>
		</div>
	</body>
</html>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const passwordField = document.getElementById("password");
        const confirmPasswordField = document.getElementById("password-confirm");
        const passwordError = document.getElementById("password-error");
        const confirmPasswordError = document.getElementById("confirm-password-error");
		const showPasswordCheckbox = document.getElementById("show-password");
        const form = document.querySelector("form");

        form.addEventListener("submit", function (event) {
            let isValid = true;

            if (passwordField.value.length < 8) {
                passwordError.textContent = "Password must be at least 8 characters long.";
                passwordError.style.marginTop = "10px";
                isValid = false;
            } else {
                passwordError.textContent = "";
                passwordError.style.marginTop = "0";
            }

            if (passwordField.value !== confirmPasswordField.value) {
                confirmPasswordError.textContent = "Password and Confirm Password do not match.";
                confirmPasswordError.style.marginTop = "10px";
                isValid = false;
            } else {
                confirmPasswordError.textContent = "";
                confirmPasswordError.style.marginTop = "0";
            }

            if (!isValid) {
                event.preventDefault();
            }
        });

        showPasswordCheckbox.addEventListener("change", function () {
            if (showPasswordCheckbox.checked) {
                passwordField.type = "text";
                confirmPasswordField.type = "text";
            } else {
                passwordField.type = "password";
                confirmPasswordField.type = "password";
            }
        });
    });
</script>