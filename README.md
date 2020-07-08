# Extended Nextras/datagrid
This package extends <a href="https://github.com/nextras/datagrid">Nextras/datagrid</a> (thanks to Nextras community to build such awesome tool!).
<br>The main goal of this package is simplify using Nextras/datagrid to write less and to create fewer templates with defined blocks

Requirements
-
- PHP >=7.4
- Nette 3
- jQuery.min.js, Nittro.min.js, vendor/e2221/Datagrid/js/scripts.js, bootstrap.min.js
- bootstrap.min.css, fontawesome.min.css,
 vendor/e2221/style/Datagrid.css

Features
-
- You can´t set FilterFormFactory and EditFormFactory, this extension generates it automatically
- You can modify each cell rendering with callbacks
- You can simply hide column
- You can set header action
- You can set row actions with callbacks
- You can set the Grid template styles without using template with blocks .latte
- Paginator and options of pagination are extended


Init + get data source
-
```php
//init   
$grid = new \e2221\Datagrid\Datagrid();

//get data from source (without change)
$grid->setDataSourceCallback(function ($filter, $order, ?\Nette\Utils\Paginator $paginator){
    return $this->manager->getData($filter, $order, $paginator);
});
```

Column
- 
You can simply hide column:
```php
$column = $grid->addColumn('id');
$column->setHidden();
```
If you want to filter or edit data, you can´t set FilterFormFactory and EditFormFactory, this extension generate this form automatically, 
so if you can make grid filterable or editable you can write only this:
```php
$column = $grid->addColumn('name');
//to set column filterable
$column->setFilterable();
//to set column editable
$column->setEditable();


//every column is generated as text so you can´t call this explicitly
$column->setHtmlType('text');
//but if you want, column could be email, password, textarea, etc...
$column->setHtmlType('email');
//or it could be also select
$column->setHtmlType('select');
//if it´s select you have to set selection
$column->setEditSelection(['1'=>'My selection 1', '2' => 'My selection 2']);

//set column required (only for editing)
$column->setRequired();

//after that you can set EditFormCallback standard way
$column->setEditFormCallback([$this, 'EditFormCallback']);
```
You can also change rendering of cells in each column without setting block in a template.
<br>
There are several helpful callbacks that you can set.
All callbacks have parameters ($row - instance of current row, $primary - primary key, $cell - value of current cell)
```php
$column->setCellValueCallback(function($row, $primary, $cell){
    if(!$this->canISeeThisValue($primary))
        return 'You can´t permission to see that!';
    return $cell;
});
```
You can wrap your text with html attributes, or you can write all in this callback:
```php
$column->setHtmlCallback(function($row, $primary, $cell){
    return Html::el('a')
        ->setAttribute('class', 'text-sm text-primary')
        ->href((string)$cell)
        ->setAttribute('target', '_blank');
});
``` 
Or if you want to print rendered text only as link:
```php
$column->setLinkCallback(function($row, $primary, $cell){
    return 'https://google.com?q=' . $cell;
});
```

