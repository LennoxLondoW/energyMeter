<?php
/*main operation file*/
require_once '../app/extensions/app.element.php';
//loging in check
if(!isset($_SESSION['username']))
{
	//do something
}
else
{
	//do something
}


if(isset($_POST['ask_email']))
{   
	
	$admin_email= $_POST['ask_admin_email'];
	unset($_POST['ask_admin_email']);
	unset($_POST['acceptance']);
	$element = new Element();
	$element->activeTable = "lentec_ask";
	$element->comparisons = array_map(function ($val)
		{
           return [$val, " = ", strip_tags($_POST[$val])];
		}, array_keys($_POST));
	$element->joiners = array_merge([''], array_fill(0,count($_POST) - 1, " && "));  
	$element->order = " BY id DESC ";
	$element->cols = "*"; 
	$element->limit = 1;
	$element->offset = 0;
	$data = $element->getData();
	//already posted
	if(count($data)>0)
	{
		die("Swal.fire({icon:'info',text: 'Thank you. We are working on your already received query! We will get back to you.'})");
	}

   $element->email_username = "Admin";
   $element->email_message = $_POST['ask_message'].
   							'<br>
   								<h3>Client Details</h3>
   							 <br>
   							 <table>
   							 	<tbody>
   							 		<tr>
   							 			<td>Name:</td>
   							 			<td>'.$_POST['ask_name'].'</td>
   							 		</tr>
   							 		<tr>
   							 			<td>Email: </td>
   							 			<td>'.$_POST['ask_email'].'</td>
   							 		</tr>
   							 		<tr>
   							 			<td>Contacts: </td>
   							 			<td>'.$_POST['ask_tel'].'</td>
   							 		</tr>
   							 	</tbody>
   							 </table>';
   $element->email_subject = $_POST['ask_subject'];
   $element->email_to = $admin_email;
   $element->email_cc = [];
   $element->email_attachment = false;
   // send email
   if(!$element->send_email())
   {
   		die("Swal.fire({icon:'info',text: `".$element->email_error."`})");
   }

    $element ->insertData = [$_POST];
	if(($reply= $element->saveData())==='success')
	{
		die("Swal.fire({icon:'success',text: `".$element->email_success."`}); $('.clr').val('');");
	}
	else
	{
		die("Swal.fire({icon:'error',text: `".$reply."`})");
	}

}

// empain template preview
if(isset($_GET['preview']))
{
	 $element = new Element();
	 // email template
      	   $element->email_username = "Preview";
		   $element->email_message ='Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
    tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
    quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
    consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
    cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
    proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';
		   $element->email_subject = "Your subject here";
   
    die("<br><br>".$element->send_email());
}


$element = new Element();
$element->activeTable = "lentec_contact";
$element->comparisons = [];
$element->joiners = [''];  
$element->order = " BY id DESC ";
$element->cols = "section_id, section_title"; 
$element->limit = 200;
$element->offset = 0;
/*get_data*/
$data = $element ->GetElementData();
