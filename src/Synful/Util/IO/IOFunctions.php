<?php

namespace Synful\Util\IO;

use Exception;
use Synful\Synful;
use Gestalt\Configuration;
use Synful\Util\Config\ConfigLoader;
use Synful\Util\Framework\SynfulException;

/**
 * Class used to handle system wide IO.
 */
class IOFunctions
{
    /**
     * Loads configuration file into system.
     *
     * @return bool
     */
    public static function loadConfig()
    {
        $return = true;
        if (file_exists('./config/')) {
            try {
                Synful::$config = Configuration::fromLoader(
                    new ConfigLoader([
                        'directory' => './config/',
                        ])
                );
            } catch (Exception $ex) {
                trigger_error('Failed to load config: '.$ex->message, E_USER_WARNING);
                $return = false;
            }
        } else {
            trigger_error('Failed to load config: File not found.', E_USER_WARNING);
            $return = false;
        }

        return $return;
    }

    /**
     * Prints output to the console and logs.
     *
     * @param  int $level
     * @param  string  $data
     * @param  bool $force
     * @param  bool $block_header_on_echo
     * @param  bool $write_to_file
     */
    public static function out($level, $data, $force = false, $block_header_on_echo = false, $write_to_file = true)
    {
        $head = 'INFO';
        if ($level == LogLevel::WARN) {
            $head = 'WARN';
        } elseif ($level == LogLevel::NOTE) {
            $head = 'NOTE';
        } elseif ($level == LogLevel::ERRO) {
            $head = 'ERRO';
        } elseif ($level == LogLevel::RESP) {
            $head = 'RESP';
        }

        $log_file = sf_conf('files.logfile');

        if (sf_conf('files.log_to_file') && $write_to_file) {
            if (! file_exists(dirname($log_file))) {
                try {
                    mkdir(dirname($log_file), 0700, true);
                    chown(dirname($log_file), exec('whoami'));
                    chmod(dirname($log_file), 0700);
                } catch (Exception $e) {
                    trigger_error($e->message, E_USER_WARNING);
                }
            }
        }

        $output = [];
        global $__minimal_output;

        foreach (preg_split('/\n|\r\n?/', $data) as $line) {
            if ((Synful::isCommandLineInterface()) || $force) {
                if ($block_header_on_echo || $__minimal_output) {
                    $output[] = $line;
                } else {
                    $out_line = '['.sf_color('SYNFUL', 'white', null, 'reset').'] ';
                    $out_line .= self::parseLogstring($level, $head, $line);
                    $output[] = $out_line;
                }
            }

            if (sf_conf('files.log_to_file') && $write_to_file) {
                if (sf_conf('files.split_log_files')) {
                    $log_id = 0;
                    $max_lines = sf_conf('files.max_logfile_lines');
                    while (file_exists($log_file) && (count(file($log_file)) - 1) > $max_lines) {
                        $log_id++;
                        $log_file = sf_conf('files.logfile').'.'.$log_id;
                    }
                }

                if (! file_exists($log_file)) {
                    try {
                        file_put_contents($log_file, '');
                        chmod($log_file, 0700);
                        chown($log_file, exec('whoami'));
                    } catch (Exception $e) {
                        trigger_error($e->message, E_USER_WARNING);
                    }
                }

                if (is_writable($log_file)) {
                    $line = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $line);
                    file_put_contents(
                        $log_file,
                        '['.time().'] [SYNFUL] ['.$head.'] '.$line."\r\n",
                        FILE_APPEND
                    );
                } else {
                    trigger_error('Failled to write to log file. Check permissions? '.
                                  'Disabling logging for rest of session.', E_USER_WARNING);
                    Synful::$config->set('files.log_to_file', false);
                }
            }
        }

