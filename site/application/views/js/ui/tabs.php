<?php
 $js = <<<JS
<div class="ink-tabs">
    <nav class="ink-navigation">
        <ul class="ink-tabs-nav menu horizontal">
            <li><a href="#home">Home</a></li>
            <li><a href="#news">News</a></li>
            <li><a href="#description">Description</a></li>
            <li><a href="#stuff">Stuff</a></li>
            <li><a href="#more_stuff">More stuff</a></li>
        </ul>
    </nav>
    <div id="home" class="ink-tabs-container">
        <h4>Home</h4>
        <p>Arnold ipsum (...)</p>
    </div>
    <div id="news" class="ink-tabs-container">
        <h4>News</h4>
        <p>Arnold ipsum (...)</p>
    </div>
    <div id="description" class="ink-tabs-container">
        <h4>Description</h4>
        <p>Arnold ipsum (...)</p>
    </div>
    <div id="stuff" class="ink-tabs-container">
        <h4>Stuff</h4>
        <p>Arnold ipsum (...)</p>
    </div>
    <div id="more_stuff" class="ink-tabs-container">
        <h4>More Stuff</h4>
        <p>Arnold ipsum (...)</p>
    </div>
</div>
<script type="text/javascript">
    var tabs = new SAPO.Ink.Tabs('.ink-tabs', {
        disabled: ['#stuff', '#more_stuff'], 
        active: '#news',
        onBeforeChange: function(tab){
            console.log('beforeChange', tab);
        }, 
        onChange: function(tab){
            console.log('Changed', tab);
        }
    });
</script>
JS;
?>

    
    <div class="ink-section">
        <div class="ink-row ink-vspace">

            <div class="ink-l30">
                <div class="ink-gutter"> 
                    <h3 id="tabs">Tabs</h3>
                    <p>
                        The <i>Tabs</i> component allows you to show images in a &quot;carousel&quot; format.
                        Supports several <a href="#" class="modal">configurations</a> and touch events!
                    </p>
                </div>
            </div>

            <div class="ink-l70">
                <div class="ink-gutter">
                    <div class="box">
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
					<a href="#" data-target="tabs_sourcecode" class="ink-button toggleTrigger">View Source Code</a>
					<pre id="tabs_sourcecode" style="display:none" class="prettyprint linenums"><?php echo(htmlentities( $js )); ?></pre>
				</div>
            </div>
        </div>
		<script type="text/javascript">
			var tabs = new SAPO.Ink.Tabs('.ink-tabs', {
				disabled: ['#stuff', '#more_stuff'], 
				active: '#news',
				onBeforeChange: function(tab){
					console.log('beforeChange', tab);
				}, 
				onChange: function(tab){
					console.log('Changed', tab);
				}
			});
	    </script>
    </div>