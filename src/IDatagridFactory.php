<?php


namespace App\Tools\Datagrid;


interface IDatagridFactory
{
    /**
     * @return Datagrid
     */
    function create();
}