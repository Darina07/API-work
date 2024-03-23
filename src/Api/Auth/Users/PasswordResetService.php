<?php

namespace Supp\Api\Auth\Users;

use HPHIO\Farret\Notif;
use hphio\util\RandomGenerator;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Supp\SuppApiService;

class PasswordResetService extends SuppApiService
{

    public function __invoke(ServerRequestInterface $request, array $routeArgs = []): ResponseInterface
    {
        $requestobject = json_decode((string)$request->getBody(), true);
        $email = filter_var($requestobject['email'], FILTER_VALIDATE_EMAIL);

        if ($this->checkEmail($email)==0){
            return new JsonResponse(['error' => "This e-mail address is not registered in the system"], 404);
        }

        $nonce = $this->container->get(RandomGenerator::class)->uuidv4();
        $sql = "UPDATE users SET nonce = :nonce WHERE email = :email";
        $values = [
            'nonce' => $nonce,
            'email' => $email
        ];
        $stmt = $this->container->get('db')->prepare($sql);
        $stmt->execute($values);
        if($stmt->errorCode() != '00000') throw new \Exception(sprintf("Database error(%s): %s", $stmt->errorCode(), $stmt->errorInfo()[2]));

        $this->sendNotification($email, $nonce);

        return new EmptyResponse(200);
    }

    private function sendNotification($email, $nonce)
    {
        $configs = $this->container->get('config');
        $buffer = [];
        $buffer[] = $configs->get("paths.assets");
        $buffer[] = 'emails';
        $templatePath = implode("/", $buffer) . '/';

        $Notif = new Notif();
        $Notif->setTemplateDirectory($templatePath);
        $Notif->loadTemplate('password-reset');
        $Notif->addFart("UUID", $nonce);
        $Notif->render();

        // To send HTML mail, the Content-type header must be set
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html';
        $headers[] = 'X-Notif: support';

        // Additional headers
        $headers[] = 'From: no-reply <';
        $headers[] = 'Bcc: ';

        $message = $Notif->body;
        $to = $email;
        $subject = '[ ACTION REQUIRED ] - Password reset for Support HPH';

        // Mail it
        mail($to, $subject, $message, implode("\r\n", $headers));
    }

    private function checkEmail($email)
    {
        $sql = "SELECT email FROM users WHERE email = :email";
        $values = [
            'email' => $email
        ];
        $stmt = $this->container->get('db')->prepare($sql);
        $stmt->execute($values);
        return $stmt->rowCount();
    }
}
