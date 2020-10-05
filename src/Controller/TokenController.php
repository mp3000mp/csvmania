<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Translation\TranslatorInterface;

use App\Entity\Token;

class TokenController extends Controller
{

    /**
     * @Route("ajax/token/edit", name="token_edit")
     * @return Response
     */
    public function editAction(Request $request, TranslatorInterface $translator)
    {
        $r = ['error'=>''];
        $r['value'] = 0;

        // liste des propriété qu'on accepte de modifier
        $listEnableProp = ['notif1', 'notif1_admin'];

        // get request
        $id = $request->request->get('id');
        $value = $request->request->get('value');
        $prop = $request->request->get('prop');

        // if authorised
        if(in_array($prop,$listEnableProp)){
            // database ope
            $em = $this->getDoctrine()->getManager();
            $token = $this->getDoctrine()
                ->getRepository('App:Token')
                ->findOneBy(array('id' => $id));

            if($prop == 'notif1'){
                $r['action'] = 'notif1';
                if($value != '' && $token->getNotif1() == ''){
                    $r['value'] = 1;
                    $token->setNotif1($value);
                    $token->setDoneAt(new \DateTime());
                }elseif($value == '' && $token->getNotif1() != ''){
                    $r['value'] = -1;
                    $token->setNotif1(null);
                    $token->setDoneAt(null);
                }

            }elseif($prop == 'notif1_admin'){
                $r['action'] = 'notif1_admin';
                if($value != '' && $token->getNotif1Admin() == ''){
                    $r['value'] = 1;
                    $token->setNotif1Admin($value);
                    $token->setValidatedAt(new \DateTime());
                }elseif($value == '' && $token->getNotif1Admin() != ''){
                    $r['value'] = -1;
                    $token->setNotif1Admin(null);
                    $token->setValidatedAt(null);
                }
            }

            $em->persist($token);
            $em->flush();

        }else{
            $r['error'] = $translator->trans('form.unauthorized');
        }

        $response = new Response(json_encode($r));
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }

}