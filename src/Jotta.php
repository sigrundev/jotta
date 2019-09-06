<?php

/*
 * This file is a part of the Sigrun's PHP SDK for Jottacloud
 * storage service;
 *
 * @author Sigrun Sp. z o.o. <sigrun@sigrun.eu>
 * @copy (C)2019 Sigrun Sp. z o.o. All rights reserved.
 */

namespace Jotta;

use Exception;
use GuzzleHttp\Client;
use Jawira\CaseConverter\Convert as Jawira;
use Jotta\Interfaces\JottaInterface;

class Jotta implements JottaInterface
{
    /**
     * Client container.
     *
     * @var Client
     */
    protected $client;

    /**
     * Username.
     *
     * @var string
     */
    protected $username;

    /**
     * Password.
     *
     * @var string
     */
    protected $password;

    /**
     * Synchronous request flag.
     *
     * @var bool
     */
    protected $synchronous = true;

    /**
     * HTTP errors flag.
     *
     * @var bool
     */
    protected $httpErrors = false;

    /**
     * Allow redirects flag.
     *
     * @var bool
     */
    protected $allowRedirects = true;

    /**
     * Class constructor.
     *
     * @param string $username
     * @param string $password
     * @param array  $arguments
     *
     * @throws Exception
     */
    public function __construct(string $username, string $password, array $arguments = [])
    {
        $this->username = $username;
        $this->password = $password;

        $clientData = $this->makeClientData($arguments);

        try {
            $this->client = new Client($clientData);
        } catch (Exception $e) {
            // TODO
            throw $e;
        }
    }

    /**
     * Make array of GuzzleHTTP default request data, by merging
     * data from $customArguments, standarizing their keys to snake case
     * and merging them with auth data.
     *
     * @param array $customArguments
     *
     * @return array
     */
    protected function makeClientData(array $customArguments = [])
    {
        $arguments = $this->getArguments();
        $customArguments = $this->sanitizeArguments($customArguments);

        $clientData = [];

        foreach ($arguments as $argument) {
            $converted = (new Jawira($argument))->toSnake();
            if (isset($customArguments[$converted])) {
                $clientData[$converted] = $customArguments[$converted];
            } else {
                $clientData[$converted] = $this->{$argument};
            }
        }

        $clientData['auth'] = [
            [$this->username, $this->password],
        ];

        return $clientData;
    }

    /**
     * Get array of client's default request arguments, by
     * reading class vars (without client var).
     *
     * @return array
     */
    protected function getArguments()
    {
        $arguments = get_class_vars(\get_class($this));
        unset($arguments['client']);

        return $arguments;
    }

    /**
     * Make Jottacloud base Uri from given username.
     *
     * @param string $username
     *
     * @return string
     */
    protected function makeBaseUri(string $username)
    {
        return 'https://www.jottacloud.com/jfs/'.$username;
    }

    /**
     * Sanitize arguments by converting their keys into snake case.
     *
     * @param array $arguments
     *
     * @return array
     */
    protected function sanitizeArguments(array $arguments = [])
    {
        if (0 === \count($arguments)) {
            return [];
        }

        $sanitized = [];

        foreach ($arguments as $key => $value) {
            $converted = (new Jawira($key))->toSnake();
            if (isset($converted) && 'client' !== $converted) {
                $sanitized[$converted] = $value;
            }
        }

        return $sanitized;
    }
}
