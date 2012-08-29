 

window.onload = function () {
 menu_button = document.getElementById('button_menu');
 app_body = document.getElementById('app_body');
 menu_overlay = document.getElementById('overlay_menu');
 
 menu_button.onclick = function () {
   if (app_body.style.display=='none') {
     app_body.style.display = 'block';
     menu_overlay.style.display = 'none';
   } else {
     app_body.style.display = 'none';
     menu_overlay.style.display = 'block';     
   } // if
   return false;
 } // menu_button.onclick
} // document.onload