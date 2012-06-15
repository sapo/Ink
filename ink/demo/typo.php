<?php include 'shared/header.php'; ?>
<!-- |||||||||||||||||||||||||||||||||  Navitation  |||||||||||||||||||||||||||||||||  -->
<div class="blackMenu" id="topMenu">
	<h1><a href="intro.php" title="Site Title">InK <small>Interface kit</small></a></h1>
	<a href="#" onclick="toogleNav()" id="toggleNavigation">Menu</a>
	<nav>
		<ul class="h_navigation">
			<li><a href="intro.php">Intro</a></li>
			<li><a href="grid.php">Grelha</a></li>
			<li class="active"><a href="typo.php">Tipografia</a></li>
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
	<h2><span>Tipografia</span></h2>
	<div class="space">
		Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
	</div>
	
	<div class="section">
		<div class="g30" id="headingExemple">
			<div class="space">
				<h1>&lt;h1&gt; Título 1</h1>
				<h2>&lt;h2&gt; Título 2</h2>
				<h3>&lt;h3&gt; Título 3</h3>
				<h4>&lt;h4&gt; Título 4</h4>
				<h5>&lt;h5&gt; Título 5</h5>
				<h6>&lt;h6&gt; Título 6</h6>
			</div>	
		</div>
		<div class="g70">
			<div class="space">
				<h3>Exemplo de Texto</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
				</p>
			</div> 
			<div class="g60">
				<div class="space">
					<h4>Links</h4>
					<p>Links (ou hiperligações) são definidos pela tag <mark>&lt;a&gt;</mark><p>
					<p>Existem quatros estados para links:</p>
					<p><a href="#">Normal</a></p>
					<p><a href="#" class="visited">Visitado</a></p>
					<p><a href="#" class="active">Activo</a></p>
					<p><a href="#" class="hover">Sobre</a></p>
				</div>
			</div>
			<div class="g40">
				<div class="space">
                 	<h4>Ênfase & Negrito</h4> 
				</div>
			</div>
		</div>
	</div>
	<div class="section" id="listExemple">
		<div class="g25 ">
			<div class="space">
				<h4>Lista</h4>
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
		<div class="g25">
			<div class="space">
				<h4>Lista sem estilo</h4>
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
		<div class="g25">
			<div class="space">
				<h4>Lista Ordenada</h4>
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
		<div class="g25">
			<div class="space">
				<h4>Lista de Definições</h4>
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
	<div class="section">
		<div class="g50">
			<div class="space">
				<h4>Abreviaturas</h4>
				<p>Podemos utilizar a tag <mark>&lt;abbr&gt;</mark> para abreviar a palavra <strong><abbr title="SAPO Interface Kit">InK</abbr></strong></p> 
				<pre class="prettyprint">&lt;abbr title=&quot;SAPO Interface Kit&quot;&gt;InK&lt;/abbr&gt;</pre>
			</div> 
		</div>
		<div class="g25">                                     
			<div class="space">
				<h4>Morada</h4>
				<address>
					<h6>Forum Telecom</h6>
					Avenida Fontes Pereira de Melo 40<br>
					1050 Lisboa<br>
				</address>
			</div>
		</div>
		<div class="g25">                                     
			<div class="space">
				<h4>Contactos</h4>
				<address>
					<h6>John Doe</h6>
					<strong>Telef:</strong> +351 111 222 333<br>
					<strong>Email:</strong> <a href="mailto:john@doe.com">john@doe.com</a>
				</address>
			</div>
		</div>
	</div>
	<div class="section">
		<div class="g50">
			<div class="space">
				<h4>Citações</h4>
				<blockquote>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
					<small>Nome do Autor</small>
				</blockquote>
			</div> 
		</div>
		<div class="g50">
			<div class="space">
		<pre class="prettyprint">&lt;blockquote&gt;
  &lt;p&gt;O Texto deve ser dividido por parágrafos&lt;/p&gt;
  Pode tambem ser quebrado por line-breaks&lt;br&gt;
  &lt;small&gt;Nome do Autor&lt;/small&gt;
&lt;/blockquote&gt;</pre>
			</div> 
		</div>
	</div>
	<div class="section">
		<div class="space">
			<h4>Notas</h4>
			<p class="note"><strong>Nota:</strong> Para utlizar o estilo das notas bastar colocar no elemento a classe <mark>.note</mark>.<br>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
		</div>
	</div>
	
	<div class="section">
		<div class="g50">
			<div class="space">
				<h4>Labels</h4>
				<p>
					As labels são úteis para utilizar no decorrer de texto ou separadamente.<br>
					Geralmente são elementos inline como <mark>&lt;span&gt;</mark> podendo ter como class
					<mark>.label_new</mark>, <mark>.label_info</mark>, <mark>.label_warning</mark>, <mark>.label_error</mark>
					  e dividem-se em:
				</p>
				<p><span class="label_new">Novo</span> Para mensagens de successo ou novidade</p>
				<p><span class="label_warning">Aviso</span> Para avisos</p>
				<p><span class="label_error">Erro</span> Para apresentar erros</p>
				<p><span class="label_info">Info</span> Para notas informativas </p>
				
			</div>
		</div>
		<div class="g50">
			<div class="space">
				<h4>Mark <span class="label_new">HTML5</span></h4>
				<p>A nova tag <mark>&lt;mark&gt;</mark> presento na especificação de HTML5 é particularmente util para marcar conteúdo em texto inline.</p>
			</div>
		</div>
	</div>
	
	</div>

		
<?php include 'shared/footer.php'; ?>	
