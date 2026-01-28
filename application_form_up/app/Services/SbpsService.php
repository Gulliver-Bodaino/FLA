<?php

namespace App\Services;

use Illuminate\Http\Request;

use App\Models\FormASetting;

use Log;

use Http;

class SbpsService
{
    // 3DS認証要求リクエスト
    private $request_3ds_secure = [
        'merchant_id'            => '',
        'service_id'             => '',
        'cust_code'              => '',
        'amount'                 => '',

        'pay_method_info' => [
            'tokenized_pan'      => '',
            'cc_expiration'      => '',
            'security_code'      => '',
        ],

        'pay_option_manage' => [
            'token'              => '',
            'token_key'          => '',
            'tds_info_token'     => '',
            'tds_info_token_key' => '',
            'ok_return_url'      => '',
            'ng_return_url'      => '',
        ],

        'encrypted_flg'          => '',
        'request_date'           => '',
        'limit_second'           => '',
        //'sps_hashcode'         => '',
    ];

    // 決済要求（3DS認証情報付加・認証結果ID指定）リクエスト
    private $request_3ds_settle = [
        'merchant_id'              => '',
        'service_id'               => '',
        'cust_code'                => '',
        'order_id'                 => '',
        'item_id'                  => '',
        'item_name'                => '',
        'tax'                      => '',
        'amount'                   => '',
        'free1'                    => '',
        'free2'                    => '',
        'free3'                    => '',
        'sps_cust_info_return_flg' => '',
        'dtls' => [
        ],
        'pay_method_info' => [
            'dealings_type' => '',
            'divide_times' => '',
        ],
        'pay_option_manage' => [
            'cust_manage_flg'       => '',
            'cardbrand_return_flg'  => '',
            'tds_authentication_id' => '',
        ],
        'encrypted_flg'        => '',
        'request_date'         => '',
        'limit_second'         => '',
//        'sps_hashcode'         => '',
    ];

    // 決済要求リクエスト
    private $tag = [
        'merchant_id'              => '',
        'service_id'               => '',
        'cust_code'                => '',
        'order_id'                 => '',
        'item_id'                  => '',
        'item_name'                => '',
        'tax'                      => '',
        'amount'                   => '',
        'free1'                    => '',
        'free2'                    => '',
        'free3'                    => '',
        'order_rowno'              => '',
        'sps_cust_info_return_flg' => '',
        'dtls' => [
        ],
        'pay_method_info' => [
            'dealings_type' => '',
            'divide_times' => '',
        ],
        'pay_option_manage' => [
            'token'                => '',
            'token_key'            => '',
            'cust_manage_flg'      => '',
            'cardbrand_return_flg' => '',
        ],
        'encrypted_flg'        => '',
        'request_date'         => '',
        'limit_second'         => '',
//        'sps_hashcode'         => '',
    ];
    private $dtl = [
        'dtl_rowno'      => '',
        'dtl_item_id'    => '',
        'dtl_item_name'  => '',
        'dtl_item_count' => '',
        'dtl_tax'        => '',
        'dtl_amount'     => '',
    ];

    // 確定要求リクエスト
    private $request_confirm = [
        'merchant_id'         => '',
        'service_id'          => '',
        'sps_transaction_id'  => '',
        'tracking_id'         => '',
        'processing_datetime' => '',
        'request_date'        => '',
        'limit_second'        => '',
//        'sps_hashcode'        => '',
    ];

