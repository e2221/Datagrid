<?php
namespace e2221\Datagrid;

interface IDatagridFactory
{
    /**
     * @return Datagrid
     */
    function create();
}