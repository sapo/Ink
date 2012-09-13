<?php include 'shared/header.php'; ?>
<!-- |||||||||||||||||||||||||||||||||  Navitation  |||||||||||||||||||||||||||||||||  -->
<div id="topbar">
	<div class="ink-container">
		<h1><a class="logoPlaceholder" href="index.php" title="Site Title">InK<small>Interface kit</small></a></h1>
		<ul>	
			
			<li><a href="grid.php">Layout</a></li>
			<li><a href="navigation.php">Navigation</a></li>
			<li class="active"><a href="typo.php">Typography & Icons</a></li>
			<li><a href="forms.php">Forms & Alerts</a></li>
			<li><a href="tables.php">Tables</a></li>
			<li><a href="widgets.php">InkJS</a></li>
		</ul>
	</div>
</div>
<!-- |||||||||||||||||||||||||||||||||  Content  |||||||||||||||||||||||||||||||||  -->
<div class="ink-container whatIs">
	<div class="ink-vspace">
		<h2>Typography & Icons</h2>
		<p>
			Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
		Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
		</p>
	</div>
</div>

<nav class="menu">
	<div class="ink-container">
		<ul>
			<li class="active"><a class="home" href="#">Home</a></li>
			<li><a href="#">Headings</a></li>
			<li><a href="#">Body & Hyper text</a></li>
			<li><a href="#">Lists</a></li>
			<li><a href="#">Special cases</a></li>
			<li><a href="#">Icon Webfont</a></li>
		</ul>
	</div>
</nav>

<div class="ink-container">

	<div class="ink-section">
		<div class="ink-l30" id="headingExemple">
			<div class="ink-space">
				<h1>&lt;h1&gt; Title 1</h1>
				<h2>&lt;h2&gt; Title 2</h2>
				<h3>&lt;h3&gt; Title 3</h3>
				<h4>&lt;h4&gt; Title 4</h4>
				<h5>&lt;h5&gt; Title 5</h5>
				<h6>&lt;h6&gt; Title 6</h6>
			</div>
		</div>
		<div class="ink-l70">
			<div class="ink-space">
				<h3>Text sample</h3>
				<p>Ink's typography is designed to create a strong hierarchy with basic styles. The primary font is the die hard Helvetica Neue, but the font stack can be easily changed with just a few adjustments.</p>
			</div>
			<div class="ink-space">
				<h4>Links</h4>
				<ul class="unstyled">
					<li>Links (or hyperlinks) are defined by the tag <mark>&lt;a&gt;</mark></li>
					<li>There are 4 states for links:</li>
					<li><a href="#">Normal</a></li>
					<li><a href="#" class="visited">Visited</a></li>
					<li><a href="#" class="active">Active</a></li>
					<li><a href="#" class="hover">Hover</a></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="ink-section" id="listExemple">
		<div class="ink-l25 ">
			<div class="ink-space">
				<h4>List</h4>
				<ul>
					<li>Proin metus odio, aliquam eget molestie</li>
					<li>Phasellus quis est sed turpis sollicitudin</li>
					<li>In condimentum facilisis porta. Sed nec diam</li>
					<li>Vestibulum mollis mauris enim. Morbi</li>
					<li>In hac habitasse platea dictumst. Nam pulvinar, odio</li>
					<li>Nam pulvinar, odio sed rhoncus</li>
				</ul>
				<pre class="prettyprint">&lt;ul&gt;
&lt;li&gt;...&lt;/li&gt;
&lt;li&gt;...&lt;/li&gt;
&lt;/ul&gt;</pre>
			</div>
		</div>
		<div class="ink-l25">
			<div class="ink-space">
				<h4>Unstyled list</h4>
				<ul class="unstyled">
					<li>Proin metus odio, aliquam eget molestie</li>
					<li>Phasellus quis est sed turpis sollicitudin</li>
					<li>In condimentum facilisis porta. Sed nec diam</li>
					<li>Vestibulum mollis mauris enim. Morbi mauris</li>
					<li>In hac habitasse platea dictumst. Nam pulvinar, odio</li>
					<li>Nam pulvinar, odio sed rhoncus</li>
				</ul>
				<pre class="prettyprint">&lt;ul class=&quot;unstyled&quot;&gt;
&lt;li&gt;...&lt;/li&gt;
&lt;li&gt;...&lt;/li&gt;
&lt;/ul&gt;</pre>
			</div>
		</div>
		<div class="ink-l25">
			<div class="ink-space">
				<h4>Ordered list</h4>
				<ol>
					<li>Proin metus odio, aliquam eget molestie</li>
					<li>Phasellus quis est sed turpis sollicitudin</li>
					<li>In condimentum facilisis porta. Sed nec diam</li>
					<li>Vestibulum mollis mauris enim. Morbi</li>
					<li>In hac habitasse platea dictumst. Nam pulvinar, odio</li>
					<li>Nam pulvinar, odio sed rhoncus</li>
				</ol>
				<pre class="prettyprint">&lt;ol&gt;