    // 3DS認証要求
    public function request3dsSecure(Request $request, $params)
    {
        $this->request_3ds_secure['merchant_id'] = env('SBPS_MERCHANT_ID');
        $this->request_3ds_secure['service_id']  = env('SBPS_SERVICE_ID');
        $this->request_3ds_secure['cust_code']   = env('SBPS_MERCHANT_ID') . env('SBPS_SERVICE_ID') . '-' . uniqid();

        $this->request_3ds_secure['amount'] = $params['amount'];

        $this->request_3ds_secure['pay_option_manage']['token'] = $request->token;
        $this->request_3ds_secure['pay_option_manage']['token_key'] = $request->tokenKey;
        $this->request_3ds_secure['pay_option_manage']['tds_info_token'] = $request->tds2infoToken;
        $this->request_3ds_secure['pay_option_manage']['tds_info_token_key'] = $request->tds2infoTokenKey;
        $this->request_3ds_secure['pay_option_manage']['ok_return_url'] = $params['ok_return_url'];
        $this->request_3ds_secure['pay_option_manage']['ng_return_url'] = $params['ng_return_url'];

        $this->request_3ds_secure['request_date'] = now()->format('YmdHis');

        $values = [];

        $xml  = '<?xml version="1.0" encoding="Shift_JIS"?>' . "\n";
        $xml .= '<sps-api-request id="TA02-00101-101">' . "\n";
        foreach ($this->request_3ds_secure as $tag1 => $element1) {
            if (is_array($element1)) {
                $xml .= "<$tag1>\n";
                foreach ($element1 as $tag2 => $element2) {
                    $values[] = $this->sjis($element2);
                    $xml .= "<$tag2>" . $this->encode($tag2, $element2) . "</$tag2>\n";
                }
                $xml .= "</$tag1>\n";
            } else {
                $values[] = $this->sjis($element1);
                $xml .= "<$tag1>" . $this->encode($tag1, $element1) . "</$tag1>\n";
            }
        }

        $values[] = $this->sjis(env('SBPS_HASHKEY'));
        $sps_hashcode = sha1(implode('', $values));

        $xml .= "<sps_hashcode>$sps_hashcode</sps_hashcode>\n";
        $xml .= '</sps-api-request>';

        $simpleXml = $this->post($xml);

        if (!is_object($simpleXml)) {
            return false;
        }

        $res_result = (string) $simpleXml->res_result;
        $res_date = (string) $simpleXml->res_date;

        $response = [
            'res_result'                 => $res_result,
            'res_sps_transaction_id'     => '',
            'res_tds_authentication_id'  => '',
            'redirect_url'               => '',

            'res_process_date' => '',
            'res_err_code'     => '',
            'res_date'         => $res_date,
        ];

        if ($res_result === 'OK') {
            $response['res_sps_transaction_id']    = (string) $simpleXml->res_sps_transaction_id;
            $response['res_tds_authentication_id'] = (string) $simpleXml->res_tds_authentication_id;
            $response['redirect_url']              = (string) $simpleXml->redirect_url;
            $response['res_process_date']          = (string) $simpleXml->res_process_date;
        }
        if ($res_result === 'NG') {
            $response['res_err_code'] = (string) $simpleXml->res_err_code;
        }

        return $response;
    }

    // 
    private function post($xml)
    {
        Log::debug($xml);

        $id = env('SBPS_MERCHANT_ID') . env('SBPS_SERVICE_ID');
        $password = env('SBPS_HASHKEY');
        $response = Http::withBasicAuth($id, $password)->withBody($xml, 'text/xml')->post(env('SBPS_API_URL'));

        $body = $response->body();
        Log::debug($body);

        return simplexml_load_string($body);
    }

    // 決済要求（3DS認証情報付加・認証結果ID指定）
    public function request3dsSettlement($params)
    {
        $this->request_3ds_settle['merchant_id'] = env('SBPS_MERCHANT_ID');
        $this->request_3ds_settle['service_id']  = env('SBPS_SERVICE_ID');
        $this->request_3ds_settle['cust_code']   = env('SBPS_MERCHANT_ID') . env('SBPS_SERVICE_ID') . '-' . uniqid();
        $this->request_3ds_settle['order_id']    = env('SBPS_MERCHANT_ID') . env('SBPS_SERVICE_ID') . '-' . uniqid();

        $this->request_3ds_settle['item_id']   = $params['item_id'];
        $this->request_3ds_settle['item_name'] = $params['item_name'];

        $this->request_3ds_settle['tax'] = $params['tax'];
        $this->request_3ds_settle['amount'] = $params['amount'];

        $this->request_3ds_settle['pay_option_manage']['tds_authentication_id'] = $params['tds_authentication_id'];

        $this->request_3ds_settle['request_date'] = now()->format('YmdHis');

        $values = [];

        $xml  = '<?xml version="1.0" encoding="Shift_JIS"?>' . "\n";
        $xml .= '<sps-api-request id="ST01-00118-101">' . "\n";
        foreach ($this->request_3ds_settle as $tag1 => $element1) {
            // dtls要素
            if ($tag1 === 'dtls') {
                if (count($element1) === 0) continue;
                $xml .= "<$tag1>\n";
                foreach ($element1 as $dtl) {
                    $xml .= "<dtl>\n";
                    foreach ($dtl as $key => $value) {
                        $values[] = $this->sjis($value);
                        $xml .= "<$key>" . $this->encode($key, $value) . "</$key>\n";
                    }
                    $xml .= "<dtl>\n";
                }
                $xml .= "</$tag1>\n";
                continue;
            }

            if (is_array($element1)) {
                $xml .= "<$tag1>\n";
                foreach ($element1 as $tag2 => $element2) {
                    $values[] = $this->sjis($element2);
                    $xml .= "<$tag2>" . $this->encode($tag2, $element2) . "</$tag2>\n";
                }
                $xml .= "</$tag1>\n";
            } else {
                $values[] = $this->sjis($element1);
                $xml .= "<$tag1>" . $this->encode($tag1, $element1) . "</$tag1>\n";
            }
        }

        $values[] = $this->sjis(env('SBPS_HASHKEY'));
        $sps_hashcode = sha1(implode('', $values));

        $xml .= "<sps_hashcode>$sps_hashcode</sps_hashcode>\n";
        $xml .= '</sps-api-request>';

        $simpleXml = $this->post($xml);

        if (!is_object($simpleXml)) {
            return false;
        }

        $res_result = (string) $simpleXml->res_result;
        $res_date = (string) $simpleXml->res_date;

        $response = [
            'res_result'             => $res_result,
            'res_sps_transaction_id' => '',
            'res_tracking_id'        => '',

            'res_process_date' => '',
            'res_err_code'     => '',
            'res_date'         => $res_date,
        ];

        if ($res_result === 'OK') {
            $response['res_sps_transaction_id'] = (string) $simpleXml->res_sps_transaction_id;
            $response['res_tracking_id']        = (string) $simpleXml->res_tracking_id;
            $response['res_process_date']       = (string) $simpleXml->res_process_date;
        }
        if ($res_result === 'NG') {
            $response['res_err_code'] = (string) $simpleXml->res_err_code;
        }

        return $response;
    }

