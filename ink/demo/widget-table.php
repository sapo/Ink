<?php include 'shared/header.php'; ?>


<style type="text/css">
</style>

<div class="ink-container">
    <div class="ink-l60">

        <h1>Table Demo</h1>
        
        <h2>with client-side model</h2>
        <table class="ink-bordered ink-zebra ink-hover"></table>
        <nav></nav>

        <h2></h2>
        <h2></h2>
        <h2>using server endpoint</h2>
        <table class="ink-hover"></table>

    </div>
</div>

<script type="text/javascript">
    var model1 = [
        {name:'Jimi Hendrix',      age:21, gender:'M'},
        {name:'Jimmy Page',        age:31, gender:'M'},
        {name:'Brian May',         age:26, gender:'M'},
        {name:'Ritchie Blackmore', age:18, gender:'M'},
        {name:'Pete Townsend',     age:42, gender:'M'},
        {name:'Joni Mitchel',      age:51, gender:'F'},
        {name:'Eddie Van Halen',   age:48, gender:'M'},
        {name:'Mark Knopfler',     age:34, gender:'M'},
        {name:'George Harrison',   age:37, gender:'M'},
        {name:'Eric Clapton',      age:22, gender:'M'}
    ];

    var t1 = new SAPO.Ink.Table(
        'table:nth-child(3)',
        {
            model:          model1,

            fields:         ['name', 'age', 'gender'],
            sortableFields: '*',
            fieldNames:     {
                                name:   'nome',
                                age:    'idade',
                                gender: 'género'
                            },
            pageSize:       3,
            pagination:     'nav:nth-child(4)'
        }
    );

    var t2 = new SAPO.Ink.Table(
        'table:nth-child(8)',
        {
            endpoint:       'widget-table-demo-endpoint.php',

            fields:         ['name', 'age', 'weight', 'gender'],
            sortableFields: '*',
            //sortableFields: ['name'],
            fieldNames:     {
                                name:   'nome',
                                age:    'idade',
                                gender: 'género',
                                weight: 'peso'
                            },
            formatters:     {
                                gender: function(fieldValue, item, tdEl) {
                                            if (!fieldValue) { return; }
                                            tdEl.innerHTML   = '<i class="icon-user"></i>';
                                            tdEl.style.color = fieldValue === 'M' ? '#66A' : '#A66';
                                        },
                                weight: function(fieldValue, item, tdEl) {
                                            tdEl.innerHTML = fieldValue ? fieldValue.toFixed(1) + ' kg' : '?';
                                        }
                            },
            pageSize:       4,
            onCellClick:    function(tbl, o) {
                console.log( JSON.stringify(o, null, '\t') );
            },
            onHeaderClick:  function(tbl, o) {
                console.log( JSON.stringify(o, null, '\t') );
            }
        }
    );
</script>
