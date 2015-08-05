<?php
	//print get_include_path();
	require_once 'PHPMailer/PHPMailerAutoload.php'; // Change

	function halp($error) { // General error handling
		print "$error Reload the page and try again.";
		//print "$error<br>Reloading in 3 seconds ...";
		//print "<META http-equiv=\"refresh\" target=\"_blank\" content=\"3;URL=http://www.rastarockrevolution.com/#!get-the-song/cmtu\">"; // Change
		die();
	} // End error code


	function dump_data($n, $e, $s) { // Put data into mailing list
		// File writing
		/*$ml = fopen("mailinglist.txt", "a");
		fwrite($ml, "$n\t|\t$e\t|\t$s\n");
		fclose($ml);*/
		
		// Database writing
		mysql_connect("localhost","[db name]","[db password]") or die("Failed to connect to database");
		mysql_select_db("[database]");
		$name=mysql_real_escape_string(substr($n, 0, 100));
		$email=mysql_real_escape_string(substr($e, 0, 100));
		$songname=mysql_real_escape_string (substr($s, 0, 100));
		$stmt = mysql_query("INSERT INTO mailinglist (names, emails, song_downloaded) VALUES ('$name', '$email', '$songname')");
		if(!$stmt) {
			halp("Couldn't add info to database!" . mysql_error());
		} else {
		     print "Email successfully sent! You should get an mail with the link to your song in a bit! If not, please resubmit your information, ensuring that all information is spelled correctly.";
		}
	} // End database addition

	function send_song($name, $email_to, $songname) { // Mail the requested song to the user
		//print $_SERVER['DOCUMENT_ROOT']."/rastarock/media/$songname";
		include_once("PHPMailer/class.phpmailer.php"); 
		include_once("PHPMailer/class.smtp.php"); 
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->CharSet='UTF-8';
		$mail->ContentType='text/html';
		$mail->Host="dedrelay.secureserver.net";
		//$mail->SMTPDebug=2;
		$mail->Port=25;
		$mail->FromName='The Rastarock Team';
		$mail->AddReplyTo('mymusic@rastarock.com', 'The Rasta Rock Opera');
		$mail->AddAddress("$email_to");
		$mail->Subject='Rastarock: Here is the song you requested!';
		$mail->Body="Thank you for registering and for joining the Respect and Love Revolution!<br><br>Here is <a href=\"http://respectandloverevolution.com/media/$songname\">your link</a> to download the single \"No More\" -- the first release from The Rasta Rock Opera's debut album.<br><br><br><br>Rock On!,<br>The Rasta Rock Opera";
		//$mail->addAttachment($_SERVER['DOCUMENT_ROOT']."/rastarock/media/$songname.flac", 'Song.flac'); // Attachments may be too big, should be less than a few MB

		if(!$mail->Send()) {
			halp("The mailer was not able to send you an email. Please try again at a later time.");
		    //print "Mailer Error: " . $mail->ErrorInfo;
    	} else {
			dump_data($name, $email_to, $songname);
    	}
	} // End sendmail

	/***** MAIN *****/
	if (htmlentities($_POST['submitted']) == 1 and htmlentities($_POST['agree']) == 'on') {
		$name=htmlentities($_POST['name']);
		if (!isset($_POST['name']) or strlen($name) < 1) {
			halp("Please input a valid name!");
		}
		$email=htmlentities($_POST['email']);
		if (!isset($_POST['email']) or !filter_var($email, FILTER_VALIDATE_EMAIL)) {
			halp("Please input a valid email!");
		}
		$songname=htmlentities($_POST['songname']);
		if (!isset($_POST['songname']) or strlen($songname) < 1) {
			halp("Please choose a valid song!");
		}
		send_song($name, $email, $songname);
	} else {
		halp("You must click 'accept' to get your song!");
	}
	/***** END MAIN *****/
?>
