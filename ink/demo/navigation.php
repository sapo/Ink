<?php include 'shared/header.php'; ?>
<!-- |||||||||||||||||||||||||||||||||  Navitation  |||||||||||||||||||||||||||||||||  -->
<nav id="topbar">
	<div class="ink-container">
		<ul class="ink-h-nav">
			<li><a class="logoPlaceholder" href="./" title="Site Title">InK</a></li>
			<li><a href="grid.php">Layout</a></li>
			<li class="active"><a href="navigation.php">Navigation</a></li>
			<li><a href="typo.php">Typography & Icons</a></li>
			<li><a href="forms.php">Forms</a></li>
			<li><a href="alerts.php">Alerts</a></li>
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
				To build this navigation you just need to use the class <code>.ink-h-nav</code>.
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
					<strong>Note: </strong>The menu can be built in many different ways, using  <code>&lt;a&gt;</code>, 
					<code>&lt;ul&gt;</code> or the new <span class="label_new">HTML5</span> tag <code>&lt;nav&gt;</code>
				</p>
			
				<pre class="prettyprint"><ol><li><span class="tag">&lt;nav</span><span class="tag">&gt;</span></li><li><span class="pln">  </span><span class="tag">&lt;ul</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-h-nav"</span><span class="tag">&gt;</span><span class="tag"></span></li><li><span class="pln">    </span><span class="tag">&lt;li&gt;&lt;a <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;</span><span class="pln">Item</span><span class="tag">&lt;/a&gt;&lt;/li&gt;</span></li><li><span class="tag">    &lt;li&gt;&lt;a <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;</span><span class="pln">Item</span><span class="tag">&lt;/a&gt;&lt;/li&gt;</span></li><li><span class="tag">    &lt;li&gt;&lt;a <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;</span><span class="pln">Item</span><span class="tag">&lt;/a&gt;&lt;/li&gt;</span></li><li><span class="tag">    &lt;li&gt;&lt;a <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;</span><span class="pln">Item</span><span class="tag">&lt;/a&gt;&lt;/li&gt;</span></li><li><span class="tag">    &lt;li&gt;&lt;a <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;</span><span class="pln">Item</span><span class="tag">&lt;/a&gt;&lt;/li&gt;</span></li><li><span class="pln">  </span><span class="tag">&lt;/ul</span><span class="tag">&gt;</span></li><li><span class="tag">&lt;/div&gt;</span></li></ol></pre>
			</div>
		
			<div class="ink-space">
				<h4>Submenu</h4>
				<p><code>&lt;ul&gt;</code> (Unsorted Lists) based menus may have submenus respecting the following structure:</p>
				
<pre class="prettyprint"><ol><li><span class="tag">&lt;nav</span><span class="tag">&gt;</span></li><li><span class="pln">  </span><span class="tag">&lt;ul</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-h-nav"</span><span class="tag">&gt;</span><span class="tag"></span></li><li><span class="pln">    </span><span class="tag">&lt;li&gt;&lt;a <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;</span><span class="pln">Item</span><span class="tag">&lt;/a&gt;&lt;/li&gt;</span></li><li><span class="tag">    &lt;li&gt;&lt;a <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;</span><span class="pln">Item</span><span class="tag">&lt;/a&gt;&lt;/li&gt;</span></li><li><span class="tag">    &lt;li&gt;&lt;a <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;</span><span class="pln">Item</span><span class="tag">&lt;/a&gt;&lt;/li&gt;</span></li><li><span class="tag">    &lt;li&gt;&lt;a <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;</span><span class="pln">Item</span><span class="tag">&lt;/a&gt;&lt;/li&gt;</span></li><li><span class="tag">    &lt;li&gt;</span></li><li><span class="tag">      &lt;a <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;</span><span class="pln">Item</span><span class="tag">&lt;/a&gt;</span></li><li>      <span class="tag">&lt;ul</span> <span class="atn">class<span class="pun">=</span><span class="atv">"submenu"</span></span><span class="tag">&gt;</span></li><li>        <span class="tag">&lt;li&gt;&lt;a</span> <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;<span class="pln">SubItem</span><span class="tag">&lt;/a&gt;&lt;/li&gt;</span></li><li>        <span class="tag">&lt;li&gt;&lt;a</span> <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;<span class="pln">SubItem</span><span class="tag">&lt;/a&gt;&lt;/li&gt;</span></li><li>      <span class="tag">&lt;/ul&gt;</span></li><li><span class="tag">    &lt;/li&gt;</span></li><li><span class="pln">  </span><span class="tag">&lt;/ul</span><span class="tag">&gt;</span></li><li><span class="tag">&lt;/nav&gt;</span></li></ol></pre>			
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
					Vertical menus are built exactly the same way as the horizontal ones, just replace the <code>&lt;ul&gt;</code> class with <code>.ink-v-nav</code>. The same goes for the submenus.
				</p>
<pre class="prettyprint"><ol><li><span class="tag">&lt;nav</span><span class="tag">&gt;</span></li><li><span class="pln">  </span><span class="tag">&lt;ul</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-v-nav"</span><span class="tag">&gt;</span><span class="tag"></span></li><li><span class="pln">    </span><span class="tag">&lt;li&gt;&lt;a <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;</span><span class="pln">Item</span><span class="tag">&lt;/a&gt;&lt;/li&gt;</span></li><li>    <span class="com">...</span></li><li><span class="tag">    &lt;li&gt;</span></li><li><span class="tag">      &lt;a <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;</span><span class="pln">Item</span><span class="tag">&lt;/a&gt;</span></li><li>      <span class="tag">&lt;ul</span> <span class="atn">class<span class="pun">=</span><span class="atv">"submenu"</span></span><span class="tag">&gt;</span></li><li>        <span class="tag">&lt;li&gt;&lt;a</span> <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;<span class="pln">SubItem</span><span class="tag">&lt;/a&gt;&lt;/li&gt;</span></li><li>        <span class="com">...</span><span class="tag"></span></li><li>      <span class="tag">&lt;/ul&gt;</span></li><li><span class="tag">    &lt;/li&gt;</span></li><li><span class="pln">  </span><span class="tag">&lt;/ul</span><span class="tag">&gt;</span></li><li><span class="tag">&lt;/nav&gt;</span></li></ol></pre>			

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
<pre class="prettyprint"><ol><li><span class="tag">&lt;footer</span><span class="tag">&gt;</span></li><li><span class="pln">  </span><span class="tag">&lt;ul</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-footer-nav"</span><span class="tag">&gt;</span><span class="tag"></span></li><li><span class="pln">    </span><span class="tag">&lt;li&gt;&lt;a <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;</span><span class="pln">footerItem</span><span class="tag">&lt;/a&gt;&lt;/li&gt;</span></li><li><span class="tag">    </span><span class="tag">&lt;li&gt;&lt;a</span> <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;<span class="pln">footerItem</span><span class="tag">&lt;/a&gt;&lt;/li&gt;</span></li><li>    <span class="com">...</span><span class="tag"></span></li><li>  <span class="tag">&lt;/ul&gt;</span></li><li><span class="tag">&lt;/footer&gt;</span></li></ol></pre>			

		</div>
	</div>
</div>
<?php include 'shared/footer.php'; ?>