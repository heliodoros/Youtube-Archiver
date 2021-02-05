<?php
$thisPage = 'index';
include 'config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>YouTube Safekeeping</title>

	<!-- Bootstrap Core Css -->
	<link href="css/bootstrap.css" rel="stylesheet" />

	<!-- Font Awesome Css -->
	<link href="css/font-awesome.min.css" rel="stylesheet" />

	<!-- Bootstrap Select Css -->
	<link href="css/bootstrap-select.css" rel="stylesheet" />

	<!-- Custom Css -->
	<link href="css/app_style.css" rel="stylesheet" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
	<div class="all-content-wrapper">
		<!-- Top Bar -->
		<?php require_once('./include/header.php'); ?>
		<!-- #END# Top Bar -->

		<section class="container" style="width: 500px;">
			<div class="form-group custom-input-space has-feedback">
				<div class="page-heading">
					<h3 class="post-title">YouTube Safekeeping</h3>
				</div>
				<div class="page-body clearfix">
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-default">
								<div class="panel-heading">Playlist Selection:</div>
								<div class="panel-body">
									<div id="my_video_list">
										<?php
										if (isset($_SESSION['user_name'])) {
											echo ('<a href="playlist">continue as ' . $_SESSION['user_name'] . '</a> or <a href="logout.php"> Logout </a>');
										} else {
											echo "<a href='{$client->createAuthUrl()}'> Login with Google</a>";
										}
										?>
									</div>
								</div>
							</div>
						</div>
						<div id="my_player">
							<div></div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
</body>

</html>