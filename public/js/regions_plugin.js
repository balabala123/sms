$(function() {
    // region plugin
    // @author Face 2017/01/13
    (function() {
        var region_class = 'mail_room_region_';
        var option = '', optionValue = '';

        // clear mailroom next select option
        function mailRoomSelectClearFun($this) {
            var mailroom_regions = $this.attr('mailroom_regions');
            var next = $this.next("select[mailroom_regions="+ mailroom_regions +"]");
            next.length && (next.hide().empty() && mailRoomSelectClearFun(next));
        }

        // fixed mailroom the select
        function mailRoomSelectFixed($this) {
            var key = $this.attr('data-step'), child = $this.prev().val();
            child = child.split(",");
            child = child[1];
            var optionData = [];
            option = '<option>请选择</option>';
            for(var i in region_data[key-1][child]) {
                optionData = region_data[key-1][child][i].split(":");
                optionValue = (typeof optionData[3] !== 'undefined') ? optionData[1] +','+ optionData[3] : optionData[1] ;
                option += '<option value="'+ optionValue +'">'+ optionData[0] +'</option>';
            }// end for

            $this.empty().append(option).show();
            mailRoomSelectClearFun($this);
        }

        // change event
        function mailRoomChangeFun() {
            option = "<option>请选择</option>";
            var $this = $(this), next = '', mailroom_regions, inputValue = '';
            mailroom_regions = $this.attr('mailroom_regions');

            mailRoomSelectClearFun($this);

            var key = $(this).attr('data-step');
            var thisValue = $(this).val(), child = '';
            if(thisValue.indexOf(',') > -1) {
                child = thisValue.split(',');
                inputValue = child[0];
                child = child[1];
                next = $this.next();

                if(typeof region_data[key] !== 'undefined') {
                    var tmpData = region_data[key][child];

                    // set option
                    var optionData = [];
                    for(var i in tmpData) {
                        optionData = tmpData[i].split(":");
                        optionValue = (typeof optionData[3] !== 'undefined') ? optionData[1] +','+ optionData[3] : optionData[1];
                        option += '<option value="'+ optionValue +'">'+ optionData[0] +'</option>';
                    }
                    next.empty().append(option).show();
                }

            } else {
                if(!thisValue || thisValue == "请选择") {

                    if($(this).prev().length) {
                        var prevValue = $(this).prev().val();
                        (prevValue.indexOf(',') > -1) && (prevValue = prevValue.split(","), prevValue = prevValue[0], thisValue = prevValue);
                    } else {
                        thisValue = '';
                    }
                }
                inputValue = thisValue;
            }

            $("input[mailroom_regions="+ mailroom_regions +"]").val(inputValue);
        }// end mailRoomChangeFun function

        // region initialize
        function regionInitialize() {
            $('select[mailroom_regions]').each(function(index,item) {
                var $this = $(this), select = '', selectStr = '', optionData = [], mailroom_regions = '', thisClass = '';

                option = "<option>请选择</option>";

                // 需要最后选中的地区 ID
                var data_regions = $this.attr('data-regions');

                mailroom_regions = region_class + index + '';
                thisClass = $this[0].className;
                select = $this;

                $this.attr("mailroom_regions", mailroom_regions);
                $this.attr('data-step', 1);

                var key = 1, region_data_length = region_data.length;
                for(var i = 1; i < region_data_length; i++) {
                    key = i + 1;
                    selectStr = '<select mailroom_regions="'+ mailroom_regions +'" class="' + thisClass +'" style="display:none" data-step="'+ key +'"><select>';
                    select.after(selectStr);
                    select = select.next();
                }

                // now,select is last by node;
                // set option
                for(var i in region_data[0]) {
                    optionData = region_data[0][i].split(":");
                    if(typeof optionData[3] !== 'undefined') {
                        optionValue = optionData[1] +','+ optionData[3];
                    } else {
                        optionValue = optionData[1];
                    }
                    option += '<option value="'+ optionValue +'">'+ optionData[0] +'</option>';
                }
                $this.append(option);

                // set input
                var input = $('<input type="hidden">');
                input.attr("mailroom_regions", mailroom_regions);
                $this.attr("name")  && (input.attr("name", $this.attr("name")), $this.attr("name", ''));
                $this.attr("id")    && (input.attr("id", $this.attr("id")), $this.attr("id", ''));
                $this.before(input[0]);

                data_regions && setMailRoomRegionValueFun($this, data_regions);

                $(document).on('change', "select[mailroom_regions="+ mailroom_regions +"]", mailRoomChangeFun);
            });// end each
        }// end regionInitialize function
        regionInitialize();

        function setMailRoomRegionValueFun(mainSelect, value) {
            var thisValue = value, region_data_length, step_stepArr = [], mailroom_regions = mainSelect.attr('mailroom_regions'), key;
            var optionData = [], tmpData = [];

            region_data_length = region_data.length;

            var iTag, iiTag, path, pathArr = [];
            iTag = iiTag = false; // set break tag
            for(var i in region_data) {
                if(i > 0) {
                    tmpData = region_data[i];
                    for(var ii in tmpData) {
                        for(var iii in tmpData[ii]) {
                            optionData = tmpData[ii][iii].split(":");
                            if(optionData[1] == thisValue) {
                                path = optionData[2];
                                iTag = iiTag = true;
                                break;
                            }
                        }// end iii for
                        if(iiTag) break;
                    }// end ii for
                    if(iTag ) break;
                } else{
                    tmpData = region_data[0];
                    for(var jj in tmpData) {
                        optionData = tmpData[jj].split(":");
                        if(optionData[1] == thisValue) {
                            path = optionData[2];
                            break;
                        }
                    }// end for
                }// end if > 0
            }// end i for

            if(!path) return false;
            pathArr = path.split(',');  // path array
            pathArr.pop();              // delete pathArr last element
            pathArr.shift();            // delete pathArr first element
            if(pathArr.length <= 0) return false;

            // set mailroom regions select
            var selectValue = 0, selectShow = false;
            $("select[mailroom_regions="+ mailroom_regions +"]").each(function(index,item) {
                if(selectShow) return false;
                selectValue = pathArr[index];
                if(!selectValue) {
                    mailRoomSelectFixed($(this));
                    selectShow = true;
                    return false;
                }
                key = $(this).attr('data-step');
                key--;
                option = '<option value="">请选择</option>';
                if(key > 0) {
                    tmpData = region_data[key];

                    var iIndex = 0, iIndexTag = false;// 匹配数据所在的数组的下标
                    for(var i in tmpData) {
                        for(var iii in tmpData[i]) {
                            optionData = tmpData[i][iii].split(":");
                            if(optionData[1] == selectValue) {
                                iIndex = i;// 找到
                                iIndexTag = true;
                                break;
                            }
                        }
                        if(iIndexTag) break;
                    }// end for

                    tmpData = region_data[key][iIndex];
                    for(var i in tmpData) {
                        optionData = tmpData[i].split(":");
                        optionValue = (typeof optionData[3] !== 'undefined') ? optionData[1] +','+ optionData[3] : optionData[1] ;
                        if(optionData[1] == selectValue) {
                            option += '<option selected="selected" value="'+ optionValue +'">'+ optionData[0] +'</option>';
                        } else {
                            option += '<option value="'+ optionValue +'">'+ optionData[0] +'</option>';
                        }
                    }// end for
                } else {
                    tmpData = region_data[0];
                    for(var i in tmpData) {
                        optionData = tmpData[i].split(":");
                        optionValue = (typeof optionData[3] !== 'undefined') ? optionData[1] +','+ optionData[3] : optionData[1] ;
                        if(optionData[1] == selectValue) {
                            option += '<option selected="selected" value="'+ optionValue +'">'+ optionData[0] +'</option>';
                        } else {
                            option += '<option value="'+ optionValue +'">'+ optionData[0] +'</option>';
                        }
                    }// end for
                }// end if
                $(this).empty().append(option).show();
            });

            $("input[mailroom_regions="+ mailroom_regions +"]").val(value);
        }// end function setMailRoomRegionValueFun
    })();
});