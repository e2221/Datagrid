
(window._stack = window._stack || []).push(function (di) {
    di.getService('snippetManager').on('after-update', function () {
        $(function() {
            itemsPageChanger();
            selectAll();
            hideEditButtons();
            showEditButtons();
            datagridSortable();
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
        axis: 'y',
        update: function(event, ui) {
            var component_prefix, data, item_id, next_id, prev_id, row, url;
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
            let itemIdParam = $(this).data('itemidparam');
            let prevIdParam = $(this).data('previdparam');
            let nextIdParam = $(this).data('nextidparam');
            let getFields = {};
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

