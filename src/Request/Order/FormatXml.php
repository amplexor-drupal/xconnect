<?php

namespace Amplexor\XConnect\Request\Order;

use Amplexor\XConnect\Request\Order;
use Amplexor\XConnect\Request\Order\FormatInterface;

/**
 * Format to generate the XML code representing the Order object.
 *
 * @package Amplexor\XConnect
 */
class FormatXml implements FormatInterface
{
    /**
     * The XML wrapper.
     *
     * @var string
     */
    private $xmlWrapper = <<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<ClientWoRequest xmlns:tns="http://www.euroscript.com/escaepe/types"
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xsi:schemaLocation="http://www.euroscript.com/escaepe/types clientOrderRequestTypes.xsd">
</ClientWoRequest>
EOL;


    /**
     * @inheritDoc
     */
    public function format(Order $order)
    {
        $data = $this->extractData($order);
        $xml = $this->createXml($data);
        $xmlString = $xml->asXML();

        // We have to add the tns namespace.
        $xmlString = str_replace(
            'ClientWoRequest',
            'tns:ClientWoRequest',
            $xmlString
        );

        return $xmlString;
    }

    /**
     * Extract the data as an array from the Order.
     *
     * @param Order $order
     *   The Order to extract the data from.
     *
     * @return array
     *   The extracted data in the same structure as the XML will be.
     */
    protected function extractData(Order $order)
    {
        $data = array(
            'ClientId' => $order->getClientId(),
            'OrderName' => $order->getOrderName(),
            'TemplateId' => $order->getTemplateId(),
            'RequestDate' => $order->getRequestDate()->format('Y-m-d\TH:i:s'),
            'RequestedDueDate' => $order->getDueDate()->format('Y-m-d'),
            'IssuedBy' => $order->getIssuedBy(),
            'ConfidentialOrder' => (int) $order->isConfidential(),
            'SourceLanguageIsoCode' => $order->getSourceLanguage(),
            'TargetLanguages' => $this->extractTargetLanguagesData($order),
            'Service' => $order->getService(),
            'ClientInstructions' => $this->extractInstructionsData($order),
            'ClientReference' => $order->getReference(),
            'ConfirmationRequested' => (int) $order->needsConfirmation(),
            'QuotationRequested' => (int) $order->needsQuotation(),
            'InputFiles' => $this->extractInputFilesData($order),
        );

        return $data;
    }

    /**
     * Create the structure for the target languages.
     *
     * @param Order $order
     *   The Order to extract the data from.
     *
     * @return array
     *   The structure for the target languages.
     */
    protected function extractTargetLanguagesData(Order $order)
    {
        $targetLanguages = $order->getTargetLanguages();

        $languages = array();
        foreach ($targetLanguages as $language) {
            $languages[] = array(
                'IsoCode' => $language,
            );
        }

        return $languages;
    }

    /**
     * Extract the instructions from the order (if any).
     *
     *
     */
    protected function extractInstructionsData(Order $order)
    {
        $instructions = $order->getInstructions();

        if (empty($instructions)) {
            return 'None';
        }

        return implode(PHP_EOL, $instructions);
    }

    /**
     * Create the structure for the files to translate.
     *
     * @param Order $order
     *   The Order to extract the data from.
     *
     * @return array
     *   The structure for the attached files.
     */
    protected function extractInputFilesData(Order $order)
    {
        $files = $order->getFiles();

        $inputFiles = array();
        foreach ($files as $file_name) {
            $inputFiles[] = array(
                'InputFile' => array(
                    'FileName' => $file_name,
                    'FileReference' => 'Input/' . $file_name,
                ),
            );
        }

        return $inputFiles;
    }

    /**
     * Create the order XML.
     *
     * @param array $data
     *   The data array to create the XML from.
     *
     * @return \SimpleXMLElement
     *   The generated XML object.
     */
    protected function createXml(array $data)
    {
        $xml = new \SimpleXMLElement($this->xmlWrapper);
        $this->arrayToXml($data, $xml);
        return $xml;
    }

    /**
     * Adds an array to an existing SimpleXMLElement object.
     *
     * @param array $array
     *   Array with values.
     * @param \SimpleXMLElement $xml
     *   XML object.
     */
    protected function arrayToXml(array $array, \SimpleXMLElement $xml)
    {
        foreach ($array as $key => $value) {
            $element_key = (is_numeric($key))
                ? 'item' . $key
                : $key;

            if (!is_array($value)) {
                $xml->addChild($element_key, htmlspecialchars($value));
                continue;
            }

            // Support numeric keyed array values.
            if (is_numeric($key)) {
                $this->arrayToXml($value, $xml);
                continue;
            }

            $sub_node = $xml->addChild($element_key);
            $this->arrayToXml($value, $sub_node);
        }
    }
}
