<?php
namespace OzSysb\FluentLogger;

use Fluent\Logger\FluentLogger as FluentLogger;

/**
 * Fluent Logger
 *
 * Fluent Logger client communicates to Fluentd with json formatted messages.
 */
class Logger
{
    /* log level debug */
    const DEBUG = 'debug';

    /* log level info */
    const INFO = 'info';

    /* log level warning */
    const WARNING = 'warning';

    /* log level error */
    const ERROR = 'error';

    /* @var Fluent\Logger\FluentLogger */
    private $client;

    private static $key;

    /* @var ozv では 固定で以下の socket を使用する */
    protected $socket = 'unix:///var/run/td-agent/td-agent.sock';

    protected $callback = array(__CLASS__, 'callback');

    protected static $defaultNamespace;

    protected static $namespaces = array(
        'app-server-side' => 'app.sp.api',
        'woodstock' => 'web.pc.front',
        'spitz' =>'web.sp.front',
        'apollo' =>'web.both.api',
        'aslan' =>'web.both.api',
        'maple' =>'web.both.api',
        'mango' =>'web.admin.front',
        'ameba' =>'web.admin.api',
        'panda-web' =>'web.pointback.front',
        'apple' =>'web.pointback.api',
        'panda-batch' =>'batch.pointback.default',
        'aslan-batch' =>'batch.both.default',
    );


    public static function setApplication($applicationName)
    {
        if (!is_string($applicationName)) {
            throw new \RuntimeException('$applicationName は文字列ではありません。: ' . var_export($applicationName, true));
        }

        if (!isset(self::$namespaces[$applicationName])) {
            throw new \RuntimeException($applicationName . ' は定義されていません。');
        }

        $default = self::$defaultNamespace;

        self::$defaultNamespace = self::$namespaces[$applicationName];

        return $default;
    }

    /**
     * create logger object.
     * @param mixed $key (strin|null) use X_AMZN_TRACE_ID OR original unique key
     * @param mixed $callback (strin|null) callback(\Exception, array log) , fluentd に書き込めなかった場合のログ保存先を callback で設定できる
     */
    public function __construct($key = null, $callback = null)
    {
        if (is_null(self::$defaultNamespace)) {
            throw new \RuntimeException('最初に OzSysb\FluentLogger\Logger::setApplication() を使い、アプリケーションを定義してください。');
        }

        if (!is_null($key) && !is_string($key)) {
            throw new \RuntimeException('$key は文字列ではありません。: ' . var_export($key, true));
        }

        if (is_callable($callback)) {
            $this->callback = $callback;
        }

        $this->prepareUniqueKey($key);

        $this->client = new FluentLogger($this->socket);
    }

    public function debug($type, $message, $function = '', $class = '')
    {
        $this->post(self::DEBUG, $type, $message, $function, $class);
    }

    public function info($type, $message, $function = '', $class = '')
    {
        $this->post(self::INFO, $type, $message, $function, $class);
    }

    public function warning($type, $message, $function = '', $class = '')
    {
        $this->post(self::WARNING, $type, $message, $function, $class);
    }

    public function error($type, $message, $function = '', $class = '')
    {
        $this->post(self::ERROR, $type, $message, $function, $class);
    }

    protected function post($level, $type, $message, $function, $class)
    {
        $log = array(
            'type' => $type,
            'pid' => getmypid(),
            'class' => $class,
            'function' => $function,
            'message' => json_encode($message),
            'unique_key' => self::$key,
        );

        if ($function) {
            $log['function'] = $function;
        }

        try {
            $this->logger->post($this->generateNamespace($level), $log);
        } catch (Exception $exception) {
            // 例外を投げず、 callback 内で定義した処理で完了させる.
            call_user_func_array($this->callback, array($exception, $log));
        }
    }

    protected function callback(\Exception $exception, array $log)
    {
        error_log(json_encode(array_merge($log, array(
            'time' => date(DATE_RFC2822),
            'exception' => array(
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'line' => $exception->getLine(),
                'file' => $exception->getFile(),
                'trace' => $exception->getTraceAsString(),
            ),
        ))));
    }

    protected function prepareUniqueKey($key = null)
    {
        if (null !== self::$key) {
            return;
        }

        if (is_string($key)) {
            self::$key = $key;
            return true;
        }

        if (isset($_SERVER['HTTP_X_AMZN_TRACE_ID'])) {
            self::$key = $_SERVER['HTTP_X_AMZN_TRACE_ID'];
            return true;
        }

        self::$key = md5(date(DATE_RFC2822) . microtime() . getmypid() . var_export(isset($_SERVER['argv']) ? $_SERVER['argv'] : self::$defaultNamespace, true));
    }

    protected function generateNamespace($level)
    {
        return sprintf('%s.%s', self::$defaultNamespace, $level);
    }
}
