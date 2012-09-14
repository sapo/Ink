<div class="ink-container whatIs">
	<div class="ink-vspace">
		<h2>Navigation</h2>
		<p>Navigation is key in any website or web application.</p>
	</div>
</div>

<nav class="menu">
	<div class="ink-container">
		<ul>
			<li class="active"><a class="home" href="#">Home</a></li>
			<li><a href="#">Horizontal menus</a></li>
			<li><a href="#">Horizontal submenus</a></li>
			<li><a href="#">Vertical menus</a></li>
			<li><a href="#">Vertical submenus</a></li>
			<li><a href="#">Simple footer menus</a></li>
		</ul>
	</div>
</nav>  

<div class="ink-container">
	<div class="ink-section">
		<div class="ink-l100">
			<div class="ink-vspace">
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
		<div class="ink-l50">
			<div class="ink-hspace">
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
		<div class="ink-l50">
			<div class="ink-hspace">
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
	<div class="ink-section">
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
		<div class="ink-l50">
			<div class="ink-space">
				<h3>Menu Estilizado</h3>
				<p></p>
			</div>
		</div>   	
	</div>
	
	<div class="ink-l100 ink-section">
		<div class="ink-vspace">
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
		<div class="ink-l33">
			<div class="ink-space">
				<h3>Round Menu</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
			</div>
		</div>
		<div class="ink-l33">
			<div class="ink-space">
				<h4>SubMenu</h4>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
			</div>
		</div>
		<div class="ink-l33">
			<div class="ink-space">
				<h4>Pesquisa Incorporada</h4>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
			</div>
		</div>
	</div>
</div>