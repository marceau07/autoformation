function sendTp(tp_id, input) {
    var formData = new FormData();
    formData.append("tp_id", tp_id);
    formData.append("file", document.querySelector('#' + input).files[0]);
    $.ajax({
        url: "/send_tp",
        method: "post",
        dataType: "json",
        processData: false,
        contentType: false,
        data: formData,
        success: function (r) {
            document.location.reload();
        }
    });
}