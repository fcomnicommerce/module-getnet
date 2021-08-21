<?php

namespace FCamara\Getnet\Api;

interface StatusByOrderIdInterface
{

    /**
     * @api
     *
     * @param int $id
     * @return mixed
     */
    public function execute(int $id);
}
