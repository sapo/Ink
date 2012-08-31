<?php include 'shared/header.php'; ?>
<!-- |||||||||||||||||||||||||||||||||  Navitation  |||||||||||||||||||||||||||||||||  -->
<div class="blackMenu" id="topMenu">
	<h1><a href="intro.php" title="Site Title">InK <small>Interface kit</small></a></h1>
	<a href="#" onclick="toogleNav()" id="toggleNavigation">Menu</a>
	<nav>
		<ul class="h_navigation">
			<li><a href="intro.php">Intro</a></li>
			<li class="active"><a href="grid.php">Layout</a></li>
			<li><a href="typo.php">Tipografia</a></li>
			<li><a href="forms.php">Formulários</a></li>
			<li><a href="tables.php">Tabelas</a></li>
			<li><a href="alerts.php">Alerts</a></li>
			<li><a href="navigation.php">Navegação</a></li>
			<li><a href="widgets.php">Widgets</a></li>
		</ul>
	</nav>
	
	<script type="text/javascript">
		$(document).ready(
			$("#toggleNavigation").click(function () {
			
				if ($("ul.h_navigation").is(":hidden")) {
					$("ul.h_navigation").slideDown("fast");
					} else {
					$("ul.h_navigation").slideUp("fast");
					}
				})
			
			);
	</script>
</div>
<!-- |||||||||||||||||||||||||||||||||  Content  |||||||||||||||||||||||||||||||||  -->		

<div class="container_width">
	<h2><span>Layout</span></h2>
	<div class="space">
		<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
	</div>
	<div class="section gridExemple clearfix">
		<div class="space box">
			<div class="g100 clear"><p>100%</p></div>
			<div class="g90 clear"><p>90%</p></div>
			<div class="g80 clear"><p>80%</p></div>
			<div class="g75 clear"><p>75%</p></div>
			<div class="g70 clear"><p>70%</p></div>
			<div class="g66 clear"><p>66%</p></div>
			<div class="g60 clear"><p>60%</p></div>
			<div class="g50 clear"><p>50%</p></div>
			<div class="g40 clear"><p>40%</p></div>
			<div class="g33 clear"><p>33%</p></div>
			<div class="g30 clear"><p>30%</p></div>
			<div class="g25 clear"><p>25%</p></div>
			<div class="g20 clear"><p>20%</p></div>
			<div class="g10 clear"><p>10%</p></div>
		</div>

		<div class="g33 clearleft">
			<div class="space">
				<h3>Markup</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
			</div>
		</div>
		<div class="g66">
			<div class="space">
				<pre  class="prettyprint">&lt;div class=&quot;g100&quot;&gt;
  Elemento ocupa 100% do elemento pai
&lt;/div&gt;

&lt;div class=&quot;g75&quot;&gt;
  Elemento ocupa 75% do elemento pai
&lt;/div&gt;

&lt;div class=&quot;g30&quot;&gt;
  Elemento ocupa 30% do elemento pai
&lt;/div&gt;
				</pre>
			</div>
		</div>		
	</div>
		<div class="section">
			<div class="g100">
				<div class="space">
					<h3 id="ddd" alt="Conteudo">Combinações</h3>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
				</div>
			</div>
			<div class="g50 gridExemple2">
				<div class="space box">
					<div class="g100 clearfix level1">
						<p>100%</p>
						<div class="g50 level2"><p>50%</p></div>
						<div class="g50 level2"><p>50%</p></div>
						<div class="g25 level2"><p>25%</p></div>
						<div class="g20 level2"><p>20%</p></div>
						<div class="g10 level2"><p>10%</p></div>
						<div class="g20 level2"><p>20%</p></div>
						<div class="g25 level2"><p>25%</p></div>
					</div>
				</div>
				<pre  class="prettyprint">&lt;div class=&quot;g100&quot;&gt;
  &lt;div class=&quot;g50&quot;&gt;&lt;/div&gt;
  &lt;div class=&quot;g50&quot;&gt;&lt;/div&gt;
  &lt;div class=&quot;g25&quot;&gt;&lt;/div&gt;
  &lt;div class=&quot;g20&quot;&gt;&lt;/div&gt;
  &lt;div class=&quot;g10&quot;&gt;&lt;/div&gt;
  &lt;div class=&quot;g20&quot;&gt;&lt;/div&gt;
  &lt;div class=&quot;g25&quot;&gt;&lt;/div&gt;
