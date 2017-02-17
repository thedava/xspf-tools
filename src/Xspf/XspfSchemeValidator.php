<?php

namespace Xspf;

class XspfSchemeValidator
{
    const URL = 'http://validator.xspf.org/';

    const RESULT_SUCCESS = '<span class="valid">Valid</span>';
    const RESULT_ERROR = '<span class="invalid">Invalid</span>';

    public static function isValid($xml)
    {
        $data = [
            'pasted' => $xml,
            'uploaded' => '',
            'url' => '',
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
        $result = file_get_contents(self::URL, false, $context);

        return (strpos($result, self::RESULT_SUCCESS) !== false && strpos($result, self::RESULT_ERROR) === false);
    }
}
