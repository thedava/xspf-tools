<?php

namespace Xspf;

class XspfSchemeValidator
{
    const URL = 'http://validator.xspf.org/';

    const RESULT_SUCCESS = '<span class="valid">Valid</span>';
    const RESULT_ERROR = '<span class="invalid">Invalid</span>';

    /**
     * Validate the given xml using the xspf online validator
     *
     * @param string $xml
     *
     * @return bool|null
     */
    public static function isValid($xml)
    {
        $data = [
            'pasted'       => $xml,
            'uploaded'     => '',
            'url'          => '',
            'submitPasted' => 'Submit',
        ];

        $options = [
            'http' => [
                'header'  => 'Content-type: application/x-www-form-urlencoded' . "\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ],
        ];
        $context = stream_context_create($options);

        try {
            $result = file_get_contents(self::URL, false, $context);
            if (!is_string($result)) {
                return null;
            }

            $isSuccess = strpos($result, self::RESULT_SUCCESS) !== false;
            $isFailure = strpos($result, self::RESULT_ERROR) !== false;

            // Unexpected validation result
            if (!$isSuccess && !$isFailure) {
                return null;
            }

            return ($isSuccess && !$isFailure);
        } catch (\Exception $error) {
            return null;
        }
    }
}
