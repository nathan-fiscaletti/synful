<?php

namespace Synful\IO;

use Synful\Colors;
use Synful\IO\LogLevel;
use Synful\Synful;

use Exception;

/**
 * Class used to handle system wide IO
 */
class IOFunctions
{

    /**
     * Loads configuration file into system
     *
     * @return boolean
     */
    public static function loadConfig()
    {
        if (file_exists('./config.ini')) {
            try {
                Synful::$config = parse_ini_file('./config.ini', true);
                return true;
            } catch (Exception $ex) {
                trigger_error('Failed to load config: '.$ex->message, E_USER_ERROR);
                return false;
            }
        }
    }

    /**
     * Prints output to the console and logs
     *
     * @param  integer $level
     * @param  string  $data
     * @param  boolean $force
     * @param  boolean $block_header_on_echo
     * @param  boolean $write_to_file
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

        $log_file = Synful::$config['files']['logfile'];

        if (Synful::$config['files']['log_to_file'] && $write_to_file) {
            if (!file_exists(dirname($log_file))) {
                mkdir(dirname($log_file), 0700, true);
                chown(dirname($log_file), exec('whoami'));
                chmod(dirname($log_file), 0700);
            }
        }

        $output = [];

        foreach (preg_split('/\n|\r\n?/', $data) as $line) {
            if (Synful::$config['system']['standalone'] || $force) {
                if ($block_header_on_echo) {
                    $output[] = $line;
                } else {
                    $out_line = '['.Colors::cs('SYNFUL', 'white', null, 'reset').'] ';
                    $out_line.= IOFunctions::parseLogstring($level, $head, $line);
                    $output[] = $out_line;
                }
            }

            if (Synful::$config['files']['log_to_file'] && $write_to_file) {
                if (Synful::$config['files']['split_logfiles']) {
                    $log_id = 0;
                    $max_lines = Synful::$config['files']['max_logfile_lines'];
                    while (file_exists($log_file) && (count(file($log_file)) - 1) > $max_lines) {
                        $log_id++;
                        $log_file = Synful::$config['files']['logfile'].'.'.$log_id;
                    }
                }

                if (!file_exists($log_file)) {
                    @file_put_contents($log_file, '');
                    chmod($log_file, 0700);
                    chown($log_file, exec('whoami'));
                }

                if (is_writable($log_file)) {
                    file_put_contents(
                        $log_file,
                        '['.time().'] [SYNFUL] ['.$head.'] '.$line."\r\n",
                        FILE_APPEND
                    );
                } else {
                    $out_line = '['.Colors::cs('SYNFUL', 'white').'] ';
                    $out_line.=  IOFunctions::parseLogstring(
                        LogLevel::ERRO,
                        'ERRO',
                        'Failed to write to config file. Check permissions?'
                    );
                    $output[] = $out_line;

                    $out_line = '['.Colors::cs('SYNFUL', 'white').'] ';
                    $out_line.= IOFunctions::parseLogstring(
                        LogLevel::ERRO,
                        'ERRO',
                        'Disabling logging for the rest of the session'
                    );
                    $output[] = $out_line;

                    Synful::$config['files']['log_to_file'] = false;
                }
            }
        }

        foreach ($output as $line) {
            echo $line.((!$block_header_on_echo) ? Colors::cs('', 'reset', null, 'reset') : '')."\r\n";
        }
    }

    /**
     * Used to catch error output from PHP and forward it to our log file
     */
    public static function catchError($errno, $errstr, $errfile, $errline)
    {
            
        $err = $errstr.' in '.$errfile.' at line '.$errline;

        switch ($errno) {
            case E_USER_ERROR : {
                IOFunctions::out(LogLevel::ERRO, 'Fatal Error: '.$err);
                break;
            }

            case E_USER_WARNING : {
                IOFunctions::out(LogLevel::WARN, 'Warning: '.$err);
                break;
            }

            case E_USER_NOTICE : {
                IOFunctions::out(LogLevel::NOTE, 'Notice: '.$err);
                break;
            }

            default : {
                IOFunctions::out(LogLevel::ERRO, 'Unknown Error: '.$err);
                break;
            }
        }

        return true;
    }

    /**
     * Handles system shut down, closes out SQL Connection
     */
    public static function onShutDown()
    {
        if (Synful::$sql != null) {
            Synful::$sql->closeSQL();
        }

        if (count(Synful::$sql_databases) > 0) {
            foreach (Synful::$sql_databases as $database) {
                $database->closeSQL();
            }
        }

        IOFunctions::out(LogLevel::INFO, 'Synful API Shutdown!');
    }

    /**
     * Parses a log string with color codes and any other nessecary parsing
     *
     * @param  LogLevel $level
     * @param  string   $head
     * @param  string   $message
     * @return string
     */
    private static function parseLogstring($level, $head, $message)
    {
        $return_string = "";

        if (Synful::$config['system']['color']) {
            switch ($level) {
                case LogLevel::INFO : {
                    $return_string = '['.Colors::cs($head, 'light_green', null, 'reset').'] ';
                    $return_string.= Colors::cs($message, 'white');
                    break;
                }

                case LogLevel::WARN : {
                    $return_string = '['.Colors::cs($head, 'light_red', null, 'reset').'] ';
                    $return_string.= Colors::cs($message, 'yellow', null, 'yellow');
                    break;
                }

                case LogLevel::NOTE : {
                    $return_string = '['.Colors::cs($head, 'light_blue', null, 'reset').'] ';
                    $return_string.= Colors::cs($message, 'white');
                    break;
                }

                case LogLevel::ERRO : {
                    $return_string = '['.Colors::cs($head, 'light_red', null, 'reset').'] ';
                    $return_string.= Colors::cs($message, 'red', null, 'red');
                    break;
                }

                case LogLevel::RESP : {
                    $return_string = '['.Colors::cs($head, 'light_cyan', null, 'reset').'] ';
                    $return_string.= Colors::cs($message, 'cyan', null, 'cyan');
                    break;
                }

                default : {
                    $return_string = '['.Colors::cs($head, 'light_green', null, 'reset').'] ';
                    $return_string.= Colors::cs($message, 'white');
                }
            }
        } else {
            $return_string = '['.$head.'] '.$message;
        }

        return $return_string;
    }
}
