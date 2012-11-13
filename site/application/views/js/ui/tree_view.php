<?php
 $js = <<<JS
<div id="tview"></div>
<script type="text/javascript">
    var tree = new SAPO.Ink.TreeView('#tview', {
        //selectable: true,
        model:
            ['root', [
                ['child 1', [
                    ['grandchild 1a'],
                    ['grandchild 1b'],
                    ['grandchild 1c']
                ], 1],
                ['child 2', [
                    ['grandchild 2a'],
                    ['grandchild 2b', [
                        ['grandgrandchild 1bA'],
                        ['grandgrandchild 1bB']
                    ]]
                ]],
                ['child 3']
            ]]
    });
</script>
JS;
?>
    <div class="ink-section">
        <div class="ink-row ink-vspace">
            <div class="ink-l40">
                <div class="ink-gutter"> 
                    <h3 id="tree_view">Tree View</h3>
                    <p>
                        The <i>Tree View</i> component allows you to show a list of items in a hierarchical format.
                        It allows multiple <a href="#" class="modal">configurations</a>, including nested sets and default states (open, closed).
                    </p>
                </div>
            </div>
            <div class="ink-l60">
                <div class="ink-gutter">
                    <div class="ink-row box">
                        <div class="ink-alert-block info">
                            <button class="close">×</button>
                            <h4>Note:</h4>
                            <p>Notice that child 1 node starts closed because its model has 1 in its 3rd position.</p>
                        </div>

                        <?php echo $js;?>
                        <br/>
                        <a href="#" data-target="treeview_sourcecode" class="toggleTrigger">View Source Code</a>
                        <pre id="treeview_sourcecode" style="display:none" class="prettyprint linenums ink-vspace"><?php echo(htmlentities( $js )); ?></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>