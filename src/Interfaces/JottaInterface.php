<?php

/*
 * This file is a part of the Sigrun's PHP SDK for Jottacloud
 * storage service;
 *
 * @author Sigrun Sp. z o.o. <sigrun@sigrun.eu>
 * @copy (C)2019 Sigrun Sp. z o.o. All rights reserved.
 */

namespace Jotta\Interfaces;

interface JottaInterface
{
    /**
     * Class constructor.
     *
     * @param string $username
     * @param string $password
     * @param array  $arguments
     *
     * @throws Exception
     */
    public function __construct(string $username, string $password, array $arguments = []);
}
