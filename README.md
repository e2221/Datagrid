# Datagrid with Nextras/datagrid core
This is component for Nette 3 Framework.

It extends <a href="https://github.com/nextras/datagrid">Nextras/datagrid</a> so if you don´t know about this component, learn it in the <a href="https://nextras.org/datagrid/docs/3.0/">documentation</a>.<br>
This component is fully compatible with Nextras/datagrid and you can use it as Nextras/datagrid. 

Features
-
- You can´t take care about FilterFormFactory and EditFormFactory if you don´t want.
- Do you need to set html title of the Grid? No problem, you can set as title Nette\Utils\Html.
- Do you need filter in multiple columns? You can easily set on each column multiple filter that will be shown on the top row of the grid. You can also exclude any column from this filter.
- Rendering data-cells is completely in your hands without using template.blocks.latte. There are several powerful callbacks.
- Also, all wrappers tag table>, thead>, tbody>, tr>, <foot> you can simple style with callbacks.
- Do you need some header action (typically 'New row')? No problem, simply set header action.
- Or do you need on each data-row your custom action? With this datagrid it´s piece of cake. You can style buttons as you wish with callbacks. 
- Do you want to set choices to select count of data per page? No problem, I extended method setPagination with these possibilities.

Requirements
-
- PHP >=7.4
- Nette 3
- jQuery.min.js, Nittro.min.js, vendor/e2221/Datagrid/js/scripts.js, bootstrap.min.js
- bootstrap.min.css, fontawesome.min.css, vendor/e2221/style/Datagrid.css
 
Install
-
composer require e2221/datagrid

Documentation
-
<a href="https://github.com/e2221/Datagrid/wiki">Documentation</a>