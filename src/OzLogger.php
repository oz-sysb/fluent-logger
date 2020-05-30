<?php
namespace OzSysb\Logger;

/**
 * [API] Fluent Logger
 *
 * composer ライブラリ fluent/logger を使い、社内で統一したフォーマットでログ出力するためのライブラリ
 * @access public
 * @author Shinichi Urabe <s-urabe@oz-vision.co.jp>
 * @copyright Copyright © OZvision Inc. All rights reserved.
 */
class OzLogger
{
    /**
     * @const log level debug
     */
    const DEBUG = 'debug';

    /**
     * @const log level info
     */
    const INFO = 'info';

    /**
     * @const log level warning
     */
    const WARNING = 'warning';

    /**
     * @const log level error
     */
    const ERROR = 'error';

    /**
     * @var Fluent\Logger\LoggerInterface
     */
    private $client;

    /**
     * @var 同一トランザクションを区別するためのキー
     * @static
     */
    private static $key;

    /**
     * @var fluentd にログを残せなかった場合の、callback 定義
     */
    protected $callback = array(__CLASS__, 'callback');

    /**
     * @var ログ保存で使用する namespace を定義
     * @static
     */
    protected static $defaultNamespace;

    /**
     * @var namespace 一覧
     * @static
     */
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

    /**
     * [API] 使用するアプリケーション名を定義
     *
     * OzSysb\Logger\OzLogger を初期化する前に呼び出す必要がある
     * アプリケーション名は以下のいずれかから登録する
     *
     *  - app-server-side
     *  - woodstock
     *  - spitz
     *  - apollo
     *  - aslan
     *  - maple
     *  - mango
     *  - ameba
     *  - panda-web
     *  - apple
     *  - panda-batch
     *  - aslan-batch
     *
     * @access public
     * @param string $applicationName
     *        アプリケーション名
     * @throws \RuntimeExceptinon
     *          定義されていないアプリケーション名の場合、例外を返す
     * @return string $default
     *        元々定義されていたアプリケーション名を返却
     * @static
     */
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
     * [API] コンストラクタ
     *
     * @access public
     * @param \Fluent\Logger\LoggerInterface
     *        Logger を定義
     * @param string|null $key
     *        明示的にトランザクション用のキーを定義する場合、ここで指定する
     * @param callable|null $callback
     *        Logger にログが保存できなかった場合のコールバック関数をここで指定する 未指定の場合は error_log 関数を通してログが残される
     *        callback(\Exception $exception, array $log)
     * @param boolean $forceUpdateKey
     *        強制的に ユニークキーを上書きするオプション
     * @throws \RuntimeException
     *         初期化前にアプリケーションが指定されていない場合や $key が文字列でない場合に例外を投げる
     */
    public function __construct(\Fluent\Logger\LoggerInterface $logger, $key = null, $callback = null, $forceUpdateKey = false)
    {
        if (is_null(self::$defaultNamespace)) {
            throw new \RuntimeException('最初に \OzSysb\Logger\OzLogger::setApplication() を使い、アプリケーションを定義してください。');
        }

        if (!is_null($key) && !is_string($key)) {
            throw new \RuntimeException('$key は文字列ではありません。: ' . var_export($key, true));
        }

        if (is_callable($callback)) {
            $this->callback = $callback;
        }

        $this->prepareUniqueKey($key, $forceUpdateKey);

        $this->client = $logger;
    }

    /**
     * [API] debug 用ログメソッド
     *
     * @access public
     * @param string @type
     *        ログの種別を定義する
     * @param mixed $message
     *        文字列、配列、オブジェクト など json_encode() した文字列を保存するため、値の形式は問わない
     * @param string $function
     *        処理を行った関数・メソッドを指定
     * @param string $class
     *        処理を行ったクラスを指定
     */
    public function debug($type, $message, $function = '', $class = '')
    {
        $this->post(self::DEBUG, $type, $message, $function, $class);
    }

    /**
     * [API] info 用ログメソッド
     *
     * @access public
     * @param string @type
     *        ログの種別を定義する
     * @param mixed $message
     *        文字列、配列、オブジェクト など json_encode() した文字列を保存するため、値の形式は問わない
     * @param string $function
     *        処理を行った関数・メソッドを指定
     * @param string $class
     *        処理を行ったクラスを指定
     */
    public function info($type, $message, $function = '', $class = '')
    {
        $this->post(self::INFO, $type, $message, $function, $class);
    }

