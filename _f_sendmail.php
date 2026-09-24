<?php
function sendmail($recipient,$subject,$message) {
    $user = "IT24-23";
    $namn = "Lorna";
    $datum = date("d.m.Y H:i:s");
    $mail_to = $recipient;
    $mail_subject ="Mail from Umbrella Corp : ".$subject;
    $mail_body ="This message is auto generated from Umbrella Corp's webpage : \n\n";
    $mail_body .="".$message."\n\n";
    $mail_body .="Message sent ".$datum." by ".$namn."\n\n";

    // Returnera true/false så anropande kod kan kontrollera om mailet skickades
    return mail($mail_to,$mail_subject,$mail_body);
}
?>