    // 確定要求
    public function requestConfirm($params)
    {
        $this->confirm_request['merchant_id']        = env('SBPS_MERCHANT_ID');
        $this->confirm_request['service_id']         = env('SBPS_SERVICE_ID');
        $this->confirm_request['sps_transaction_id'] = $params['sps_transaction_id'];
        $this->confirm_request['tracking_id']        = $params['tracking_id'];
        $this->confirm_request['request_date']       = now()->format('YmdHis');

        $values = [];

        $xml  = '<?xml version="1.0" encoding="Shift_JIS"?>' . "\n";
        $xml .= '<sps-api-request id="ST02-00101-101">' . "\n";
        foreach ($this->confirm_request as $tag => $element) {
            $values[] = $this->sjis($element);
            $xml .= "<$tag>" . $this->encode($tag, $element) . "</$tag>\n";
        }

        $values[] = $this->sjis(env('SBPS_HASHKEY'));
        $sps_hashcode = sha1(implode('', $values));

        $xml .= "<sps_hashcode>$sps_hashcode</sps_hashcode>\n";
        $xml .= '</sps-api-request>';

        $simpleXml = $this->post($xml);

        if (!is_object($simpleXml)) {
            return false;
        }

        $res_result = (string) $simpleXml->res_result;
        $res_date = (string) $simpleXml->res_date;

        $response = [
            'res_result'             => $res_result,
            'res_sps_transaction_id' => '',

            'res_process_date' => '',
            'res_err_code'     => '',
            'res_date'         => $res_date,
        ];

        if ($res_result === 'OK') {
            $response['res_sps_transaction_id'] = (string) $simpleXml->res_sps_transaction_id;
            $response['res_process_date']       = (string) $simpleXml->res_process_date;
        }
        if ($res_result === 'NG') {
            $response['res_err_code'] = (string) $simpleXml->res_err_code;
        }

        return $response;

    }








