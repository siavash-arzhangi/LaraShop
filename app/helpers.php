<?php

use Morilog\Jalali\Jalalian;
use Alkoumi\LaravelHijriDate\Hijri;

function uuid(string $type) {
    $data = openssl_random_pseudo_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    return $type . '-' . $uuid;
}

function responseCode(string $code) {
    $response = array();
    switch ($code) {
        case '200':
        case 'true':
            $response['message'] = 'success';
            $response['code'] = '200';
            break;
        case '401':
            $response['message'] = 'unauthorised access';
            $response['code'] = '401';
            break;
        case '403':
        case 'false':
            $response['message'] = 'forbidden';
            $response['code'] = '403';
            break;
        case '404':
            $response['message'] = 'not found';
            $response['code'] = '404';
            break;
        case '500':
            $response['message'] = 'server error';
            $response['code'] = '500';
            break;
                
        default:
            # code...
            break;
    }

    return $response;
}

function pagination(array $request, string $type) {
    if (isset($request['limit'])) {
        return $request['limit'];
    }else {
        return config("app.pagination.$type");
    }
}

function firstMember($item) {
    return $item[0];
}

function isAdmin($auth) {
    switch ($auth->role) {
        case config("app.auth.roles.admin.role"): return true;
        default: return false;
    }
}

function dateConvert(string $date, string $type = null) {
    if (!$type)
        $type = config("app.dates");

    switch ($type) {
        case 'AD': return $date;
        case 'SH': return Jalalian::fromDateTime('2022-09-07 12:01')->format('Y/m/d H:i');
        case 'AH': return Hijri::Date('Y/m/d H:i', '2022-09-07 12:01');
        default: return $date;
    }
}

function isNull($data) {
    switch (gettype($data)) {
        case 'object':
        case 'array': if (count($data) > 0) return false;
        case 'string': if (strlen($data) > 0) return false;
        case 'integer': if ($data > 0) return false;
        default: return true;
    }
    return true;
}

function payDesc(string $invoice_id, string $user_name) {
    return "Invoice id $invoice_id is paid by $user_name";
}