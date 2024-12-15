<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use URL;
use App\Models\TempOrder;

class PathaoController extends Controller
{
    private $base_url;
    public function __construct()
    {
        if(env('PATHAO_SANDBOX_MODE')==1){
            $this->base_url = "https://courier-api-sandbox.pathao.com";
        } else {
            $this->base_url = "https://api-hermes.pathao.com";
        }
    }

    public function init($data){

        $data['store_id'] = 29894;
        $data['sender_name'] = env('APP_NAME');
        $data['sender_phone'] = '01768305720';
        $data['delivery_type'] = 48;
        $data['item_type'] = 2;
        $data['order_type'] ='bulk';

        return $this->create($data);
    }


    public function getToken(){
        $header = array(
                'accept:application/json',
                'content-Type:application/json',
                );

        $request_data = array(
            'client_id'=> env('PATHAO_CLIENT_ID'),
            'client_secret'=>env('PATHAO_CLIENT_SECRET'),
            'username'=>env('PATHAO_CLIENT_EMAIL'),
            'password'=>env('PATHAO_CLIENT_PASSWORD'),
            'grant_type'=>'password'
        );

        $request_data_json=json_encode($request_data);

        $url = curl_init($this->base_url.'/aladdin/api/v1/issue-token');
        curl_setopt($url,CURLOPT_HTTPHEADER, $header);
        curl_setopt($url,CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url,CURLOPT_POSTFIELDS, $request_data_json);
        curl_setopt($url,CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $resultdata = curl_exec($url);
        curl_close($url);

        //dd($resultdata);

        $access_token = json_decode($resultdata)->access_token;
        $refresh_token = json_decode($resultdata)->refresh_token;
        $data['access_token'] = $access_token;
        $data['refresh_token'] = $refresh_token;

        //*Issue refresh token**
        $header = array(
                'accept:application/json',
                'content-Type:application/json',
                );

        $request_data = array(
            'client_id'=> env('PATHAO_CLIENT_ID'),
            'client_secret'=>env('PATHAO_CLIENT_SECRET'),
            'refresh_token'=>$data['refresh_token'],
            'grant_type'=>'refresh_token'
        );

        $request_data_json=json_encode($request_data);

        $url = curl_init($this->base_url.'/aladdin/api/v1/issue-token');
        curl_setopt($url,CURLOPT_HTTPHEADER, $header);
        curl_setopt($url,CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url,CURLOPT_POSTFIELDS, $request_data_json);
        curl_setopt($url,CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $resultdata = curl_exec($url);
        curl_close($url);
        $access_token = json_decode($resultdata)->access_token;
        $refresh_token = json_decode($resultdata)->refresh_token;
        $data['access_token'] = $access_token;
        $data['refresh_token'] = $refresh_token;

        // dd($resultdata);
        return $data;
    }


    public function create($data){
        //dd($data);
        $tokenData = $this->getToken();
        $data['access_token'] = $tokenData['access_token'];
        $data['refresh_token'] = $tokenData['refresh_token'];

        $requestbody = array();

        if($data['order_type']=='bulk'){
            $orders = $data['orders'];
            $requests = [];
            for($i=0; $i<sizeof($orders); $i++){
                $request = array(
                    'store_id' => intval($data['store_id']),
                    'merchant_order_id' => $orders[$i]['merchant_order_id'],
                    'recipient_name' => $orders[$i]['recipient_name'],
                    'recipient_phone' => $orders[$i]['recipient_phone'],
                    'recipient_address' => $orders[$i]['recipient_address'],
                    'recipient_city' => intval($orders[$i]['recipient_city']),
                    'recipient_zone' => intval($orders[$i]['recipient_zone']),
                    'recipient_area' => intval($orders[$i]['recipient_area']),
                    'delivery_type' => intval($data['delivery_type']),
                    'item_type' => intval($data['item_type']),
                    'item_quantity' => intval($orders[$i]['item_quantity']),
                    'item_weight' => $orders[$i]['item_weight'],
                    'amount_to_collect' => intval($orders[$i]['amount_to_collect']),
                    'item_description' => 'nothing',
                    'special_instruction' => 'nothing',
                );

                array_push($requests, $request);
            }

            $requestbody = array(
                'orders' => $requests,
            );
            // dd(' requestbody', $requestbody);
        }else{
            $requestbody = array(
                'store_id' => $data['store_id'],
                'merchant_order_id' => $data['merchant_order_id'],
                'recipient_name' => $data['recipient_name'],
                'recipient_phone' => $data['recipient_phone'],
                'recipient_address' => $data['recipient_address'],
                'recipient_city' => $data['recipient_city'],
                'recipient_zone' => $data['recipient_zone'],
                'delivery_type' => $data['delivery_type'],
                'item_type' => $data['item_quantity'],
                'item_quantity' => $data['item_quantity'],
                'item_weight' => $data['item_weight'],
                'amount_to_collect' => intval($data['amount_to_collect']),
                'item_description' => 'nothing',
                'special_instruction' => 'nothing',
            );
        }
        $requestbodyJson = json_encode($requestbody);
        //dd('requestbodyJson',$requestbodyJson);

        $header = array(
            'Authorization:Bearer '. $data['access_token'],
            'Content-Type:application/json',
            'Accept:application/json'
        );

        if($data['order_type']=='bulk'){
            $url = curl_init($this->base_url.'/aladdin/api/v1/orders/bulk');
            //dd($url);
        }else{
            $url = curl_init($this->base_url.'/aladdin/api/v1/orders');
        }

        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $requestbodyJson);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $resultdata = curl_exec($url);
        curl_close($url);

        //dd(json_decode($resultdata));

        //return redirect((json_decode($resultdata)->bkashURL));

        return json_decode($resultdata);
    }


    public function getCities()
    {
        $tokenData = $this->getToken();
        $data['access_token'] = $tokenData['access_token'];
        $data['refresh_token'] = $tokenData['refresh_token'];

        $header = array(
            'Authorization:Bearer '. $data['access_token'],
            'Content-Type:application/json',
            'Accept:application/json'
        );

        $url = curl_init($this->base_url.'/aladdin/api/v1/countries/1/city-list');

        curl_setopt($url,CURLOPT_HTTPHEADER, $header);
        curl_setopt($url,CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($url,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url,CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $resultdata = curl_exec($url);
        curl_close($url);

        //dd($resultdata);
        return json_decode($resultdata);
    }


    public function getZones($cityId)
    {
        $tokenData = $this->getToken();
        $data['access_token'] = $tokenData['access_token'];
        $data['refresh_token'] = $tokenData['refresh_token'];

        $header = array(
            'Authorization:Bearer '. $data['access_token'],
            'Content-Type:application/json',
            'Accept:application/json'
        );

        $url = '/aladdin/api/v1/cities/'.$cityId.'/zone-list';
        $url = curl_init($this->base_url.$url);

        curl_setopt($url,CURLOPT_HTTPHEADER, $header);
        curl_setopt($url,CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($url,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url,CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $resultdata = curl_exec($url);
        curl_close($url);

        //dd($resultdata);
        return json_decode($resultdata);
    }


    public function getAreas($zoneId)
    {
        $tokenData = $this->getToken();
        $data['access_token'] = $tokenData['access_token'];
        $data['refresh_token'] = $tokenData['refresh_token'];

        $header = array(
            'Authorization:Bearer '. $data['access_token'],
            'Content-Type:application/json',
            'Accept:application/json'
        );

        $url = '/aladdin/api/v1/zones/'.$zoneId.'/area-list';
        $url = curl_init($this->base_url.$url);

        curl_setopt($url,CURLOPT_HTTPHEADER, $header);
        curl_setopt($url,CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($url,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url,CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $resultdata = curl_exec($url);
        curl_close($url);

        //dd($resultdata);
        return json_decode($resultdata);
    }
}
