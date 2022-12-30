<!DOCTYPE html>
<html>
    <head>
        <title>eGGtor</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
        <link rel="stylesheet" type="text/css" href="assets/css/style.css?ver=15"/>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.4/jspdf.debug.js"></script>
        <script type="text/javascript" src="assets/script/jquery.min.js"></script>
        <script type="text/javascript" src="assets/script/javascript.js?ver=18"></script>
        <script>
            
        </script>
        <style>
        </style>
    </head>
    <div id="container" style="display:none"></div>
    <div class="popup" id="popup1"><a href="download.php?q=NoWater_opio_20221229080039">Download</a>
        <div class="popupcontent">
            <span parentcontrol="popup" class="popuperrormessageclose">X</span>
            <p class="popuptext">Thank you for being part of our <b class="popupbrand">Samakala Nigalvugal</b> family.</p>
            <p>
                <a  onclick="printDiv()" class="popuplink" style="display:none" parentcontrol="popup" target="_blank" shoulddeletethefile="1" >Download your Grievance!</a>
            </p>
            <p  style="display:none" class="popuperrormessage">Something went wrong. Please try after sometimes
                
            </p>    
        </div>
    </div>
    <div class="container">
        <?php            
            require ('domainservices.php');
            print_r(buildPage('grievancegenerator')); 
        ?>
    </div>
    <div class="footer"><?php            
            //require ('domainservices.php');
            print_r(generatefooter('grievancegenerator')); 
            ?>
    </div>  
    </body>
</html>