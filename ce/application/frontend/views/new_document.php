<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Welcome to CodeIgniter</title>

	<script type="text/javascript" src="http://js.sapo.pt/SAPO/"></script>
	<script type="text/javascript" src="http://js.sapo.pt/SAPO/Dom/Loaded/"></script>
	<script type="text/javascript" src="http://js.sapo.pt/SAPO/Dom/Event/"></script>
	<script type="text/javascript" src="http://js.sapo.pt/SAPO/Dom/Css/"></script>
	<script type="text/javascript" src="http://js.sapo.pt/SAPO/Dom/Element/"></script>
	<script type="text/javascript" src="http://js.sapo.pt/SAPO/Dom/Selector/"></script>
	<script type="text/javascript" src="http://js.sapo.pt/SAPO/Communication/Ajax/1.1/"></script>

	<style type="text/css">

	::selection{ background-color: #E13300; color: white; }
	::moz-selection{ background-color: #E13300; color: white; }
	::webkit-selection{ background-color: #E13300; color: white; }

	body {
		background-color: #fff;
		margin: 40px;
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #4F5155;
	}

	a {
		color: #003399;
		background-color: transparent;
		font-weight: normal;
	}

	h1 {
		color: #444;
		background-color: transparent;
		border-bottom: 1px solid #D0D0D0;
		font-size: 19px;
		font-weight: normal;
		margin: 0 0 14px 0;
		padding: 14px 15px 10px 15px;
	}

	code {
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 12px;
		background-color: #f9f9f9;
		border: 1px solid #D0D0D0;
		color: #002166;
		display: block;
		margin: 14px 0 14px 0;
		padding: 12px 10px 12px 10px;
	}

	#body{
		margin: 0 15px 0 15px;
	}
	
	p.footer{
		text-align: right;
		font-size: 11px;
		border-top: 1px solid #D0D0D0;
		line-height: 32px;
		padding: 0 10px 0 10px;
		margin: 20px 0 0 0;
	}
	
	#container{
		margin: 10px;
		border: 1px solid #D0D0D0;
		-webkit-box-shadow: 0 0 8px #D0D0D0;
	}
	</style>
</head>
<body>
<!-- div. -->
<script type="text/javascript">
    (function(){
        var req = new SAPO.Communication.Ajax(
          'http://10.134.132.97/api.php/document/new',
          {
          	method: "GET",
          	onSuccess: function(obj){
          		console.log(obj.responseText);

          	}
          }
         );
    }());
</script>
</body>
</html>