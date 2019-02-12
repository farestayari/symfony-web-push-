<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Notification;
use App\Entity\User;
use App\Form\MessageType;
use App\Form\NotificationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\NotificationService;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param NotificationService $notification
     */
    public function index(NotificationService $notification)
    {
//       $notification->sendMessage();
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/send" , name="send")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showSendNotification(Request $request, NotificationService $notificationService )
    {

//        $notificationService->test($em, $body);

        $notification = new Notification();

        $form = $this->createForm(NotificationType::class, $notification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($notification);
            $em->flush();
            $notificationService->sendMessage();
        }
        return $this->render('home/send.html.twig',[
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/subscribe" ,  name="subscribe")
     * @param Request $request
     */
    public function subscribe(Request $request)
    {
        $user = $this->getUser();
        $playerId = $user->getplayerId();
        if ($user and !$playerId){
            $user->setplayerId($request->request->get('userId'));
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        return new JsonResponse(true);
    }


    /**
     * @Route("/users" , name="users")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listUsers(Request $request, EntityManagerInterface $em , NotificationService $notificationService)
    {
        $notification = new Message();
        $message = ['message' => 'this is a message'];
        $form = $this->createFormBuilder($message, [])
            ->add('message', TextType::class)
            ->add('user_id' , HiddenType::class)
            ->add('save', SubmitType::class, [
                'label' => 'Envoyer',
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data =  $request->request->get('form');
            $userId = $data['user_id'];
            $message = $data['message'];
            $notification->setBody($message);
            $notification->setUserId($userId);
            $em = $this->getDoctrine()->getManager();
            $em->persist($notification);
            $em->flush();
            $user = $em->getRepository(User::class)->find($userId);
            $playerId = $user->getPlayerId();
            $notificationService->sendToUser($user , $notification);
        }

        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        return $this->render('home/users.html.twig',[
            'users'=>$users,
            'form' =>$form->createView()
        ]);

    }

    /**
     * @Route("/admin" , name="admin")
     */
    public function showAdmin()
    {
        return $this->render('home/admin.html.twig');
    }
}
