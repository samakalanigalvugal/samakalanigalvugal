<?php
session_start();
      
/*use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;*/
require ('assets/libraries/Exception.php');
require ('assets/libraries/PHPMailer.php');
require 'assets/libraries/SMTP.php';


global $contentroot, $page_rules, $defaultimage, 
        $page_data, $districtsinfo, $taluksinfo, 
        $physical_output_folder, $villagesinfo;

$contentroot = "assets/rules/";
$page_rules = $contentroot ."data/pages.json";
$defaultimage = "assets/images/default.png";

$page_data = json_decode(file_get_contents($page_rules),true);

function downloadpdf()
{
      $physical_output_folder = $_SERVER['DOCUMENT_ROOT'] .dirname($_SERVER['PHP_SELF']) .'/outputdocs/';
      $contentroot = "assets/rules/";
      $selected_language = $_POST['language'];
      switch($_POST['grievanceid'])
      {
          case 'NoWater':
          {
              if($selected_language == 'TL')
              {

              }   
              else{
                  
              }
              break;
          }
      }    

      $grievance_file_name = $contentroot .'data/grievancelists.json'; 
      $grievance_file_data =  json_decode(file_get_contents($grievance_file_name),true);

      $fromname = ''; 
      $fromhousenumber ='';
      $fromstreetname ='';
      $fromcity ='';
      $frompostal ='';
      $fromdistrict ='';
      $toname = ''; 
      $streetname = '';
      $city = '';
      $postal = '';
      $district = '';
      $postalcode = '';
      $header ='';
      $fromheading ='';
      $toheading ='';
      $iyyatheading ='';
      $subjectheading ='';
      $subject ='';
      $placeheading ='';
      $dateheading ='';
      $currentdate ='';
      $ippadikkuheading ='';
      $url = '';
                          
      for($i=0; $i< count($grievance_file_data); $i++)
      { 
          if($grievance_file_data[$i]['grievanceid'] == $_POST['grievanceid'] )
          {
              $header = '';
              if(isset($grievance_file_data[$i]['header']))
              {
                $header = $grievance_file_data[$i]['header'];
              }
              $content_file_name = $contentroot .'data/contents/'. $_POST['language'] .  $_POST['grievanceid'] .'.txt';
              $template_file_name = $contentroot .'data/templates/'. $_POST['language'] .  $_POST['grievanceid'] .'template.html';
              
              if(isset($grievance_file_data[$i]['addresses']))
              {
                  $addresskey = $_POST['state'].$_POST['district'].$_POST['taluk'].$_POST['village'];
                  $address = $grievance_file_data[$i]['addresses'];
                  
                  if(isset($address[0]))
                  { 
                      foreach($address[0] as $json_key => $json_value)       
                      {
                          if($json_key == $addresskey)
                          {
                              $value = $json_value[0];
                              if(isset($value['toname'])) $toname = $value['toname'];
                              if(isset($value['streetname'])) $streetname = $value['streetname'];
                              if(isset($value['city'])) $city = $value['city'];
                              if(isset($value['postal'])) $postal = $value['postal'];
                              if(isset($value['district']))$district = $value['district'];
                              if(isset($value['postalcode']))$postalcode = $value['postalcode'];
                          }
                      } 
                  }
                  if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')   
                      $url = "https://";   
                  else  
                      $url = "http://";  
      
                  // Append the host(domain name, ip) to the URL.   
                  $output_file = $_POST['grievanceid'] .'_' .$_POST['fromname'] . '_' . date("Ymdhis") ;
                  $url.= $_SERVER['HTTP_HOST'] .dirname($_SERVER['PHP_SELF']) .'/'."outputdocs/" .$output_file;
                  
                  $pdf_file_name =  $physical_output_folder .$output_file.'.pdf';

                  require('assets/libraries/fpdf.php');
                  $pdf = new FPDF();
                  // $pdf->PHP_SAPI = 'A';
                  $pdf->AddPage();
                  $pdf->SetFont('Arial','B',10);
                  $pdf->Cell(60,20,$header,0,0,'C');
                  $pdf->Output($pdf_file_name, 'F');

                  //sendemail($output_file, $pdf_file_name);

                  
                  echo $url.'.pdf';
              }
          }
      }
  } 
  
    
    /*  // the message
    $msg = "First line of text\nSecond line of text";

    // use wordwrap() if lines are longer than 70 characters
    $msg = wordwrap($msg,70);

    // send email
    //mail("samakalanigalvugal.gmail.com","My subject",$msg);

    $to = "xyz@somedomain.com";
         $subject = "This is subject";
         
         $message = "<b>This is HTML message.</b>";
         $message .= "<h1>This is headline.</h1>";
         
         $header = "From:abc@somedomain.com \r\n";
         $header .= "Cc:afgh@somedomain.com \r\n";
         $header .= "MIME-Version: 1.0\r\n";
         $header .= "Content-type: text/html\r\n";

         ini_set("SMTP","localhost");
   ini_set("smtp_port","25");
   ini_set("sendmail_from","samakalanigalvugal.gmail.com");
   ini_set("sendmail_path", "C:\xampp\sendmail\sendmail.exe -t");
         
         $retval = mail ("samakalanigalvugal.gmail.com","My subject",$msg);
         
         if( $retval == true ) {
            echo "Message sent successfully...";
         }else {
            echo "Message could not be sent...";
         }*/

    
    //sendemail();


  function sendemail($subject, $fileAttachment)
  {

    $email_file = 'assets/rules/data/emailcontents.json';    
    $email_content = json_decode(file_get_contents($email_file),true);

    $fromaddress           = $email_content['sendmail_from'];
    $fromfullname           = $email_content['sendmail_name'];
    $toAddress           = $_POST['emailaddress'];
    $sendername           = $_POST['fromname'];
    $pathInfo       = pathinfo($fileAttachment);
    $attchmentName  = $subject;
    $bcc = $email_content['bcc'] ;
   
    $attachment    = chunk_split(base64_encode(file_get_contents($fileAttachment)));
    $boundary      = "PHP-mixed-".md5(time());
    $boundWithPre  = "\n--".$boundary;
   
    $headers   = "From: $fromaddress \r\n Bcc: $bcc \r\n";
    $headers  .= "Reply-To: $fromaddress \r\n";
    $headers  .= "Content-Type: multipart/mixed; boundary=\"".$boundary."\"";
   
    $message   = $boundWithPre;
    $message  .= "\n Content-Type: text/plain; charset=UTF-8\n";
    //$message  .= "\n $mailMessage";
   
    $message .= $boundWithPre;
    $message .= "\nContent-Type: application/octet-stream; name=\"".$subject."\"";
    $message .= "\nContent-Transfer-Encoding: base64\n";
    $message .= "\nContent-Disposition: attachment\n";
    $message .= $attachment;
    $message .= $boundWithPre."--";


    //PHPMailer Object
$mail = new PHPMailer(true); //Argument true in constructor enables exceptions

//From email address and name
$mail->From = $fromaddress;
$mail->FromName = $fromfullname;

//To address and name
$mail->addAddress($toAddress, $sendername);
//$mail->addAddress("recepient1@example.com"); //Recipient name is optional

//Address to which recipient will reply
$mail->addReplyTo("reply@yourdomain.com", "Reply");

//CC and BCC
$mail->addCC($bcc);
$mail->addBCC($bcc);

//Send HTML or Plain Text email
$mail->isHTML(true);

$mail->Subject = $subject;
$mail->Body = "<i>Mail body in HTML</i>";
$mail->AltBody = "This is the plain text version of the email content";

try {
    $mail->send();
    echo "Message has been sent successfully";
} catch (Exception $e) {
    echo "Mailer Error: " . $mail->ErrorInfo;
}

    /*ini_set("SMTP", $email_content['smtp']);
    ini_set("smtp_port",$email_content['smtp_port']);
    ini_set("sendmail_from", $email_content['sendmail_from']);
    ini_set("sendmail_path", $email_content['sendmail_path']);
    ini_set("auth_username",$email_content['auth_username']);
    ini_set("auth_password",$email_content['auth_password']);
   
    $status = mail($toAddress, $subject, $message, $headers);*/

    /*ini_set("SMTP","localhost");
    ini_set("smtp_port","25");
    ini_set("sendmail_from","samakalanigalvugal@gmail.com");
    ini_set("sendmail_path", "C:\xampp\sendmail\sendmail.exe -t");
    ini_set("auth_username","samakalanigalvugal@gmail.com");
    ini_set("auth_password","#password11#");
         
    $retval = mail ("samakalanigalvugal@gmail.com","My subject","Message sent successfully...");
    
    if( $retval == true ) {
      echo "";
    }
    else {
      echo "";
    }

    /*$mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";

    $mail->SMTPDebug  = 0;  
    $mail->SMTPAuth   = TRUE;
    $mail->SMTPSecure = "tls";
    $mail->Port       = 465;
    $mail->Host       = "smtp.gmail.com";
    $mail->Username   = "samakalanigalvugal@gmail.com";
    $mail->Password   = "#password11#";
    $mail->IsHTML(true);
    $mail->AddAddress("samakalanigalvugal@gmail.com", "recipient-name");
    $mail->SetFrom("samakalanigalvugal@gmail.com", "from-name");
    $mail->AddReplyTo("samakalanigalvugal@gmail.com", "reply-to-name");
    $mail->AddCC("samakalanigalvugal@gmail.com", "cc-recipient-name");
    $mail->Subject = "Test is Test Email sent via Gmail SMTP Server using PHP Mailer";
    $content = "<b>This is a Test Email sent via Gmail SMTP Server using PHP mailer class.</b>";
    $mail->MsgHTML($content); 
    if(!$mail->Send()) {
      echo "Error while sending Email.";
      var_dump($mail);
    } else {
      echo "Email sent successfully";
    }*/
  }  
  if(isset($_POST['datarequestedby']) && $_POST['datarequestedby'] == 'generatagrievance'){
    $pdfname = downloadpdf();
  }
  else if (isset($_POST['datarequestedfrom'])) {
      return processrequest();
  }
  else if (isset($_POST['datarequestedby']) && $_POST['datarequestedby'] == 'deletefile') {
      return deletefile($_POST['filename']);
  }

  function deletefile($filename){
    $physical_output_folder = $_SERVER['DOCUMENT_ROOT'] .dirname($_SERVER['PHP_SELF']) .'/outputdocs/';
    echo $physical_output_folder;
    $status = unlink($physical_output_folder .$filename);
  }

  function processrequest(){
    if($_POST['datarequestedfrom'] == 'grievancegenerator'){
      switch($_POST['datarequestedby']){
        case 'ddlstate':
        {
          $returndata = getdistrictslist($_POST['datarequestedfrom'], $_POST['state']);
          print_r($returndata);
          break;
        }
        case 'ddldistrict':
        {
          $returndata = gettalukslist($_POST['datarequestedfrom'], $_POST['state'],$_POST['district']);
          print_r($returndata);
          break;
        }
        case 'ddltaluk':
        {
          $returndata = getvillageslist($_POST['datarequestedfrom'], $_POST['state'],$_POST['district'],$_POST['taluk']);
          print_r($returndata);
          break;
        }
        case 'ddlvillage':
        {
          $returndata = getofficeslist($_POST['datarequestedfrom'], $_POST['state'],$_POST['district'],$_POST['taluk'],$_POST['village']);
          print_r($returndata);
          break;
        }
      }
    }
  }

  function contentfilename($pageid){
    for($i=0; $i< count($page_data); $i++)
    { 
      if($page_data[$i]['pageid'] == $pageid)
      {
        return $page_data[$i]['pagecontent'];
      }
    }     
  }
  function getstatecontents()
  {
    $contentfile = 'assets/rules/data/states.json';    
    return  json_decode(file_get_contents($contentfile),true);
  } 
  function getdistrictslist($pageid, $state)
  {
    global $districtsinfo;
    $state_data = getstatecontents();
    $local_data = '';

    for($i=0; $i< count($state_data); $i++)
    { 
      if($state_data[$i]['stateid'] == $state)
      {
        if(isset($state_data[$i]['districts']))
        {
          $_SESSION["districtsinfo"] = $state_data[$i]['districts'];
          $local_data .= '<option value="select">--- Select ---</option>';
          $districtsinfo = $_SESSION["districtsinfo"];
          for($k=0; $k < count($districtsinfo); $k++)
          { 
            if(isset($districtsinfo[$k]['districtid']) && isset($districtsinfo[$k]['districtname']))
              $local_data .= '<option value=' . $districtsinfo[$k]['districtid'] .'>' . $districtsinfo[$k]['districtname'] .'</option>';
          }
        } 
        else{
          $local_data .= '<option id="select">No data found</option>';
        }
      }
    }
    return $local_data ;
  }

  function gettalukslist($pageid, $state, $district)
  {
    $local_data = '';
    $districtsinfo = $_SESSION["districtsinfo"];
    if(count($districtsinfo) > 0)
    {
      $local_data .= '<option value="select">--- Select ---</option>';
      for($i=0; $i< count($districtsinfo); $i++)
      { 
        if($districtsinfo[$i]['districtid'] ==  $district && isset($districtsinfo[$i]['taluks']))
        {
          $_SESSION['talukinfo'] = $districtsinfo[$i]['taluks'];       
          for($k=0; $k < count($_SESSION['talukinfo']); $k++)
          { 
            if(isset($_SESSION['talukinfo'][$k]['talukid']) && isset($_SESSION['talukinfo'][$k]['talukname']))
              $local_data .= '<option value=' . $_SESSION['talukinfo'][$k]['talukid'] .'>' . $_SESSION['talukinfo'][$k]['talukname'] .'</option>';
          }
        } 
      }
    }
    return $local_data ;
  }
  
  function getvillageslist($pageid, $state,$district,$taluk)
  {
    $local_data = '';
    $talukinfo = $_SESSION['talukinfo'];
    if(count($talukinfo) > 0)
    {
      for($i=0; $i< count($talukinfo); $i++)
      { 
        if($talukinfo[$i]['talukid'] ==  $taluk && isset($talukinfo[$i]['villages']))
        {
            $_SESSION['villageinfo'] = $talukinfo[$i]['villages'];
            $villageinfo =  $_SESSION['villageinfo'];
            print_r($villageinfo);
            $local_data .= '<option value="select">--- Select ---</option>';
            for($k=0; $k < count($villageinfo); $k++)
            { 
              if(isset($villageinfo[$k]['villageid']) && isset($villageinfo[$k]['villagename']))
                $local_data .= '<option value=' . $villageinfo[$k]['villageid'] .'>' . $villageinfo[$k]['villagename'] .'</option>';
            }
        } 
      }
    }
    return $local_data ;
  }

  function getofficeslist($pageid, $state,$district,$taluk,$village)
  {
    $local_data = '';
    $villageinfo = $_SESSION['villageinfo'];
    if(count($villageinfo) > 0)
    {
      for($i=0; $i< count($villageinfo); $i++)
      { 
        if(isset($villageinfo[$i]['villageid']) && 
          isset($villageinfo[$i]['villagename']) && 
          $villageinfo[$i]['villageid'] == $village)
        {
          if(isset($villageinfo[$i]['offices']))
          {
            $officeInfo = $villageinfo[$i]['offices'];
            $local_data .= '<option value="select">--- Select ---</option>';
            for($k=0; $k < count($officeInfo); $k++)
            { 
              $local_data .= '<option value=' . $officeInfo[$k]['officeid'] .' address="' . $officeInfo[$k]['address'] .'">' . $officeInfo[$k]['issue'] .'</option>';
            }
          } 
          else{
            $local_data .= '<option id="select">No data found</option>';
          }
        } 
        else{
          $local_data .= '<option id="select">No data found</option>';
        }
      }
    }
    return $local_data ;
  }

  function getpagedata()
  {
    return $page_data;
  }

  function buildPage($pagename) {
    global $page_heading; 
    global $page_content; 
    global $contentroot;
    global $navcontent;
    global $defaultimage;
    global $page_rules;
    global $page_data;// = getpagedata();

    return buildPageContent($pagename, $page_data);
  }

  function buildheadercontent(){
    return '<div id="" class="header boxshadow">
                <div class="headerleft">
                    <img src="assets/images/logo.jpg">
                </div>
                <div class="headermiddle">
                    <a href="index.php" title="Home">
                        <img src="assets/images/logo.jpg">
                    </a>
                </div>
                <div class="headerright">
                    <img src="assets/images/logo.jpg">
                </div>
            </div>';
  }

  function buildPageContent($pagename, $page_data)
  {
    global $page_heading, $navcontent;
    $header_content = buildheadercontent();
    $column_left_content = buildcolumnleftcontent($pagename, $page_data);
    $column_middle_content ='';
    switch($pagename)
    {
      case 'index':
      {
        $column_middle_content = buildHomePage($pagename, $navcontent);
        break;
      }
      case "archives" :
      {
        $column_middle_content = buildArchivePage();
        break;
      }
      case "judicialdecisions" :
      {
        //$column_middle_content = buildJudicialDecisionsPage();
        break;
      }
      case "governmentcontactlist" :
      {
        $column_middle_content = buildGovenmentContactListPage();
        break;
      }
      case "governmentdecisions" :
      {
        //$column_middle_content = buildGovernmentDecisionsPage();
        break;
      }
      case "contactus" :
      {
        $column_middle_content = buildContactUsPage();
        break;
      }
      case "faq" :
      {
        $column_middle_content = buildFAQPage();
        break;
      }
      default :// "grievancegenerator"
      {;
        $column_middle_content = buildGrievanceGenerator($pagename);
        break;
      }
    }
    $retrun_data =  $header_content 
                    .'<div class="content">'
                      .$column_left_content
                      .'<div class="column middle"><p class="pageheader">'. $page_heading .'</p>'. $column_middle_content .'</div>' 
                      .'<div class="column right">' .buildcolumnrightcontent() .'</div>'
                    .'</div>';

    return $retrun_data;
  }

  function buildFAQPage()
  {
    global $contentroot; 
    $file_name = $contentroot ."data/faq.json";
    $json_data = json_decode(file_get_contents($file_name),true);
    $page_content ='';
    for($i=0; $i< count($json_data); $i++)
    { 
      $page_content .='<div class="faqsection"><div class="faqheading" id="' .$json_data[$i]['faqid'] . '">' . ($i+1) . '). '.$json_data[$i]['faqname'].'</div>'
                    .'<div class="faqcontent" id="' .$json_data[$i]['faqid'] . 'content">' .$json_data[$i]['faqcontent'] ;
      if(isset($json_data[$i]['video']))
      {echo 1;
        $page_content .='<div>
                          <video class="faqvideo" id="' .$json_data[$i]['faqid'] . 'video" controls>
                            <source src="'. $json_data[$i]['video'] .'" type=video/mp4>
                          </video>
                        </div>';
      }
      $page_content .='</div></div>';
    }
    
    return $page_content;
  }

  function buildcolumnleftcontent($pagename, $page_data)
  {
    $local_data = '<div class="column left">';

    global $page_heading; 
    global $page_content; 
    
    for($i=0; $i< count($page_data); $i++)
    { 
      if($page_data[$i]['pageid'] == $pagename)
      { 
        $page_heading = $page_data[$i]['pagename'];
        if(isset($page_data[$i]['pagecontenttype']) && $page_data[$i]['pagecontenttype'] == 'text')
        {
          $page_content = $page_data[$i]['pagecontent'];
        }
        $local_data .=  
        '<div class="menuitem ' . $page_data[$i]['pageid'] . ' selected"><a href="#" id="' . $page_data[$i]['pageid'] . '">' . $page_data[$i]['pagename'] . '</a></div>';
      }
      else{
        $local_data .=  
        '<div class="menuitem ' . $page_data[$i]['pageid'] . '"><a href=' . $page_data[$i]['pageid'] . '.php id="' . $page_data[$i]['pageid'] . '">' . $page_data[$i]['pagename'] . '</a></div>';
      }
    }
    return $local_data .'</div>';
  }

  function buildcolumnrightcontent()
  {
    return '
        <h3>Updates</h3>
        <div class="scrollcontents">
            <div class="menuitem index selected">
                <a href="#" id="index">Home</a>
            </div>
            <div class="menuitem grievancegenerator">
                <a href="grievancegenerator.php" id="grievancegenerator">Grievance Generator</a>
            </div>
            <div class="menuitem archives">
                <a href="archives.php" id="archives">Archives</a>
            </div>
            <div class="menuitem governmentcontactlist">
                <a href="governmentcontactlist.php" id="governmentcontactlist">Government Contacts</a>
            </div>
            <div class="menuitem judicialdecisions">
                <a href="judicialdecisions.php" id="judicialdecisions">Judicial Decisions</a>
            </div>
            <div class="menuitem governmentdecisions">
                <a href="governmentdecisions.php" id="governmentdecisions">Government Decisions</a>
            </div>
            <div class="menuitem contactus">
                <a href="contactus.php" id="contactus">Contact Us</a>
            </div>
        </div>';
  }

  function buildHomePage($pagename, $navcontent){
    global $page_content;
    global $page_heading;  
    return $page_content; 
  }

  function buildArchivePage()
  {
    global $contentroot; 
    global $defaultimage;
    $file_name = $contentroot ."data/archivelist.json";
    $json_data = json_decode(file_get_contents($file_name),true);
    $page_content ='';
    for($i=0; $i< count($json_data); $i++)
    { 
      $imagefilename = $defaultimage;
      if(isset($json_data[$i]['imagename']) && $json_data[$i]['imagename']  != ''){
        
        $imagefilename = $json_data[$i]['filepath'] . $json_data[$i]['imagename'];
      }
      if(isset($json_data[$i]['starting'])){
        $page_content .= '<div class="row archivelist">';
        $page_content .= '<p class="tilesection">' .$json_data[$i]['starting'] .'</p>';
      }
      if(isset($json_data[$i]['filename'])){
          $page_content .= 
            '<div class="column">
                <div class="card" 
                style ="background-image: url(' .$imagefilename . ');width:100%;">
                  <a href="' .$json_data[$i]['filepath'] . $json_data[$i]['filename'] . '"  target="_blank">
                  <h3>' . $json_data[$i]['filetitle'] . '</h3>
                  <p> - by ' . $json_data[$i]['fileauthor'] . '</p>
                  <p>' . $json_data[$i]['fileclassified'] .'</p></a>
                </div> 
            </div>';
        }

        if(isset($json_data[$i]['ending'])){
          $page_content .= '</div>';
        }
    }
    
    return $page_content;
  }

  function buildGrievanceGenerator($pagename)
  { 
    global $contentroot;
    $contentfile = 'controls/grievancegenerator.json';
    
    $page_data = json_decode(file_get_contents($contentroot .$contentfile),true);
    $local_data = '<p class="pageinstructionheader">Provide your inputs</p>';

    for($i=0; $i< count($page_data); $i++)
    {  
      if((isset($page_data[$i]['display']) && $page_data[$i]['display'] == '1') ||
                !isset($page_data[$i]['display']))
      {
      
        $local_data .= '<div>';

        if($page_data[$i]['controltype'] != 'button'){
          $local_data .= '<p><i>' . $page_data[$i]['title'] .':</i></p>';
        }
        if($page_data[$i]['controltype'] == 'dropdown'){
          
          $local_data .= 
                  '<select pagename="'. $pagename .'" class="grievancegeneratordata" required=' . $page_data[$i]['required'] .' id="' . $page_data[$i]['controlid'] .'">';
          
          if(isset($page_data[$i]['controldatatype'] ) )
          {
              if($page_data[$i]['controldatatype'] == 'text')
              {
                foreach($page_data[$i]['controldata'] as $json_key => $json_value)       
                {
                  $local_data .= '<option value=' . $json_key .'>' . $json_value .'</option>';
                } 
              }               
              
              else if($page_data[$i]['controldatatype'] == 'file'){
                $filename = $contentroot .$page_data[$i]['controldata'];
                $file_data = json_decode(file_get_contents($filename),true);
                $local_data .= '<option id="select">--- Select ---</option>';
                for($k=0; $k < count($file_data); $k++)
                { 
                  if(isset($page_data[$i]['filecontentid']) && isset($page_data[$i]['filecontentname'] ))
                  {
                    $local_data .= '<option value=' . $file_data[$k][$page_data[$i]['filecontentid']] .'>' . $file_data[$k][$page_data[$i]['filecontentname'] ] ;'</option>';
                  }
                }
              }
          }
          else if($page_data[$i]['controltype'] == 'file'){
                
          }
          $local_data .= '</select>';
          if($page_data[$i]['required']  == true ){
            $local_data .= 
                  '<div class="outererrormessage" style="display:none;" id="' . $page_data[$i]['controlid'] .'errormessage">'. $page_data[$i]['errormsg'] . '</div>';
          } 
        }
        else if($page_data[$i]['controltype'] == 'input'){
          $local_data .='<input class="input ' .$pagename .'data" type="text" required=' . $page_data[$i]['required'] .' errmsg=' . $page_data[$i]['errormsg'] .' id="' . $page_data[$i]['controlid'] .'">';
          if($page_data[$i]['required']  == true ){
            $local_data .= 
                  '<div class="outererrormessage" style="display:none;" id="' . $page_data[$i]['controlid'] .'errormessage">'. $page_data[$i]['errormsg'] . '</div>';
          }                
          
        }
        else if($page_data[$i]['controltype'] == 'button'){
          $local_data .= '<div class="button black section" id="' . $page_data[$i]['controlid'] .'" pageid="' .$pagename .'">'
                          . $page_data[$i]['title']
                          .'</div>';
        }
        $local_data .= '</div>';
      }
    }
    return $local_data;
  }

  function buildJudicialDecisionsPage()
  {

  }

  function buildGovenmentContactListPage()
  {
    global $contentroot, $defaultimage; 
    $file_name = $contentroot ."data/governmentcontactlist.json";
    $json_data = json_decode(file_get_contents($file_name),true);
    $page_content ='';
    for($i=0; $i< count($json_data); $i++)
    { 
      $imagefilename = $defaultimage;
      if(isset($json_data[$i]['imagename']) && $json_data[$i]['imagename']  != ''){
        $imagefilename = $json_data[$i]['imagepath'] . $json_data[$i]['imagename'];
      }
      
      if(isset($json_data[$i]['starting'])){
        $page_content .= '<div class="row governmentcontactlist">';
        $page_content .= '<p class="tilesection">' .$json_data[$i]['starting'] .'</p>';
      }

      if(isset($json_data[$i]['url'])){
          $page_content .= 
            '<div class="column">
                <div class="card" 
                style ="background-image: url(' .$imagefilename . ');">
                  <a href="' .$json_data[$i]['url'] . '"  target="_blank">
                  <h3>' . $json_data[$i]['title'] . '</h3></a>
                </div> 
            </div>';
        }

        if(isset($json_data[$i]['ending'])){
          $page_content .= '</div>';
        }
    }
    
    return $page_content;
  }
  function buildGovernmentDecisionsPage()
  {
    
  }
  function buildContactUsPage()
  {
    global $page_content;
    global $page_heading;  
    return $page_content; 
  }

  function generatefooter(){
      return '<p>Copyright &copy; 2022 Samakala Nigalvugal. All rights reserved. Design by <a href="www.samakalanigalvugal.com/">Samakala Nigalvugal</a>.</p>';
  }
?>