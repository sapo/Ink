<?php include 'shared/header.php'; ?>

<link rel="stylesheet" type="text/css" href="http://js.staging.sapo.pt/Assets/Images/DatePicker/style.css"/>

<div class="ink-l70">   
    <div class="ink-space">

        <h3>default (on input text focus)</h3>
        <p>
            <input id="x" type="text" value=""/>
        </p>



        <h3>mode displayInSelect</h3>
        <p>
            <select id="y"></select>
            <select id="m"></select>
            <select id="d"></select>
            <a href="#" id="pick">open</a>
        </p>

    </div>
</div>


<script type="text/javascript">
    var dp1 = new SAPO.Ink.DatePicker('#x');
    

    
    var fillSelectWithRange = function(selectId, minVal, maxVal, labels) {
        var sel = s$(selectId);
        var option = document.createElement('option');
        option.selected = "selected";
        sel.appendChild(option);
        var i, label, idx = 0;
        for (i = minVal; i <= maxVal; ++i) {
            label = (labels) ? labels[idx++] : i;
            option = document.createElement('option');
            option.value = i;
            option.innerHTML = label;
            sel.appendChild(option);
        }

    }
    fillSelectWithRange('d',    1,   31);
    fillSelectWithRange('m',    1,   12, ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez']);
    fillSelectWithRange('y', 2000, 2020);

    var dp2 = new SAPO.Ink.DatePicker(
        '',
        {
            displayInSelect: true,
            pickerField:     '#pick',
            yearField:       '#y',
            monthField:      '#m',
            dayField:        '#d'
        }
    );
</script>
