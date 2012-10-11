<?php include 'shared/header.php'; ?>

<style type="text/css">
</style>

<div class="ink-l70">   
    <div class="ink-space">

        <div id="REPLACEME"></div>
</div>


<script type="text/javascript">
    var tv = new SAPO.Ink.TreeView('#REPLACEME', {
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
                        ['grandgrandchild 1bB'],
                    ]]
                ]],
                ['child 3']
            ]]
    });
</script>
