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
        //$data = $request->getParsedBody();
        $data['notificationTitle'] = 'Example Title';
        $data['notificationToName'] = 'Example To Name';
        $data['notificationHTML'] = '<html>Test HTML</html>';
        $data['notificationBody'] = 'Information can go here what this notification is about';

        // Create a message
        $message = (new \Swift_Message($data['notificationTitle']))
            ->setFrom(['doNotReply@pwbox.test' => 'PwBox'])
            ->setTo(['parareda8@gmail.com', 'r.ceci.97@gmail.com' => $data['notificationToName']])
            ->setBody($data['notificationHTML']);

        // Send the message
        $result = $this->mailer->send($message);

        $repo = $this->container->get('user_repository');

        $repo->insertNotification($_COOKIE['user_id'], $data['notificationTitle'] ,$data['notificationBody']);

        return $response->withStatus(302)->withHeader("Location", "/dashboard");
    }
}