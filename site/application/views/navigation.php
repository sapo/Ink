<div class="ink-container whatIs">
	<div class="ink-vspace">
		<h2>Navigation</h2>
		<p>Navigation is key in any website or web application.</p>
	</div>
</div>

<nav class="">
	<div class="menu-second-level">
		<div class="ink-container">
			<nav class="ink-navigation">
				<ul class="menu horizontal">
					<li class="active"><a class="home" href="#">Home</a></li>
					<li><a href="#">Horizontal menu</a></li>
					<li><a href="#">Vertical menu</a></li>
					<li><a href="#">Simple footer menu</a></li>
				</ul>
			</nav>
		</div>
	</div>
</nav>  

<div class="ink-container">
	<div class="ink-section">
		<div class="ink-vspace">
			<h3>Horizontal menu</h3>
			<p>
				To build a horizontal menu with ink start with a block-level element and use the <code>ink-navigation</code> class. We prefer to use a <code><?php echo htmlentities('<nav>') ?></code> tag to wrap our navigation items, but a <code><?php echo htmlentities('<div>') ?></code> will do just fine.
			</p>
			<p>The menu itself is built with a unordered list with the <code>menu</code> and <code>horizontal</code> classes. Adding color is as simple as adding a class to the menu list element. Available classes are <code>grey</code>, <code>green</code>, <code>blue</code>, <code>red</code>, <code>orange</code> and <code>black</code>!</p>
		</div>
		
		<nav class="ink-navigation">
			<ul class="menu horizontal">
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

		<nav class="ink-navigation ink-vspace">
			<ul class="menu horizontal grey">
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

		<nav class="ink-navigation ink-vspace">
			<ul class="menu horizontal black">
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

		<nav class="ink-navigation ink-vspace">
			<ul class="menu horizontal green">
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

		<nav class="ink-navigation ink-vspace">
			<ul class="menu horizontal blue">
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

		<nav class="ink-navigation ink-vspace">
			<ul class="menu horizontal red">
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

		<nav class="ink-navigation ink-vspace">
			<ul class="menu horizontal orange">
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
<pre class="prettyprint linenums">
<?php echo(htmlentities('<nav class="ink-navigation">
  <ul class="menu horizontal grey">
    <li><a href="#">Item</a></li>
    <li><a href="#">Item</a></li>
    <li><a href="#">Item</a></li>
    <li><a href="#">Item</a></li>
    <li><a href="#">Item</a></li>
  </ul>
</div>')) ?></pre>
			</div>
		
			<div class="ink-vspace">
				<h4>Submenu</h4>
				<p><code>&lt;ul&gt;</code> (Unsorted Lists) based menus may have submenus respecting the following structure:</p>
<pre class="prettyprint linenums">
<?php echo(htmlentities('<nav class="ink-navigation">
  <ul class="menu horizontal grey">
    <li><a href="#">Item</a></li>
    <li><a href="#">Item</a></li>
    <li><a href="#">Item</a></li>
    <li><a href="#">Item</a></li>
    <li>
      <a href="#">Item</a>
      <ul class="submenu">
        <li><a href="#">SubItem</a></li>
        <li><a href="#">SubItem</a></li>
      </ul>
    </li>
  </ul>
</nav>')) ?></pre>
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
					<nav class="ink-navigation">
						<ul class="menu vertical green">
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
					</nav>
				</div>
			</div>
			<div class="ink-l60">
				<div class="ink-gutter">
					<p>
						Vertical menus are built exactly the same way as the horizontal ones, just replace the <code>&lt;ul&gt;</code> class with <code>.ink-v-nav</code>. The same goes for the submenus.
					</p>
<pre class="prettyprint linenums">
<?php echo(htmlentities('<nav>
  <ul class="ink-v-nav">
    <li><a href="#">Item</a></li>
    ...
    <li>
      <a href="#">Item</a>
      <ul class="submenu">
        <li><a href="#">SubItem</a></li>
        ...
      </ul>
    </li>
  </ul>
</nav>')) ?></pre>			
	
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
<pre class="prettyprint linenums">
<?php echo(htmlentities('<footer>
  <ul class="ink-footer-nav">
    <li><a href="#">footerItem</a></li>
    <li><a href="#">footerItem</a></li>
    ...
  </ul>
</footer>')) ?></pre>
		</div>
	</div>
	<div class="ink-section">
		<div class="ink-vspace">
			<h3>Pagination</h3>
			<p>
				Paginations can be created by adding the <code>.pagination</code> class to a <code>.ink-h-nav</code> list. The <code>.active</code> and <code>.disabled</code> classes are available to diferentiate the current page and disabled links.
			</p>
			<div class="ink-vspace visual-example">
				<h4>Example</h4>
				<div class="example-container">
					<nav class="ink-navigation">
						<ul class="pagination">
							<li class="disabled previous"><a href="#">Previous</a></li>
							<li><a href="#">1</a></li>
							<li><a href="#">2</a></li>
							<li><a href="#">3</a></li>
							<li class="active"><a href="#">4</a></li>
							<li><a href="#">5</a></li>
							<li><a href="#">6</a></li>
							<li><a href="#">7</a></li>
							<li class="next"><a href="#">Next</a></li>
						</ul>
					</nav>
				</div>
			</div>
<pre class="prettyprint linenums">
<?php echo(htmlentities('<nav class="ink-navigation">
   <ul class="pagination">
   <li class="disabled"><a href="#">&laquo;</a></li>
   <li><a href="#">1</a></li>
   <li><a href="#">2</a></li>
   <li><a href="#">3</a></li>
   <li class="active"><a href="#">4</a></li>
   <li><a href="#">5</a></li>
   <li><a href="#">6</a></li>
   <li><a href="#">7</a></li>
   <li><a href="#">&raquo;</a></li>
</ul></nav>')) ?></pre>
		</div>
	</div>
	<div class="ink-section">
		<div class="ink-vspace">
			<h3>Pills</h3>
			<p>
				Pills can be created by adding the <code>.pills</code> class to a <code>.ink-h-nav</code> list. The <code>.active</code> and <code>.disabled</code> classes are available.
			</p>
			<div class="ink-vspace visual-example">
				<h4>example</h4>
				<div class="example-container">
					<nav class="ink-navigation">
						<ul class="pills">
							<li><a href="#">Item</a></li>
							<li class="active"><a href="#">Item</a></li>
							<li><a href="#">Item</a></li>
							<li><a href="#">Item</a></li>
						</ul>
					</nav>
				</div>
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
		<p>
				Breadcrumbs can be created by adding the <code>.breadcrumbs</code> class to a <code>.ink-h-nav</code> list. The <code>.active</code> and <code>.disabled</code> classes are available.
			</p>
		<div class="ink-vspace">
			<nav class="ink-navigation">
				<ul class="breadcrumbs">
					<li><a href="#">Home</a><span class="separator">/</span></li>
					<li><a href="#">Products</a><span class="separator">/</span></li>
					<li><a href="#">Category</a><span class="separator">/</span></li>
					<li class="active"><a href="#">Current item</a></li>
				</ul>
			</nav>
<pre class="prettyprint linenums">
<?php echo(htmlentities('<ul class="ink-h-nav breadcrumbs">
   <li><a href="#">Home</a><span class="separator">/</span></li>
   <li><a href="#">Products</a><span class="separator">/</span></li>
   <li><a href="#">Category</a><span class="separator">/</span></li>
   <li class="active"><a href="#">Current item</a></li>
</ul>')) ?></pre>
		</div>
	</div>
</div>
