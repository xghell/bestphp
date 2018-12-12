<?php


namespace Best\Route\Dispatch\Contract;


interface Dispatch
{
    /**
     * Execute the dispatch action, return the http response message
     *
     * @return mixed
     */
    public function execute();
}