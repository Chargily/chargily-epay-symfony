<?php

namespace Chargily\SymfonyBundle\Controller;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Payum\Core\Reply\HttpRedirect;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ChargilyEpaySymfonyController extends AbstractController
{
    /*
        public function __construct(ContainerInterface $container)
        {
            $this->setContainer($container);
        }
    */
    /**
     * @Route("/chargily/pay",name="chargily_epay_symfony")
     * @throws \Exception
     */
    public function pay(array $payload)
    {
        if(!isset($payload['mode']) or ($payload['mode'] != 'CIB' and $payload['mode'] != 'EDAHABIA')){
            return new JsonResponse(['response' => 'There mode must be CIB,EDAHABIA option Only'], 400);
        }

        if(!isset($payload['amount']) or $payload['amount'] < 75){
            return new JsonResponse(['response' => 'There amount must be numeric and greather or equal than 75'], 400);
        }

        if (isset($payload['invoice_number'])
            && isset($payload['amount'])
            && isset($payload['discount'])
            && isset($payload['back_url'])
            && isset($payload['api_key'])) {

            $environment_url = 'http://epay.chargily.com.dz/api/invoice';

            $client = new Client();
            $client = new \GuzzleHttp\Client(['verify' => false]);

            try {
                $response = $client->post($environment_url, [
                    RequestOptions::HEADERS => [
                        "X-Authorization" => $payload['api_key'],
                        'Accept' => 'application/json'
                    ],
                    RequestOptions::FORM_PARAMS => $payload,
                    RequestOptions::TIMEOUT => 90
                ]);

                $response = json_decode($response->getBody()->getContents(), true);
                $redirectUrl = $response['checkout_url'];
                return new JsonResponse(['response' => $redirectUrl], 200);
            } catch (\Exception $exception) {
                return new JsonResponse(['response' => "There is issue for connecting payment gateway. Sorry for the inconvenience. {$exception->getMessage()}"], 400,);
            } catch (GuzzleException $e) {
                return new JsonResponse(['response' => "There is issue for connecting payment gateway. Sorry for the inconvenience. {$e->getMessage()}"], 400,);
            }
        } else {
            return new JsonResponse(['response' => 'There is missing information in payment parameters'], 400);
        }
    }
}
