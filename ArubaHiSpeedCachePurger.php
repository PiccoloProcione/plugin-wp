<?php
/**
 * @category Project
 * @package  Aruba-HiSpeed-Cache
 * @author   Aruba Developer <hispeedcache.developer@aruba.it>
 * @license  https://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */
declare(strict_types=1);

namespace ArubaHiSpeedCache;

/**
 * Undocumented class
 */
abstract class ArubaHiSpeedCachePurger
{

    /**
     * $servr_host for the requst
     *
     * @var string
     */
    protected string $serverHost;

    /**
     * $server_port for the request
     *
     * @var string
     */
    protected string $serverPort;

    /**
     * $time_out of request
     *
     * @var integer
     */
    protected int $timeOut;

    /**
     * Purge the cache of a single page
     *
     * @param  string $url
     * @return void
     */
    abstract public function purgeUrl(string $url);

    /**
     * Purge the cache of a list of pages
     *
     * @param  array $urls
     * @return void
     */
    abstract public function purgeUrls(array $urls);

    /**
     * Purge the alla chace of site
     *
     * @return void
     */
    abstract public function purgeAll();


    /**
     * DoRemoteGet
     *
     * @param string $path path to purge
     * @param string $host the host name for log reason
     *
     * @return void
     */
    abstract public function doRemoteGet(string $path = '/', string $host = '');

    /**
     * PreparePurgeRequestUri
     *
     * @param string $url Url to prepare
     *
     * @return string for the purge request
     */
    public function preparePurgeRequestUri(string $url)
    {
        return \sprintf(
            "http://%s:%s/purge%s",
            $this->getServerHost(),
            $this->getServerPort(),
            filter_var($url, FILTER_SANITIZE_URL)
        );
    }

    /**
     * Undocumented function
     *
     *  $config [
     *  'time_out'     => int 5;
     *  'server_host'  => string '127.0.0.1'
     *  'server_port'  => string '8889'
     *  ];
     *
     * @param array $configs
     * @return void
     */
    public function setPurger(array $configs)
    {
        $this->setTimeOut($configs['time_out']);
        $this->setServerHost($configs['server_host']);
        $this->setServerPort($configs['server_port']);
    }

    /**
     * Get undocumented variable
     *
     * @return  integer
     */
    public function getTimeOut()
    {
        return $this->timeOut;
    }

    /**
     * Set undocumented variable
     *
     * @param integer $timeOut Undocumented variable
     *
     * @return self
     */
    public function setTimeOut($timeOut)
    {
        $this->timeOut = $timeOut;

        return $this;
    }

    /**
     * Get undocumented variable
     *
     * @return  string
     */
    public function getServerPort()
    {
        return $this->serverPort;
    }

    /**
     * Set undocumented variable
     *
     * @param string $serverPort Undocumented variable
     *
     * @return self
     */
    public function setServerPort(string $serverPort)
    {
        $this->serverPort = $serverPort;

        return $this;
    }

    /**
     * Get undocumented variable
     *
     * @return string
     */
    public function getServerHost()
    {
        return $this->serverHost;
    }

    /**
     * Set undocumented variable
     *
     * @param string $serverHost Undocumented variable
     *
     * @return self
     */
    public function setServerHost(string $serverHost)
    {
        $this->serverHost = $serverHost;

        return $this;
    }
}
