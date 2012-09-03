<?php include 'shared/header.php'; ?>
<!-- |||||||||||||||||||||||||||||||||  Navitation  |||||||||||||||||||||||||||||||||  -->
<nav id="topbar">
	<div class="container_width">
		<h1><a href="intro.php" title="Site Title">InK<small>Interface kit</small></a></h1>
		<ul>
			<li><a href="grid.php">Layout</a></li>
			<li><a href="navigation.php">Navigation</a></li>
			<li><a href="typo.php">Typography & Icons</a></li>
			<li><a href="forms.php">Forms & Alerts</a></li>
			<li><a href="tables.php">Tables</a></li>
			<li class="active"><a href="widgets.php">InkJS</a></li>
		</ul>
	</div>
</nav>  
<!-- |||||||||||||||||||||||||||||||||  Content  |||||||||||||||||||||||||||||||||  -->		
<div class="container_width whatIs">
	<div class="space">
		<h2>InK JS</h2>
		<p>A JS component lib to go along with your awsome site</p>
	</div>
</div>

<nav class="menu">
	<div class="container_width">
		<ul>
			<li class="active"><a href="#">UI components</a></li>
			<li><a href="#">Visual effects</a></li>
			<li><a href="#">Core</a></li>
		</ul>
	</div>
</nav>  

<div class="container_width">
	<div class="section">
		<div class="g33">
			<div class="space">
				<h3>DatePicker</h3>
				<p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
			</div>
		</div>
		<form class="g66">
			<fieldset class="box space">
				<div class="e_wrap">
					<p>Neste caso o componente está a ser utilizado com onFocus (comportamento default) e com o formato mm/dd/yyyy</p>
					<input id="data" type="text" value="">
				</div>
				
				<div class="e_wrap">
					<p>Neste caso o componente irá iniciar a data em 1980-11-22</p>
					<input id="data_start" type="text" value="">
				</div>
			
				<div class="e_wrap">
					<p>Neste caso o componente preenche as select inputs ao seu lado</p>
					<select id="dia2" title="Dia" name="dia2" class="miniInput">
						<option></option>
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="6">6</option>
						<option value="7">7</option>
						<option value="8">8</option>
						<option value="9">9</option>
						<option value="10">10</option>
						<option value="11">11</option>
						<option value="12">12</option>
						<option value="13">13</option>
						<option value="14">14</option>
						<option value="15">15</option>
						<option value="16">16</option>
						<option value="17">17</option>
						<option value="18">18</option>
						<option value="19">19</option>
						<option value="20">20</option>
						<option value="21">21</option>
						<option value="22">22</option>
						<option value="23">23</option>
						<option value="24">24</option>
						<option value="25">25</option>
						<option value="26">26</option>
						<option value="27">27</option>
						<option value="28">28</option>
						<option value="29">29</option>
						<option value="30">30</option>
						<option value="31">31</option>
					</select>
					<select id="mes2" title="Mês" name="mes2" class="miniInput">
						<option></option>
						<option value="1">Jan</option>
						<option value="2">Fev</option>
						<option value="3">Mar</option>
						<option value="4">Abr</option>
						<option value="5">Mai</option>
						<option value="6">Jun</option>
						<option value="7">Jul</option>
						<option value="8">Ago</option>
						<option value="9">Set</option>
						<option value="10">Out</option>
						<option value="11">Nov</option>
						<option value="12">Dez</option>
					</select>
					<select id="ano2" title="Ano" name="ano2" class="miniInput">
						<option></option>
						<option value="2000">2000</option>
						<option value="2001">2001</option>
						<option value="2002">2002</option>
						<option value="2003">2003</option>
						<option value="2004">2004</option>
						<option value="2005">2005</option>
						<option value="2006">2006</option>
						<option value="2007">2007</option>
						<option value="2008">2008</option>
						<option value="2009">2009</option>
						<option value="2010">2010</option>
						<option value="2011">2011</option>
						<option value="2012">2012</option>
						<option value="2013">2013</option>
						<option value="2014">2014</option>
						<option value="2015">2015</option>
						<option value="2016">2016</option>
						<option value="2017">2017</option>
						<option value="2018">2018</option>
						<option value="2019">2019</option>
						<option value="2020">2020</option>
					</select>
					<a id="picker2" href="#">abrir</a>
				</div>
				<p>Neste caso temos o componente a ser utilizado com recurso a link e com o formato default yyyy-mm-dd</p>
				<div class="e_wrap">
					<input id="data3" type="text" value="">
					<a id="picker3" href="#">abrir</a>
				</div>	
			</fieldset>
		</form>
	</div>
</div>

<script type="text/javascript">
	// for convenience O:)
	function fillSelectWithRange(selectId, minVal, maxVal, labels) {
	var sel = s$(selectId);
	var option = document.createElement('option');
	option.selected = "selected";
	sel.appendChild(option);
	var i, label, idx = 0;
	for (i = minVal; i <= maxVal; ++i) {
	label = (labels) ? labels[idx++] : i;
	option = document.createElement('option');
	option.value = i;
	option.innerHTML = label;
	sel.appendChild(option);
	}
	}
	fillSelectWithRange('dia2', 1, 31);
	fillSelectWithRange('mes2', 1, 12, ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez']);
	fillSelectWithRange('ano2', 2000, 2020);
	new SAPO.Component.DatePicker({
	elementId: 'data',
	format: 'mm/dd/yyyy',
	cssURI: '/Assets/Images/DatePicker/style.css'
	});
	new SAPO.Component.DatePicker({
	elementId: 'data_start',
	cssURI: '/Assets/Images/DatePicker/style.css',
	startDate: '1980-11-22'
	});
	new SAPO.Component.DatePicker({
	displayInSelect: true,
	pickerId: 'picker2',
	yearId: 'ano2',
	monthId: 'mes2',
	dayId: 'dia2',
	cssURI: '/Assets/Images/DatePicker/style.css'
	});
	new SAPO.Component.DatePicker({
	elementId: 'data3',
	onFocus: false,
	pickerId: 'picker3',
	cssURI: '/Assets/Images/DatePicker/style.css'
	});
</script>
<?php include 'shared/footer.php'; ?>