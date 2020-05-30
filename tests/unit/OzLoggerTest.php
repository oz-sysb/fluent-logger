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

    protected $defaultCondition;

    protected $defaultErrorLog;

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

    protected function assertPreConditions()
    {
        $this->defaultCondition = \MockLogger::forceException();
        $this->defaultErrorLog = ini_get('error_log');
    }

    protected function assertPostConditions()
    {
        \MockLogger::forceException($this->defaultCondition);
        ini_set('error_log', $this->defaultErrorLog);
    }

    /**
     * @covers OzSysb\Logger\OzLogger::__construct
     * @expectedException \RuntimeException
     * @expectedExceptionMessage 最初に \OzSysb\Logger\OzLogger::setApplication() を使い、アプリケーションを定義してください。
     **/
    public function testConstructor()
    {
        $this->object = new OzLogger($this->mock);
    }

    /**
     * @covers OzSysb\Logger\OzLogger::__construct
     * @expectedException \RuntimeException
     * @expectedExceptionMessage $key は文字列ではありません。: 0.1
     **/
    public function testConstructor2()
    {
        OzLogger::setApplication('woodstock');
        $this->object = new OzLogger($this->mock, 0.1);
    }

    /**
     * @covers OzSysb\Logger\OzLogger::__construct
     **/
    public function testConstructor3()
    {
        OzLogger::setApplication('woodstock');
        $isCalled = false;
        \MockLogger::forceException(true);
        $this->object = new OzLogger($this->mock, 'dummy.key', function ($e, $l) use (&$isCalled) {
            $isCalled = true;
        });
        $this->object->error('test.err', 'error message', __CLASS__, __FUNCTION__);
        $this->assertTrue($isCalled);
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

        $this->assertEquals($expectedNamespace, $namespace);
        $this->assertEquals($expectedMessage, $log['message']);
        $this->assertEquals('test.debug', $log['type']);
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

        $this->assertEquals($expectedNamespace, $namespace);
        $this->assertEquals($expectedMessage, $log['message']);
        $this->assertEquals('test.info', $log['type']);
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

        $this->assertEquals($expectedNamespace, $namespace);
        $this->assertEquals($expectedMessage, $log['message']);
        $this->assertEquals('test.warning', $log['type']);
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

        $this->assertEquals($expectedNamespace, $namespace);
        $this->assertEquals($expectedMessage, $log['message']);
        $this->assertEquals('test.error', $log['type']);
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
     * @covers OzSysb\Logger\OzLogger::post
     * @dataProvider postDataProvider
     */
    public function testPost($level, $message, $expectedNamespace, $expectedMessage)
    {
        OzLogger::setApplication('app-server-side');
        $this->object = new OzLogger($this->mock);
        $reflection = new \ReflectionClass($this->object);
        $post = $reflection->getMethod('post');
        $post->setAccessible(true);
        $post->invokeArgs($this->object, array($level, 'test.post', $message, '', ''));

        $namespace = $this->mock->getLastKey();
        $log = $this->mock->getLastLog();

        $this->assertEquals($expectedNamespace, $namespace);
        $this->assertEquals($expectedMessage, $log['message']);

        $this->assertEquals($expectedNamespace, $namespace);
        $this->assertEquals($expectedMessage, $log['message']);
        $this->assertEquals('test.post', $log['type']);
    }

    /**
     * @covers OzSysb\Logger\OzLogger::post
     */
    public function testPost2()
    {
        OzLogger::setApplication('app-server-side');
        \MockLogger::forceException(true);
        $isCalled = false;
        $this->object = new OzLogger($this->mock, null, function ($e, $l) use (&$isCalled) {
            $isCalled = true;
        });
        $reflection = new \ReflectionClass($this->object);
        $post = $reflection->getMethod('post');
        $post->setAccessible(true);
        $post->invokeArgs($this->object, array(OzLogger::DEBUG, 'test.post', 'hoge', '', ''));

        $this->assertTrue($isCalled);
    }

    public function postDataProvider()
    {
        $testObj = $this->mock;
        $message = <<<'XSS'
'';!--"<XSS>=&{()}``\"
<script src=http://example.com/xss.js></script>
XSS;
        return array(
            'number' => array(OzLogger::DEBUG, 10/3, 'app.sp.api.debug', json_encode(10/3)),
            'array' => array(OzLogger::INFO, array(new \Exception('')), 'app.sp.api.info', json_encode(array(new \Exception('')))),
            'object' => array(OzLogger::WARNING, $testObj, 'app.sp.api.warning', json_encode($testObj)),
            'string' => array(OzLogger::ERROR, $message, 'app.sp.api.error', json_encode($message)),
        );
    }

    /**
     * @covers OzSysb\Logger\OzLogger::callback
     */
    public function testCallback()
    {
        \MockLogger::forceException(true);
        $object = new OzLogger($this->mock, null, function ($exception, $log) use (&$result) {
            $result = array(
                'exception' => $exception,
                'log' => $log,
            );
        });
        $object->info('test.callback', 'info message');

        $this->assertEquals('forced error', $result['exception']->getMessage());
        $this->assertEquals('test.callback', $result['log']['type']);
        $this->assertEquals(json_encode('info message'), $result['log']['message']);
    }

    /**
     * @covers OzSysb\Logger\OzLogger::callback
     */
    public function testCallback2()
    {
        OzLogger::setApplication('app-server-side');
        \MockLogger::forceException(true);

        $callbackTest = tempnam(sys_get_temp_dir(), 'ozsysb-logger-callback');
        ini_set('error_log', $callbackTest);

        $this->object = new OzLogger($this->mock);
        $reflection = new \ReflectionClass($this->object);
        $callback = $reflection->getMethod('callback');
        $callback->setAccessible(true);
        $callback->invokeArgs($this->object, array(new \Exception('わんわん'), array('a' => 'b')));

        $log = file_get_contents($callbackTest);

        $this->assertRegExp('/"exception":/i', $log);
        $this->assertRegExp('/"message":"/i', $log);
    }

    /**
     * @covers OzSysb\Logger\OzLogger::prepareUniqueKey
     */
    public function testPrepareUniqueKey()
    {
        OzLogger::setApplication('app-server-side');
        $this->object = new OzLogger($this->mock);

        $reflection = new \ReflectionClass($this->object);
        $prepareUniqueKey = $reflection->getMethod('prepareUniqueKey');
        $prepareUniqueKey->setAccessible(true);

        $nowKey = $this->object->getUniqueKey();

        $prepareUniqueKey->invokeArgs($this->object, array(null, false));
        // キーは変わらない
        $this->assertEquals($nowKey, $this->object->getUniqueKey());

        $prepareUniqueKey->invokeArgs($this->object, array('hoge', true));
        // 強制的にキーが変わる -> 指定したもの
        $this->assertEquals('hoge', $this->object->getUniqueKey());

        $_SERVER['HTTP_X_AMZN_TRACE_ID'] = 'x-amzn';
        $prepareUniqueKey->invokeArgs($this->object, array(null, true));
        // 強制的にキーが変わる -> X-Amzn-Trace-ID
        $this->assertEquals('x-amzn', $this->object->getUniqueKey());

        unset($_SERVER['HTTP_X_AMZN_TRACE_ID']);
        $prepareUniqueKey->invokeArgs($this->object, array(null, true));
        // 強制的にキーが変わる -> original uniqueKey
        $this->assertRegExp('/[0-9a-f]{16}/i', $this->object->getUniqueKey());
    }

    /**
     * @covers OzSysb\Logger\OzLogger::getUniqueKey
     * @dataProvider getUniqueKeyDataProvider
     */
    public function testGetUniqueKey($key, $expectedKey, $assertionMethod)
    {
        static $oldKey = null;
        $object = new OzLogger($this->mock, $key);
        $object->info('test.callback', 'info message');
        if ($oldKey) {
            $this->assertEquals($oldKey, $object->getUniqueKey());
        }
        $oldKey = $key;
        $object = new OzLogger($this->mock, $key, null, true);
        $object->debug('test.callback', 'debug message');
        $object->error('test.callback', 'error message');
        $object->info('test.callback', 'info2 message');

        $this->$assertionMethod($expectedKey, $object->getUniqueKey());
    }

    public function getUniqueKeyDataProvider()
    {
        return array(
            'null' => array(null, '/^[a-f0-9]{32}$/i', 'assertRegExp'),
            'string' => array('hogehogeKey', 'hogehogeKey', 'assertEquals'),
            'hash' => array(md5('hogehogeKey'), md5('hogehogeKey'), 'assertEquals'),
            'x-amzn-trace-id' => array('Root=1-67891233-abcdef012345678912345678', 'Root=1-67891233-abcdef012345678912345678', 'assertEquals'),
        );
    }

    /**
     * @covers OzSysb\Logger\OzLogger::generateNamespace
     */
    public function testGenerateNamespace()
    {
        OzLogger::setApplication('app-server-side');
        $this->object = new OzLogger($this->mock);

        $reflection = new \ReflectionClass($this->object);
        $generateNamespace = $reflection->getMethod('generateNamespace');
        $generateNamespace->setAccessible(true);
        $result = $generateNamespace->invokeArgs($this->object, array(OzLogger::INFO));

        $this->assertEquals('app.sp.api.info', $result);
    }
}
