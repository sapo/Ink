<div class="whatIs">
   <div class="ink-container" id="ui_home">
		<h2>Ink-js</h2>
		<p>Beautiful js components to go with your project.</p>
	</div>
</div>

<div class="menu-second-levelx">
	<div class="ink-container">
		<nav class="ink-navigation" id="dockedMenu">
			<ul class="menu horizontal blue ink-l100 ink-m100 ink-s100">
				<li class="active"><a class="scrollableLink home" href="#ui_home">Home</a></li>
				<?php foreach( $components as $component => $configuration ){ ?>
					<li><a class="scrollableLink" href="#<?php echo $component;?>"><?php echo $configuration['label'];?></a></li>
				<?php } ?>
			</ul>
		</nav>
	</div>
</div>

<div class="ink-container">
	<?php
		foreach( $components as $component => $configuration ){
			echo $configuration['view'];
		}
	?>
	<!-- <div class="ink-section">
		<div class="ink-vspace">
			<div class="ink-l30">
				<div class="ink-space">	
					<h3 id="modalbox">Modal box</h3>
					<p>
						Chuck ipsum. A blind man once stepped on Chuck Norris' shoe. Chuck replied, "Don't you know who I am? I'm Chuck Norris!" 
						The mere mention of his name cured this man blindness. Sadly the first, last, and only thing this man ever saw, was a fatal roundhouse delivered by Chuck Norris.
					</p>
				</div>
			</div>
			<div class="ink-l70">
				<div class="ink-space">
					<div class="ink-modal">
						<a href="#" class="close">x</a>
					</div>
				</div>
			</div>
		</div>
	</div> -->

	<!-- <div class="ink-section">
		<div class="ink-vspace">
			<h3 id="gallery">Gallery</h3>
			<p>
				Chuck ipsum. A blind man once stepped on Chuck Norris' shoe. Chuck replied, "Don't you know who I am? I'm Chuck Norris!" 
				The mere mention of his name cured this man blindness. Sadly the first, last, and only thing this man ever saw, was a fatal roundhouse delivered by Chuck Norris.
			</p>
		</div>
		
		<div class="ink-vspace">
			<div class="ink-l30">
				<div class="ink-space">
					<h4>Standart gallery</h4>
					<p>
						Chuck ipsum. A blind man once stepped on Chuck Norris' shoe. Chuck replied, "Don't you know who I am? I'm Chuck Norris!" 
						The mere mention of his name cured this man blindness. Sadly the first, last, and only thing this man ever saw, was a fatal roundhouse delivered by Chuck Norris.
					</p>
				</div>
			</div>
			
			<div class="ink-l70">	
				<div class="ink-space">
					<div class="ink-gallery">
						<div class="stage">
							<nav>
								<ul class="unstyled">
									<li><a href="#" class="next"></a></li>
									<li><a href="#" class="previous"></a></li>
								</ul>
							</nav>
							<div class="slider">
								<ul style="margin-left:-1200px">
									<li><img src="../../../tree/assets/imgs/ink-js-placeholders/1.1.png"></li>
									<li><img src="../../../tree/assets/imgs/ink-js-placeholders/1.2.png" alt="1"></li>
									<li><img src="../../../tree/assets/imgs/ink-js-placeholders/1.3.png" alt="1"></li>
								</ul>
							</div>
							<div class="pagination">
								<a href="#" class="active"></a>
								<a href="#"></a>
								<a href="#"></a>
								<a href="#"></a>
								<a href="#"></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="ink-vspace">
			<div class="ink-l70">
				<div class="ink-space">
					<div class="ink-gallery">
						<div class="stage">
							<nav>
								<ul class="unstyled">
									<li><a href="#" class="next"></a></li>
									<li><a href="#" class="previous"></a></li>
								</ul>
							</nav>
							<div class="slider">
								<ul>
									<li><img src="../../../tree/assets/imgs/ink-js-placeholders/1.1.png" alt="1"></li>
									<li><img src="../../../tree/assets/imgs/ink-js-placeholders/1.2.png" alt="1"></li>
									<li><img src="../../../tree/assets/imgs/ink-js-placeholders/1.3.png" alt="1"></li>
								</ul>
							</div>
							<div class="article_text example1">
								<h1>Etiam eleifend dui vel felis viverra</h1>
								<p>Aliquam tincidunt venenatis sem, vel interdum augue venenatis a. Donec tristique pretium enim nec tempor. Nulla facilisi. Integer elementum placerat diam, viverra molestie elit vestibulum luctus.</p>
							</div>
						</div>
						<div class="thumbs">
							<ul class="unstyled">
								<li>
									<a href="#">
										<img src="../../../tree/assets/imgs/ink-js-placeholders/thumb1.png" alt="1">
										<span>Etiam eleifend dui vel felis viverra congue.</span>
									</a>
								</li>
								<li>
									<a href="#">
										<img src="../../../tree/assets/imgs/ink-js-placeholders/thumb2.png" alt="2">
										<span>Mauris at eros eu eros lacinia bibendum.</span>
									</a>
								</li>
								<li>
									<a href="#">
										<img src="../../../tree/assets/imgs/ink-js-placeholders/thumb3.png" alt="3">
										<span>Sed luctus justo vel eros mattis euismod.</span>
									</a>
								</li>
								<li>
									<a href="#">
										<img src="../../../tree/assets/imgs/ink-js-placeholders/thumb4.png" alt="4">
										<span>Aenean vitae elit at quam dignissim auctor.</span>
									</a>
								</li>
								<li>
									<a href="#">
										<img src="../../../tree/assets/imgs/ink-js-placeholders/thumb5.png" alt="5">
										<span>Sed tincidunt est quis sem facilisis tempus.</span>
									</a>
								</li>
								<li>
									<a href="#">
										<img src="../../../tree/assets/imgs/ink-js-placeholders/thumb6.png" alt="6">
										<span>Praesent at leo urna, vel aliquam sapien.</span>
									</a>
								</li>
								<li>
									<a href="#">
										<img src="../../../tree/assets/imgs/ink-js-placeholders/thumb7.png" alt="7">
										<span>Morbi porttitor nisl a eros congue molestie eget non mi.</span>
									</a>
								</li>
								<li>
									<a href="#">
										<img src="../../../tree/assets/imgs/ink-js-placeholders/thumb8.png" alt="7">
										<span>Morbi porttitor nisl a eros congue molestie eget non mi.</span>
									</a>
								</li>
							</ul>
							<div class="pagination">
								<a href="#" class="previous"></a>
								<a href="#" class="active"></a>
								<a href="#"></a>
								<a href="#"></a>
								<a href="#"></a>
								<a href="#"></a>
								<a href="#" class="next"></a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="ink-l30">
				<div class="ink-space">
					<h4>Gallery with bottom navigation</h4>
					<p>
						Chuck ipsum. A blind man once stepped on Chuck Norris' shoe. Chuck replied, "Don't you know who I am? I'm Chuck Norris!" 
						The mere mention of his name cured this man blindness. Sadly the first, last, and only thing this man ever saw, was a fatal roundhouse delivered by Chuck Norris.
					</p>
				</div>
			</div>
		</div>
		
		<div class="ink-vspace">
			<div class="ink-l30">
				<div class="ink-space">
					<h4>Gallery with bottom navigation</h4>
					<p>
						Chuck ipsum. A blind man once stepped on Chuck Norris' shoe. Chuck replied, "Don't you know who I am? I'm Chuck Norris!" 
						The mere mention of his name cured this man blindness. Sadly the first, last, and only thing this man ever saw, was a fatal roundhouse delivered by Chuck Norris.
					</p>
				</div>
			</div>
			<div class="ink-l70">
				<div class="ink-space">
					<div class="ink-gallery">
						<div class="stage">
							<nav>
								<ul class="unstyled">
									<li><a href="#" class="next"></a></li>
									<li><a href="#" class="previous"></a></li>
								</ul>
							</nav>
							<div class="slider">
								<ul class="unstyled">
									<li><img src="../../../tree/assets/imgs/ink-js-placeholders/1.1.png" alt="1"></li>
									<li><img src="../../../tree/assets/imgs/ink-js-placeholders/1.2.png" alt="1"></li>
									<li><img src="../../../tree/assets/imgs/ink-js-placeholders/1.3.png" alt="1"></li>
								</ul>
							</div>
							<div class="article_text example2">
								<p>Lorem ipsum - dolor sit amet, consectetur adipiscing elit.</p>
							</div>
						</div>
						<div class="thumbs">
							<ul class="unstyled">
								<li>
									<a href="#">
										<img src="../../../tree/assets/imgs/ink-js-placeholders/thumb1.png" alt="1">
										<span>Etiam eleifend dui vel felis viverra congue.</span>
									</a>
								</li>
								<li>
									<a href="#">
										<img src="../../../tree/assets/imgs/ink-js-placeholders/thumb2.png" alt="2">
										<span>Mauris at eros eu eros lacinia bibendum.</span>
									</a>
								</li>
								<li>
									<a href="#">
										<img src="../../../tree/assets/imgs/ink-js-placeholders/thumb3.png" alt="3">
										<span>Sed luctus justo vel eros mattis euismod.</span>
									</a>
								</li>
								<li>
									<a href="#">
										<img src="../../../tree/assets/imgs/ink-js-placeholders/thumb4.png" alt="4">
										<span>Aenean vitae elit at quam dignissim auctor.</span>
									</a>
								</li>
								<li>
									<a href="#">
										<img src="../../../tree/assets/imgs/ink-js-placeholders/thumb5.png" alt="5">
										<span>Sed tincidunt est quis sem facilisis tempus.</span>
									</a>
								</li>
								<li>
									<a href="#">
										<img src="../../../tree/assets/imgs/ink-js-placeholders/thumb6.png" alt="6">
										<span>Praesent at leo urna, vel aliquam sapien.</span>
									</a>
								</li>
								<li>
									<a href="#">
										<img src="../../../tree/assets/imgs/ink-js-placeholders/thumb7.png" alt="7">
										<span>Morbi porttitor nisl a eros congue molestie eget non mi.</span>
									</a>
								</li>
								<li>
									<a href="#">
										<img src="../../../tree/assets/imgs/ink-js-placeholders/thumb8.png" alt="7">
										<span>Morbi porttitor nisl a eros congue molestie eget non mi.</span>
									</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="ink-vspace">
			<div class="ink-l70">
				<div class="ink-space">
					<div class="ink-gallery rightNav">
						<div class="stage">
							<nav>
								<ul class="unstyled">
									<li><a href="#" class="next"></a></li>
									<li><a href="#" class="previous"></a></li>
								</ul>
							</nav>
							<div class="slider">
								<ul class="unstyled" style="margin-left:-400px">
									<li><img src="../../../tree/assets/imgs/ink-js-placeholders/2.1.png" alt="1"></li>
									<li><img src="../../../tree/assets/imgs/ink-js-placeholders/2.2.png" alt="1"></li>
									<li><img src="../../../tree/assets/imgs/ink-js-placeholders/2.3.png" alt="1"></li>
								</ul>
							</div>
							<div class="article_text example2">
								<p>Lorem ipsum - dolor sit amet, consectetur adipiscing elit.</p>
							</div>
						</div>
						<ul class="thumbs unstyled">
							<li>
								<a href="#">
									<img src="../../../tree/assets/imgs/ink-js-placeholders/thumb1.png" alt="1">
									<span>Etiam eleifend dui vel felis viverra congue.</span>
								</a>
							</li>
							<li>
								<a href="#">
									<img src="../../../tree/assets/imgs/ink-js-placeholders/thumb2.png" alt="2">
									<span>Mauris at eros eu eros lacinia bibendum.</span>
								</a>
							</li>
							<li>
								<a href="#">
									<img src="../../../tree/assets/imgs/ink-js-placeholders/thumb3.png" alt="3">
									<span>Sed luctus justo vel eros mattis euismod.</span>
								</a>
							</li>
							<li>
								<a href="#">
									<img src="../../../tree/assets/imgs/ink-js-placeholders/thumb4.png" alt="4">
									<span>Aenean vitae elit at quam dignissim auctor.</span>
								</a>
							</li>
							<li>
								<a href="#">
									<img src="../../../tree/assets/imgs/ink-js-placeholders/thumb5.png" alt="5">
									<span>Sed tincidunt est quis sem facilisis tempus.</span>
								</a>
							</li>
							<li>
								<a href="#">
									<img src="../../../tree/assets/imgs/ink-js-placeholders/thumb6.png" alt="6">
									<span>Praesent at leo urna, vel aliquam sapien.</span>
								</a>
							</li>
							<li>
								<a href="#">
									<img src="../../../tree/assets/imgs/ink-js-placeholders/thumb7.png" alt="7">
									<span>Morbi porttitor nisl a eros congue molestie eget non mi.</span>
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="ink-l30">
				<div class="ink-space">
					<h4>Gallery with right navigation</h4>
					<p>
						Chuck ipsum. A blind man once stepped on Chuck Norris' shoe. Chuck replied, "Don't you know who I am? I'm Chuck Norris!" 
						The mere mention of his name cured this man blindness. Sadly the first, last, and only thing this man ever saw, was a fatal roundhouse delivered by Chuck Norris.
					</p>
				</div>
			</div>
		</div>
	</div>
