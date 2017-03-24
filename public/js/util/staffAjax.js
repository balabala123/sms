//跟staff相关的ajax方法

function getStaffById(send_staff_id,url,staffData){

    $.ajax({
        url: url,
        type: 'post',
        dataType: 'json',
        data: {"staff_id": send_staff_id},
        success: function (data) {
            // console.log(data);
            staffData(data);
        },
        error: function (data, status, e) {
            alert("{:L('ALERT_CHECK_STAFF_ERR')}");
        }
    });
}