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

            <div class="ink-l40">
                <div class="ink-gutter"> 
                    <h3 id="tabs">Tabs</h3>
                    <p>
                        The <i>Tabs</i> component allows you to show images in a &quot;carousel&quot; format.
                        Supports several <a href="#" class="modal">configurations</a> and touch events!
                    </p>
                </div>
            </div>

            <div class="ink-l60">
                <div class="ink-gutter">
                    <div class="box">
                        <div class="ink-tabs">
                            <nav class="ink-navigation">
                                <ul class="ink-tabs-nav menu horizontal">
                                    <li><a href="#ui_tabs_home">Home</a></li>
                                    <li><a href="#ui_tabs_news">News</a></li>
                                    <li><a href="#ui_tabs_description">Description</a></li>
                                    <li><a href="#ui_tabs_stuff">Stuff</a></li>
                                </ul>
                            </nav>
                            <div class="ink-vspace">
                                <div id="ui_tabs_home" class="ink-tabs-container">
                                    <h4>Home</h4>
                                    <p>
                                        Arnold ipsum. Well then God shouldn't have cloned my dog. I'm a cybernetic
                                        organizm. Living tissue over endoskeleton. You're not going to have your
                                        mommies right behind you to wipe your little tushies. Blondes. Consider
                                        it a divorce. Talk to the hand. Take it BACK. I'll be back. Knock knock.
                                        I did nothing. The pavement with his enemy. You LIE! I'm not shitting on
                                        you. Get down or I'll put you down. You are not you you're me. Scumbag.
                                        Bring your toy back to the carpet. Bring it back to the carpet. Dylan.
                                        You son of a bitch. Get down. Of course. I'm a Terminator. Talk to the
                                        hand. I need your clothes, your boots, and your motorcycle. Into the tunnel.
                                        My name is John Kimble and I love my car. I'll be back.
                                    </p>
                                </div>
                                <div id="ui_tabs_news" class="ink-tabs-container">
                                    <h4>News</h4>
                                    <p>
                                        Aliens do exist. They're just waiting for Chuck Norris to die before they attack. 
                                        When his martial arts prowess fails to resolve a situation, Chuck Norris plays dead. 
                                        When playing dead doesn't work, he plays zombie. Chuck Norris invented water.
                                        Shortly after the farm animal sprang back to life and a crowd had gathered, 
                                        Chuck Norris roundhouse kicked the animal, breaking its neck, to remind the crew once more that Chuck giveth, 
                                        and the good Chuck, he taketh away.
                                    </p>
                                </div>
                                <div id="ui_tabs_description" class="ink-tabs-container">
                                    <h4>Description</h4>
                                    <p>
                                        Aliens do exist. They're just waiting for Chuck Norris to die before they attack. 
                                        When his martial arts prowess fails to resolve a situation, Chuck Norris plays dead. When playing dead doesn't work,
                                        he plays zombie. Chuck Norris invented water. Chuck Norris eats transformer toys in vehicle mode and poos them out 	
                                        transformed into a robot. Filming on location for Walker: 
                                        Texas Ranger, Chuck Norris brought a stillborn baby lamb back to life by giving it a prolonged beard rub.
                                    </p>
                                </div>
                                <div id="ui_tabs_stuff" class="ink-tabs-container">
                                    <h4>Stuff</h4>
                                    <p>
                                        They now play poker every second Wednesday of the month. Chuck Norris doesnt have AIDS but he gives it to people anyway.
                                         When you open a can of whoop-ass, Chuck Norris jumps out. If you have five dollars and Chuck Norris has five dollars,
                                          Chuck Norris has more money than you. A blind man once stepped on Chuck Norris' shoe. Chuck replied, 
                                          "Don't you know who I am? I'm Chuck Norris!" The mere mention of his name cured this man blindness. Sadly the first, 
                                          last, and only thing this man ever saw, was a fatal roundhouse delivered by Chuck Norris. Helen Keller's favorite color 										is Chuck Norris Chuck Norris
                                           invented water.
                                    </p>
                                </div>
                            </div>
                    	</div>
                    </div>
                	
                    <a href="#" data-target="tabs_sourcecode" class="toggleTrigger ink-button">View Source Code</a>
                    <pre id="tabs_sourcecode" style="display:none" class="prettyprint linenums"><?php echo(htmlentities( $js )); ?></pre>

                </div>
            </div>
        </div>
    	
		<script type="text/javascript">
            var tabs = new SAPO.Ink.Tabs('.ink-tabs', {
                disabled: ['#stuff', '#more_stuff'], 
                active: '#ui_tabs_news',
                onBeforeChange: function(tab){
                }, 
                onChange: function(tab){
                }
            });
        </script>
    </div>