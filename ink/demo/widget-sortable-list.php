<?php include 'shared/header.php'; ?>

<style type="text/css">
    .ink-sortable-list {
    -webkit-touch-callout:  none;
      -webkit-user-select:  none;
       -khtml-user-select:  none;
         -moz-user-select:  none;
          -ms-user-select:  none;
              user-select:  none;
    }
</style>

<div class="ink-l70">   
    <div class="ink-space">

        <ul class="ink-sortable-list-source">
            <li>primeiro</li>
            <li>segundo</li>
            <li>terceiro</li>
        </ul>

    </div>
</div>


<script type="text/javascript">
    var sl = new SAPO.Ink.SortableList('.ink-sortable-list-source', {dragLabel: ''});
</script>
