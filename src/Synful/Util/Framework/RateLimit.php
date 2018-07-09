<?php

namespace Synful\Util\Framework;

use \Touhonoob\RateLimit\RateLimit as Limiter;
use \Touhonoob\RateLimit\Adapter\APC as RateLimitAdapterAPC;

/**
 * Class used to manage Rate Limits
 */
class RateLimit
{
    /**
     * An unlimited rate limit constant.
     */
    public const Unlimited = 0;

    /**
     * The name of the RateLimitter.
     *
     * @var string
     */
    private $name;

    /**
     * The limit of requests that can be made
     * per $rate seconds.
     *
     * @var integer
     */
    private $limit = 0;

    /**
     * The rate in seconds.
     *
     * @var integer
     */
    private $rate = 0;

    /**
     * The adapter to use with this RateLimit.
     *
     * @var mixed
     */
    private $adapter = null;

    /**
     * Construct the RateLimit.
     *
     * @param string $name
     * @param int    $limit
     * @param int    $rate
     * @param mixed  $adapter
     */
    public function __construct($name, $limit, $rate, $adapter = null)
    {
        $this->name = $name;
        $this->limit = $limit;
        $this->rate = $rate;
        $this->adapter = ($adapter == null) 
            ? self::globalAdapter()
            : $adapter;
    }

    /**
     * Check if this connection is being rate limited.
     *
     * @param  mixed $id
     * @return boolean
     */
    public function isLimited($id)
    {
        $rateLimit = new Limiter(
            $this->name,
            $this->limit,
            $this->rate,
            $this->adapter
        );

        return $rateLimit->check($id) <= 0;
    }

    /**
     * Retrieve the limit.
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Retrieve the rate.
     *
     * @return int
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * Check if this is an unlimited rate limit.
     *
     * @return bool
     */
    public function isUnlimited()
    {
        return (
            $this->limit == self::Unlimited &&
            $this->rate == self::Unlimited
        );
    }

    /**
     * Retrieve the Global RateLimit.
     *
     * @return \Synful\Util\Framework\RateLimit
     */
    public static function global()
    {
        return new self(
            'Synful_Global',
            sf_conf('rate.global_rate_limit.requests'),
            sf_conf('rate.global_rate_limit.per_seconds')
        );
    }

    /**
     * Retrieve the rate limit adapter.
     *
     * @return mixed
     */
    public static function globalAdapter()
    {
        if (! function_exists('apcu_store')) {
            $response = (new SynfulException(500, 1031))->response;
            sf_respond($response->code, $response->serialize());
            exit;
        }

        return new RateLimitAdapterAPC();
    }
}