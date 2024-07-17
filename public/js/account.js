var signaturePad = new SignaturePad(document.querySelector('#signature-pad'), {
    backgroundColor: 'rgba(255, 255, 255, 0)',
    penColor: 'rgb(0, 0, 0)',
    velocityFilterWeight: .7,
    minWidth: 0.5,
    maxWidth: 2.5,
    throttle: 16, // max x milli seconds on event update, OBS! this introduces lag for event update
    minPointDistance: 3,
});

var approveButton = document.querySelector('#approve-signature');
var clearButton = document.querySelector('#clear-signature');

approveButton.addEventListener('click', function (event) {
    document.querySelector('#form_signature').value = document.querySelector('#signature-pad').toDataURL('image/png');
});

clearButton.addEventListener('click', function (event) {
    signaturePad.clear();
    document.querySelector('#form_signature').value = '';
});

function changerAvatar(img) {
    var avatars = document.querySelectorAll('.img-preview');
    avatars.forEach(avatar => {
        avatar.classList.remove('img-selected');
    });
    document.querySelector('.img-profile').src = img.src;
    img.classList.add('img-selected');
    $('#form_avatar').val(img.dataset.id);
}

function sendAgreement(input) {
    var formData = new FormData();
    formData.append("file", document.querySelector('#' + input).files[0]);
    $.ajax({
        url: "/send_agreement",
        method: "post",
        dataType: "json",
        processData: false,
        contentType: false,
        data: formData,
        success: function (r) {
            document.location.reload();
        },
        error: function (e) {
            console.error(e.getMessage());
        },
    });
}

function sendCertificate(input) {
    var formData = new FormData();
    formData.append("file", document.querySelector('#' + input).files[0]);
    $.ajax({
        url: "/send_certificate",
        method: "post",
        dataType: "json",
        processData: false,
        contentType: false,
        data: formData,
        success: function (r) {
            document.location.reload();
        },
        error: function (e) {
            console.error(e.getMessage());
        },
    });
}

function sendEvaluation(input) {
    var formData = new FormData();
    formData.append("file", document.querySelector('#' + input).files[0]);
    $.ajax({
        url: "/send_evaluation",
        method: "post",
        dataType: "json",
        processData: false,
        contentType: false,
        data: formData,
        success: function (r) {
            document.location.reload();
        },
        error: function (e) {
            console.error(e.getMessage());
        },
    });
}