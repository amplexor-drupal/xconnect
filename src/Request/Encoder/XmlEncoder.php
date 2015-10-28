<?php
/**
 * This file is part of the Amplexor\XConnect library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/amplexor-drupal/xconnect/
 * @version 1.0.0
 * @package Amplexor.XConnect
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Amplexor\XConnect\Request\Encoder;

use Amplexor\XConnect\Request\Order;

/**
 * Format to generate the XML code representing the Order object.
 *
 * @package Amplexor\XConnect
 */
class XmlEncoder implements EncoderInterface
{
    /**
     * @inheritDoc
     */
    public function encode(Order $order)
    {
        $data = $this->extractData($order);
        $xml = $this->createXml($data);
        return $xml->saveXML();
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
     * @return \DOMDocument
     *   The generated XML object.
     */
    protected function createXml(array $data)
    {
        $xml = new \DOMDocument('1.0', 'UTF-8');

        // Add the request root element.
        $request = $xml->createElementNs(
            'http://www.euroscript.com/escaepe/types',
            'tns:ClientWoRequest'
        );
        $request->setAttribute(
            'xmlns:xsi',
            'http://www.w3.org/2001/XMLSchema-instance'
        );
        $request->setAttribute(
            'xsi:schemaLocation',
            'http://www.euroscript.com/escaepe/types clientOrderRequestTypes.xsd'
        );
        $xml->appendChild($request);

        // Add the data to the request in the XML.
        $this->arrayToXml($data, $request);
        return $xml;
    }

    /**
     * Adds an array to an existing SimpleXMLElement object.
     *
     * @param array $array
     *   Array with values.
     * @param \DOMElement $element
     *   The Dom element to who the data should be added as children.
     */
    protected function arrayToXml(array $array, \DOMElement $element)
    {
        foreach ($array as $key => $value) {
            $element_key = (is_numeric($key))
                ? 'item' . $key
                : $key;

            if (!is_array($value)) {
                $child  = new \DOMElement(
                    $element_key,
                    htmlspecialchars($value)
                );
                $element->appendChild($child);
                continue;
            }

            // Support numeric keyed array values.
            if (is_numeric($key)) {
                $this->arrayToXml($value, $element);
                continue;
            }

            // Add the array data within a child element.
            $child = new \DOMElement($element_key);
            $element->appendChild($child);
            $this->arrayToXml($value, $child);
        }
    }
}
