<?php

namespace XspfTest;

use XspfMock\WhiteAndBlacklistProviderTraitMock;

class WhiteAndBlacklistProviderTraitTest extends \PHPUnit_Framework_TestCase
{
    public function whiteListDataProvider()
    {
        return [
            [
                '*.json',
                [
                    'composer.json' => true,
                    'composer.lock' => false,
                    'test.json'     => true,
                ],
            ],
            [
                '*.avi',
                [
                    'HomeVideo.avi' => true,
                    'FOO.AVI'       => true,
                    'Cover.jpg'     => false,
                ],
            ],
        ];
    }

    /**
     * @dataProvider whiteListDataProvider
     *
     * @param string $whiteListPattern
     * @param array  $fileList
     */
    public function testWhiteList($whiteListPattern, $fileList)
    {
        $mock = new WhiteAndBlacklistProviderTraitMock([], [$whiteListPattern]);

        foreach ($fileList as $file => $isAllowed) {
            $this->assertEquals($isAllowed, $mock->checkFileAllowed($file));
        }
    }

    public function blackListDataProvider()
    {
        return [
            [
                '*.lock',
                [
                    'composer.json' => true,
                    'composer.lock' => false,
                    'test.json'     => true,
                ],
            ],
            [
                '*.jpg',
                [
                    'HomeVideo.avi' => true,
                    'FOO.AVI'       => true,
                    'Cover.jpg'     => false,
                ],
            ],
        ];
    }

    /**
     * @dataProvider blackListDataProvider
     *
     * @param string $blackListPattern
     * @param array  $fileList
     */
    public function testBlackList($blackListPattern, $fileList)
    {
        $mock = new WhiteAndBlacklistProviderTraitMock([$blackListPattern]);

        foreach ($fileList as $file => $isAllowed) {
            $this->assertEquals($isAllowed, $mock->checkFileAllowed($file));
        }
    }

    public function combinedListDataProvider()
    {
        return [
            [
                'HomeVideo*',
                '*.jpg',
                [
                    'HomeVideo CD1.avi' => true,
                    'HomeVideo CD2.avi' => true,
                    'HomeVideo CD1.jpg' => false,
                    'HomeVideo CD2.jpg' => false,
                ],
            ],
        ];
    }

    /**
     * @dataProvider combinedListDataProvider
     *
     * @param string $whiteListPattern
     * @param string $blackListPattern
     * @param array  $fileList
     */
    public function testCombinedList($whiteListPattern, $blackListPattern, $fileList)
    {
        $mock = new WhiteAndBlacklistProviderTraitMock([$blackListPattern], [$whiteListPattern]);

        foreach ($fileList as $file => $isAllowed) {
            $this->assertEquals($isAllowed, $mock->checkFileAllowed($file));
        }
    }
}
