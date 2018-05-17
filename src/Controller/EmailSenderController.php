<?php

namespace PwBox\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require_once __DIR__ . '/../../vendor/autoload.php';

class EmailSenderController
{
    protected $container;

    // Create the Transport
    protected $mailer;

    public function __construct(ContainerInterface $container){
        $this->container = $container;

        $transport = (new \Swift_SmtpTransport('smtp.mailtrap.io', 2525))
            ->setUsername('87ea2cbd424caa')
            ->setPassword('a3a195b85b7028');

        /*$transport = (new \Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl'))
            ->setUsername('parareda8')
            ->setPassword('isyyxywuuzwjoopy');*/
        // Create the Mailer using your created Transport
        $this->mailer = new \Swift_Mailer($transport);
    }

    public function sendEmail(Request $request, Response $response)
    {
        // Create a message
        $message = (new \Swift_Message('Fills de puta'))
            ->setFrom(['john@doe.com' => 'John Doe'])
            ->setTo(['parareda8@gmail.com', 'r.ceci.97@gmail.com' => 'Holas'])
            ->setBody('<html>Here is the message itself, yolo</html>');

        // Send the message
        $result = $this->mailer->send($message);

        return $response->withStatus(302)->withHeader("Location", "/dashboard");
    }
}