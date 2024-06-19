<?php

namespace AddressBook\Responses;

class JsonResponse
{
    public static function handle(array $response, int $responseCode = 200): void
    {
        http_response_code($responseCode);
        header('Content-Type: application/json');
        echo json_encode($response);
        die();
    }

    public static function file(array $response, int $responseCode = 200): void
    {
        $jsonContent = json_encode($response);
        http_response_code($responseCode);
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . basename('response.json') . '"');
        header('Content-Length: ' . strlen($jsonContent));

        echo $jsonContent;
        die();
    }
}