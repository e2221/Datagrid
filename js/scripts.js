
(window._stack = window._stack || []).push(function (di) {
    di.getService('snippetManager').on('after-update', function () {
        $(function() {
            itemsPageChanger();
            selectAll();
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
    })
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
    })


}
