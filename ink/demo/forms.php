<?php include 'shared/header.php'; ?>
<!-- |||||||||||||||||||||||||||||||||  Navitation  |||||||||||||||||||||||||||||||||  -->
<div class="blackMenu" id="topMenu">
	<h1><a href="intro.php" title="Site Title">InK <small>Interface kit</small></a></h1>
	<a href="#" onclick="toogleNav()" id="toggleNavigation">Menu</a>
	<nav>
		<ul class="h_navigation">
			<li><a href="intro.php">Intro</a></li>
			<li><a href="grid.php">Layout</a></li>
			<li><a href="typo.php">Tipografia</a></li>
			<li class="active"><a href="forms.php">Formulários</a></li>
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
	<h2><span>Formulários</span></h2>
	<div class="space">
	<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
	</div>
	<div class="section">
		<form class="g66">
			<fieldset class="box space">
				<p class="note"><strong>Nota:</strong> Os campos * são de preenchimento Obrigatório</p>
				<div class="e_wrap">
					<label for="inpuTypeTex">Input</label>
					<input id="inpuTypeTex" type="text">
				</div>
				<div class="e_wrap">
					<label for="textarea">Textarea</label>
					<textarea id="textarea" rows="8" cols="40"></textarea>
				</div>
				<div class="e_wrap">
					<label for="fileInput">File Input</label>
					<input id="fileInput" type="file">
				</div>
				<div class="e_wrap">
					<label for="normalSelect">Select</label>
					<select name="normalSelect" id="normalSelect">
						<option>1</option>
						<option>2</option>
						<option>3</option>
						<option>4</option>
						<option>5</option>
					</select>
				</div>
				<div class="e_wrap">
					<label for="multiSelect">Multiple select</label>
					<select multiple="multiple" id="multiSelect">
						<option>1</option>
						<option>2</option>
						<option>3</option>
						<option>4</option>
						<option>5</option>
					</select>
				</div>
				<div class="e_wrap emptyLabel">
					<label for="checkbox"><input id="checkbox" type="checkbox">Checkbox Label</label>
				</div>
				<div class="e_wrap emptyLabel" >
					<label for="radio"><input id="radio" type="radio">Radio Label</label>
				</div>
				<div class="e_wrap">
					<label>Inline Inputs</label>
					<input type="text" class="miniInput" placeholder="Ano"> de <input type="text" class="miniInput" placeholder="Mês">
				</div>
				<div class="e_wrap">
					<label>Lista de Checkboxs</label>
					<ul class="formList">
						<li><label for="checkbox1"><input id="checkbox1" type="checkbox"><span>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</span></label></li>
						<li><label for="checkbox2"><input id="checkbox2" type="checkbox"><span>Checkbox Label</span></label></li>
						<li><label for="checkbox3"><input id="checkbox3" type="checkbox"><span>Checkbox Label</span></label></li>
					</ul>
				</div>
				<div class="e_wrap error">
					<label for="inpuTypeTex">Input com Erro</label>
					<input id="inpuTypeTex" type="text">
					<p class="help">Este Campo é de Preencimento Obrigatório</p>
				</div>
				<div class="e_wrap warning">
					<label for="fileInput">Input com Aviso</label>
					<input id="fileInput" type="file">
				</div>	
			</fieldset>
		</form> 
		
		<div class="g33">
			<div class="space">
				<h3>Estrutura</h3>
				<p>Os campos dos formulários devem ser envolvidos por um elemento com a class <mark>.e_wrap</mark><br>
					Esta class existe para separar os elementos do formulários assim como assiste na estilização de erros
				</p>
				<pre class="prettyprint">&lt;form&gt;
