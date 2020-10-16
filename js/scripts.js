
(window._stack = window._stack || []).push(function (di) {
    di.getService('snippetManager').on('after-update', function () {
        $(function() {
            itemsPageChanger();
            selectAll();
            hideEditButtons();
            showEditButtons();
            datagridSortable();
            datagridConnect();
            datagridDraggable();
            datagridDropable();
        });
    });
});


var openLinkAjax;
openLinkAjax = function(url, method, params){
    method = method || 'POST';
    _context.invoke(function(di) {
        di.getService('page').open(url, method, params);
    });
}


/**
 * Check all checkboxes by dataset name
 * @type {NodeListOf<Element>}
 */
function selectAll()
{
    let selector = document.querySelectorAll('.datagrid-select-all');
    selector.forEach(function (item) {
        item.addEventListener("change", function () {
            var checkboxListSelector = document.querySelectorAll('.' + item.dataset.name);
            checkboxListSelector.forEach(function (checkbox){
                checkbox.checked = item.checked;
            });
        })
    });
}


/**
 * Items per page changer
 */
function itemsPageChanger()
{
    let itemsPerPageSelector = document.querySelectorAll('.datagrid-items-per-page');
    itemsPerPageSelector.forEach(function (item) {
        item.addEventListener("change", function () {
            let link = this.options[this.selectedIndex].dataset.link;
            openLinkAjax(link, 'POST');
        })
    });
}

/**
 * Hide edit buttons
 */
function hideEditButtons()
{
    let editButtons = document.querySelectorAll('.datagrid-edit-button');
    editButtons.forEach(function(item){
        item.addEventListener("click", function (){
            var btns = document.querySelectorAll("[data-datagrid-edit="+item.dataset.datagridName+"]");
            btns.forEach(function(button){
                button.classList.add("disabled");
            });
        });
    });
}

/**
 * Show edit buttons
 */
function showEditButtons()
{
    let cancelButtons = document.querySelectorAll('.datagrid-cancel-button');
    cancelButtons.forEach(function (item) {
        item.addEventListener("click", function () {
            let buttons = document.querySelectorAll("[data-datagrid-edit='"+item.dataset.datagridName+"']");
            buttons.forEach(function(button){
                button.classList.remove("disabled");
            });
        });
    });
}

datagridSortable = function() {
    if (typeof $.fn.sortable === 'undefined') {
        return;
    }
    return $('.grid [data-sortable]').sortable({
        handle: '.handle-sort',
        items: 'tr',
        update: function(event, ui) {
            let item_id, next_id, prev_id, row, url;
            row = ui.item.closest('tr[data-id]');
            item_id = row.data('id');
            prev_id = null;
            next_id = null;
            if (row.prev().length) {
                prev_id = row.prev().data('id');
            }
            if (row.next().length) {
                next_id = row.next().data('id');
            }
            url = $(this).data('sortable-url');
            let itemIdParam = $(this).data('itemIdParam');
            let prevIdParam = $(this).data('prevIdParam');
            let nextIdParam = $(this).data('nextIdParam');
            let getFields;
            getFields = {
                [itemIdParam]: item_id,
                [prevIdParam]: prev_id,
                [nextIdParam]: next_id,
            };
            return openLinkAjax(url, 'GET', getFields);
        },
        helper: function(e, ui) {
            ui.children().each(function() {
                return $(this).width($(this).width());
            });
            return ui;
        }
    });
};

datagridConnect = function() {
    if (typeof $.fn.sortable === 'undefined') {
        return;
    }
    return $('.grid [data-connect]').sortable({
        handle: '.handle-connect',
        items: 'tr',
        connectWith: '.datagrid-connected-grids',
        receive: function(event, ui) {
            let item_id, next_id, prev_id, row, url, table_id;
            row = ui.item.closest('tr[data-id]');
            item_id = row.data('id');
            prev_id = null;
            next_id = null;
            if (row.prev().length) {
                prev_id = row.prev().data('id');
            }
            if (row.next().length) {
                next_id = row.next().data('id');
            }
            url = $(this).data('connect-url');
            table_id = row.parent().data('tableId')
            let itemIdParam = $(this).data('itemIdParam');
            let prevIdParam = $(this).data('prevIdParam');
            let nextIdParam = $(this).data('nextIdParam');
            let tableIdParam = $(this).data('connectTableIdParam');
            let getFields;
            getFields = {
                [itemIdParam]: item_id,
                [prevIdParam]: prev_id,
                [nextIdParam]: next_id,
                [tableIdParam]: table_id,
            };
            return openLinkAjax(url, 'GET', getFields);
        },
        helper: function(e, ui) {
            ui.children().each(function() {
                return $(this).width($(this).width());
            });
            return ui;
        }
    });
};

datagridDraggable = function() {
    if (typeof $.fn.draggable === 'undefined') {
        return;
    }
    return $('.grid [data-draggable]').draggable({
        handle: '.handle-drag',
        scope: "datagrid-draggable-items",
        revert: "invalid",
        zIndex: 2000,
        helper: function(e, ui){
            let helper, helperText;
            helper=$(this);
            helperText = helper.data('helper');
            if(helperText)
            {
                return $("<div class='ui-widget-header'>"+helperText+"</div>");
            }else{
                return $(this).clone();
            }
        },
        opacity: 0.8,
        cursor: "move",
        cursorAt: { top: 0, left: 0 },
    });
}

datagridDropable = function(){
    return $('.grid [data-droppable]').droppable({
        scope: "datagrid-draggable-items",
        drop: function(e, ui){
            let $drop, parent, effect_class, $drag, url, itemIdParam, itemMovedParam;
            $drop = $(this);
            parent = $drop.parent();
            effect_class = parent.data('effect')
            url = parent.data('dropUrl');
            itemIdParam = parent.data('itemIdParam');
            itemMovedParam = parent.data('itemMovedParam');
            if(effect_class){
                $drop.removeClass(effect_class);
            }
            $drag = $(ui.draggable);
            let getFields = {};
            getFields = {
                [itemIdParam]: $drag.data('id'),
                [itemMovedParam]: $drop.data('id'),
            };
            return openLinkAjax(url, 'GET', getFields);
        },
        over: function (e, ui){
            let $drop, effect, effect_class;
            $drop = $(this);
            effect = $drop.parent();
            effect_class = effect.data('effect')
            if(effect_class){
                $drop.addClass(effect_class);
            }
        },
        out: function (e, ui){
            let $drop, effect, effect_class;
            $drop = $(this);
            effect = $drop.parent();
            effect_class = effect.data('effect')
            if(effect_class){
                $drop.removeClass(effect_class);
            }
        },
    });
}
