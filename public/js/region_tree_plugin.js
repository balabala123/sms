$(function () {
    // region tree
    // @author Face 2017/01/22
    (function() {

        var checkbox_str            = '<div class="region_tree_list">',
            node_child_box_start    = '<div class="node-child-box">',
            node_child_box_end      = '</div>',
            region_tree_list        = '',
            node_child_box          = '',
            region_tree_data        = '',
            region_name             = 'region';

        var region_tree = '';

        // tree click event
        function regionNodeFun() {
            var $this = $(this);
            $this.hasClass('node-close') ?
                $this.removeClass('node-close').addClass('node-handle').parent('span').next('div.node-child-box').show() :
                $this.removeClass('node-handle').addClass('node-close').parent('span').next('div.node-child-box').hide();
        }

        // cancel superior checkbox
        function cancelSuperiorFun($this) {
            var pid = $this.attr('pid');
            var p = region_tree_list.find("input[nid=" + pid + "]");
            (p && p.length) && (p.prop("checked", $this.prop("checked")), cancelSuperiorFun($(p[0])));
        }

        // set subordinate checkbox
        function subordinateCheckboxFun(tag) {
            var tmp = node_child_box.find("input");
            tmp.length && tmp.prop('checked', tag);
        }

        // set region display
        function setRegionDisplayFun() {
            var grade2List = region_tree_list.find("input[grade=2]");

            var region_arr = [];

            grade2List.each(function(index, item) {
                var $this = $(this), checkedLength = 0, checkboxLength = 0, div, text = '';
                div = $this.parent().next('div.node-child-box');
                checkedLength = div.find("input[grade=4]:checked").length;
                checkboxLength = div.find("input[grade=4]").length;
                checkedLength && (text = checkboxLength == checkedLength ? '(全省)' : '(部分区域)',region_arr.push($this.attr('node_name') + text));
            });
            region_tree.text(region_arr.join(','));
        }// end function setRegionDisplayFun

        // set region value
        function setRegionValueFun() {
            var region_input, selectedCheckbox, selectedValue = "|";
            region_input = region_tree_list.find("input[type=hidden]");
            selectedCheckbox = region_tree_list.find("input[type=checkbox]:checked");
            selectedCheckbox.each(function(index, item) {
                selectedValue += $(this).val() + "|";
            });
            (selectedValue == "|") && (selectedValue = "");
            region_input.val(selectedValue);
        }

        // checkbox click event function
        function checkboxFun() {
            var $this = $(this), tag;
            node_child_box = $this.parent('span').next('div.node-child-box');
            region_tree_list = $this.parents('div.region_tree_list');
            tag = $this.prop('checked');
            tag == false && cancelSuperiorFun($this);
            node_child_box.length && subordinateCheckboxFun(tag);
            setRegionValueFun();
            setRegionDisplayFun();
        }

        $(document).on('click', '.region_node', regionNodeFun);
        $(document).on('click', '.region_tree_list input[type=checkbox]', checkboxFun);

        function traversalTree(data,tag) {
            var childClass = 'node-hasc';
            var checkedStr = 'checked="checked"', checkedAttribute = '';
            var result = false;
            tag || (tag = 'father');
            tag == 'child' && (checkbox_str += node_child_box_start);
            for(var i in data) {

                (typeof data[i].list !== 'undefined') ? (childClass = 'node-hasc', tag = 'child') : childClass = '' ;

                region_tree_data && (checkedAttribute = region_tree_data.indexOf("|" + data[i].id + "|") > -1 ? checkedStr : '');

                checkbox_str += '<span class="node ' + childClass + '">' +
                '<span class="node-close region_node" pid="' + data[i].parent_id + '" nid="' + data[i].id + '" hasc="9">&nbsp;&nbsp; </span>' +
                '<input type="checkbox" node_name="' + data[i].name + '" grade="' + data[i].grade + '" value="' + data[i].id + '" nid="' + data[i].id + '" pid="' + data[i].parent_id + '" ' + checkedAttribute + '>' +
                '<span class="node-name">' + data[i].name + '</span>' +
                '</span>';

                (typeof data[i].list !== 'undefined') && traversalTree(data[i].list, tag);
            }// end for

            tag == 'child' && (checkbox_str += node_child_box_end);
        }// end function traversalTree

        // region tree initialize
        $('.region_tree').each(function (index, item) {
            $(this).attr('data-index', index);
            var $this = $(this);
            initializeTreeFun($this, index);

            $this.before("<span class='region_span' style='margin: 0px;'></span>");
            region_tree = $this.prev('span.region_span');
            region_tree_list = $this.next('div.region_tree_list');
            setRegionDisplayFun();
        });

        function initializeTreeFun($this, index) {
            var thisName = $this.attr("name"), thisId = $this.attr("id");
            region_tree_data = $this.attr("region_tree_data");

            region_name = thisName ? thisName : region_name + "_" + index;
            thisId || (thisId = "region_input_" + index);

            checkbox_str = '<div class="region_tree_list" id="region_tree_list_' + index + '">';
            checkbox_str += '<input type="hidden" id="input_' + thisId + '" name="' + region_name + '" value="' + region_tree_data + '">';
            traversalTree(region_data_tree, 'father');

            checkbox_str += '</div>';
            $this.after(checkbox_str);

            $("#region_tree_list_" + index).append('<a href="javascript:;" class="determine_region_tree">确定</a>');
        }

        // add mask display is none
        $('body').append('<div id="region_tree_mask" style="width: 100%;height: 100%;position: absolute;top: 0px;left: 0px;z-index: 100;background-color: white;display: none;"></div>');

        // the tree click event
        $("body").on('click', '.region_tree', function () {
            var $this = $(this);
            region_tree = $this.prev('span.region_span');
            var index = $this.attr('data-index');

            if(!index) {
                $('.region_tree').each(function (iindex, item) {
                    if($(this)[0] === $this[0]) {
                        $this.attr("data-index", iindex);
                        index = iindex;
                        initializeTreeFun($this, iindex);
                    }
                });
            }


            var mask = $('#region_tree_mask');
            var region_tree_list_index = $("#region_tree_list_" + index);

            mask.show();
            region_tree_list_index.css('z-index', mask.css('z-index') * 1 + 1 * 1);
            region_tree_list_index.show();
        });

        $("body").on("click",".determine_region_tree", function() {
            var region_tree_list = $(this).parents('.region_tree_list');
            region_tree_list.hide(), $('#region_tree_mask').hide();
        });

    })();
});