<?php 

namespace api\models\backoffice;

use api\exceptions\BadGatewayHttpException;
use GuzzleHttp\Client as Guzzle;
use api\models\User;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use yii\web\ForbiddenHttpException;

class Api 
{
    const BASE_URL = 'https://apiex.cartorionobrasil.com.br';

    const API_RETURN_OK = 'ok';
    const API_RETURN_ERROR = 'erro';

    private $email;
    private $password;
    private $key;
    private $client;

    private $guzzle;

    function __construct($email, $password) 
    {
        if (empty($email) || empty($password)) {
            throw new ForbiddenHttpException('Customer doesn\'t have autorization to connect to the backoffice');
        }

        $this->email = $email;
        $this->password = $password;
        $this->guzzle = new Guzzle([
            'headers' => ['Content-Type' => 'application/json'],
            'timeout' => 10,
            'http_errors' => false
        ]);
    }

    private function url($endpoint, $params = [])
    {
        $url = trim($endpoint, '/');
        
        if (!empty($params)) {
            $url .= '/' . http_build_query($params);
        }

        return self::BASE_URL . '/' . $url;
    }

    private function needsLogin()
    {
        if (empty($this->key) || empty($this->client)) {
            return true;
        }

        return false;
    }

    public function post($endpoint, $body = [])
    {

    }

    public function get($endpoint, $params = [])
    {
        if ($this->needsLogin()) {
            $this->login();
        }
        
        $url = $this->url($endpoint, $params);
        $response = $this->guzzle->get($url);

        $response = json_decode($response->getBody());

        return $response;
    }

    private function login()
    {
        try {
            $url = $this->url('/login');
            $response = $this->guzzle->post($url, [
                'form_params' => [
                    'EMAIL' => $this->email,
                    'PASS' => $this->password
                ]
            ]);

            $response = json_decode($response->getBody());

            echo "<pre>";
            print_r($response);
            echo "</pre>";
            exit();

            if ($response->return == self::API_RETURN_OK) {
                $this->key = $response->key;
                $this->client = $response->client;
            } elseif ($response->return == self::API_RETURN_ERROR) {
                throw new BadGatewayHttpException($response->msg);
            }
        } catch (RequestException $e) {
            throw new BadGatewayHttpException('Error while reaching the backoffice API', 0, $e);
        }
    }

    public function getCities($uf) 
    {
        $response = $this->get('/' . $uf);

        return $response;
    }

    public function getNotariesOffices($uf) 
    {
        return $this->get('/' . $uf);
    }

    public function insertCredits()
    {
        return true;
    }

    private function debug()
    {
        // Grab the client's handler instance.
        $clientHandler = $this->guzzle->getConfig('handler');
        // Create a middleware that echoes parts of the request.
        $tapMiddleware = Middleware::tap(function ($request) {
            echo $request->getHeaderLine('Content-Type');
            echo $request->getBody();
        });

        $response = $this->guzzle->request('PUT', '/login', [
            'json'    => ['EMAIL' => $this->email,
            'PASS' => $this->password],
            'handler' => $tapMiddleware($clientHandler)
        ]);
    }

    private function curlLogin()
    {
        $ch = curl_init('https://apiex.cartorionobrasil.com.br/login');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type' => 'application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'EMAIL' => $this->email,
            'PASS' => $this->password
        ]);
        $response = curl_exec($ch);

        curl_close($ch);

        // do anything you want with your response
        $response = json_decode($response);

        if ($response->return == self::API_RETURN_OK) {
            $this->key = $response->Key;
            $this->client = $response->Client;
        } elseif ($response->return == self::API_RETURN_ERROR) {
            throw new BadGatewayHttpException($response->msg);
        }
        exit();
    }
}