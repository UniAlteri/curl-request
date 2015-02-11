<?php
/**
 * Curl Request
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 * @copyright   Copyright (c) Darrell Hamilton <darrell.noice@gmail.com> (initial developer)
 * @link        http://teknoo.it/curl Project website
 * @license     http://teknoo.it/curl/license/mit         MIT License
 * @license     http://teknoo.it/curl/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @author      Darrell Hamilton <darrell.noice@gmail.com> (initial developer)
 * @version     0.8.0
 */

namespace UniAlteri\Tests\Curl;

use UniAlteri\Curl\ErrorException;
use UniAlteri\Curl\Request;

/**
 * Class RequestTest
 *
 * @package     CurlRequest
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 * @copyright   Copyright (c) Darrell Hamilton <darrell.noice@gmail.com> (initial developer)
 * @link        http://teknoo.it/curl Project website
 * @license     http://teknoo.it/curl/license/mit         MIT License
 * @license     http://teknoo.it/curl/license/gpl-3.0     GPL v3 License
 * @author      Darrell Hamilton <darrell.noice@gmail.com> (initial developer)
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructWithUrl()
    {
        $options = $this->getMock('UniAlteri\Curl\Options');

        $options->expects($this->once())
            ->method('setOptionValue')
            ->willReturnCallback(
                function ($resource, $name, $value) {
                    $this->assertNotEmpty($resource);
                    $this->assertEquals(CURLOPT_URL, $name);
                    $this->assertEquals('http://teknoo.it', $value);
                }
            );

        $request = new Request($options, 'http://teknoo.it');
    }

    public function testSetOption()
    {
        $options = $this->getMock('UniAlteri\Curl\Options');
        $request = new Request($options);

        $options->expects($this->once())
            ->method('setOptionValue')
            ->with(
                $this->equalTo($request->getHandle()),
                $this->equalTo(CURLOPT_URL),
                $this->equalTo('http://teknoo.it')
            );

        $this->assertSame($request, $request->setOption(CURLOPT_URL, 'http://teknoo.it'));
    }

    public function testSetOptionArray()
    {
        $options = $this->getMock('UniAlteri\Curl\Options');
        $request = new Request($options);

        $options->expects($this->once())
            ->method('setOptionsValuesArray')
            ->with(
                $this->equalTo($request->getHandle()),
                $this->equalTo(array('foo' => 'bar'))
            );

        $this->assertSame($request, $request->setOptionArray(array('foo'=>'bar')));
    }

    /**
     * @expectedException UniAlteri\Curl\ErrorException
     */
    public function testExecuteError()
    {
        $options = $this->getMock('UniAlteri\Curl\Options');
        $request = new Request($options);

        $options->expects($this->once())
            ->method('setOptionValue')
            ->with(
                $this->equalTo($request->getHandle()),
                $this->equalTo(CURLOPT_URL),
                $this->equalTo('http://badurl')
            )->willReturnCallback(
                function ($resource, $option, $string) {
                    curl_setopt($resource, $option, $string);
                    curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);
                }
            );

        $request->setOption(CURLOPT_URL, 'http://badurl');
        $request->execute();
    }

    public function testExecute()
    {
        $options = $this->getMock('UniAlteri\Curl\Options');
        $request = new Request($options);

        $options->expects($this->once())
            ->method('setOptionValue')
            ->with(
                $this->equalTo($request->getHandle()),
                $this->equalTo(CURLOPT_URL),
                $this->equalTo('http://teknoo.it')
            )->willReturnCallback(
                function ($resource, $option, $string) {
                    curl_setopt($resource, $option, $string);
                    curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);
                }
            );

        $request->setOption(CURLOPT_URL, 'http://teknoo.it');
        $result = $request->execute();

        $this->assertNotEmpty($result);
        $this->assertTrue(is_string($result));
    }

    public function testSetReturnValueEnable()
    {
        $options = $this->getMock('UniAlteri\Curl\Options');
        $request = new Request($options);

        $options->expects($this->once())
            ->method('setOptionValue')
            ->with(
                $this->equalTo($request->getHandle()),
                $this->equalTo(CURLOPT_RETURNTRANSFER),
                $this->equalTo(true)
            );

        $this->assertEquals($request, $request->setReturnValue(true));
    }

    public function testSetReturnValueDisable()
    {
        $options = $this->getMock('UniAlteri\Curl\Options');
        $request = new Request($options);

        $options->expects($this->once())
            ->method('setOptionValue')
            ->with(
                $this->equalTo($request->getHandle()),
                $this->equalTo(CURLOPT_RETURNTRANSFER),
                $this->equalTo(false)
            );

        $this->assertEquals($request, $request->setReturnValue(false));
    }

    public function testSetUrl()
    {
        $options = $this->getMock('UniAlteri\Curl\Options');
        $request = new Request($options);

        $options->expects($this->once())
            ->method('setOptionValue')
            ->with(
                $this->equalTo($request->getHandle()),
                $this->equalTo(CURLOPT_URL),
                $this->equalTo('http://teknoo.it')
            );

        $this->assertEquals($request, $request->setUrl('http://teknoo.it'));
    }

    public function testGetInfoAll()
    {
        $options = $this->getMock('UniAlteri\Curl\Options');
        $request = new Request($options);
        $this->assertTrue(is_array($request->getInfo()));
    }

    public function testGetInfoFlag()
    {
        $options = $this->getMock('UniAlteri\Curl\Options');
        $request = new Request($options);
        $this->assertTrue(is_int($request->getInfo(CURLINFO_REDIRECT_COUNT)));
    }

    public function testSetMethodGet()
    {
        $options = $this->getMock('UniAlteri\Curl\Options');
        $request = new Request($options);

        $options->expects($this->once())
            ->method('setOptionValue')
            ->with(
                $this->equalTo($request->getHandle()),
                $this->equalTo(CURLOPT_HTTPGET),
                $this->equalTo(true)
            );

        $this->assertSame($request, $request->setMethod('GET'));
    }

    public function testSetMethodHead()
    {
        $options = $this->getMock('UniAlteri\Curl\Options');
        $request = new Request($options);

        $options->expects($this->once())
            ->method('setOptionValue')
            ->with(
                $this->equalTo($request->getHandle()),
                $this->equalTo(CURLOPT_NOBODY),
                $this->equalTo(true)
            );

        $this->assertSame($request, $request->setMethod('HEAD'));
    }

    public function testSetMethodPost()
    {
        $options = $this->getMock('UniAlteri\Curl\Options');
        $request = new Request($options);

        $options->expects($this->once())
            ->method('setOptionValue')
            ->with(
                $this->equalTo($request->getHandle()),
                $this->equalTo(CURLOPT_POST),
                $this->equalTo(true)
            );

        $this->assertSame($request, $request->setMethod('POST'));
    }

    public function testSetMethodPut()
    {
        $options = $this->getMock('UniAlteri\Curl\Options');
        $request = new Request($options);

        $options->expects($this->once())
            ->method('setOptionValue')
            ->with(
                $this->equalTo($request->getHandle()),
                $this->equalTo(CURLOPT_PUT),
                $this->equalTo(true)
            );

        $this->assertSame($request, $request->setMethod('PUT'));
    }

    public function testSetMethodCustom()
    {
        $options = $this->getMock('UniAlteri\Curl\Options');
        $request = new Request($options);

        $options->expects($this->once())
            ->method('setOptionValue')
            ->with(
                $this->equalTo($request->getHandle()),
                $this->equalTo(CURLOPT_CUSTOMREQUEST),
                $this->equalTo('fooBar')
            );

        $this->assertSame($request, $request->setMethod('fooBar'));
    }

    public function testClone()
    {
        $options = $this->getMock('UniAlteri\Curl\Options');

        $request = new Request($options);
        $clone = clone $request;

        $this->assertThat(
            $request->getHandle(),
            $this->logicalNot(
                $this->equalTo($clone->getHandle())
            )
        );
    }
}