&lt;/div&gt;</pre>
			</div>
			<div class="g50 gridExemple2">
				<div class="space box">
					<div class="g100 clearfix level1">
						<div class="g50 level2">
							<p>50%</p>
							<div class="g50  level2"><p>50%</p></div>
							<div class="g50  level2"><p>50%</p></div>
						</div>
						<div class="g50 level2">
							<p>50%</p>
							<div class="g50  level2"><p>50%</p></div>
							<div class="g50  level2"><p>50%</p></div>
						</div>
						<div class="g25"><p>25%</p></div>
						<div class="g25"><p>25%</p></div>
						<div class="g25"><p>25%</p></div>
						<div class="g25"><p>25%</p></div>
					</div>
				</div>
				<pre  class="prettyprint">&lt;div class=&quot;g50&quot;&gt;
  &lt;div class=&quot;g50&quot;&gt;&lt;/div&gt;
  &lt;div class=&quot;g50&quot;&gt;&lt;/div&gt;
&lt;/div&gt;
&lt;div class=&quot;g50&quot;&gt;
  &lt;div class=&quot;g50&quot;&gt;&lt;/div&gt;
  &lt;div class=&quot;g50&quot;&gt;&lt;/div&gt;
&lt;/div&gt;
&lt;div class=&quot;g25&quot;&gt;&lt;/div&gt;
&lt;div class=&quot;g25&quot;&gt;&lt;/div&gt;
&lt;div class=&quot;g25&quot;&gt;&lt;/div&gt;
&lt;div class=&quot;g25&quot;&gt;&lt;/div&gt;</pre>
			</div>		
		</div>
	
	
	<div class="section" id="spaceExemples">
		<h2><span>Margens</span></h2>
		<div class="g33">
			<h4 class="space">Margens Verticais</h4>
			<div class="space box">
				<div class="v_space"><p>.v_space</p></div>
			</div>
			<div class="h_space">
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
			</div><pre  class="prettyprint">//Define a Largura
&lt;div class=&quot;g33&quot;&gt; 
  //Define as Margins ou Paddings
  &lt;div class=&quot;v_space&quot;&gt;...&lt;/div&gt;
&lt;/div&gt;</pre>
		</div>
		<div class="g33">
			<h4 class="space">Margens Horizontais</h4>
			<div class="space box">
				<div class="h_space" id="ola3" alt="bla vla vla"><p>.h_space</p></div>
			</div>
			<div class="h_space">
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
			</div><pre  class="prettyprint">//Define a Largura
&lt;div class=&quot;g33&quot;&gt; 
  //Define as Margins ou Paddings
  &lt;div class=&quot;h_space&quot;&gt;...&lt;/div&gt;
&lt;/div&gt;</pre>
		</div>
		<div class="g33">
			<h4 class="space">Margens Horizontais & Verticais</h4>
			<div class="space box">
				<div class="space"><p>.space</p></div>
			</div>
			<div class="h_space">
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
			</div><pre  class="prettyprint">//Define a Largura
&lt;div class=&quot;g33&quot;&gt; 
  //Define as Margins ou Paddings
  &lt;div class=&quot;space&quot;&gt;...&lt;/div&gt;
&lt;/div&gt;</pre>
			
		</div>
	</div>
	<div class="section">
		<h3>Lista de Classes Disponíveis</h3>
		<div class="space">
			.container_width <br>
			.section<br>
			.space<br>
			.v_space<br>
			.h_space<br>
			
		</div>
	</div>
</div>

<?php include 'shared/footer.php'; ?>	