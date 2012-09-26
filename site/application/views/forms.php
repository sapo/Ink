<div class="ink-container whatIs">
	<div class="ink-vspace">
		<h2>Forms</h2>
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
			<li><a href="#">Form building</a></li>
			<li><a href="#">Text & Number entry</a></li>
			<li><a href="#">Dropdowns and list boxes</a></li>
			<li><a href="#">Buttons</a></li>
			<li><a href="#">Alerts</a></li>
		</ul>
	</div>
</nav>

<div class="ink-container">
	<div class="ink-section">
		<div class=" ink-space">
			<h3>Form building</h3>
			<p>
				Os campos dos formulários devem ser envolvidos por um elemento com a class <code>.ink-form-wrapper</code>. 
				Esta class existe para separar os elementos do formulário bem como assistir na estilização de erros
			</p>
			<pre class="prettyprint"><ol><li><span class="tag">&lt;form</span><span class="tag"> <span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-form-block"</span>&gt;</span></li><li>  <span class="tag">&lt;div</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-form-wrapper"</span><span class="tag">&gt;</span></li><li><span class="tag">    &lt;label</span><span class="pln"> </span><span class="atn">for</span><span class="pun">=</span><span class="atv">"inputId"</span><span class="tag">&gt;</span><span class="pln">Name</span><span class="tag">&lt;/label&gt;</span></li><li>    <span class="tag">&lt;input</span><span class="pln"> </span><span class="atn">type</span><span class="pun">=</span><span class="atv">"text" <span class="atn">id</span><span class="pun">=</span>"inputId"</span><span class="tag">&gt;</span></li><li><span class="tag">  &lt;/div&gt;</span><span class="tag"></span></li><li><span class="tag">&lt;/form&gt;</span><span class="tag"></span></li></ol></pre>
		</div>
	</div>
	<div class="ink-section">
		<div class="ink-space">
			<h3>Text & Number entry</h3>
		</div>
		<div class="ink-l50">
			<form action="" class="ink-form-block ink-hspace">
				<fieldset>
					<legend><h4>Fieldset legend</h4></legend>
					<div class="ink-form-wrapper">
						<label for="text-input">Text input</label>
						<input id="text-input" type="text" placeholder="Please input some text">
						<p class="ink-field-tip">You can add help text to fields</p>
					</div>
					<div class="ink-form-wrapper ink-warning-field">
						<label for="text-input">Text input</label>
						<input id="text-input" type="text" placeholder="Please input some text">
						<p class="ink-form-validation ink-warning">Warn about somthing</p>
					</div>
					<div class="ink-form-wrapper ink-required-field">
						<label for="text-input">Text input</label>
						<input id="text-input" type="text" placeholder="Please input some text">
						<p class="ink-form-validation ink-caution">Something is missing. Let your user know about it</p>
					</div>
					<div class="ink-form-wrapper">
						<label for="select">Select</label>
						<select name="" id="select">
							<option value="">onions</option>
							<option value="">carrots</option>
							<option value="">potatoes</option>
							<option value="">beets</option>
						</select>
						
					</div>
					<div class="ink-form-wrapper">
						<label for="multiSelect">Multiple select</label>
						<select multiple="multiple" id="multiSelect">
							<option>onions</option>
							<option>carrots</option>
							<option>potatoes</option>
							<option>beets</option>
							<option>kale</option>
						</select>
					</div>
					<div class="ink-form-wrapper">
						<label for="textarea">Textarea</label>
						<textarea name="" id="textarea" placeholder="Please enter some text"></textarea>
					</div>
					<div class="ink-form-wrapper">
						<label for="file-input">File input</label>
						<input type="file" name="" id="file-input">
					</div>
				</fieldset>
			</form>
		</div>
		<div class="ink-l50">
			<div class="ink-hspace">
				<p>
					Neste exemplo a <code>&lt;label&gt;</code> do respectivo campo do formulário encontra-se acima deste, em bloco
					Para este efeito o <code>&lt;form&gt;</code> deverá ter a class <code>.ink-form-block</code>
				</p>
				<p>
					Caso se pretenda usar a <code>&lt;label&gt;</code> inline basta usar no <code>&lt;form&gt;</code> a class <code>.ink-form-inline</code>
				</p>
			</div>
		</div>
	</div>
	<div class="ink-section">
		<div class="ink-space">
			<h3>Checkboxes and radio buttons</h3>
		</div>
		
		<div class="ink-l50">
			<form action="" class="ink-form-block ink-hspace">
				<fieldset>
					<legend><h4>Group of checkboxes</h4></legend>
					<ul class="ink-form-wrapper unstyled">
						<p class="ink-field-tip">Please select one or more options</p>
						<li>
							<input type="checkbox" id="cb1" name="cb1" value="">
							<label for="cb1">Option 1</label>
						</li>
						<li>
							<input type="checkbox" id="cb2" name="cb2" value="">
							<label for="cb2">Option 2</label>
						</li>
						<li>
							<input type="checkbox" id="cb3" name="cb3" value="">
							<label for="cb3">Option 3</label>
						</li>
						<li>
							<input type="checkbox" id="cb4" name="cb4" value="">
							<label for="cb4">Option 4</label>
						</li>
					</ul>
				</fieldset>
				<pre class="prettyprint"><ol><li><span class="tag">&lt;form</span><span class="tag"> <span class="pln"></span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-form-block"</span>&gt;</span></li><li>  <span class="tag">&lt;ul</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-form-wrapper unstyled"</span><span class="tag">&gt;</span></li><li>    <span class="tag">&lt;li</span><span class="pln"></span><span class="tag">&gt;</span></li><li><span class="tag">      &lt;label</span><span class="pln"> </span><span class="atn">for</span><span class="pun">=</span><span class="atv">"checkboxId"</span><span class="tag">&gt;</span><span class="pln">Option 1</span><span class="tag">&lt;/label&gt;</span></li><li>    <span class="tag">  &lt;input</span><span class="pln"> </span><span class="atn">type</span><span class="pun">=</span><span class="atv">"text" <span class="atn">id</span><span class="pun">=</span>"checkboxId"</span><span class="tag">&gt;</span></li><li><span class="tag">    &lt;li</span><span class="pln"></span><span class="tag">&gt;</span></li><li class="com">    ...</li><li><span class="tag">  &lt;/ul&gt;</span><span class="tag"></span></li><li><span class="tag">&lt;/form&gt;</span><span class="tag"></span></li></ol></pre>
			</form>	
		</div>
		<div class="ink-l50">
			<form action="" class="ink-form-block ink-hspace">
				<fieldset>
					<legend><h4>Group of radio buttons</h4></legend>
					<ul class="ink-form-wrapper unstyled">
						<p class="ink-field-tip">Please select one of these options</p>
						<li>
							<input type="radio" id="rb1" name="rb" value="">
							<label for="rb1">Option 1</label>
						</li>
						<li>
							<input type="radio" id="rb2" name="rb" value="">
							<label for="rb2">Option 2</label>
						</li>
						<li>
							<input type="radio" id="rb3" name="rb" value="">
							<label for="rb3">Option 3</label>
						</li>
						<li>
							<input type="radio" id="rb4" name="rb" value="">
							<label for="rb4">Option 4</label>
						</li>
					</ul>
				</fieldset>
				<pre class="prettyprint"><ol><li><span class="tag">&lt;form</span><span class="tag"> <span class="pln"></span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-form-block"</span>&gt;</span></li><li>  <span class="tag">&lt;ul</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-form-wrapper unstyled"</span><span class="tag">&gt;</span></li><li>    <span class="tag">&lt;li</span><span class="pln"></span><span class="tag">&gt;</span></li><li><span class="tag">      &lt;label</span><span class="pln"> </span><span class="atn">for</span><span class="pun">=</span><span class="atv">"radioId"</span><span class="tag">&gt;</span><span class="pln">Option 1</span><span class="tag">&lt;/label&gt;</span></li><li>    <span class="tag">  &lt;input</span><span class="pln"> </span><span class="atn">type</span><span class="pun">=</span><span class="atv">"radio" <span class="atn">id</span><span class="pun">=</span>"radioId"</span><span class="tag">&gt;</span></li><li><span class="tag">    &lt;li</span><span class="pln"></span><span class="tag">&gt;</span></li><li class="com">    ...</li><li><span class="tag">  &lt;/ul&gt;</span><span class="tag"></span></li><li><span class="tag">&lt;/form&gt;</span><span class="tag"></span></li></ol></pre>
			</form>	
		</div>
		
	</div>
	
	<div class="ink-section">
		<div class="ink-space">
			<h3>Buttons</h3>
			<p>Button styling can be applied to almost any html element by using the <code>.ink-button</code> class.</p>
		</div>
		<div class="ink-space">
			<table class="ink-table ink-bordered">
				<thead>
					<tr>
						<th>type</th>
						<th>active state</th>
						<th>disabled state</th>
						<th>description</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Default</td>
						<td><button class="ink-button">Default</button></td>
						<td><button class="ink-button" disabled>Default</button></td>
						<td><code>&lt;button&gt;Default&lt;/button&gt;</code></td>
					</tr>
					<tr>
						<td>Success</td>
						<td><button class="ink-button success">Success</button></td>
						<td><button class="ink-button success" disabled>Success</button>
						</td>
						<td>
							<p><code>&lt;button class=&quot;ink-success&quot;&gt;Success&lt;/button&gt;</code></p>
							<p><code>&lt;button type=&quot;button&quot; class=&quot;ink-success disabled&quot;&gt;Success&lt;/button&gt;</code></p>
						</td>
					</tr>
					<tr>
						<td>Warning</td>
						<td><button class="ink-button warning">Warning</button></td>
						<td><button class="ink-button warning" disabled>Warning</button></td>
						<td><code>blah</code></td>
					</tr>
					<tr>
						<td>Caution</td>
						<td><button class="ink-button caution">Caution</button></td>
						<td><button class="ink-button caution" disabled>Caution</button></td>
						<td><code>blah</code></td>
					</tr>
					<tr>
						<td>Info</td>
						<td><button class="ink-button info">Info</button></td>
						<td><button class="ink-button info" disabled>Info</button></td>
						<td><code>blah</code></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>