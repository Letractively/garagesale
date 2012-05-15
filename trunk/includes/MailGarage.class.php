<?php

require("phpmailer/class.phpmailer.php");
/**
 * @method
 * @param Destinatario, assunto, conteudo, anexo, multiplos emails
 * @return se enviou com sucesso
 */
function SendMailGarage($to, $subject, $body, $attachment=null, $array_reply_mails=null)
{

	$phpmailer = new PHPMailer();
	$phpmailer->IsSMTP();
	$phpmailer->SMTPAuth = true;
	$phpmailer->Host = 'smtp.gmail.com';
	$phpmailer->Port = '587';
	$phpmailer->SMTPSecure = 'tls';
	$phpmailer->Username = 'garagesale.open@gmail.com';
	$phpmailer->Password = 'trabalhotcc';
	$phpmailer->FromName = 'Open Garage Sale';
	$phpmailer->CharSet = 'ISO-8859-1';
	$phpmailer->SetLanguage("br");
	$phpmailer->IsHTML(true);

	// Adicionar anexos...
	if ($attachment)
	{
		$phpmailer->AddAttachment($attachment);
	}

	// Destinos...
	if ($to)
	{
		$phpmailer->AddAddress($to);
	}
	elseif ($array_mails)
	{
		foreach ($array_reply_mails as $mail) {
			$phpmailer->AddReplyTo($mail);
		}
	}

	$phpmailer->Subject = $subject;
	$phpmailer->Body = $body;
	if ($phpmailer->Send())
	{
		return true;
	}
	else
	{
		return false;
	}

}


?>
