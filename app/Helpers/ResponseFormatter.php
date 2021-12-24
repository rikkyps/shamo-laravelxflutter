<?php

namespace App\Helpers;

class ResponseFormatter {

  protected static $reponse = [
    'meta' => [
      'code' => 200,
      'status' => 'success',
      'message' => null
    ],
    'data' => null
  ];

  public static function success($data = null, $message = null)
  {
    self::$reponse['meta']['message'] = $message;
    self::$reponse['data'] = $data;

    return response()->json(self::$reponse, self::$reponse['meta']['code']);
  }

  public static function error($data = null, $message = null, $code = 400)
  {
    self::$reponse['meta']['status'] = 'error';
    self::$reponse['meta']['code'] = $code;
    self::$reponse['meta']['message'] = $message;
    self::$reponse['data'] = $data;

    return response()->json(self::$reponse, self::$reponse['meta']['code']);
  }
}