Global header actions
-
These actions are render on the header of Datagrid on the right side
```php
function addCustomAction(string $name, ?string $title=null){};
```
Default header Action class is 'btn btn-xs btn-secondary', but you can change what you want
```php
$grid->addCustomAction('actionName', 'Action Title')
        // You can set own class with string
        ->setClass('myClass')
        // Or you can only add your classes as array
        ->setAddClass(['classToAdd', 'classToAlsoAdd'])
        // You can set Icon
        ->setIconClass('fas fa-plus')
        // Link to signal in your presenter/component
        ->setLink($this->link('addEntry!'))
        // If you want to create javascript confirmation message
        ->setConfirmationMessage('Do you really want to create new entry?')
        // You can also set any HTML attribute
        ->setDataAttributes(['id' => 'myId', 'data-myDataAttribute' => 'value']);
```
Row actions
-
Row Action has similar options as header actions but for each setter you can use callbacks.<br>
All callbacks has in parameters ($row - instance of current row, $primary - primary key of current row)
```php
function addRowCustomAction(string $uniqueNmae, ?string $title=null){};
```
```php
$grid->addRowCustomAction('delete', 'Delete')
    //You can use set class foreach fields static
    ->setClass('btn btn-xs btn-danger')
    //or callback dynamic
    ->setClassCallback(function($row, $primary){
        if($primary < 20)
            return 'btn btn-xs disabled';
        return 'btn btn-xs btn-danger';
    })  
    //you can set icon
    ->setIconClass('fa fa-trash')
    //you can set link or linkCallback
    ->setLinkCallback(function ($row, $primary){
       return $this->link('deleteItemEntry!', $primary);
    })
    //and also javascript confirmation callback
    ->setConfirmationMessageCallback(function ($row, $primary){
       return 'Opravdu chcete smazat záznam ' .  $row->title . '?';
    })
    //you can show it only if you want
    ->setShowIfCallback(function ($row, $primary){
        return $primary > 15;
    });
```
If you have editable grid you can also change rendering of edit/save/cancel buttons, the same properties as on row actions and also with callbacks:
```php
//edit button
 $grid->getRowActionEdit()
    ->setShowIfCallback(function ($row, $primary){
        return (bool)$this->hasAccessToEdit($primary);
    });
  
//save button
 $grid->getRowActionSave()
    ->setConfiramationMessageCallback(function ($row, $primary){
        return 'Do you really want to delete ' . $row->name;
    });

//cancel button
 $grid->getRowActionCancel();
```
Item Detail
-
One of predefined Row action is Item detail.
<br>All callbacks has again parameters ($row, $primary)

Example with content callback:
```php
$grid->setRowActionItemDetail()
    ->setShowIfCallback(function ($row, $primary){
        return $primary < 15;
    })
    ->setContentCallback(function($row, $primary){
        $text = Html::el('p')
            ->setText('text of detail id ' . $primary);
        return Html::el('div')
            ->setAttribute('class', 'bg-primary')
            ->addHtml($text);
    });
```
Or you have to add cells template with block #row-item-detail
Available variables: $primary, $row
```latte
{block row-item-detail}
    {if $primary < 15}
        Item detail row {$primary}
    {/if}
{/block}
```
Or with tr tag to decoration
Available variables: $itemDetailId, $colspan, $primary, $row
```latte
{define row-item-detail-tr}
    {* You have to put item Detail Id *}
    <tr id="{$itemDetailId}" class="collapse">
        <td colspan="{$colspan}">
            Your content ...
        </td>
    </tr>
{/define}
```
Pagination
-
Method $grid->setPagination is overwrite now you can set 3rd (array with show items on one page) and 4th argument (string with option all option title).<br>
Both parameters are optional.
```php
function setPagination(int $defaultCountPerPage, callable $getData, ?array $itemsPerPageSettings=null, ?string $showAllTitle=null){};
```

```php
$grid->setPagination(15, function ($filter){
     return $this->manager->getData($filter);
}, [15, 30, 60], 'Show All');
```

Document Template
-
You can style grid table without change your template.blocks.latte
```php
$documentTemplate = $grid->getDocumentTemplate();

//set grid title
$documentTemplate->setTableTitle(Html::el('h6')
                   ->setText('Title of DataGrid!')
                   ->setAttribute('class', 'float-right')
                   );

//grid table class is by default set 'table table-sm table-border table-hover table-stripped'
//you can change anything you want

//set own class as text
$documentTemplate->setTableClass('myClass');

//or only add classes array
$documentTemplate->setAddClass(['myClass', 'mySecondClass']);

//remove default settings (stripped, border, hover)
$documentTemplate->removeStyles();

//set border, stripped, hover, borderles [true, false] - true is default value
$documentTemplate->setStripped();
$documentTemplate->setHover(false);
$documentTemplate->setBorder(true);
$documentTemplate->setBorderless();
```


Why to use this package?
-
There are several another DataGrids, why to use this?
From my opinion there are this advantages:
- Nextras/datagrid core of this package works great
- Easy to start using
- If you are using Nextras/Datagrid you can continue using it, packages are compatible
- You can take control about a render with 100% with callbacks or settings your own template with blocks
- You can use this Datagrid on same page as many times on one Presenter/Control as you want without any conflicts

TODO List
-
1) Create playground with dummy data
2) Support sortable (jQuery-sortable)
3) Filter on multiple columns
4) Export data
 


