<?php
class Logger {
    public static function log($level, $message, $context = []) {
        $logDir = __DIR__ . '/../../logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $line = date('Y-m-d H:i:s') . ' [' . strtoupper($level) . '] ' . $message;
        if (!empty($context)) {
            $line .= ' ' . json_encode($context, JSON_UNESCAPED_SLASHES);
        }

        error_log($line . PHP_EOL, 3, $logDir . '/app.log');
    }

    public static function error($message, $context = []) {
        self::log('error', $message, $context);
    }

    public static function warning($message, $context = []) {
        self::log('warning', $message, $context);
    }

    public static function info($message, $context = []) {
        self::log('info', $message, $context);
    }
}
