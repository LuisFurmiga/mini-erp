<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '../vendor/autoload.php'; // Garante que o PHPMailer seja carregado

function enviarEmail($para, $assunto, $mensagem) {
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $mail->isHTML(false);

    try {
        // Configurações do servidor SMTP do Mailtrap
        $mail->isSMTP();
        $mail->Host       = 'live.smtp.mailtrap.io';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'api'; // conforme mostrado na interface do Mailtrap
        $mail->Password   = 'SUA_SENHA_AQUI'; // substitua pela senha real da sua conta
        $mail->Port       = 587;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        // Remetente e destinatário
        $mail->setFrom('hello@demomailtrap.co', 'Mini ERP');
        $mail->addAddress($para);

        // Conteúdo do e-mail
        $mail->isHTML(false); // Texto puro (se quiser HTML, mude para true)
        $mail->Subject = $assunto;
        $mail->Body    = $mensagem;

        // Envia o e-mail
        $mail->send();

        return true;

    } catch (Exception $e) {
        error_log("Erro ao enviar e-mail: {$mail->ErrorInfo}");
        return false;
    }
}
