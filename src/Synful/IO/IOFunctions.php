<?php /** @noinspection PhpParamsInspection */

namespace Synful\IO;

use Ansi\Color16;
use Exception;
use Synful\Ansi\StringBuilder;
use Synful\Synful;
use Gestalt\Configuration;
use Synful\Config\ConfigLoader;
use Synful\Framework\SynfulException;

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
                Synful::$config = new Configuration(
                    (new ConfigLoader([
                        'directory' => './config/',
                    ]))->load()
                );
            } catch (Exception $ex) {
                trigger_error('Failed to load config: '.$ex->getMessage(), E_USER_WARNING);
                $return = false;
            }
        } else {
            trigger_error('Failed to load config: \'./config\' missing.', E_USER_WARNING);
            $return = false;
        }

        return $return;
    }

    /**
     * Prints output to the console and logs.
     *
     * @param  int     $level
     * @param  string  $data
     * @param  bool    $force
     * @param  bool    $block_header_on_echo
     */
    public static function out($level, $data, $force = false, $block_header_on_echo = false)
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

        $output = [];
        global $__minimal_output;

        foreach (preg_split('/\n|\r\n?/', $data) as $line) {
            if ((Synful::isCommandLineInterface()) || $force) {
                if ($block_header_on_echo || $__minimal_output) {
                    $output[] = $line;
                } else {
                    $sb = new StringBuilder();
                    $out_line = $sb->raw('[')->color16(
                        Color16::FG_WHITE,
                        'SYNFUL'
                    )->reset()->raw(']');
                    $out_line .= self::parseLogString($level, $head, $line);
                    $output[] = $out_line;
                }
            }
        }

        foreach ($output as $line) {
            $sb = new StringBuilder();
            echo $line.((! $block_header_on_echo && ! $__minimal_output)
                ? $sb->reset() : '')."\r\n";
        }
    }

    /**
     * Used to catch error output from PHP and forward it to our log file.
     *
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     *
     * @return bool
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
                self::out(LogLevel::ERRO, 'Fatal Error: '.$err, false, false);
                if (! Synful::isCommandLineInterface()) {
                    $response = (new SynfulException(500, $errno, 'Fatal Error: '.$err))->response;
                    sf_respond($response->code, $response->serialize());
                    exit();
                }
                break;
            }

            case E_USER_WARNING: {
                self::out(LogLevel::WARN, 'Warning: '.$err, false, false);
                if (! Synful::isCommandLineInterface()) {
                    $response = (new SynfulException(500, $errno, 'Warning: '.$err))->response;
                    sf_respond($response->code, $response->serialize());
                    exit();
                }
                break;
            }

            case E_USER_NOTICE: {
                self::out(LogLevel::NOTE, 'Notice: '.$err, false, false);
                if (! Synful::isCommandLineInterface()) {
                    $response = (new SynfulException(500, $errno, 'Notice: '.$err))->response;
                    sf_respond($response->code, $response->serialize());
                    exit();
                }
                break;
            }

            default: {
                self::out(LogLevel::ERRO, 'Unknown Error: '.$err, false, false);
                if (! Synful::isCommandLineInterface()) {
                    $response = (new SynfulException(500, $errno, 'Unknown Error: '.$err))->response;
                    sf_respond($response->code, $response->serialize());
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
        // Not implemented.
    }

    /**
     * Parses a log string with color codes and any other necessary parsing.
     *
     * @param  int      $level
     * @param  string   $head
     * @param  string   $message
     * @return string
     */
    public static function parseLogString($level, $head, $message)
    {
        $sb = new StringBuilder();

        if (sf_conf('system.color')) {
            switch ($level) {
                case LogLevel::INFO: {
                    $return_string = $sb->empty()
                                        ->raw('[')
                                        ->color16(
                                            Color16::FG_LIGHT_GREEN,
                                            $head
                                        )
                                        ->reset()
                                        ->raw('] ')
                                        ->color16(
                                            Color16::FG_WHITE,
                                            $message
                                        );
                    break;
                }

                case LogLevel::WARN: {
                    $return_string = $sb->empty()
                                        ->raw('[')
                                        ->color16(
                                            Color16::FG_LIGHT_RED,
                                            $head
                                        )
                                        ->reset()
                                        ->raw('] ')
                                        ->color16(
                                            Color16::FG_YELLOW,
                                            $message
                                        );
                    break;
                }

                case LogLevel::NOTE: {
                    $return_string = $sb->empty()
                                        ->raw('[')
                                        ->color16(
                                            Color16::FG_LIGHT_BLUE,
                                            $head
                                        )
                                        ->reset()
                                        ->raw('] ')
                                        ->color16(
                                            Color16::FG_WHITE,
                                            $message
                                        );
                    break;
                }

                case LogLevel::ERRO: {
                    $return_string = $sb->empty()
                                        ->raw('[')
                                        ->color16(
                                            Color16::FG_LIGHT_RED,
                                            $head
                                        )
                                        ->reset()
                                        ->raw('] ')
                                        ->color16(
                                            Color16::FG_RED,
                                            $message
                                        );
                    break;
                }

                case LogLevel::RESP: {
                    $return_string = $sb->empty()
                                        ->raw('[')
                                        ->color16(
                                            Color16::FG_LIGHT_CYAN,
                                            $head
                                        )
                                        ->reset()
                                        ->raw('] ')
                                        ->color16(
                                            Color16::FG_CYAN,
                                            $message
                                        );
                    break;
                }

                default: {
                    $return_string = $sb->empty()
                                        ->raw('[')
                                        ->color16(
                                            Color16::FG_LIGHT_GREEN,
                                            $head
                                        )
                                        ->reset()
                                        ->raw('] ')
                                        ->color16(
                                            Color16::FG_WHITE,
                                            $message
                                        );
                }
            }
        } else {
            $return_string = '['.$head.'] '.$message;
        }

        return $return_string;
    }
}
