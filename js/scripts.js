
(window._stack = window._stack || []).push(function (di) {
    di.getService('snippetManager').on('after-update', function () {
        $(function() {
            itemsPageChanger();
            selectAll();
            hideEditButtons();
            showEditButtons();
        });
    });
});


var openLinkAjax;
openLinkAjax = function(url, params){
    _context.invoke(function(di) {
        di.getService('page').open(url, 'POST', params);
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
            openLinkAjax(link);
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
            var editButtons_a = document.querySelectorAll('.datagrid-edit-button');
            editButtons_a.forEach(function(button){
                button.classList.add("d-none");
            });
        });
    });
}

/**
 * Show edit buttons
 */
function showEditButtons()
{
    let cancelButton = document.querySelector('.datagrid-cancel-button');
    cancelButton.addEventListener("click", function(){
        var editButtons_a = document.querySelectorAll('.datagrid-edit-button');
        editButtons_a.forEach(function(button){
            button.classList.remove("d-none");
        });
    });
}
