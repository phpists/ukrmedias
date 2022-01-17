document.querySelectorAll("div.model_files_sortable").forEach(function (div) {
    sortable(div);
    div.addEventListener('sortupdate', function (e) {
        var ids = [];
        $(e.target).find("div.js-file-div").each(function () {
            ids.push(this.dataset.id);
        });
        $.post("/admin/data/model-file-sort", {ids: ids});
    });
});
document.querySelectorAll("div.admin-sortable-grid").forEach(function (div) {
    var tbody = div.querySelector("table tbody");
    sortable(tbody);
    tbody.addEventListener('sortupdate', function (e) {
        var ids = [];
        $(e.target).find("tr").each(function () {
            ids.push(this.dataset.key);
        });
        tbody.classList.add("wait");
        $.post(div.dataset.sorturl, {ids: ids}, function () {
            tbody.querySelectorAll("tr td:first-child").forEach(function (td, index) {
                td.innerText = index + 1;
            });
            tbody.classList.remove("wait");
        });
    });
});
