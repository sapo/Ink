<?php include 'shared/header.php'; ?>
<!-- |||||||||||||||||||||||||||||||||  Navitation  |||||||||||||||||||||||||||||||||  -->
<nav id="topbar">
	<div class="ink-container">
		<ul class="ink-h-nav">
			<li><a class="logoPlaceholder" href="./" title="Site Title">InK</a></li>
			<li><a href="grid.php">Layout</a></li>
			<li class="active"><a href="navigation.php">Navigation</a></li>
			<li><a href="typo.php">Typography & Icons</a></li>
			<li><a href="forms.php">Forms & Alerts</a></li>
			<li><a href="tables.php">Tables</a></li>
			<li><a href="widgets.php">InkJS</a></li>
		</ul>
	</div>
</nav>  
<!-- |||||||||||||||||||||||||||||||||  Content  |||||||||||||||||||||||||||||||||  -->		
<div class="ink-container whatIs">
	<div class="ink-vspace">
		<h2>Navigation</h2>
		<p>Navigation is key in any website or web application.</p>
	</div>
</div>

<nav class="menu">
	<div class="ink-container">
		<ul class="ink-h-nav">
			<li class="active"><a class="home" href="#">Home</a></li>
			<li><a href="#">Horizontal menu</a></li>
			<li><a href="#">Vertical menu</a></li>
			<li><a href="#">Simple footer menu</a></li>
		</ul>
	</div>
</nav>  

<div class="ink-container">
	<div class="ink-section">
		<div class="ink-space">
			<h3>Horizontal menu</h3>
			<p>
				Horizontal menus can be used as primary navigation of the website or applied as top bar.
				To build this navigation you just need to use the class <mark>.ink-h-nav</mark>.
			</p>
		</div>
		
		<nav class="ink-hspace">
			<ul class="ink-h-nav example_menu">
				<li class="active"><a href="#">Item</a></li>
				<li><a href="#">Item</a></li>
				<li><a href="#">Item</a></li>
				<li><a href="#">Item</a></li>
				<li>
					<a href="#">Submenu<i class="icon-caret-down"></i></a>
					<ul class="submenu">
						<li><a href="#">Item with a very big title</a></li>
						<li><a href="#">Item</a></li>
						<li><a href="#">Item</a></li>
					</ul>
				</li>
			</ul>
		</nav>
		
		<div class="ink-l100">
			<div class="ink-space">
				<p class="note">
					<strong>Note: </strong>The menu can be built in many different ways, using  <mark>&lt;a&gt;</mark>, 
					<mark>&lt;ul&gt;</mark> or the new <span class="label_new">HTML5</span> tag <mark>&lt;nav&gt;</mark>
				</p>
			
				<pre class="prettyprint">
&lt;nav&gt;
   &lt;ul class="ink-h-nav"&gt;
      &lt;li class=&quot;active&quot;&gt;&lt;a href=&quot;#&quot;&gt;Item&lt;/a&gt;&lt;/li&gt; 
      &lt;li&gt;&lt;a href=&quot;#&quot;&gt;Item&lt;/a&gt;&lt;/li&gt; 
      &lt;li&gt;&lt;a href=&quot;#&quot;&gt;Item&lt;/a&gt;&lt;/li&gt; 
      &lt;li&gt;&lt;a href=&quot;#&quot;&gt;Item&lt;/a&gt;&lt;/li&gt; 
   &lt;/ul&gt;
&lt;/nav&gt;</pre>
			</div>
		
			<div class="ink-space">
				<h4>Submenu</h4>
				<p><mark>&lt;ul&gt;</mark> (Unsorted Lists) based menus may have submenus respecting the following structure:</p>
				
				<pre class="prettyprint">
&lt;nav&gt;
   &lt;ul class="ink-h-nav"&gt;
      &lt;li class=&quot;active&quot;&gt;&lt;a href=&quot;#&quot;&gt;Item&lt;/a&gt;&lt;/li&gt; 
      &lt;li&gt;&lt;a href=&quot;#&quot;&gt;Item&lt;/a&gt;&lt;/li&gt;
      &lt;li&gt;&lt;a href=&quot;#&quot;&gt;Item&lt;/a&gt;
         &lt;ul class=&quot;submenu&quot;&gt;
            &lt;li&gt;&lt;a href=&quot;#&quot;&gt;SubItem&lt;/a&gt;&lt;/li&gt; 
            &lt;li&gt;&lt;a href=&quot;#&quot;&gt;SubItem&lt;/a&gt;&lt;/li&gt;  
         &lt;/ul&gt;
      &lt;/li&gt; 
   &lt;/ul&gt;
&lt;/nav&gt;</pre>
			</div>
		</div>
	</div>
	
	<div class="ink-section">
		<div class="ink-l40">
			<div class="ink-space">
				<h3>Vertical menu</h3>
			</div>
			<div class="ink-space">
				<ul class="unstyled ink-v-nav">
					<li><a href="#">Suspendisse</a></li>
					<li><a href="#">Vivamus</a></li>
					<li><a href="#">Condimentum</a></li>
					<li><a href="#">Ccommodo</a></li>
					<li><a href="#">Egestas</a></li>
					<li>
						<a href="#">Submenu<i class="icon-caret-right"></i></a>
						<ul class="submenu">
							<li><a href="#">Item with a very big title</a></li>
							<li><a href="#">Item</a></li>
							<li><a href="#">Item</a></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
		<div class="ink-l60">
			<div class="ink-space">
				<p>
					Os menus verticais podem ser utilizados como navegação primária do site ou aplicação assim como barra de topo.
					Para construir este tipo de navegação basta usar a class <mark>.ink-v-nav</mark>.
				</p>
				<pre class="prettyprint">&lt;nav&gt;
   &lt;ul class="ink-v-nav"&gt;
      &lt;li class=&quot;active&quot;&gt;&lt;a href=&quot;#&quot;&gt;Item&lt;/a&gt;&lt;/li&gt; 
      &lt;li&gt;&lt;a href=&quot;#&quot;&gt;Item&lt;/a&gt;&lt;/li&gt;
      &lt;li&gt;&lt;a href=&quot;#&quot;&gt;Item&lt;/a&gt;
         &lt;ul class=&quot;submenu&quot;&gt;
            &lt;li&gt;&lt;a href=&quot;#&quot;&gt;SubItem&lt;/a&gt;&lt;/li&gt; 
            &lt;li&gt;&lt;a href=&quot;#&quot;&gt;SubItem&lt;/a&gt;&lt;/li&gt;  
         &lt;/ul&gt;
      &lt;/li&gt; 
   &lt;/ul&gt;
&lt;/nav&gt;</pre>
			</div>
		</div>
	</div>
	<div class="ink-section">
		<div class="ink-space">
			<h3>Simple footer menu</h3>
			<p>
				Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
				Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. 
				Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
			</p>
<pre class="prettyprint">&lt;footer&gt;
   &lt;ul class="ink-footer-nav"&gt;
      &lt;li class=&quot;active&quot;&gt;&lt;a href=&quot;#&quot;&gt;Item&lt;/a&gt;&lt;/li&gt; 
      &lt;li&gt;&lt;a href=&quot;#&quot;&gt;Item&lt;/a&gt;&lt;/li&gt;
      &lt;li&gt;&lt;a href=&quot;#&quot;&gt;Item&lt;/a&gt;&lt;/li&gt; 
   &lt;/ul&gt;
&lt;/footer&gt;</pre>
		</div>
	</div>
</div>
<?php include 'shared/footer.php'; ?>