&lt;div class=&quot;e_wrap&quot;&gt;
&lt;label for=&quot;inputId&quot;&gt;Nome&lt;/label&gt;
&lt;input type=&quot;text&quot; id=&quot;inputId&quot; /&gt;
&lt;/div&gt;
&lt;/form&gt;</pre><br><br>  
				<div class="v_space">
					<h4>Formulários em bloco</h4>
					<form class="form_block exempleForm">
						<label for="exField">Nome do Campo</label>
						<input type="text" id="exField" style="width:100%"/>
					</form>
					<p>Neste exemplo a <mark>&lt;label&gt;</mark> do respectivo campo do formulário encontra-se acima deste, em bloco</p>
					<p>Para este efeito o <mark>&lt;form&gt;</mark> deverá ter a class <mark>.form_block</mark></p>
				</div>
				<br><br>
				<div class="v_space">
					<h4>Erros e Avisos</h4>
					<form class="form_block exempleForm"> 
						<div class="e_wrap error">
							<label for="inpuTypeTex"> Input com Erro</label>
							<input id="inpuTypeTex" type="text"  style="width:100%">
						</div>
					</form>
					<p>Para assinalar um campo com erro ou aviso basta adicionar ao element <mark>.e_wrap</mark> a class <mark>.error</mark> ou <mark>.warning</mark>.</p>
					<p>A messagem de erro pode aparecer inline usando o elemento <mark>&lt;p&gt;</mark> com a class <mark>.help</mark> podendo esta classe tambem ser usada para notas várias</p>
				</div>
			</div>
		</div>
	</div>
	
	<!-- .................................  ERROS	.................................  -->
	
	<div class="section">
		<div class="g50">
			<div class="space">
				<h4>Lista de Checkboxes</h4>
				<p>Listas de checkbox são especialmente úteis para formulários que exigem a aceitação de vários dados.</p>
				<pre class="prettyprint">&lt;form  class=&quot;form_block&quot;&gt;
&lt;ul class=&quot;formList&quot;&gt;
&lt;li&gt;
  &lt;label for=&quot;checkboxId1&quot;&gt;
    &lt;input id=&quot;checkboxId1&quot; type=&quot;checkbox&quot;&gt;
    &lt;span&gt;Checkbox Label&lt;/span&gt;
  &lt;/label&gt;
&lt;/li&gt;
&lt;/ul&gt;
&lt;/form&gt;</pre><br>
			</div>
		</div>
		<div class="g50">
			<div class="space">
				<h4>Inline Inputs</h4>
				<p>Listas de checkbox são especialmente úteis para formulários que exigem a aceitação de vários dados.</p>
				<form class="exempleForm">
					<label>Inline Inputs</label>
					<input type="text" class="miniInput" placeholder="Ano"> de <input type="text" class="miniInput" placeholder="Mês">
				</form>
				<pre class="prettyprint">&lt;div class=&quot;e_wrap&quot;&gt;
  &lt;label&gt;Inline Inputs&lt;/label&gt;
  &lt;input type=&quot;text&quot; class=&quot;miniInput&quot;&gt;
  de
  &lt;input type=&quot;text&quot; class=&quot;miniInput&quot;&gt;
&lt;/div&gt;</pre>
			</div>
		</div>
	</div>
	
	<!-- .................................  BOTÕES	.................................  -->
	<div class="section" id="buttonsExemple">

		<div class="g66">
			<form class="space"> 
				<div class="e_wrap">
					<button type="button" class="defaultBtn">Default</button>
					<input id="inpuTypeTex" type="submit" value="Gravar" class="btn_success">
					<button type="button" class="btn_info">Info</button>
					<button type="button" class="btn_delete">Apagar</button>
				</div> 
				<div class="e_wrap">
					<button type="button" class="defaultBtn MediumBtn">Default</button>
					<input id="inpuTypeTex" type="submit" value="Gravar" class="btn_success MediumBtn">
					<button type="button" class="btn_info MediumBtn">Info</button>
					<button type="button" class="btn_delete MediumBtn">Apagar</button>
				</div>
				
				<div class="e_wrap">
					<button type="button" class="defaultBtn bigBtn">Default</button>
					<input id="inpuTypeTex" type="submit" value="Gravar" class="btn_success bigBtn">
					<button type="button" class="btn_info bigBtn">Info</button>
					<button type="button" class="btn_delete bigBtn">Apagar</button>
				</div>
			</form>
		</div>
		<div class="g33">
			<div class="space">
				<h4>Botões</h4> 
				<p>Estão contemplados no estilo base dos formulários quatro tipo de botões:</p>
				<p><mark>.defaultBtn</mark> - Para vários tipos acções</p> 
				<p><mark>.btn_success</mark> - Associado a acções submissão</p>
				<p><mark>.btn_info</mark> - Para obter mais informações</p>
				<p><mark>.btn_delete</mark> - Associado a acções de eliminação</p>    
				<hr />
				<p class="note"><strong>Nota:</strong> Qualquer tipo de elemento <mark>&lt;button&gt;</mark> ou <mark>&lt;input type=&quot;button&quot;&gt;</mark> sem class definida herdará o estilo default </p>
			</div>
		</div>	
	</div>
	
</div>

<?php include 'shared/footer.php'; ?>	