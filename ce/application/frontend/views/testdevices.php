<html>
  <head>
    <style type="text/css">
      body {
        background: #333;
      }
     
      div.device-frame {
        margin: 20px auto;
        background: #000;
        border-radius: 20px;
      }

      .device {
        margin: 30px;
        background: #fff;
      }

    </style>
  </head>
  <body>
  <div class="device-frame" style="width:<?=$w+60?>px; height: <?=$h+60?>px;">
    <frame class="device" style="width:<?=$w?>px; height: <?=$h?>px;">
      <?=$page?>
    </frame>
    
  </div>
  </body>
</html>