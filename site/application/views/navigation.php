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
		<div class="ink-vspace">
			<h3>Horizontal menu</h3>
			<p>
				Horizontal menus can be used as primary navigation of the website or applied as top bar.
				To build this navigation you just need to use the class <code>.ink-h-nav</code>.
			</p>
		</div>
		
		<nav>
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
			<div class="ink-vspace">
				<p class="note">
					<strong>Note: </strong>The menu can be built in many different ways, using  <code>&lt;a&gt;</code>, 
					<code>&lt;ul&gt;</code> or the new <span class="label_new">HTML5</span> tag <code>&lt;nav&gt;</code>
				</p>
			
				<pre class="prettyprint"><ol><li><span class="tag">&lt;nav</span><span class="tag">&gt;</span></li><li><span class="pln">  </span><span class="tag">&lt;ul</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-h-nav"</span><span class="tag">&gt;</span><span class="tag"></span></li><li><span class="pln">    </span><span class="tag">&lt;li&gt;&lt;a <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;</span><span class="pln">Item</span><span class="tag">&lt;/a&gt;&lt;/li&gt;</span></li><li><span class="tag">    &lt;li&gt;&lt;a <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;</span><span class="pln">Item</span><span class="tag">&lt;/a&gt;&lt;/li&gt;</span></li><li><span class="tag">    &lt;li&gt;&lt;a <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;</span><span class="pln">Item</span><span class="tag">&lt;/a&gt;&lt;/li&gt;</span></li><li><span class="tag">    &lt;li&gt;&lt;a <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;</span><span class="pln">Item</span><span class="tag">&lt;/a&gt;&lt;/li&gt;</span></li><li><span class="tag">    &lt;li&gt;&lt;a <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;</span><span class="pln">Item</span><span class="tag">&lt;/a&gt;&lt;/li&gt;</span></li><li><span class="pln">  </span><span class="tag">&lt;/ul</span><span class="tag">&gt;</span></li><li><span class="tag">&lt;/div&gt;</span></li></ol></pre>
			</div>
		
			<div class="ink-vspace">
				<h4>Submenu</h4>
				<p><code>&lt;ul&gt;</code> (Unsorted Lists) based menus may have submenus respecting the following structure:</p>
				
<pre class="prettyprint"><ol><li><span class="tag">&lt;nav</span><span class="tag">&gt;</span></li><li><span class="pln">  </span><span class="tag">&lt;ul</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-h-nav"</span><span class="tag">&gt;</span><span class="tag"></span></li><li><span class="pln">    </span><span class="tag">&lt;li&gt;&lt;a <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;</span><span class="pln">Item</span><span class="tag">&lt;/a&gt;&lt;/li&gt;</span></li><li><span class="tag">    &lt;li&gt;&lt;a <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;</span><span class="pln">Item</span><span class="tag">&lt;/a&gt;&lt;/li&gt;</span></li><li><span class="tag">    &lt;li&gt;&lt;a <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;</span><span class="pln">Item</span><span class="tag">&lt;/a&gt;&lt;/li&gt;</span></li><li><span class="tag">    &lt;li&gt;&lt;a <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;</span><span class="pln">Item</span><span class="tag">&lt;/a&gt;&lt;/li&gt;</span></li><li><span class="tag">    &lt;li&gt;</span></li><li><span class="tag">      &lt;a <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;</span><span class="pln">Item</span><span class="tag">&lt;/a&gt;</span></li><li>      <span class="tag">&lt;ul</span> <span class="atn">class<span class="pun">=</span><span class="atv">"submenu"</span></span><span class="tag">&gt;</span></li><li>        <span class="tag">&lt;li&gt;&lt;a</span> <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;<span class="pln">SubItem</span><span class="tag">&lt;/a&gt;&lt;/li&gt;</span></li><li>        <span class="tag">&lt;li&gt;&lt;a</span> <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;<span class="pln">SubItem</span><span class="tag">&lt;/a&gt;&lt;/li&gt;</span></li><li>      <span class="tag">&lt;/ul&gt;</span></li><li><span class="tag">    &lt;/li&gt;</span></li><li><span class="pln">  </span><span class="tag">&lt;/ul</span><span class="tag">&gt;</span></li><li><span class="tag">&lt;/nav&gt;</span></li></ol></pre>			
</div>
		</div>
	</div>
	
	<div class="ink-section">
		<div class="ink-vspace">
			<h3>Vertical menu</h3>
		</div>
		<div class="ink-row">
			<div class="ink-l40">
				<div class="ink-gutter">
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
				<div class="ink-gutter">
					<p>
						Vertical menus are built exactly the same way as the horizontal ones, just replace the <code>&lt;ul&gt;</code> class with <code>.ink-v-nav</code>. The same goes for the submenus.
					</p>
	<pre class="prettyprint"><ol><li><span class="tag">&lt;nav</span><span class="tag">&gt;</span></li><li><span class="pln">  </span><span class="tag">&lt;ul</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-v-nav"</span><span class="tag">&gt;</span><span class="tag"></span></li><li><span class="pln">    </span><span class="tag">&lt;li&gt;&lt;a <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;</span><span class="pln">Item</span><span class="tag">&lt;/a&gt;&lt;/li&gt;</span></li><li>    <span class="com">...</span></li><li><span class="tag">    &lt;li&gt;</span></li><li><span class="tag">      &lt;a <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;</span><span class="pln">Item</span><span class="tag">&lt;/a&gt;</span></li><li>      <span class="tag">&lt;ul</span> <span class="atn">class<span class="pun">=</span><span class="atv">"submenu"</span></span><span class="tag">&gt;</span></li><li>        <span class="tag">&lt;li&gt;&lt;a</span> <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;<span class="pln">SubItem</span><span class="tag">&lt;/a&gt;&lt;/li&gt;</span></li><li>        <span class="com">...</span><span class="tag"></span></li><li>      <span class="tag">&lt;/ul&gt;</span></li><li><span class="tag">    &lt;/li&gt;</span></li><li><span class="pln">  </span><span class="tag">&lt;/ul</span><span class="tag">&gt;</span></li><li><span class="tag">&lt;/nav&gt;</span></li></ol></pre>			
	
				</div>
			</div>
		</div>
	</div>
	<div class="ink-section">
		<div class="ink-vspace">
			<h3>Simple footer menu</h3>
			<p>
				Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
				Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. 
				Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
			</p>
