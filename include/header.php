		<header>
			<style>
				.navbar-default {
					background: #f5f5f5;
				}
				ul.navbar-right li a:hover {
					background: #518d8a !important;
					color: #FFF !important;
				}
			</style>

			<nav class="navbar navbar-default">
				<div class="container">
					<!-- Brand and toggle get grouped for better mobile display -->
					<div class="navbar-header">
						<button type="button" data-target="#navbarCollapse" data-toggle="collapse" class="navbar-toggle">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<a href="/" class="navbar-brand" style=" padding: 5px !important; margin-left: 30px !important; "> <img src="images/logo.png" class="site-logo" /></a> hai
					</div>
					<!-- Collection of nav links and other content for toggling -->
					<div id="navbarCollapse" class="collapse navbar-collapse">
						<ul class="nav navbar-nav navbar-right">
							<li>
							   <a href="/" data-close="true"> Home </a>
							</li>

		<?php  if(isset($_SESSION['user_name']) || $thisPage=='playlist') echo("<li>
											 <a href=\"/logout\"> Logout </a>
										</li>");
			 ?>



						</ul>
					</div>
				</div>
			</nav>

		</header>
