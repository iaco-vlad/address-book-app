<?php

namespace AddressBook\Responses;

use Exception;
use SimpleXMLElement;

class XmlResponse
{
    /**
     * @throws Exception
     */
    public static function file(array $data, string $rootElement, int $responseCode = 200): void
    {
        $xmlContent = self::getXml($data, $rootElement);

        http_response_code($responseCode);
        header('Content-Type: application/xml');
        header('Content-Disposition: attachment; filename="' . basename('response.xml') . '"');
        header('Content-Length: ' . strlen($xmlContent));

        echo $xmlContent;
        die();
    }

    /**
     * @throws Exception
     */
    public static function handle(array $data, string $rootElement, int $responseCode = 200): void
    {
        http_response_code($responseCode);
        header('Content-Type: application/xml');

        echo self::getXml($data, $rootElement);
        die();
    }

    private static function getXml(array $data, string $rootElement)
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><' . $rootElement . '/>');

        foreach ($data as $item) {
            $itemXml = $xml->addChild(strtolower(basename(str_replace('\\', '/', get_class($item)))));
            self::buildXmlFromArray($itemXml, $item);
        }

        return $xml->asXML();
    }

    private static function buildXmlFromArray(SimpleXMLElement $xml, $data): void
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $childXml = $xml->addChild($key);
                self::buildXmlFromArray($childXml, $value);
            } else {
                $xml->addChild($key, htmlspecialchars($value));
            }
        }
    }
}