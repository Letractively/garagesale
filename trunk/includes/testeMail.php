<?php

require_once 'MailGarage.class.php';

if (SendMailGarage('darenhart@univates.br', 'assunto email phpmailer', 'corpo do email teste')){
	echo 'sucesso';
}else {
	echo 'erro';
}