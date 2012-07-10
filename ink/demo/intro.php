<?php include 'shared/header.php'; ?>
<!-- |||||||||||||||||||||||||||||||||  Navitation  |||||||||||||||||||||||||||||||||  -->

<!-- |||||||||||||||||||||||||||||||||  Content  |||||||||||||||||||||||||||||||||  -->		
    


<div class="callToAction">
	<img src="styles/imgs/logo_home.png" width="830" height="400" alt="Logo Home" class="logo_hp"/>
	<div  class="glow1"><img src="styles/imgs/glow1.png" width="830" height="400" alt="Glow1"/> </div>
	 <div  class="glow2"><img src="styles/imgs/glow2.png" width="830" height="400" alt="Glow1"/> </div> 
</div> 
<div class="blackMenu">
	<h1><a href="intro.php" title="Site Title">InK <small>Interface kit</small></a></h1>
	<a href="#" onclick="toogleNav()" id="toggleNavigation">Menu</a>
	<nav >
	<ul class="h_navigation">
		<li><a href="intro.php">Intro</a></li>
		<li><a href="grid.php">Grelha</a></li>
		<li><a href="typo.php">Tipografia</a></li>
		<li><a href="forms.php">Formulários</a></li>
		<li><a href="tables.php">Tabelas</a></li>
		<li class="active"><a href="alerts.php">Alerts</a></li>
		<li><a href="navigation.php">Navegação</a></li>
		<li><a href="widgets.php">Widgets</a></li>
	</ul>
	</nav>
</div>    

<div class="container_width">
	<div class="section">
		<div class="g100" id="whatIs">
			<div class="space">
				<p>O <abbr title="Interface Toolkit">InK</abbr> é uma framework de HTML e CSS feito para acelerar o processo de prototipagem, desenvolvimento e concepção de interfaces em ambientes web.</p>
			</div>
		</div>
		<div class="g66">
			<div class="space">
				<h3>Contém:</h3>
				<ul>
					<li>Grelhas</li>
					<li>Tipografia</li>
					<li>Formulários</li>   
					<li>Alertas</li>  
					<li>Navegação</li>
					<li>Componentes da Lib SAPO.JS</li>
				</ul>
			</div>
		</div>
		<div class="g33" >
			<div class="space" id="download"> 
   				<p>Faça Download, Fork, Pull, Reporte problemas ou comente no repositório do InK no Github</p>
				<a href="#" >Repo Ink no Github</a>
			</div>
		</div>		
	</div>
</div>

		
<?php include 'shared/footer.php'; ?>	
        
<style type="text/css" media="screen">
.callToAction{
	position: relative;
	
	color: white;
	
	width: 100%;
	height: 100%;
		
	background-image: -webkit-gradient(radial, 50% 75%, 0, 50% 75%, 700, 
				from(#66cde1), to(rgba(55,89,138,0))),
			-webkit-gradient(linear,center bottom,center top,
				color-stop(0, #385A8C),color-stop(0.6, #000000));
	background-image: -moz-radial-gradient(50% 75%, circle, 
			rgba(55,89,138,0.5) 0px, rgba(55,89,138,0) 400px),
			-moz-linear-gradient(center bottom,#385A8C 0%,#000000 60%); 
	overflow: hidden; 
}  

.logo_hp {margin:auto; display: block; position:relative; z-index: 9; opacity: 0.8;
-moz-opacity: 0.8;
filter:alpha(opacity=80);

-webkit-transition: all 500ms ; 
   -moz-transition: all 500ms ; 
    -ms-transition: all 500ms ; 
     -o-transition: all 500ms ; 
        transition: all 500ms  /* custom */    
}    
.glow1, .glow2 {position:absolute; top:0px; width: 100%; height: 400px; text-align: center;}  

 .glow1 {
-webkit-animation: glower 3s infinite;
 -moz-animation: glower 3s infinite;
 -ms-animation: glower 3s infinite;  
z-index: 8;
} 

 .glow2 {
-webkit-animation: glower 2s infinite;
 -moz-animation: glower 2s infinite;
 -ms-animation: glower 2s infinite;  
z-index: 7;
}

.logo_hp:hover {
	opacity: 1;
	-moz-opacity: 1;
	filter:alpha(opacity=1);  
	
	
}

@-webkit-keyframes glower {
        0%   { opacity: 1;}
        50% { opacity: 0;}
		100%   { opacity: 1;}
}
@-moz-keyframes glower {
    0%   { opacity: 0; }
    50% { opacity: 1; }
	100%   { opacity: 0; }
}
@-ms-keyframes glower {
    0%   { opacity: 0; }
    50% { opacity: 1; }
	100%   { opacity: 0; }
}

</style>