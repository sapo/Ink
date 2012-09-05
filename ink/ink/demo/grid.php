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
	<div class="ink-section gridExemple ink-clearfix">
		<div class="ink-space box">
			<div class="ink-g100 ink-clear"><p>100%</p></div>
			<div class="ink-g90 ink-clear"><p>90%</p></div>
			<div class="ink-g80 ink-clear"><p>80%</p></div>
			<div class="ink-g75 ink-clear"><p>75%</p></div>
			<div class="ink-g70 ink-clear"><p>70%</p></div>
			<div class="ink-g66 ink-clear"><p>66%</p></div>
			<div class="ink-g60 ink-clear"><p>60%</p></div>
			<div class="ink-g50 ink-clear"><p>50%</p></div>
			<div class="ink-g40 ink-clear"><p>40%</p></div>
			<div class="ink-g33 ink-clear"><p>33%</p></div>
			<div class="ink-g30 ink-clear"><p>30%</p></div>
			<div class="ink-g25 ink-clear"><p>25%</p></div>
			<div class="ink-g20 ink-clear"><p>20%</p></div>
			<div class="ink-g10 ink-clear"><p>10%</p></div>
		</div>

		<div class="ink-g33 ink-clear-left">
			<div class="ink-space">
				<h3>Markup</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
			</div>
		</div>
		<div class="ink-g66">
			<div class="ink-space">
				<pre  class="prettyprint">&lt;div class=&quot;ink-g100&quot;&gt;
  Elemento ocupa 100% do elemento pai
&lt;/div&gt;

&lt;div class=&quot;ink-g75&quot;&gt;
  Elemento ocupa 75% do elemento pai
&lt;/div&gt;

&lt;div class=&quot;ink-g30&quot;&gt;
  Elemento ocupa 30% do elemento pai
&lt;/div&gt;
				</pre>
			</div>
		</div>		
	</div>
		<div class="ink-section">
			<div class="ink-g100">
				<div class="ink-space">
					<h3 id="ddd" alt="Conteudo">Combinações</h3>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
				</div>
			</div>
			<div class="ink-g50 gridExemple2">
				<div class="ink-space box">
					<div class="ink-g100 ink-clearfix level1">
						<p>100%</p>
						<div class="ink-g50 level2"><p>50%</p></div>
						<div class="ink-g50 level2"><p>50%</p></div>
						<div class="ink-g25 level2"><p>25%</p></div>
						<div class="ink-g20 level2"><p>20%</p></div>
						<div class="ink-g10 level2"><p>10%</p></div>
						<div class="ink-g20 level2"><p>20%</p></div>
						<div class="ink-g25 level2"><p>25%</p></div>
					</div>
				</div>
				<pre  class="prettyprint">&lt;div class=&quot;ink-g100&quot;&gt;
  &lt;div class=&quot;ink-g50&quot;&gt;&lt;/div&gt;
  &lt;div class=&quot;ink-g50&quot;&gt;&lt;/div&gt;
  &lt;div class=&quot;ink-g25&quot;&gt;&lt;/div&gt;
  &lt;div class=&quot;ink-g20&quot;&gt;&lt;/div&gt;
  &lt;div class=&quot;ink-g10&quot;&gt;&lt;/div&gt;
  &lt;div class=&quot;ink-g20&quot;&gt;&lt;/div&gt;
  &lt;div class=&quot;ink-g25&quot;&gt;&lt;/div&gt;
&lt;/div&gt;</pre>
			</div>
			<div class="ink-g50 gridExemple2">
				<div class="ink-space box">
					<div class="ink-g100 ink-clearfix level1">
						<div class="ink-g50 level2">
							<p>50%</p>
							<div class="ink-g50  level2"><p>50%</p></div>
							<div class="ink-g50  level2"><p>50%</p></div>
						</div>
						<div class="ink-g50 level2">
							<p>50%</p>
							<div class="ink-g50  level2"><p>50%</p></div>
							<div class="ink-g50  level2"><p>50%</p></div>
						</div>
						<div class="ink-g25"><p>25%</p></div>
						<div class="ink-g25"><p>25%</p></div>
						<div class="ink-g25"><p>25%</p></div>
						<div class="ink-g25"><p>25%</p></div>
					</div>
				</div>
				<pre  class="prettyprint">&lt;div class=&quot;ink-g50&quot;&gt;
  &lt;div class=&quot;ink-g50&quot;&gt;&lt;/div&gt;
  &lt;div class=&quot;ink-g50&quot;&gt;&lt;/div&gt;
&lt;/div&gt;
&lt;div class=&quot;ink-g50&quot;&gt;
  &lt;div class=&quot;ink-g50&quot;&gt;&lt;/div&gt;
  &lt;div class=&quot;ink-g50&quot;&gt;&lt;/div&gt;
&lt;/div&gt;
&lt;div class=&quot;ink-g25&quot;&gt;&lt;/div&gt;
&lt;div class=&quot;ink-g25&quot;&gt;&lt;/div&gt;
&lt;div class=&quot;ink-g25&quot;&gt;&lt;/div&gt;
&lt;div class=&quot;ink-g25&quot;&gt;&lt;/div&gt;</pre>
			</div>		
		</div>
	
	
	<div class="ink-section" id="spaceExemples">
		<h2><span>Margens</span></h2>
		<div class="ink-g33">
			<h4 class="ink-space">Margens Verticais</h4>
			<div class="ink-space box">
				<div class="ink-vspace"><p>.ink-vspace</p></div>
			</div>
			<div class="ink-hspace">
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
			</div><pre  class="prettyprint">//Define a Largura
&lt;div class=&quot;ink-g33&quot;&gt; 
  //Define as Margins ou Paddings
  &lt;div class=&quot;ink-vspace&quot;&gt;...&lt;/div&gt;
&lt;/div&gt;</pre>
		</div>
		<div class="ink-g33">
			<h4 class="ink-space">Margens Horizontais</h4>
			<div class="ink-space box">
				<div class="ink-hspace" id="ola3" alt="bla vla vla"><p>.ink-hspace</p></div>
			</div>
			<div class="ink-hspace">
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
			</div><pre  class="prettyprint">//Define a Largura
&lt;div class=&quot;ink-g33&quot;&gt; 
  //Define as Margins ou Paddings
  &lt;div class=&quot;ink-hspace&quot;&gt;...&lt;/div&gt;
&lt;/div&gt;</pre>
		</div>
		<div class="ink-g33">
			<h4 class="ink-space">Margens Horizontais & Verticais</h4>
			<div class="ink-space box">
				<div class="ink-space"><p>.ink-space</p></div>
			</div>
			<div class="ink-hspace">
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
			</div><pre  class="prettyprint">//Define a Largura
&lt;div class=&quot;ink-g33&quot;&gt; 
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