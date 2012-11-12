<div class="whatIs">
   <div class="ink-container">
		<h2>Tables</h2>
		<p>Tables are still unbeatable for displaying tabular data.</p>
	</div>
</div>

<div class="menu-second-level">
	<div class="ink-container">
		<nav class="ink-navigation">
			<ul class="menu horizontal">
				<li class="active"><a class="scrollableLink home" href="#">Home</a></li>
				<li><a class="scrollableLink" href="#">Simple</a></li>
				<li><a class="scrollableLink" href="#">Borderless</a></li>
				<li><a class="scrollableLink" href="#">Sortable</a></li>
			</ul>
		</nav>
	</div>
</div>

<div class="ink-container">
	<div class="ink-l100 ink-vspace">
		<h3>Table basics</h3>
		<p>The default table style can be accessed by adding the <code>ink-table</code> class to the <code>&lt;table&gt;</code> element. This will enable you to use the default style. You can then add other classes to get any of the variations.</p>
		<h4>Default style</h4>
		<pre class="prettyprint linenums">
<?php echo(htmlentities('<table class="ink-table">
')) ?></pre>
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
		<p>To better distinguish between consecutive rows, combine the <code>ink-table</code> class with the <code>ink-zebra</code> class to get an alternating background color effect.</p>
		<pre class="prettyprint linenums">
<?php echo(htmlentities('<table class="ink-table ink-zebra">
')) ?></pre>
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
		<p>To highligh rows on mouse hover, add the <code>ink-hover</code> class.</p>
		<pre class="prettyprint linenums">
<?php echo(htmlentities('<table class="ink-table ink-hover">
')) ?></pre>
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
		<p>To get a more classic table with borders all around, use <code>ink-bordered</code>.</p>
		<pre class="prettyprint linenums">
<?php echo(htmlentities('<table class="ink-table ink-bordered">
')) ?></pre>
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
		<p>For great flexibility you can combine all of the above styles in the same table, obtaining a fully bordered table with alternating colored rows that light up on hover.</p>
		<pre class="prettyprint linenums">
<?php echo(htmlentities('<table class="ink-table ink-bordered ink-zebra ink-hover">
')) ?></pre>
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
		<p>You can use the <a href="typography#utilities">Ink utility classes</a> to style rows that need special attention. Use <code>ink-info</code>, <code>ink-warning</code>, <code>ink-caution</code> or <code>ink-success</code> on any <code>&lt;tr&gt;</code> to paint the row in the corresponding background color.</p>
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
					<td class="ink-success">55</td>
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