<!--	
	<div class="ink-section">
		<div class="ink-vspace">
			<div class="ink-l70">
				<div class="ink-space">
					<div class="ink-tabs">
						<nav class="ink-navigation">
							<ul class="ink-tabs-nav menu horizontal">
								<li class="active"><a href="#home">Home</a></li>
								<li><a href="#news">News</a></li>
								<li><a href="#description">Description</a></li>
								<li><a href="#stuff">Stuff</a></li>
								<li><a href="#more_stuff">More stuff</a></li>
							</ul>
						</nav>
						<div id="home" class="ink-tabs-container">
							<p>
								Arnold ipsum. Well then God shouldn't have cloned my dog. I'm a cybernetic organizm. Living tissue over endoskeleton. 
								You're not going to have your mommies right behind you to wipe your little tushies. Blondes. Consider it a divorce. 
								Talk to the hand. Take it BACK. I'll be back. Knock knock. I did nothing. The pavement with his enemy. You LIE! 
								I'm not shitting on you. Get down or I'll put you down. You are not you you're me. Scumbag. Bring your toy back to the carpet. 
								Bring it back to the carpet. Dylan. You son of a bitch. Get down. Of course. I'm a Terminator. 
								Talk to the hand. I need your clothes, your boots, and your motorcycle. Into the tunnel. My name is John Kimble and I love my car. I'll be back.
							</p>
						</div>
						<div id="news" class="ink-tabs-container" style="display:none">
							<p>
								Arnold ipsum. Well then God shouldn't have cloned my dog. I'm a cybernetic organizm. Living tissue over endoskeleton. 
								You're not going to have your mommies right behind you to wipe your little tushies. Blondes. Consider it a divorce. 
								Talk to the hand. Take it BACK. I'll be back. Knock knock. I did nothing. The pavement with his enemy. You LIE! 
								I'm not shitting on you. Get down or I'll put you down. You are not you you're me. Scumbag. Bring your toy back to the carpet. 
								Bring it back to the carpet. Dylan. You son of a bitch. Get down. Of course. I'm a Terminator. 
								Talk to the hand. I need your clothes, your boots, and your motorcycle. Into the tunnel. My name is John Kimble and I love my car. I'll be back.
							</p>
						</div>
						<div id="descripton" class="ink-tabs-container" style="display:none">
							<p>
								Arnold ipsum. Well then God shouldn't have cloned my dog. I'm a cybernetic organizm. Living tissue over endoskeleton. 
								You're not going to have your mommies right behind you to wipe your little tushies. Blondes. Consider it a divorce. 
								Talk to the hand. Take it BACK. I'll be back. Knock knock. I did nothing. The pavement with his enemy. You LIE! 
								I'm not shitting on you. Get down or I'll put you down. You are not you you're me. Scumbag. Bring your toy back to the carpet. 
								Bring it back to the carpet. Dylan. You son of a bitch. Get down. Of course. I'm a Terminator. 
								Talk to the hand. I need your clothes, your boots, and your motorcycle. Into the tunnel. My name is John Kimble and I love my car. I'll be back.
							</p>
						</div>
						<div id="stuff" class="ink-tabs-container" style="display:none">
							<p>
								Arnold ipsum. Well then God shouldn't have cloned my dog. I'm a cybernetic organizm. Living tissue over endoskeleton. 
								You're not going to have your mommies right behind you to wipe your little tushies. Blondes. Consider it a divorce. 
								Talk to the hand. Take it BACK. I'll be back. Knock knock. I did nothing. The pavement with his enemy. You LIE! 
								I'm not shitting on you. Get down or I'll put you down. You are not you you're me. Scumbag. Bring your toy back to the carpet. 
								Bring it back to the carpet. Dylan. You son of a bitch. Get down. Of course. I'm a Terminator. 
								Talk to the hand. I need your clothes, your boots, and your motorcycle. Into the tunnel. My name is John Kimble and I love my car. I'll be back.
							</p>
						</div>
						<div id="more_stuff" class="ink-tabs-container" style="display:none">
							<p>
								Arnold ipsum. Well then God shouldn't have cloned my dog. I'm a cybernetic organizm. Living tissue over endoskeleton. 
								You're not going to have your mommies right behind you to wipe your little tushies. Blondes. Consider it a divorce. 
								Talk to the hand. Take it BACK. I'll be back. Knock knock. I did nothing. The pavement with his enemy. You LIE! 
								I'm not shitting on you. Get down or I'll put you down. You are not you you're me. Scumbag. Bring your toy back to the carpet. 
								Bring it back to the carpet. Dylan. You son of a bitch. Get down. Of course. I'm a Terminator. 
								Talk to the hand. I need your clothes, your boots, and your motorcycle. Into the tunnel. My name is John Kimble and I love my car. I'll be back.
							</p>
						</div>
					</div>
				</div>
			</div>
			<div class="ink-l30">
				<div class="ink-space">	
					<h3>Tabs</h3>
					<p>
						Chuck ipsum. A blind man once stepped on Chuck Norris' shoe. Chuck replied, "Don't you know who I am? I'm Chuck Norris!" 
						The mere mention of his name cured this man blindness. Sadly the first, last, and only thing this man ever saw, was a fatal roundhouse delivered by Chuck Norris.
					</p>
				</div>
			</div>
		</div>
	</div>
	<div class="ink-section">
		<div class="ink-vspace">
			<div class="ink-l30">
				<div class="ink-space">	
					<h3>Sortable list</h3>
					<p>
						Chuck ipsum. A blind man once stepped on Chuck Norris' shoe. Chuck replied, "Don't you know who I am? I'm Chuck Norris!" 
						The mere mention of his name cured this man blindness. Sadly the first, last, and only thing this man ever saw, was a fatal roundhouse delivered by Chuck Norris.
					</p>
				</div>
			</div>
			<div class="ink-l70">
				<div class="ink-space">
					<ul class="unstyled ink-sortable-list">
						<li class="drag"><span class="ink-label ink-info"><i class="icon-reorder"></i>drag here</span><strong>1.</strong> Integer id lacus nec tellus mattis pretium ut nec nisi</li>
						<li><span class="ink-label ink-info"><i class="icon-reorder"></i>drag here</span><strong>2.</strong> Nam at lectus justo, sed dictum tortor</li>
						<li><span class="ink-label ink-info"><i class="icon-reorder"></i>drag here</span><strong>3.</strong> Duis sed sem at justo sagittis tincidunt</li>
						<li><span class="ink-label ink-info"><i class="icon-reorder"></i>drag here</span><strong>4.</strong> Duis quis orci lectus, eu porttitor enim</li>
						<li><span class="ink-label ink-info"><i class="icon-reorder"></i>drag here</span><strong>5.</strong> Cras et sem in neque lobortis venenatis</li>
						<li><span class="ink-label ink-info"><i class="icon-reorder"></i>drag here</span><strong>6.</strong> Morbi adipiscing sem sed odio vulputate commodo</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="ink-section">
		<div class="ink-l60">
			<div class="ink-space">
				<ul class="ink-tree-view">
					<li>
						<button class="icon-caret-down"></button>
						<a href="#">Root 1</a>
						<ul>
							<li>
								<button class="icon-caret-right"></button>
								<a href="#">Child 1.1</a>
							</li>
							<li>
								<button class="icon-caret-down"></button>
								<a href="#">Child 1.2</a>
								<ul>
									<li><a href="#">Child Child 1.2.1</a></li>
									<li><a href="#">Child Child 1.2.2</a></li>
								</ul>
							</li>
							<li>
								<button class="icon-caret-right"></button>
								<a href="#">Child 1.3</a>
							</li>
							<li>
								<button class="icon-caret-right"></button>
								<a href="#">Child 1.4</a>
							</li>
						</ul>
					</li>
					<li>
						<button class="icon-caret-right"></button>
						<a href="#">Root 2</a>
					</li>
					<li>
						<button class="icon-caret-right"></button>
						<a href="#">Root 3</a>
					</li>
					<li>
						<button class="icon-caret-right"></button>
						<a href="#">Root 4</a>
					</li>
				</ul>
			</div>
		</div>
		<div class="ink-l40">
			<div class="ink-space">	
				<h3>Tree view</h3>
				<p>
					Chuck ipsum. A blind man once stepped on Chuck Norris' shoe. Chuck replied, "Don't you know who I am? I'm Chuck Norris!" 
					The mere mention of his name cured this man blindness. Sadly the first, last, and only thing this man ever saw, was a fatal roundhouse delivered by Chuck Norris.
				</p>
			</div>
		</div>
	</div>
	<div class="ink-section">
		<div class="ink-l40">
			<div class="ink-space">	
				<h3>Date picker</h3>
				<p>
					Chuck ipsum. A blind man once stepped on Chuck Norris' shoe. Chuck replied, "Don't you know who I am? I'm Chuck Norris!" 
					The mere mention of his name cured this man blindness. Sadly the first, last, and only thing this man ever saw, was a fatal roundhouse delivered by Chuck Norris.
				</p>
			</div>
		</div>
		<form class="ink-l60">
			<fieldset class="box ink-space">
				<div class="ink-form-wrapper">
					<p>Neste caso o componente está a ser utilizado com onFocus (comportamento default) e com o formato mm/dd/yyyy</p>
					<input id="data" type="text" value="">
				</div>
				
				<div class="ink-form-wrapper">
					<p>Neste caso o componente irá iniciar a data em 1980-11-22</p>
					<input id="data_start" type="text" value="">
				</div>
			
				<div class="ink-form-wrapper">
					<p>Neste caso o componente preenche as select inputs ao seu lado</p>
					<select id="dia2" title="Dia" name="dia2">
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
					<select id="mes2" title="Mês" name="mes2">
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
					<select id="ano2" title="Ano" name="ano2">
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
				<div class="ink-form-wrapper">
					<input id="data3" type="text" value="">
					<a id="picker3" href="#">abrir</a>
				</div>	
			</fieldset>
		</form>
	</div>
	<div class="ink-section">
		<div class="ink-l70">
			<div class="ink-space">	
				<div class="ink-progress-bar progress-info">
					<div class="bar grey" style="width: 80%">I am a grey progress bar</div>
				</div>
				<div class="ink-progress-bar progress-info">
					<div class="bar green" style="width: 60%">I am a green progress bar</div>
				</div>
				<div class="ink-progress-bar progress-info">
					<div class="bar blue" style="width: 50%">I am a blue progress bar</div>
				</div>
				<div class="ink-progress-bar progress-info">
					<div class="bar red" style="width: 40%">I am a red progress bar</div>
				</div>
				<div class="ink-progress-bar progress-info">
					<div class="bar orange" style="width: 30%">I am an orange progress bar</div>
				</div>
				<div class="ink-progress-bar progress-info">
					<div class="bar black" style="width: 20%">I am a black progress bar</div>
				</div>
			</div>
		</div>
		<div class="ink-l30">
			<div class="ink-space">	
				<h3>Progress bars</h3>
				<p>
					Chuck ipsum. A blind man once stepped on Chuck Norris' shoe. Chuck replied, "Don't you know who I am? I'm Chuck Norris!" 
					The mere mention of his name cured this man blindness. Sadly the first, last, and only thing this man ever saw, was a fatal roundhouse delivered by Chuck Norris.
				</p>
			</div>
		</div>
		
	</div> -->

</div>
<style>
/* DOCKED TENTATIVE PROPOSAL */
	#dockedMenu {
		/*position:           absolute;*/
	}

	.ink-docked {
		position:           fixed !important;
		opacity:            0.75;
		z-index:            1000;
	}

	.ink-docked:hover {
		opacity:            1;
            }
</style>
<script>
	new SAPO.Ink.Docked('#dockedMenu', {
		fixedHeight: 50
	});
	// horizontal menu
	new SAPO.Ink.HorizontalMenu('#topbar > nav');
	new SAPO.Ink.HorizontalMenu('#dockedMenu');
	
	var toggleTriggers = SAPO.Dom.Selector.select('.toggleTrigger');
	for(i=0;i<toggleTriggers.length;i+=1){
		SAPO.Dom.Event.observe(toggleTriggers[i],'click',function(event){
			var targetElm = s$(this.getAttribute('data-target'));
			this.innerHTML = ( ( targetElm.style.display === 'none' ) ? 'Hide' : 'View' ) + ' Source Code';
			SAPO.Dom.Css.toggle(targetElm);
			SAPO.Dom.Event.stop(event);
		});
	}
</script>