        foreach ($output as $line) {
            echo $line.((! $block_header_on_echo && ! $__minimal_output)
                ? sf_color('', 'reset', null, 'reset') : '')."\r\n";
        }
    }

    /**
     * Used to catch error output from PHP and forward it to our log file.
     */
    public static function catchError($errno, $errstr, $errfile, $errline)
    {
        if (Synful::$config == null) {
            echo $errstr.' in '.$errfile.' at line '.$errline."\r\n";
            exit();
        }

        $err = $errstr.((sf_conf('system.production')) ? '' : ' in '.$errfile.' at line '.$errline);

        switch ($errno) {
            case E_USER_ERROR: {
                self::out(LogLevel::ERRO, 'Fatal Error: '.$err, false, false, true);
                if (! Synful::isCommandLineInterface()) {
                    $response = (new SynfulException(500, $errno, 'Fatal Error: '.$err))->response;
                    sf_respond($response->code, $response->serialize(), $to_file = true);
                    exit();
                }
                break;
            }

            case E_USER_WARNING: {
                self::out(LogLevel::WARN, 'Warning: '.$err, false, false, true);
                if (! Synful::isCommandLineInterface()) {
                    $response = (new SynfulException(500, $errno, 'Warning: '.$err))->response;
                    sf_respond($response->code, $response->serialize(), $to_file = true);
                    exit();
                }
                break;
            }

            case E_USER_NOTICE: {
                self::out(LogLevel::NOTE, 'Notice: '.$err, false, false, true);
                if (! Synful::isCommandLineInterface()) {
                    $response = (new SynfulException(500, $errno, 'Notice: '.$err))->response;
                    sf_respond($response->code, $response->serialize(), $to_file = true);
                    exit();
                }
                break;
            }

            default: {
                self::out(LogLevel::ERRO, 'Unknown Error: '.$err, false, false, true);
                if (! Synful::isCommandLineInterface()) {
                    $response = (new SynfulException(500, $errno, 'Unknown Error: '.$err))->response;
                    sf_respond($response->code, $response->serialize(), $to_file = true);
                    exit();
                }
                break;
            }
        }

        return true;
    }

    /**
     * Handles system shut down, closes out SQL Connection.
     */
    public static function onShutDown()
    {
        if (count(Synful::$sql_databases) > 0) {
            foreach (Synful::$sql_databases as &$database) {
                $database = null;
            }
        }
    }

    /**
     * Parses a log string with color codes and any other nessecary parsing.
     *
     * @param  LogLevel $level
     * @param  string   $head
     * @param  string   $message
     * @return string
     */
    public static function parseLogstring($level, $head, $message)
    {
        $return_string = '';

        if (sf_conf('system.color')) {
            switch ($level) {
                case LogLevel::INFO: {
                    $return_string = '['.sf_color($head, 'light_green', null, 'reset').'] ';
                    $return_string .= sf_color($message, 'white');
                    break;
                }

                case LogLevel::WARN: {
                    $return_string = '['.sf_color($head, 'light_red', null, 'reset').'] ';
                    $return_string .= sf_color($message, 'yellow', null, 'yellow');
                    break;
                }

                case LogLevel::NOTE: {
                    $return_string = '['.sf_color($head, 'light_blue', null, 'reset').'] ';
                    $return_string .= sf_color($message, 'white');
                    break;
                }

                case LogLevel::ERRO: {
                    $return_string = '['.sf_color($head, 'light_red', null, 'reset').'] ';
                    $return_string .= sf_color($message, 'red', null, 'red');
                    break;
                }

                case LogLevel::RESP: {
                    $return_string = '['.sf_color($head, 'light_cyan', null, 'reset').'] ';
                    $return_string .= sf_color($message, 'cyan', null, 'cyan');
                    break;
                }

                default: {
                    $return_string = '['.sf_color($head, 'light_green', null, 'reset').'] ';
                    $return_string .= sf_color($message, 'white');
                }
            }
        } else {
            $return_string = '['.$head.'] '.$message;
        }

        return $return_string;
    }
}