<pre class="prettyprint"><ol><li><span class="tag">&lt;footer</span><span class="tag">&gt;</span></li><li><span class="pln">  </span><span class="tag">&lt;ul</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-footer-nav"</span><span class="tag">&gt;</span><span class="tag"></span></li><li><span class="pln">    </span><span class="tag">&lt;li&gt;&lt;a <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;</span><span class="pln">footerItem</span><span class="tag">&lt;/a&gt;&lt;/li&gt;</span></li><li><span class="tag">    </span><span class="tag">&lt;li&gt;&lt;a</span> <span class="atn">href<span class="pun">=</span><span class="atv">"#"</span></span>&gt;<span class="pln">footerItem</span><span class="tag">&lt;/a&gt;&lt;/li&gt;</span></li><li>    <span class="com">...</span><span class="tag"></span></li><li>  <span class="tag">&lt;/ul&gt;</span></li><li><span class="tag">&lt;/footer&gt;</span></li></ol></pre>			

		</div>
	</div>
	<div class="ink-section">
		<div class="ink-vspace">
			<h3>Pagination</h3>
			<p>
				Paginations can be created by adding the <code>.pagination</code> class to a <code>.ink-h-nav</code> list. The <code>.active</code> and <code>.disabled</code> classes are available to diferentiate the current page and disabled links.
			</p>
			<div class="ink-vspace">
				<ul class="ink-h-nav pagination">
					<li class="disabled"><a href="#">&laquo;</a></li>
					<li><a href="#">1</a></li>
					<li><a href="#">2</a></li>
					<li><a href="#">3</a></li>
					<li class="active"><a href="#">4</a></li>
					<li><a href="#">5</a></li>
					<li><a href="#">6</a></li>
					<li><a href="#">7</a></li>
					<li><a href="#">&raquo;</a></li>
				</ul>
			</div>
<pre class="prettyprint linenums">
<?php echo(htmlentities('<ul class="ink-h-nav pagination">
   <li class="disabled"><a href="#">&laquo;</a></li>
   <li><a href="#">1</a></li>
   <li><a href="#">2</a></li>
   <li><a href="#">3</a></li>
   <li class="active"><a href="#">4</a></li>
   <li><a href="#">5</a></li>
   <li><a href="#">6</a></li>
   <li><a href="#">7</a></li>
   <li><a href="#">&raquo;</a></li>
</ul>')) ?></pre>
		</div>
	</div>
	<div class="ink-section">
		<div class="ink-vspace">
			<h3>Pills</h3>
			<p>
				Pills can be created by adding the <code>.pills</code> class to a <code>.ink-h-nav</code> list. The <code>.active</code> and <code>.disabled</code> classes are available.
			</p>
			<div class="ink-vspace">
				<ul class="ink-h-nav pills">
					<li><a href="#">Item</a></li>
					<li class="active"><a href="#">Item</a></li>
					<li><a href="#">Item</a></li>
					<li><a href="#">Item</a></li>
				</ul>
			</div>
<pre class="prettyprint linenums">
<?php echo(htmlentities('<ul class="ink-h-nav pills">
   <li><a href="#">Item</a></li>
   <li class="active"><a href="#">Item</a></li>
   <li><a href="#">Item</a></li>
   <li><a href="#">Item</a></li>
</ul>')) ?></pre>
		</div>
	</div>
	<div class="ink-section">
		<h3>Breadcrumbs</h3>
		<p>Breadcrumbs are made from bread!</p>
		<ul class="ink-h-nav breadcrumbs">
			<li><a href="#">Home</a><span class="separator">/</span></li>
			<li><a href="#">Products</a><span class="separator">/</span></li>
			<li><a href="#">Category</a><span class="separator">/</span></li>
			<li class="active"><a href="#">Item</a></li>
		</ul>
	</div>
</div>