&lt;li&gt;...&lt;/li&gt;
&lt;li&gt;...&lt;/li&gt;
&lt;/ol&gt;</pre>
			</div>
		</div>
		<div class="ink-l25">
			<div class="ink-space">
				<h4>Defenition list</h4>
				<dl>
					<dt>Proin metus odio, aliquam eget molestie</li>
					<dd>Phasellus quis est sed turpis sollicitudin</dd>
					<dt>Vestibulum mollis mauris enim</dt>
					<dd>In hac habitasse platea dictumst. Nam pulvinar, odio</dd>
					<dt>Nam pulvinar, odio sed rhoncus</dt>
					<dd>In condimentum facilisis porta. Sed nec diam</dd>

				</dl>
				<pre class="prettyprint">&lt;dl&gt;
&lt;dt&gt;Título&lt;/dt&gt;
&lt;dd&gt;Descrição&lt;/dd&gt;
&lt;/dl&gt;</pre>
			</div>
		</div>
	</div>
	<div class="ink-section">
		<div class="ink-l50">
			<div class="ink-space">
				<h4>Abbreviations</h4>
				<p>
					We can use the tag
					<mark>&lt;abbr&gt;</mark>
					to abbreviate the word <strong><abbr title="SAPO Interface Kit">InK</abbr></strong> 
				</p>
				<pre class="prettyprint">&lt;abbr title=&quot;SAPO Interface Kit&quot;&gt;InK&lt;/abbr&gt;</pre>
			</div>
		</div>
		<div class="ink-l25">
			<div class="ink-space">
				<h4>Address</h4>
				<address>
					<h6>Forum Telecom</h6>
					Avenida Fontes Pereira de Melo 40
					<br>
					1050 Lisboa
					<br></address>
			</div>
		</div>
		<div class="ink-l25">
			<div class="ink-space">
				<h4>Contacts</h4>
				<address>
					<h6>John Doe</h6> <strong>Phone:</strong>
					+351 111 222 333
					<br>
					<strong>Email:</strong>
					<a href="mailto:john@doe.com">john@doe.com</a>
				</address>
			</div>
		</div>
	</div>
	<div class="ink-section">
		<div class="ink-l50">
			<div class="ink-space">
				<h4>Quotes</h4>
				<blockquote>
					<p>
						Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
					</p>
					<small>Author's name</small>
				</blockquote>
			</div>
		</div>
		<div class="ink-l50">
			<div class="ink-space">
				<pre class="prettyprint">&lt;blockquote&gt;
&lt;p&gt;O Texto deve ser dividido por parágrafos&lt;/p&gt;
Pode tambem ser quebrado por line-breaks&lt;br&gt;
&lt;small&gt;Nome do Autor&lt;/small&gt;
&lt;/blockquote&gt;</pre>
			</div>
		</div>
	</div>
	<div class="ink-section">
		<div class="ink-space">
			<h4>Notes</h4>
			<p class="note">
				<strong>Note:</strong>
				To use the notes style you just need to add the <mark>.note</mark> class to the element.
				<br>
				Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
			</p>
		</div>
	</div>

	<div class="ink-section">
		<div class="ink-l50">
			<div class="ink-space">
				<h4>Labels</h4>
				<p>
					Labels can be created by adding the
					<mark>ink-label</mark>
					class to an element. 
			When combined with the
					<mark>ink-success</mark>
					,
					<mark>ink-warning</mark>
					,
					<mark>ink-caution</mark>
					or
					<mark>ink-info</mark>
					classes further meaning is added by the means of color.
				</p>
				<p>
					<mark class="ink-label ink-success">New</mark>
					For success and new messages
				</p>
				<p>
					<mark class="ink-label ink-warning">Warning</mark>
					For warnings
				</p>
				<p>
					<mark class="ink-label ink-caution">Error</mark>
					For presenting errors
				</p>
				<p>
					<mark class="ink-label ink-info">Info</mark>
					For informative notes
				</p>

			</div>
		</div>
		<div class="ink-l50">
			<div class="ink-space">
				<h4>Mark <span class="label_new">HTML5</span></h4>
				<p>The new <mark>&lt;mark&gt;</mark> tag present in the HTML5 spec is particularly useful to mark content in inline text.</p>
			</div>
		</div>
	</div>

</div>

<?php include 'shared/footer.php'; ?>