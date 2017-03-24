$(function() {
    Array.prototype.contains = function ( needle ) {
        for (var i in this) {
            if (this[i] == needle) return true;
        }
        return false;
    }
    var selectDataArr = [];
    function selectChangeFun() {
        selectDataArr = [];
        var value = $(this).val();
        $(this).removeClass("sel_way");
        $(this).attr('data-value', $(this).val());
        $("select").find("option").attr("disabled", false);
        var select = $("select").each(function () {
            selectDataArr.contains($(this).val()) || selectDataArr.push($(this).val());
        });
        selectDataArr.filter(function(item) {
            $("select option[value=" + item + "]").attr("disabled", true);
        });

        select.each(function () {
            $(this).find("option[value=" + $(this).attr('data-value') + "]").attr("disabled",false);
        });
    }


    $(document).on("change", "select", selectChangeFun);
});