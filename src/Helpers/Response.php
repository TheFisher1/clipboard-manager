<?php

class Response
{
    
    public static function json($data, int $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode($data, JSON_UNESCAPED_UNICODE);

        exit;
    }


    public static function error(string $message, int $status = 400)
    {
        self::json([
            'error' => true,
            'message' => $message
        ], $status);
    }

    public static function success($data = null, int $status = 200)
    {
        $response = [
            'success' => true
        ];
        
        if ($data !== null) {
            if (is_array($data) && isset($data['message'])) {
                $response = array_merge($response, $data);
            } else {
                $response['data'] = $data;
            }
        }
        
        self::json($response, $status);
    }
}
