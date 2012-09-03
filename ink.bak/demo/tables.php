<?php include 'shared/header.php'; ?>
<!-- |||||||||||||||||||||||||||||||||  Navitation  |||||||||||||||||||||||||||||||||  -->
<div class="blackMenu" id="topMenu">
	<h1><a href="intro.php" title="Site Title">InK <small>Interface kit</small></a></h1>
	<a href="#" onclick="toogleNav()" id="toggleNavigation">Menu</a>
	<nav>
		<ul class="h_navigation">
			<li><a href="intro.php">Intro</a></li>
			<li><a href="grid.php">Layout</a></li>
			<li><a href="typo.php">Tipografia</a></li>
			<li><a href="forms.php">Formulários</a></li>
			<li class="active"><a href="tables.php">Tabelas</a></li>
			<li><a href="alerts.php">Alerts</a></li>
			<li><a href="navigation.php">Navegação</a></li>
			<li><a href="widgets.php">Widgets</a></li>
		</ul>
	</nav>
	<script type="text/javascript">
		$(document).ready(
			$("#toggleNavigation").click(function () {
			
				if ($("ul.h_navigation").is(":hidden")) {
					$("ul.h_navigation").slideDown("fast");
					} else {
					$("ul.h_navigation").slideUp("fast");
					}
				})
			
			);
	</script>
</div>

<!-- |||||||||||||||||||||||||||||||||  Content  |||||||||||||||||||||||||||||||||  -->		
<div class="container_width">
	<h2><span>Tabelas</span></h2>
	<div class="space">
	<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
	</div> 
	
	<div class="g25">
		<div class="space">
			<h3>Tabelas</h3>
			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
		</div>					
	</div>
	<div class="g75">
		<div class="space">
			<table>
				<tr>
					<th>Table Header</th>
					<th>Table Header</th>
					<th>Table Header</th>
				</tr>
				<tr>
					<td>Table Content</td>
					<td>Table Content</td>
					<td>Table Content</td>
				</tr>
				<tr>
					<td>Table Content</td>
					<td>Table Content</td>
					<td>Table Content</td>
				</tr>
				<tr>
					<td>Table Content</td>
					<td>Table Content</td>
					<td>Table Content</td>
				</tr>
				<tr>
					<td>Table Content</td>
					<td>Table Content</td>
					<td>Table Content</td>
				</tr>
				<tr>
					<td>Table Content</td>
					<td>Table Content</td>
					<td>Table Content</td>
				</tr>
			</table>
		</div>
	</div>
</div>

<?php include 'shared/footer.php'; ?>	