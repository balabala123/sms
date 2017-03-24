// 一些公用基础的JS


//获取快递公司对应的价格，参数包裹快递公司ID，重量，区域编号，请求地址，对象赋值
//对应地址标准 "{:U('SystemUtil/freightCalculation')}"

function getExpressPrice(regions_id, express_company_id, weight, url, obj) {
    $.ajax({
        url: url,
        type: 'post',
        dataType: 'json',
        data: {"regions_id": regions_id, "express_company_id": express_company_id, "weight": weight},
        success: function (data) {
            console.log(data);
            if (data.retCode == 0) {
                // return data.price;
                obj.val(data.price);
            } else {
                alert(data.retMsg);
            }
        },
        error: function (data, status, e) {
            alert(data.retMsg);
        }
    });
}

//获取最优快递
function getBestExpressPrice(regions_id, express_type, weight, express_attr, url, fun, errFun) {
    $.ajax({
        url: url,
        type: 'post',
        dataType: 'json',
        data: {"regions_id": regions_id, "express_type": express_type, "weight": weight, "express_attr": express_attr},
        success: function (data) {
            console.log("==>",data);
            if (data.retCode == 0) {
                fun(data.expressId, data.expressPrice);
            } else {
                errFun(data.retMsg);
            }
        },
        error: function (data, status, e) {
            errFun(data.retMsg);
        }
    });
}