<?php include 'shared/header.php'; ?>

<div class="ink-container">
	<div class="ink-l60">   
		<div class="ink-space">
			<form class="ink-form-block">
				<fieldset>
					<legend><h4>default (on input text focus)</h4></legend>
					<div class="ink-form-wrapper">
						<input id="x" type="text" value=""/>
					</div>
	
					<legend><h4>mode displayInSelect</h4></legend>
					<div class="ink-form-wrapper">
						<select class="ink-mini-input" id="y"></select>
						<select class="ink-mini-input" id="m"></select>
						<select class="ink-mini-input" id="d"></select>
						<a href="#" id="pick">open</a>
					</div>
				</fieldset>
			</form>
		</div>
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
