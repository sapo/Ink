<?php include 'shared/header.php'; ?>

<style type="text/css">
</style>

<div class="ink-l70">   
    <div class="ink-space">

        <h3>a)</h3>

        <ul class="ink-tree-view-source">
            <li>
                0
                <ul>
                    <li>
                        a
                        <ul>
                            <li>a1</li>
                            <li>a2</li>
                            <li>a3</li>
                        </ul>
                    </li>
                    <li>b</li>
                    <li>c</li>
                </ul>
            </li>
        </ul>

        <h3>b)</h3>

        <ul class="ink-tree-view-source">
            <li>
                0
                <ul>
                    <li>
                        a
                        <ul>
                            <li>a1</li>
                            <li>a2</li>
                            <li>a3</li>
                        </ul>
                    </li>
                    <li>b</li>
                    <li>c</li>
                </ul>
            </li>
            <li>
                0
                <ul>
                    <li>
                        a
                        <ul>
                            <li>a1</li>
                            <li>a2</li>
                            <li>a3</li>
                        </ul>
                    </li>
                    <li>b</li>
                    <li>c</li>
                </ul>
            </li>
        </ul>

    </div>
</div>


<script type="text/javascript">
    var tv = new SAPO.Ink.TreeView('.ink-tree-view-source', {
        //selectable: true
    });

    var tv2 = new SAPO.Ink.TreeView('.ink-tree-view-source', {
        selectable: true,
        onClick: function(o, tv) {
            console.log(JSON.stringify(o));
        }
    });
</script>
