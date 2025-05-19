function giveTO(list = []) {
    var interval_to = 150;
    if (list.length >= 50) {
        interval_to = 1;
    } else if (list.length >= 25) {
        interval_to = 25;
    } else if (list.length >= 10) {
        interval_to = 50;
    }
    return interval_to;
}

function sendFeedback() {
    var formData = new FormData();
    formData.append("form_feedback_category", document.querySelector('#form_feedback_category').value);
    formData.append("form_feedback_annotation", document.querySelector('#form_feedback_annotation').value);
    formData.append("form_feedback_link", document.querySelector('#form_feedback_link').value);
    formData.append("form_feedback_weight", document.querySelector('#form_feedback_weight').value);
    $.ajax({
        url: "/feedback",
        method: "post",
        dataType: "json",
        processData: false,
        contentType: false,
        data: formData,
        success: function (r) {
            if (r.status == 200) {
                $('#form_feedback')[0].reset();
                $('#modalFeedback').modal('hide');
            }
        },
        error: function (e) {
            console.error(e);
        },
    });
    $('#form_feedback')[0].reset();
    $('#modalFeedback').modal('hide');
}

document.addEventListener('DOMContentLoaded', function () {
    // Sélectionner tous les conteneurs d'images
    document.querySelectorAll(".shimmer-container").forEach(container => {
        const img = container.querySelector("img");
        const shimmer = container.querySelector(".shimmer");

        console.log(img.getAttribute("data-src"));

        // Charger l'image dynamiquement pour éviter le problème de cache
        const newImage = new Image();
        newImage.src = img.getAttribute("data-src");

        newImage.onload = function () {
            img.src = newImage.src; // Assigner l'image chargée
            shimmer.style.display = "none"; // Cacher le shimmer
            img.classList.remove("d-none"); // Afficher l'image
        };

        newImage.onerror = function () {
            console.error("Erreur lors du chargement de l'image :", newImage.src);
            shimmer.style.display = "none"; // Cacher le shimmer en cas d'échec
        };
    });
});