<?php include 'shared/header.php'; ?>
<!-- |||||||||||||||||||||||||||||||||  Navitation  |||||||||||||||||||||||||||||||||  -->
<nav id="topbar">
	<div class="ink-container">
		<h1><a class="logoPlaceholder" href="intro.php" title="Site Title">InK<small>Interface kit</small></a></h1>
		<ul>
			<li class="active"><a href="grid.php">Layout</a></li>
			<li><a href="navigation.php">Navigation</a></li>
			<li><a href="typo.php">Typography & Icons</a></li>
			<li><a href="forms.php">Forms & Alerts</a></li>
			<li><a href="tables.php">Tables</a></li>
			<li><a href="alerts.php">InkJS</a></li>
		</ul>
	</div>
</nav>  

<div class="ink-container whatIs">
	<div class="ink-vspace">
		<h2>Layout</h2>
		<p>Insert hipster text, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
	</div>
</div>

<nav class="menu">
	<div class="ink-container">
		<ul>
			<li class="active"><a class="home" href="#">Home</a></li>
			<li><a href="grid.php">Containers</a></li>
			<li><a href="navigation.php">Division</a></li>
			<li><a href="typo.php">Spacing</a></li>
		</ul>
	</div>
</nav>  

<!-- |||||||||||||||||||||||||||||||||  Content  |||||||||||||||||||||||||||||||||  -->		

<div class="ink-container">
	<div class="ink-space">
		<h2>Containers</h2>
		<p>The ink-container class is where you define the width of your layout. You should define either a static width, a relative width or a maximum width for this element and use it to contain your layout.</p>
	</div>
	<div class="ink-section gridExemple ink-clear">
		<div class="ink-space box">
			<div class="ink-l100 ink-m100"><p>100%</p></div>
			<div class="ink-l90 ink-m100 ink-clear"><p>90%</p></div>
			<div class="ink-l80 ink-m100 ink-clear"><p>80%</p></div>
			<div class="ink-l75 ink-m100 ink-clear"><p>75%</p></div>
			<div class="ink-l70 ink-m100 ink-clear"><p>70%</p></div>
			<div class="ink-l66 ink-m100 ink-clear"><p>66%</p></div>
			<div class="ink-l60 ink-m100 ink-clear"><p>60%</p></div>
			<div class="ink-l50 ink-m100 ink-clear"><p>50%</p></div>
			<div class="ink-l40 ink-m100 ink-clear"><p>40%</p></div>
			<div class="ink-l33 ink-m100 ink-clear"><p>33%</p></div>
			<div class="ink-l30 ink-m100 ink-clear"><p>30%</p></div>
			<div class="ink-l25 ink-m100 ink-clear"><p>25%</p></div>
			<div class="ink-l20 ink-m100 ink-clear"><p>20%</p></div>
			<div class="ink-l10 ink-m100 ink-clear"><p>10%</p></div>
		</div>

		<div class="ink-l33 ink-clear-left">
			<div class="ink-space">
				<h3>Markup</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
			</div>
		</div>
		<div class="ink-l66">
			<div class="ink-space">
				<pre  class="prettyprint">&lt;div class=&quot;ink-l100&quot;&gt;
  Elemento ocupa 100% do elemento pai
&lt;/div&gt;

&lt;div class=&quot;ink-l75&quot;&gt;
  Elemento ocupa 75% do elemento pai
&lt;/div&gt;

&lt;div class=&quot;ink-l30&quot;&gt;
  Elemento ocupa 30% do elemento pai
&lt;/div&gt;
				</pre>
			</div>
		</div>		
	</div>
		<div class="ink-section">
			<div class="ink-l100">
				<div class="ink-space">
					<h3 id="ddd" alt="Conteudo">Combinações</h3>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
				</div>
			</div>
			<div class="ink-l50 gridExemple2">
				<div class="ink-space box">
					<div class="ink-l100 ink-m100 level1">
						<p>100%</p>
						<div class="ink-l50 ink-m100 level2"><p>50%</p></div>
						<div class="ink-l50 ink-m100 level2"><p>50%</p></div>
						<div class="ink-l25 ink-m100 level2"><p>25%</p></div>
						<div class="ink-l20 ink-m100 level2"><p>20%</p></div>
						<div class="ink-l10 ink-m100 level2"><p>10%</p></div>
						<div class="ink-l20 ink-m100 level2"><p>20%</p></div>
						<div class="ink-l25 ink-m100 level2"><p>25%</p></div>
					</div>
				</div>
				<pre  class="prettyprint">&lt;div class=&quot;ink-l100&quot;&gt;
  &lt;div class=&quot;ink-l50&quot;&gt;&lt;/div&gt;
  &lt;div class=&quot;ink-l50&quot;&gt;&lt;/div&gt;
  &lt;div class=&quot;ink-l25&quot;&gt;&lt;/div&gt;
  &lt;div class=&quot;ink-l20&quot;&gt;&lt;/div&gt;
  &lt;div class=&quot;ink-l10&quot;&gt;&lt;/div&gt;
  &lt;div class=&quot;ink-l20&quot;&gt;&lt;/div&gt;
  &lt;div class=&quot;ink-l25&quot;&gt;&lt;/div&gt;
