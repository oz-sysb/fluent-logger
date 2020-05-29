<?php
namespace OzSysb\Logger;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2020-05-28 at 14:08:20.
 */
class OzLoggerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \MockLogger implements\Fluent\Logger\LoggerInterface mock
     */
    protected $mock;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->mock = new \MockLogger();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @test for __construct
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage 最初に \OzSysb\Logger\OzLogger::setApplication() を使い、アプリケーションを定義してください。
     **/
    public function testConstructor()
    {
        $this->object = new OzLogger($this->mock);
    }

    /**
     * @covers OzSysb\Logger\OzLogger::setApplication
     * @dataProvider setApplicationDataProvider
     */
    public function testSetApplication($param, $errorClass)
    {
        if (is_null($errorClass)) {
            try {
                OzLogger::setApplication($param);
            } catch (\Exception $e) {
                $this->fail();
                return;
            }

            $this->assertTrue(true);
            return;
        }

        $this->setExpectedException($errorClass);
        OzLogger::setApplication($param);
    }

    public function setApplicationDataProvider()
    {
        return array(
            'number' => array(1, '\\RuntimeException'),
            'array' => array(array('woodstock'), '\\RuntimeException'),
            'undefined' => array('hoge', '\\RuntimeException'),
            'app-server-side' => array('app-server-side', null),
            'woodstock' => array('woodstock', null),
            'spitz' => array('spitz', null),
            'apollo' => array('apollo', null),
            'aslan' => array('aslan', null),
            'maple' => array('maple', null),
            'mango' => array('mango', null),
            'ameba' => array('ameba', null),
            'panda-web' => array('panda-web', null),
            'apple' => array('apple', null),
            'panda-batch' => array('panda-batch', null),
            'aslan-batch' => array('aslan-batch', null),
        );
    }

    /**
     * @covers OzSysb\Logger\OzLogger::debug
     * @dataProvider debugDataProvider
     */
    public function testDebug($message, $expectedNamespace, $expectedMessage)
    {
        OzLogger::setApplication('woodstock');
        $this->object = new OzLogger($this->mock, 'hogehoge');
        $this->object->debug('test.debug', $message, __CLASS__, __FUNCTION__);
        $namespace = $this->mock->getLastKey();
        $log = $this->mock->getLastLog();

        $this->assertEquals($namespace, $expectedNamespace);
        $this->assertEquals($log['message'], $expectedMessage);
        $this->assertEquals($log['type'], 'test.debug');
    }

    public function debugDataProvider()
    {
        $testObj = new \DateTime();
        $message = 'こんにちはこれはテスト';
        return array(
            'number' => array(1, 'web.pc.front.debug', json_encode(1)),
            'array' => array(array('woodstock'), 'web.pc.front.debug', json_encode(array('woodstock'))),
            'object' => array($testObj, 'web.pc.front.debug', json_encode($testObj)),
            'string' => array($message, 'web.pc.front.debug', json_encode($message)),
        );
    }

    /**
     * @covers OzSysb\Logger\OzLogger::info
     * @dataProvider infoDataProvider
     */
    public function testInfo($message, $expectedNamespace, $expectedMessage)
    {
        OzLogger::setApplication('panda-web');
        $this->object = new OzLogger($this->mock);
        $this->object->info('test.info', $message, __CLASS__, __FUNCTION__);
        $namespace = $this->mock->getLastKey();
        $log = $this->mock->getLastLog();

        $this->assertEquals($namespace, $expectedNamespace);
        $this->assertEquals($log['message'], $expectedMessage);
        $this->assertEquals($log['type'], 'test.info');
    }

    public function infoDataProvider()
    {
        $testObj = new \Exception('message');
        $message = 'ああ華族様だよ　と私は嘘を吐くのであった';
        return array(
            'number' => array(-0.00001, 'web.pointback.front.info', json_encode(-0.00001)),
            'array' => array(array('         '), 'web.pointback.front.info', json_encode(array('         '))),
            'object' => array($testObj, 'web.pointback.front.info', json_encode($testObj)),
            'string' => array($message, 'web.pointback.front.info', json_encode($message)),
        );
    }

    /**
     * @covers OzSysb\Logger\OzLogger::warning
     * @dataProvider warningDataProvider
     */
    public function testWarning($message, $expectedNamespace, $expectedMessage)
    {
        OzLogger::setApplication('aslan-batch');
        $this->object = new OzLogger($this->mock);
        $this->object->warning('test.warning', $message, __CLASS__, __FUNCTION__);
        $namespace = $this->mock->getLastKey();
        $log = $this->mock->getLastLog();

        $this->assertEquals($expectedNamespace, $namespace);
        $this->assertEquals($expectedMessage, $log['message']);

        $this->assertEquals($namespace, $expectedNamespace);
        $this->assertEquals($log['message'], $expectedMessage);
        $this->assertEquals($log['type'], 'test.warning');
    }

    public function warningDataProvider()
    {
        $testObj = new \Exception('hoge');
        $message = <<<'FIZZBUZZ'
1.upto(100){|i|puts i%15==0?'fizzbuzz':i%5==0?'buzz':i%3==0?'fizz':i}
FIZZBUZZ;
        return array(
            'number' => array(0x1, 'batch.both.default.warning', json_encode(0x1)),
            'array' => array(array(json_encode(1), json_encode(null)), 'batch.both.default.warning', json_encode(array(json_encode(1), json_encode(null)))),
            'object' => array($testObj, 'batch.both.default.warning', json_encode($testObj)),
            'string' => array($message, 'batch.both.default.warning', json_encode($message)),
        );
    }

    /**
     * @covers OzSysb\Logger\OzLogger::error
     * @dataProvider errorDataProvider
     */
    public function testError($message, $expectedNamespace, $expectedMessage)
    {
        OzLogger::setApplication('app-server-side');
        $this->object = new OzLogger($this->mock);
        $this->object->error('test.error', $message, __CLASS__, __FUNCTION__);
        $namespace = $this->mock->getLastKey();
        $log = $this->mock->getLastLog();

        $this->assertEquals($expectedNamespace, $namespace);
        $this->assertEquals($expectedMessage, $log['message']);

        $this->assertEquals($namespace, $expectedNamespace);
        $this->assertEquals($log['message'], $expectedMessage);
        $this->assertEquals($log['type'], 'test.error');
    }

    public function errorDataProvider()
    {
        $testObj = $this->mock;
        $message = <<<'XSS'
'';!--"<XSS>=&{()}``\"
<script src=http://example.com/xss.js></script>
XSS;
        return array(
            'number' => array(10/3, 'app.sp.api.error', json_encode(10/3)),
            'array' => array(array(new \Exception('')), 'app.sp.api.error', json_encode(array(new \Exception('')))),
            'object' => array($testObj, 'app.sp.api.error', json_encode($testObj)),
            'string' => array($message, 'app.sp.api.error', json_encode($message)),
        );
    }

    /**
     * @covers OzSysb\Logger\OzLogger::callback
     */
    public function testCallback()
    {
        $defaultCondition = \MockLogger::forceException(true);
        $object = new OzLogger($this->mock, null, function ($exception, $log) use (&$result) {
            $result = array(
                'exception' => $exception,
                'log' => $log,
            );
        });
        $object->info('test.callback', 'info message');

        $this->assertEquals($result['exception']->getMessage(), 'forced error');
        $this->assertEquals($result['log']['type'], 'test.callback');
        $this->assertEquals($result['log']['message'], json_encode('info message'));

        \MockLogger::forceException($defaultCondition);
    }
}
