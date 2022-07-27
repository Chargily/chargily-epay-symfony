# chargily-epay-symfony
Symfony Plugin for Chargily ePay Gateway

![Chargily ePay Gateway](https://raw.githubusercontent.com/Chargily/epay-gateway-php/main/assets/banner-1544x500.png "Chargily ePay Gateway")

# Installation
1. Via Composer (Recomended)
```bash
composer require chargily/epay-symfony
```

2. Register the bundle, add this line at the end of the file config/bundles.php 
```bash
\Chargily\SymfonyBundle\ChargilySymfonyBundle::class => ['all' => true],
```

3. Import the services, Add the follow line in config/services.yml
```bash
imports:
    - { resource: "@ChargilySymfonyBundle/config/services.yaml" }
```

4. Configure the api keys Add the follow line in config/services.yml
```bash
parameters:
    api_key: "API_KEY"
    secret_key: "SECRET_KEY"
```

5. Process payment and redirection to payment page

```bash
        $payload = array(
            "client" => "test",
            'client_email' => "test@gmail.com",
            "invoice_number" => '123456789',
            "amount" => 110,
            'discount' => 0,
            'mode' => 'CIB',
            'back_url' => "https://test.com",
            'webhook_url' => "https://test.com" . "/" . "webHookSuffixRoute". "/" ."OrderNumber",
            //back_url example when you want to take your host base url
            //'back_url'  => $request->getSchemeAndHttpHost(),
            //webhook_url example when you want to take your host base url and add your suffix route for the webhook
            //'webhook_url' => $request->getSchemeAndHttpHost() . "/" . you_back_url_suffix_here . "/" .Order_Number,
            'comment' => 'My Payment Comment.',
            'api_key' => $this->getParameter('api_key'),
        );

        $chargyliController = new ChargilyEpaySymfonyController();

        $response = $chargyliController->pay($payload);
        $status_code = $response->getStatusCode();
        $response = json_decode($response->getContent());
        if ($status_code == 200) {
            //redirect to chargily payment gateway
            return $this->redirect($response->response);
        } else {
            // This is a error message depending on issue that happen
            dd($status_code . " " . $response->response);
        }

```

6. success Message for the Process payment
```bash
200 => getting redirection link with success => Redirection to url
```

7. Error Message for the Process payment
```bash
400 => There mode must be CIB,EDAHABIA option Only
400 => There amount must be numeric and greather or equal than 75
400 => There is issue \for connecting payment gateway. Sorry \for the inconvenience => with error message
400 => There is missing information in payment parameters
```
8. Webhook Template
```bash
    /**
     * @Route("/chargily/webhook/{OrderNumber}",name="chargily_webhook")
     * @throws \Exception
     */
    public function chargilyWebhook(Request $request)
    {
        //getting your order number
        $number = $request->attributes->get('OrderNumber');

        //part or code for searching your order by number
        /*
         *
         */

        //getting request content
        $data = json_decode($request->getContent(), true);
        $headers = json_decode($request->headers, true);

        $hashedData =  hash_hmac('sha256', json_encode($data) , $this->getParameter('secret_key'));

        if (isset($data) and isset($number)) {
            if($data['invoice']['status'] == 'paid'){

                //part where you update your order status for paid status

                return new JsonResponse([
                    'code' => 200,
                    'message' => 'Update status with success'
                ]);
            }elseif($data['invoice']['status'] == 'failed'){
                //part where you update your order status for failed status

                return new JsonResponse([
                    'code' => 200,
                    'message' => 'Update status with success'
                ]);
            }
            elseif( $data['invoice']['status'] == 'canceled'){
                //part where you update your order status for canceled status
                return new JsonResponse([
                    'code' => 200,
                    'message' => 'Update status with success'
                ]);
            }
        } else {
            return new JsonResponse([
                'code' => 400,
                'message' => 'Update status Failed'
            ]);
        }

    }
```
9. Clear the Cache And Enjoy
```bash
php bin/console cache:clear
```

This Plugin is to integrate ePayment gateway with Chargily easily.
- Currently support payment by **CIB / EDAHABIA** cards and soon by **Visa / Mastercard** 
- This repo is recently created for **Sylius Plugin**, If you are a developer and want to collaborate to the development of this plugin, you are welcomed!

# Contribution tips
1. Make a fork of this repo.
2. Take a tour to our [API documentation here](https://dev.chargily.com/docs/#/epay_integration_via_api)
3. Get your API Key/Secret from [ePay by Chargily](https://epay.chargily.com.dz) dashboard for free.
4. Start developing.
5. Finished? Push and merge.