&lt;/div&gt;</pre>
			</div>
			<div class="ink-l50 gridExemple2">
				<div class="ink-space box">
					<div class="ink-l100 ink-m100 level1">
						<div class="ink-l50 ink-m100 level2">
							<div class="ink-l100 ink-m100 level2"><p>50%</p></div>
							<div class="ink-l50 ink-m100 level2"><p>50%</p></div>
							<div class="ink-l50 ink-m100 level2"><p>50%</p></div>
						</div>
						<div class="ink-l50 ink-m100 level2">
							<div class="ink-l100 ink-m100 level2"><p>50%</p></div>
							<div class="ink-l50 ink-m100 level2"><p>50%</p></div>
							<div class="ink-l50 ink-m100 level2"><p>50%</p></div>
						</div>
						<div class="ink-l25 ink-m100"><p>25%</p></div>
						<div class="ink-l25 ink-m100"><p>25%</p></div>
						<div class="ink-l25 ink-m100"><p>25%</p></div>
						<div class="ink-l25 ink-m100"><p>25%</p></div>
					</div>
				</div>
				<pre  class="prettyprint">&lt;div class=&quot;ink-l50&quot;&gt;
  &lt;div class=&quot;ink-l50&quot;&gt;&lt;/div&gt;
  &lt;div class=&quot;ink-l50&quot;&gt;&lt;/div&gt;
&lt;/div&gt;
&lt;div class=&quot;ink-l50&quot;&gt;
  &lt;div class=&quot;ink-l50&quot;&gt;&lt;/div&gt;
  &lt;div class=&quot;ink-l50&quot;&gt;&lt;/div&gt;
&lt;/div&gt;
&lt;div class=&quot;ink-l25&quot;&gt;&lt;/div&gt;
&lt;div class=&quot;ink-l25&quot;&gt;&lt;/div&gt;
&lt;div class=&quot;ink-l25&quot;&gt;&lt;/div&gt;
&lt;div class=&quot;ink-l25&quot;&gt;&lt;/div&gt;</pre>
			</div>		
		</div>
	
	
	<div class="ink-section" id="spaceExemples">
		<h2><span>Margens</span></h2>
		<div class="ink-l33">
			<h4 class="ink-space">Margens Verticais</h4>
			<div class="ink-space box">
				<div class="ink-vspace"><p>.ink-vspace</p></div>
			</div>
			<div class="ink-hspace">
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
			</div><pre  class="prettyprint">//Define a Largura
&lt;div class=&quot;ink-l33&quot;&gt; 
  //Define as Margins ou Paddings
  &lt;div class=&quot;ink-vspace&quot;&gt;...&lt;/div&gt;
&lt;/div&gt;</pre>
		</div>
		<div class="ink-l33">
			<h4 class="ink-space">Margens Horizontais</h4>
			<div class="ink-space box">
				<div class="ink-hspace" id="ola3" alt="bla vla vla"><p>.ink-hspace</p></div>
			</div>
			<div class="ink-hspace">
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
			</div><pre  class="prettyprint">//Define a Largura
&lt;div class=&quot;ink-l33&quot;&gt; 
  //Define as Margins ou Paddings
  &lt;div class=&quot;ink-hspace&quot;&gt;...&lt;/div&gt;
&lt;/div&gt;</pre>
		</div>
		<div class="ink-l33">
			<h4 class="ink-space">Margens Horizontais & Verticais</h4>
			<div class="ink-space box">
				<div class="ink-space"><p>.ink-space</p></div>
			</div>
			<div class="ink-hspace">
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
			</div><pre  class="prettyprint">//Define a Largura
&lt;div class=&quot;ink-l33&quot;&gt; 
  //Define as Margins ou Paddings
  &lt;div class=&quot;ink-space&quot;&gt;...&lt;/div&gt;
&lt;/div&gt;</pre>
			
		</div>
	</div>
	<div class="ink-section">
		<h3>List of available Classes</h3>
		<div class="ink-space">
			.ink-container <br>
			.ink-section<br>
			.ink-space<br>
			.ink-vspace<br>
			.ink-hspace<br>
			
		</div>
	</div>
</div>

<?php include 'shared/footer.php'; ?>	