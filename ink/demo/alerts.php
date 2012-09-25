<?php include 'shared/header.php'; ?>
<!-- |||||||||||||||||||||||||||||||||  Navitation  |||||||||||||||||||||||||||||||||  -->
<nav id="topbar">
	<div class="ink-container">
		<ul class="ink-h-nav">
			<li><a class="logoPlaceholder" href="./" title="Site Title">InK</a></li>
			<li><a href="grid.php">Layout</a></li>
			<li><a href="navigation.php">Navigation</a></li>
			<li><a href="typo.php">Typography & Icons</a></li>
			<li><a href="forms.php">Forms</a></li>
			<li class="active"><a href="alerts.php">Alerts</a></li>
			<li><a href="tables.php">Tables</a></li>
			<li><a href="widgets.php">InkJS</a></li>
		</ul>
	</div>
</nav>

<!-- |||||||||||||||||||||||||||||||||  Content  |||||||||||||||||||||||||||||||||  -->		
<div class="ink-container whatIs">
	<div class="ink-vspace">
		<h2>Alerts</h2>
		<p>
			Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
			Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
		</p>
	</div>
</div>

<nav class="menu">
	<div class="ink-container">
		<ul class="ink-h-nav">
			<li class="active"><a class="home" href="#">Home</a></li>
			<li><a href="#">Basic alerts</a></li>
			<li><a href="#">Block alerts</a></li>
		</ul>
	</div>
</nav>

<div class="ink-container">
	<div class="ink-section">
		<div class="ink-l30">
			<div class="ink-space">
				<h3>Alertas Básicos</h3>
				<p>Os Avisos Básicos são úteis para formulários ou notificações simples.</p>
				<p>Para usar o estilo dos avisos básicos basta usar a class <mark>.alert-msg</mark>, podendo esta ser acompanhada das classes:</p>
				<ul class="unstyled">
					<li><mark>.error</mark> - Para Mensagens de erro</li>
					<li><mark>.success</mark> - Para Mensagens de successo</li>
					<li><mark>.info</mark> - Para Mensagens Informativas</li>
				</ul>
			</div>
		</div> 
		<div class="ink-l70">
			<div class="ink-space">
					<div class="alert-msg">
						<button class="close">&times;</button>
						<p><b>Aviso:</b> Aqui fica o texto da notificação</p>
					</div>

					<div class="alert-msg error">
						<button class="close">&times;</button>
						<p><b>Erro:</b> Mensagem de Erro</p>
					</div>

					<div class="alert-msg success">
						<button class="close">&times;</button>
						<p><b>Concluído:</b> Mensagem de successo</p>
					</div>

					<div class="alert-msg info">
						<button class="close">&times;</button>
						<p><b>Nota:</b> Mensagem de Informação</p>
					</div>
					<pre class="prettyprint"><ol><li><span class="tag">&lt;div</span><span class="tag"> <span class="pln"></span><span class="atn">class</span><span class="pun">=</span><span class="atv">"alert-msg"</span>&gt;</span></li><li>  <span class="tag">&lt;button</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"close"</span><span class="tag">&gt;</span><span class="pln">x</span><span class="tag">&lt;/button&gt;</span></li><li>  <span class="tag">&lt;p&gt;</span><span class="pln">Texto da mensagem</span><span class="tag">&lt;/p&gt;</span><span class="tag"></span></li><li><span class="tag">&lt;/div&gt;</span><span class="tag"></span></li></ol></pre>
			</div>
		</div>
	</div>
	<div class="ink-section">
		<div class="ink-l30">
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
		<div class="ink-l70">
			<div class="ink-space">
				<div class="block-alert-msg">
					<button class="close">&times;</button>
					<h4>Isto é uma notificação</h4>
					<p>
						Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
						Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
					</p>
				</div>

				<div class="block-alert-msg error">
					<button class="close">&times;</button>
					<h4>Ocurreram os seguintes erros:</h4>
					<ul>
						<li>Nome de Utilizador Inválido</li>
						<li>Email Inválido</li>
					</ul>
				</div>

				<div class="block-alert-msg success">
					<button class="close">&times;</button>
					<h4>Processo Concluído</h4>
					<p>O seu formulário foi submetido e aguarda revisão.</p>
				</div>
				<pre class="prettyprint"><ol><li><span class="tag">&lt;div</span><span class="tag"> <span class="pln"></span><span class="atn">class</span><span class="pun">=</span><span class="atv">"block-alert-msg error"</span>&gt;</span></li><li><span class="tag">   &lt;button</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"close"</span><span class="tag">&gt;</span><span class="pln">x</span><span class="tag">&lt;/button&gt;</span></li><li><span class="tag">   &lt;h4</span><span class="pln"></span><span class="atv"></span><span class="tag">&gt;</span><span class="pln">Ocorreram os seguintes erros</span><span class="tag">&lt;/h4&gt;</span></li><li>   <span class="tag">&lt;ul</span><span class="tag">&gt;</span></li><li>      <span class="tag">&lt;li&gt;</span><span class="pln">Nome de utilizador inválido</span><span class="tag">&lt;/li&gt;</span><span class="tag"></span></li><li><span class="tag">      &lt;li&gt;</span><span class="pln">Nome de utilizador inválido</span><span class="tag">&lt;/li&gt;</span><span class="tag"></span></li><li><span class="tag">   &lt;/ul</span><span class="pln"></span><span class="tag">&gt;</span><span class="tag"></span><span class="tag"></span></li><li><span class="tag">&lt;/div&gt;</span><span class="tag"></span></li></ol></pre>
			</div>
		</div>
		
	</div>

</div>

		
<?php include 'shared/footer.php'; ?>