    /**
     * [API] warning 用ログメソッド
     *
     * @access public
     * @param string @type
     *        ログの種別を定義する
     * @param mixed $message
     *        文字列、配列、オブジェクト など json_encode() した文字列を保存するため、値の形式は問わない
     * @param string $function
     *        処理を行った関数・メソッドを指定
     * @param string $class
     *        処理を行ったクラスを指定
     */
    public function warning($type, $message, $function = '', $class = '')
    {
        $this->post(self::WARNING, $type, $message, $function, $class);
    }

    /**
     * [API] error 用ログメソッド
     *
     * @access public
     * @param string @type
     *        ログの種別を定義する
     * @param mixed $message
     *        文字列、配列、オブジェクト など json_encode() した文字列を保存するため、値の形式は問わない
     * @param string $function
     *        処理を行った関数・メソッドを指定
     * @param string $class
     *        処理を行ったクラスを指定
     */
    public function error($type, $message, $function = '', $class = '')
    {
        $this->post(self::ERROR, $type, $message, $function, $class);
    }

    /**
     * [API] post ログメソッド
     *
     * 直接このメソッドは実行せす、ログレベル名のメソッドを実行します
     * Ozv の形式のログフォーマットに整形し、fluent/logger にログを post します
     *
     * @access protected
     *
     * @param string @level
     *        予め定義済みの 4 種類のログレベルを利用
     * @param string @type
     *        ログの種別を定義する
     * @param mixed $message
     *        文字列、配列、オブジェクト など json_encode() した文字列を保存するため、値の形式は問わない
     * @param string $function
     *        処理を行った関数・メソッドを指定
     * @param string $class
     *        処理を行ったクラスを指定
     */
    protected function post($level, $type, $message, $function, $class)
    {
        $log = array(
            'type' => $type,
            'pid' => getmypid(),
            'class' => $class,
            'function' => $function,
            'message' => json_encode($message),
            'unique_key' => $this->getUniqueKey(),
        );

        try {
            $this->client->post($this->generateNamespace($level), $log);
        } catch (\Exception $exception) {
            // 例外を投げず、 callback 内で定義した処理で完了させる.
            call_user_func_array($this->callback, array($exception, $log));
        }
    }

    /**
     * [API] fluent/logger にログを送出できないときのデフォルトの callback 先
     *
     * デフォルトでは error_log() にログを残します
     *
     * @access private
     *
     * @param \Exception @exception
     *        fluent/logger から投げられた例外
     * @param string @log
     *        fluent/logger に送信しようとしていた ログ配列
     */
    private function callback(\Exception $exception, array $log)
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

    /**
     * [API] トランザクション内で一意に区別するキーを定義
     *
     * すでに self::$key が定義済みでない場合は、引数に指定された $key がユニークキーとして利用され、
     * そうでない場合は、aws ELB でリクエストヘッダに付与される X-amzn-trace-id をキーとして利用
     * aws ELB 経由でヘッダが付与されない場合や コマンドラインの場合は独自のキーを精製
     *
     * @access protected
     *
     * @param string|null @key
     *      　自前で定義したキー
     * @param boolean $force
     *        強制的に ユニークキーを上書きするオプション
     */
    protected function prepareUniqueKey($key = null, $force = false)
    {
        if (null !== self::$key && !$force) {
            return;
        }

        if (is_string($key)) {
            self::$key = $key;
            return;
        }

        if (isset($_SERVER['HTTP_X_AMZN_TRACE_ID'])) {
            self::$key = $_SERVER['HTTP_X_AMZN_TRACE_ID'];
            return;
        }

        self::$key = md5(date(DATE_RFC2822) . microtime() . getmypid() . var_export(isset($_SERVER['argv']) ? $_SERVER['argv'] : self::$defaultNamespace, true));
    }

    /**
     * [API] ユニークキーを取得
     *
     * @access public
     *
     * @return string $key
     *        ユニークキーを返却
     */
    public function getUniqueKey()
    {
        return self::$key;
    }

    /**
     * [API] fluentd で利用する tag (ネームスペース) を生成
     *
     * @access protected
     *
     * @param string @level
     *      　debug, info, warning, error のいずれか
     */
    protected function generateNamespace($level)
    {
        return sprintf('%s.%s', self::$defaultNamespace, $level);
    }
}
