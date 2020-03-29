<?php

namespace XspfTest;

use PHPUnit\Framework\TestCase;
use Xspf\WhiteAndBlacklistService;

class WhiteAndBlacklistServiceTest extends TestCase
{
    public function whiteListDataProvider()
    {
        return [
            [
                '*.JSON',
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
            [
                '*[Family]*',
                [
                    'HomeVideo [Family].avi' => true,
                    'Work [Work].mp4'        => false,
                ],
            ],
        ];
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
     * @dataProvider whiteListDataProvider
     *
     * @param string $whiteListPattern
     * @param array  $fileList
     */
    public function testWhiteList($whiteListPattern, $fileList)
    {
        $service = new WhiteAndBlacklistService([$whiteListPattern]);

        foreach ($fileList as $file => $isAllowed) {
            $this->assertEquals($isAllowed, $service->isFileAllowed($file), 'Mismatch between ' . $whiteListPattern . ' and ' . $file);
        }
    }

    /**
     * @dataProvider blackListDataProvider
     *
     * @param string $blackListPattern
     * @param array  $fileList
     */
    public function testBlackList($blackListPattern, $fileList)
    {
        $service = new WhiteAndBlacklistService([], [$blackListPattern]);

        foreach ($fileList as $file => $isAllowed) {
            $this->assertEquals($isAllowed, $service->isFileAllowed($file));
        }
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
        $service = new WhiteAndBlacklistService([$whiteListPattern], [$blackListPattern]);

        foreach ($fileList as $file => $isAllowed) {
            $this->assertEquals($isAllowed, $service->isFileAllowed($file));
        }
    }
}
