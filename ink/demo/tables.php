<?php include 'shared/header.php'; ?>
<!-- |||||||||||||||||||||||||||||||||  Navitation  |||||||||||||||||||||||||||||||||  -->
<nav id="topbar">
	<div class="ink-container">
		<h1><a class="logoPlaceholder" href="intro.php" title="Site Title">InK<small>Interface kit</small></a></h1>
		<ul>
			<li>
				<a href="grid.php">Layout</a>
			</li>
			<li>
				<a href="navigation.php">Navigation</a>
			</li>
			<li>
				<a href="typo.php">Typography & Icons</a>
			</li>
			<li>
				<a href="forms.php">Forms & Alerts</a>
			</li>
			<li class="active">
				<a href="tables.php">Tables</a>
			</li>
			<li>
				<a href="widgets.php">InkJS</a>
			</li>
		</ul>
	</div>
</nav>

<!-- |||||||||||||||||||||||||||||||||  Content  |||||||||||||||||||||||||||||||||  -->
<div class="ink-container whatIs">
	<div class="ink-space">
		<h2>Tables</h2>
		<p>Tables are tables...</p>
	</div>
</div>

<nav class="menu">
	<div class="ink-container">
		<ul>
			<li class="active"><a class="home" href="#">Home</a></li>
			<li><a href="#">Simple</a></li>
			<li><a href="#">Borderless</a></li>
			<li><a href="#">Sortable</a></li>
		</ul>
	</div>
</nav>

<div class="ink-container">
	<div class="ink-l25">
		<div class="ink-space">
			<h3>Tabelas</h3>
			<p>
				Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
			</p>
		</div>
	</div>
	<div class="ink-l75">
		<div class="ink-space">
			<div class="ink-l100 ink-vspace">
				<h4>Default table style</h4>
				<p>The default table style can be accessed by adding the ink-table css class to the table element. This will enable you to use the default style or any of the variations.</p>
				<table class="ink-table">
					<thead>
						<tr>
							<th>ID</th>
							<th>Product name</th>
							<th>Product price</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="3">This is a table footer</td>
						</tr>
					</tfoot>
					<tbody>
						<tr>
							<td>24</td>
							<td>Twin color basketball shoe</td>
							<td>&euro;26,00</td>
						</tr>
						<tr>
							<td>13</td>
							<td>Cast iron waffle maker</td>
							<td>&euro;158,00</td>
						</tr>
						<tr>
							<td>55</td>
							<td>Sports duffle bag</td>
							<td>&euro;15,00</td>
						</tr>
						<tr>
							<td>23</td>
							<td>Some stuff </td>
							<td>&euro;489,00</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="ink-l100 ink-vspace">
				<h4>Alternating colored rows</h4>
				<p>Combining the ink-table class with the ink-zebra class...</p>
				<table class="ink-table ink-zebra">
					<thead>
						<tr>
							<th>ID</th>
							<th>Product name</th>
							<th>Product price</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="3">This is a table footer</td>
						</tr>
					</tfoot>
					<tbody>
						<tr>
							<td>24</td>
							<td>Twin color basketball shoe</td>
							<td>&euro;26,00</td>
						</tr>
						<tr>
							<td>13</td>
							<td>Cast iron waffle maker</td>
							<td>&euro;158,00</td>
						</tr>
						<tr>
							<td>55</td>
							<td>Sports duffle bag</td>
							<td>&euro;15,00</td>
						</tr>
						<tr>
							<td>23</td>
							<td>Some stuff </td>
							<td>&euro;489,00</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="ink-l100 ink-vspace">
				<h4>Highlight rows on hover</h4>
				<table class="ink-table ink-hover">
					<thead>
						<tr>
							<th>ID</th>
							<th>Product name</th>
							<th>Product price</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="3">This is a table footer</td>
						</tr>
					</tfoot>
					<tbody>
						<tr>
							<td>24</td>
							<td>Twin color basketball shoe</td>
							<td>&euro;26,00</td>
						</tr>
						<tr>
							<td>13</td>
							<td>Cast iron waffle maker</td>
							<td>&euro;158,00</td>
						</tr>
						<tr>
							<td>55</td>
							<td>Sports duffle bag</td>
							<td>&euro;15,00</td>
						</tr>
						<tr>
							<td>23</td>
							<td>Some stuff </td>
							<td>&euro;489,00</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="ink-l100 ink-vspace">
				<h4>Full borders</h4>
				<table class="ink-table ink-bordered">
					<thead>
						<tr>
							<th>ID</th>
							<th>Product name</th>
							<th>Product price</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td>24</td>
							<td>Twin color basketball shoe</td>
							<td>&euro;26,00</td>
						</tr>
					</tfoot>
					<tbody>
						<tr>
							<td>24</td>
							<td>Twin color basketball shoe</td>
							<td>&euro;26,00</td>
						</tr>
						<tr>
							<td>13</td>
							<td>Cast iron waffle maker</td>
							<td>&euro;158,00</td>
						</tr>
						<tr>
							<td>55</td>
							<td>Sports duffle bag</td>
							<td>&euro;15,00</td>
						</tr>
						<tr>
							<td>23</td>
							<td>Some stuff </td>
							<td>&euro;489,00</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="ink-l100 ink-vspace">
				<h4>Combining several styles</h4>
				<table class="ink-table ink-bordered ink-zebra ink-hover">
					<thead>
						<tr>
							<th>ID</th>
							<th>Product name</th>
							<th>Product price</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td>24</td>
							<td>Twin color basketball shoe</td>
							<td>&euro;26,00</td>
						</tr>
					</tfoot>
					<tbody>
						<tr>
							<td>24</td>
							<td>Twin color basketball shoe</td>
							<td>&euro;26,00</td>
						</tr>
						<tr>
							<td>13</td>
							<td>Cast iron waffle maker</td>
							<td>&euro;158,00</td>
						</tr>
						<tr>
							<td>55</td>
							<td>Sports duffle bag</td>
							<td>&euro;15,00</td>
						</tr>
						<tr>
							<td>23</td>
							<td>Some stuff </td>
							<td>&euro;489,00</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="ink-l100 ink-vspace">
				<h4>Special colored rows</h4>
				<table class="ink-table ink-bordered">
					<thead>
						<tr>
							<th>ID</th>
							<th>Product name</th>
							<th>Product price</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td>24</td>
							<td>Twin color basketball shoe</td>
							<td>&euro;26,00</td>
						</tr>
					</tfoot>
					<tbody>
						<tr class="ink-warning">
							<td>24</td>
							<td>Twin color basketball shoe</td>
							<td>&euro;26,00</td>
						</tr>
						<tr class="ink-success">
							<td>13</td>
							<td>Cast iron waffle maker</td>
							<td>&euro;158,00</td>
						</tr>
						<tr>
							<td>55</td>
							<td>Sports duffle bag</td>
							<td>&euro;15,00</td>
						</tr>
						<tr>
							<td>23</td>
							<td>Some stuff </td>
							<td>&euro;489,00</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<?php include 'shared/footer.php'; ?>
