<?php
/*
 * This file is part of Ecentria Services.
 *
 * (c) 2015, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Ecentria\Libraries\EcentriaRestBundle\Model;

use \Symfony\Component\HttpFoundation\Request;

/**
 * Requester Model - Holds information about the current requester (IP and username)
 *
 * @author Ryan Wood <ryan.wood@opticsplanet.com>
 */
class Requester
{
    /**
     * Name of requester
     *
     * @var string
     */
    private $username;

    /**
     * IP of requester
     *
     * @var string
     */
    private $ip;

    /**
     * Constructor
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $fromName = $request->headers->get('From');
        if (!empty($fromName)) {
            $this->username = $fromName;
        }
        $requesterName = $request->headers->get('X-EC-API-REQUEST-USER');
        if (!empty($requesterName)) {
            $this->username = $requesterName;
        }
        $this->ip = $request->getClientIp();
    }

    /**
     * Get name of requester
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Does this requester have an associated username
     *
     * @return bool
     */
    public function hasUsername()
    {
        return !empty($this->username);
    }

    /**
     * Get IP of requester
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Get string version of requester (just the username)
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getUsername();
    }
}
