<?php
/**
 * Created by PhpStorm.
 * User: ben-amara-pixels
 * Date: 2/6/19
 * Time: 2:47 PM
 */

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Http\Client\Common\HttpMethodsClient as HttpClient;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use OneSignal\Config;
use OneSignal\OneSignal;
use Symfony\Component\Routing\Annotation\Route;

class NotificationService
{


    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    function sendMessage()
    {

        $message = new Notification();
        $messages = $this->em->getRepository(Notification::class)->findAll();

        foreach ($messages  as $message){
            $body = $message->getBody();
        }


        $config = new Config();
        $config->setApplicationId('94bf959a-efb0-4b90-b2d4-bdd0acc67333');
        $config->setApplicationAuthKey('MGVjYTA4YTctMWE0YS00YzIwLTg0ZTAtNTY1MTIwZGNmZDZm');
        $config->setUserAuthKey('NDEwOGEyYTUtMjQyNi00MDNlLWIwMmQtMzdmYjZmOTc5ZmU1');

        $guzzle = new GuzzleClient([ // http://docs.guzzlephp.org/en/stable/quickstart.html
            // ..config
        ]);

        $client = new HttpClient(new GuzzleAdapter($guzzle), new GuzzleMessageFactory());
        $api = new OneSignal($config, $client);


        $notification = $api->notifications->add([
            'contents' => [
                'en' => $body
            ],
            'included_segments' => ['All'],
            'data' => ['foo' => 'bar'],
//            'isChrome' => true,
            'send_after' => new \DateTime('1 second'),

            // ..other options
        ]);
    }


    function sendToUser($id){

        $user = $this->em->getRepository(User::class)->find($id);

        $playerId = $user->getPlayerId();



        $config = new Config();
        $config->setApplicationId('94bf959a-efb0-4b90-b2d4-bdd0acc67333');
        $config->setApplicationAuthKey('MGVjYTA4YTctMWE0YS00YzIwLTg0ZTAtNTY1MTIwZGNmZDZm');
        $config->setUserAuthKey('NDEwOGEyYTUtMjQyNi00MDNlLWIwMmQtMzdmYjZmOTc5ZmU1');

        $guzzle = new GuzzleClient([ // http://docs.guzzlephp.org/en/stable/quickstart.html
            // ..config
        ]);

        $client = new HttpClient(new GuzzleAdapter($guzzle), new GuzzleMessageFactory());
        $api = new OneSignal($config, $client);


        $notification = $api->notifications->add([
            'contents' => [
                'en' => $body
            ],
            'included_segments' => ['All'],
            'data' => ['foo' => 'bar'],
//            'isChrome' => true,
            'send_after' => new \DateTime('1 second'),

            // ..other options
        ]);
    }

}