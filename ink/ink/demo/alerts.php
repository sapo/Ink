<?php include 'shared/header.php'; ?>
<!-- |||||||||||||||||||||||||||||||||  Navitation  |||||||||||||||||||||||||||||||||  -->
<nav id="topbar">
	<div class="ink-container">
		<h1><a href="intro.php" title="Site Title">InK<small>Interface kit</small></a></h1>
		<ul>
			<li><a href="grid.php">Layout</a></li>
			<li><a href="navigation.php">Navigation</a></li>
			<li><a href="typo.php">Typography & Icons</a></li>
			<li><a href="forms.php">Forms & Alerts</a></li>
			<li><a href="tables.php">Tables</a></li>
			<li class="active"><a href="alerts.php">InkJS</a></li>
		</ul>
	</div>
</nav>  

<!-- |||||||||||||||||||||||||||||||||  Content  |||||||||||||||||||||||||||||||||  -->		
<div class="ink-container">
	<h2><span>Alertas</span></h2>
	<div class="ink-space">
	<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
	</div> 
    
	<div class="ink-section">
		<div class="ink-l25">
			<div class="ink-space">
				<h3>Alertas Básicos</h3>
				<p>Os Avisos Básicos são úteis para formulários ou notificações simples.</p><br>
				<p>Para usar o estilo dos avisos básicos basta usar a class <mark>.alert-msg</mark>, podendo esta ser acompanhada das classes:</p>
				<ul class="unstyled">
					<li><mark>.error</mark> - Para Mensagens de erro</li>
					<li><mark>.success</mark> - Para Mensagens de successo</li>
					<li><mark>.info</mark> - Para Mensagens Informativas</li>
				</ul>
			</div>
		</div> 
		<div class="ink-l75">
			<div class="ink-space">
					<div class="alert-msg">
						<a href="#close" class="close">&times;</a>
						<p><b>Aviso:</b> Aqui fica o texto da notificação</p>
					</div>

					<div class="alert-msg error">
						<a href="#close" class="close">&times;</a>
						<p><b>Erro:</b> Mensagem de Erro</p>
					</div>

					<div class="alert-msg success">
						<a href="#close" class="close">&times;</a>
						<p><b>Concluído:</b> Mensagem de successo</p>
					</div>

					<div class="alert-msg info">
						<a href="#close" class="close">&times;</a>
						<p><b>Nota:</b> Mensagem de Informação</p>
					</div>
					<pre class="prettyprint">&lt;div class=&quot;alert-msg&quot;&gt;
  &lt;a href=&quot;#&quot; class=&quot;close&quot;&gt;&times;&lt;/a&gt;
  &lt;p&gt;Texto da Mensagem&lt;/p&gt;
&lt;/div&gt;</pre>
			</div>
		</div>
	</div>
	<div class="ink-section">
		<div class="ink-l25">
			<div class="ink-space">
				<h3>Alertas em Bloco</h3>
				<p>Os Avisos Básicos são úteis para formulários com explicação detalhada de erros ou erros onde será necessário acção.</p>
				<ul class="unstyled">
					<li><mark>.error</mark> - Para Mensagens de erro</li>
					<li><mark>.success</mark> - Para Mensagens de successo</li>
					<li><mark>.info</mark> - Para Mensagens Informativas</li>
				</ul>
			</div>
		</div>
		<div class="ink-l75">
			<div class="ink-space">
					<div class="block-alert-msg">
						<a href="#close" class="close">&times;</a>
						<h4>Isto é uma notificação</h4>
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
					</div>

					<div class="block-alert-msg error">
						<a href="#close" class="close">&times;</a>
						<h4>Ocurreram os seguintes erros:</h4>
						<ul>
							<li>Nome de Utilizador Inválido</li>
							<li>Email Inválido</li>
						</ul>
					</div>

					<div class="block-alert-msg success">
						<a href="#close" class="close">&times;</a>
						<h4>Processo Concluído</h4>
						<p>O seu formulário foi submetido e aguarda revisão.<br> No entanto pode:</p>
						<p><a href="#" class="ink-button">Voltar ao site</a><a href="#"  class="ink-button">Sair</a></p>
					</div>
					<pre class="prettyprint">&lt;div class=&quot;block-alert-msg error&quot;&gt;
  &lt;a href=&quot;#close&quot; class=&quot;close&quot;&gt;&times;&lt;/a&gt;
  &lt;h4&gt;Ocurreram os seguintes erros:&lt;/h4&gt;
  &lt;ul&gt;
	&lt;li&gt;Nome de Utilizador Inv&aacute;lido&lt;/li&gt;
    &lt;li&gt;Email Inv&aacute;lido&lt;/li&gt;
  &lt;/ul&gt;
&lt;/div&gt;</pre>
			</div>
		</div>
		
	</div>

</div>

		
<?php include 'shared/footer.php'; ?>