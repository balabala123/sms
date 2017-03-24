//公用一些验证方法

function alertShow(msg) {
    Wind.use("artDialog", "iframeTools", function() {
        art.dialog.tips(msg, 3);
    });
}

function _validateNumber(content,minL,maxL){
    if(content == null || content == ""){
        return false;
    }else{
        if(content.length < minL || content.length > maxL){
            return false;
        }else{

        }
    }
}
