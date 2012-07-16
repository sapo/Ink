<?php include 'shared/header.php'; ?>
<!-- |||||||||||||||||||||||||||||||||  Navitation  |||||||||||||||||||||||||||||||||  -->
<div id="topMenu"  class="blackMenu" >
	<h1><a href="intro.php" title="Site Title">InK <small>Interface kit</small></a></h1>
	<a href="#" onclick="toogleNav()" id="toggleNavigation">Menu</a>
	<nav>
		<ul class="h_navigation">
			<li><a href="intro.php">Intro</a></li>
			<li><a href="grid.php">Grelha</a></li>
			<li><a href="typo.php">Tipografia</a></li>
			<li><a href="forms.php">Formulários</a></li>
			<li><a href="tables.php">Tabelas</a></li>
			<li><a href="alerts.php">Alerts</a></li>
			<li class="active"><a href="navigation.php">Navegação</a></li>
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
	<h2><span>Navegação</span></h2>
	<div class="space lead">
		<p>A Navegação é parte integrante de qualquer site ou aplicação web.<br />
		Estão contemplados no <abbr title="Interface kit">InK</abbr> dois estilos de navegação, horizontal e verticais. </p>
    </div>   
	
	<div class="section">
		<div class="g100">
			<div class="v_space">
				<nav>
					<ul class="h_navigation">
						<li class="active"><a href="#">Item</a></li>
						<li><a href="#">Item</a></li>
						<li><a href="#">Item</a></li>
						<li><a href="#">Item</a></li>
						<li><a href="#">Item</a></li>
						<li>
							<a href="#">Submenu</a>
							<ul class="submenu">
								<li><a href="#">Item Bastante Grande</a></li>
								<li><a href="#">Item</a></li>
								<li><a href="#">Item</a></li>
							</ul>
						</li> 
					</ul>
				</nav>
			</div>
		</div>
		<div class="g50">
			<div class="h_space">
				<h3>Menu Horizontal</h3>
				<p>Os menus horizontais podem ser utilizados como navegação primária do site ou aplicação assim como barra de topo.</p>
				<p>Para construir este tipo de navegação basta usar a class <mark>.h_navigation</mark>.</p>
				<p class="note"><strong>Nota: </strong>O menu pode ser construído de várias formas podendo usar um elemento com <mark>&lt;a&gt;</mark> ou
				<mark>&lt;ul&gt;</mark>. Pode também utilizar a nova tag <mark>&lt;nav&gt;</mark> <span class="label_new">HTML5</span>
				</p>
			</div>
			<pre class="prettyprint">&lt;ul class="h_navigation"&gt;
	  &lt;li class=&quot;active&quot;&gt;&lt;a href=&quot;#&quot;&gt;Item&lt;/a&gt;&lt;/li&gt; 
	  &lt;li&gt;&lt;a href=&quot;#&quot;&gt;Item&lt;/a&gt;&lt;/li&gt; 
	  &lt;li&gt;&lt;a href=&quot;#&quot;&gt;Item&lt;/a&gt;&lt;/li&gt; 
	  &lt;li&gt;&lt;a href=&quot;#&quot;&gt;Item&lt;/a&gt;&lt;/li&gt; 
	&lt;/ul&gt;</pre>
		</div>
		<div class="g50">
			<div class="h_space">
				<h4>Submenu</h4>
				<p>Os menus que se baseiam em <mark>&lt;ul&gt;</mark>(Unsorted Lists) pode ter submenus respeitando a estrutura abaixo.</p>
			</div>  
			<pre class="prettyprint">&lt;ul class="h_navigation"&gt;
	  &lt;li class=&quot;active&quot;&gt;&lt;a href=&quot;#&quot;&gt;Item&lt;/a&gt;&lt;/li&gt; 
	  &lt;li&gt;&lt;a href=&quot;#&quot;&gt;Item&lt;/a&gt;&lt;/li&gt;
	  &lt;li&gt;&lt;a href=&quot;#&quot;&gt;Item&lt;/a&gt;
	  &lt;ul class=&quot;submenu&quot;&gt;
		&lt;li&gt;&lt;a href=&quot;#&quot;&gt;SubItem&lt;/a&gt;&lt;/li&gt; 
		&lt;li&gt;&lt;a href=&quot;#&quot;&gt;SubItem&lt;/a&gt;&lt;/li&gt;  
	  &lt;/ul&gt;
	  &lt;/li&gt; 
	&lt;/ul&gt;</pre>
		</div>
	
	</div>
	<div class="section">
		<div class="blackMenu">
			<nav>
				<ul class="h_navigation">
					<li><a href="#">Item</a></li>
					<li class="active"><a href="#">Item</a></li>
					<li><a href="#">Item</a></li>
					<li><a href="#">Item</a></li>
					<li><a href="#">Item</a></li>
					<li>
						<a href="#">Submenu</a>
						<ul class="submenu">
							<li><a href="#">Item Bueda Comprido dsadas</a></li>
							<li><a href="#">Item</a></li>
							<li><a href="#">Item</a></li>
						</ul>
					</li>
				</ul>
			</nav>
			<form><input type="text" placeholder="Pesquisar"/></form>
			<a href="#" onClick="toogleNav()" id="toggleNavigation">Menu</a>
		</div> 
		<div class="g50">
			<div class="space">
				<h3>Menu Estilizado</h3>
				<p></p>
			</div>
		</div>   	
	</div>
	
	<div class="g100 section">
		<div class="v_space">
			<div id="exempleBar">
				<div class="whiteMenu">
					<a href="#" onClick="toogleNav()" id="toggleNavigation">Menu</a>
					<nav>
						<ul id="navigation">
							<li class="active"><a href="#">Item</a></li>
							<li><a href="#">Item</a></li>
							<li><a href="#">Item</a></li>
							<li><a href="#">Item</a></li>
							<li><a href="#">Item</a></li>
							<li><a href="#">Item</a></li>
						</ul>
					</nav>
					<form><input type="text" placeholder="Pesquisar"/></form>
				</div>
			</div>
		</div>
		<div class="g33">
			<div class="space">
				<h3>Round Menu</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
			</div>
		</div>
		<div class="g33">
			<div class="space">
				<h4>SubMenu</h4>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
			</div>
		</div>
		<div class="g33">
			<div class="space">
				<h4>Pesquisa Incorporada</h4>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
			</div>
		</div>
	</div>
</div>
<?php include 'shared/footer.php'; ?>