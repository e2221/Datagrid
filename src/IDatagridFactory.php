<?php
namespace App\Tools\Datagrid;

use e2221\Datagrid\Datagrid;

interface IDatagridFactory
{
    /**
     * @return Datagrid
     */
    function create();
}