    // 決済要求
    public function requestSettlement(Request $request, $params)
    {

        $this->tag['merchant_id'] = env('SBPS_MERCHANT_ID');
        $this->tag['service_id']  = env('SBPS_SERVICE_ID');
        $this->tag['cust_code']   = env('SBPS_MERCHANT_ID') . env('SBPS_SERVICE_ID') . '-' . uniqid();
        $this->tag['order_id']    = env('SBPS_MERCHANT_ID') . env('SBPS_SERVICE_ID') . '-' . uniqid();

        $this->tag['item_id']   = $params['item_id'];
        $this->tag['item_name'] = $params['item_name'];

        $this->tag['tax'] = $params['tax'];
        $this->tag['amount'] = $params['amount'];

        $this->tag['pay_option_manage']['token'] = $request->token;
        $this->tag['pay_option_manage']['token_key'] = $request->tokenKey;

        $this->tag['request_date'] = now()->format('YmdHis');

        $values = [];

        $xml  = '<?xml version="1.0" encoding="Shift_JIS"?>' . "\n";
        $xml .= '<sps-api-request id="ST01-00131-101">' . "\n";
        foreach ($this->tag as $tag1 => $element1) {
            // dtls要素
            if ($tag1 === 'dtls') {
                if (count($element1) === 0) continue;
                $xml .= "<$tag1>\n";
                foreach ($element1 as $dtl) {
                    $xml .= "<dtl>\n";
                    foreach ($dtl as $key => $value) {
                        $values[] = $this->sjis($value);
                        $xml .= "<$key>" . $this->encode($key, $value) . "</$key>\n";
                    }
                    $xml .= "<dtl>\n";
                }
                $xml .= "</$tag1>\n";
                continue;
            }

            if (is_array($element1)) {
                $xml .= "<$tag1>\n";
                foreach ($element1 as $tag2 => $element2) {
                    $values[] = $this->sjis($element2);
                    $xml .= "<$tag2>" . $this->encode($tag2, $element2) . "</$tag2>\n";
                }
                $xml .= "</$tag1>\n";
            } else {
                $values[] = $this->sjis($element1);
                $xml .= "<$tag1>" . $this->encode($tag1, $element1) . "</$tag1>\n";
            }
        }

        $values[] = $this->sjis(env('SBPS_HASHKEY'));
        $sps_hashcode = sha1(implode('', $values));

        $xml .= "<sps_hashcode>$sps_hashcode</sps_hashcode>\n";

        $xml .= '</sps-api-request>';
        Log::debug($xml);


        $id = env('SBPS_MERCHANT_ID') . env('SBPS_SERVICE_ID');
        $password = env('SBPS_HASHKEY');
        $response = Http::withBasicAuth($id, $password)->withBody($xml, 'text/xml')->post(env('SBPS_API_URL'));

        $body = $response->body();
        Log::debug($body);

        $simpleXml = simplexml_load_string($body);

        if (!is_object($simpleXml)) {
            return ['res_result' => 'ERROR'];
        }


        $res_result = (string) $simpleXml->res_result;
        $res_date = (string) $simpleXml->res_date;

        $response = [
            'res_result'             => $res_result,
            'res_sps_transaction_id' => '',
            'res_tracking_id'        => '',

            'res_process_date' => '',
            'res_err_code'     => '',
            'res_date'         => $res_date,
        ];

        if ($res_result === 'OK') {
            $response['res_sps_transaction_id'] = (string) $simpleXml->res_sps_transaction_id;
            $response['res_tracking_id']        = (string) $simpleXml->res_tracking_id;
            $response['res_process_date']       = (string) $simpleXml->res_process_date;
        }
        if ($res_result === 'NG') {
            $response['res_err_code'] = (string) $simpleXml->res_err_code;
        }

        return $response;
    }

    private function sjis($value)
    {
        return mb_convert_encoding(trim($value), 'Shift_JIS', 'UTF-8');
    }

    private function encode($key, $value)
    {
        $value = $this->sjis($value);
        if (in_array($key, ['item_name', 'free1', 'free2', 'free3', 'dtl_item_name'])) {
            return base64_encode($value);
        }
        return $value;
    }

    // 確定要求
    public function confirmSettlement(Request $request)
    {
        $this->confirm_request['merchant_id']        = env('SBPS_MERCHANT_ID');
        $this->confirm_request['service_id']         = env('SBPS_SERVICE_ID');
        $this->confirm_request['sps_transaction_id'] = $request->res_sps_transaction_id;
        $this->confirm_request['tracking_id']        = $request->res_tracking_id;
        $this->confirm_request['request_date']       = now()->format('YmdHis');

        $values = [];

        $xml  = '<?xml version="1.0" encoding="Shift_JIS"?>' . "\n";
        $xml .= '<sps-api-request id="ST02-00101-101">' . "\n";
        foreach ($this->confirm_request as $tag => $element) {
            $values[] = $this->sjis($element);
            $xml .= "<$tag>" . $this->encode($tag, $element) . "</$tag>\n";
        }

        $values[] = $this->sjis(env('SBPS_HASHKEY'));
        $sps_hashcode = sha1(implode('', $values));

        $xml .= "<sps_hashcode>$sps_hashcode</sps_hashcode>\n";

        $xml .= '</sps-api-request>';
        Log::debug($xml);

        $id = env('SBPS_MERCHANT_ID') . env('SBPS_SERVICE_ID');
        $password = env('SBPS_HASHKEY');
        $response = Http::withBasicAuth($id, $password)->withBody($xml, 'text/xml')->post(env('SBPS_API_URL'));

        $body = $response->body();
        Log::debug($body);

        $simpleXml = simplexml_load_string($body);

        if (!is_object($simpleXml)) {
            return ['res_result' => 'ERROR'];
        }


        $res_result = (string) $simpleXml->res_result;
        $res_date = (string) $simpleXml->res_date;

        $response = [
            'res_result'             => $res_result,
            'res_sps_transaction_id' => '',

            'res_process_date' => '',
            'res_err_code'     => '',
            'res_date'         => $res_date,
        ];

        if ($res_result === 'OK') {
            $response['res_sps_transaction_id'] = (string) $simpleXml->res_sps_transaction_id;
            $response['res_process_date']       = (string) $simpleXml->res_process_date;
        }
        if ($res_result === 'NG') {
            $response['res_err_code'] = (string) $simpleXml->res_err_code;
        }

        return $response;

    }
}
