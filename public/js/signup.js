function actionCode() {
    var input = document.querySelector('input[name="form_signup_code"]');
    if (input.value.length === 6) {
        console.log("valeur ok")
        // document.querySelector('input[type="submit"]').click();
    } else {
        console.log("Code à 6 chiffres requis");
    }
}

// Permet de déclencher automatiquement le bouton lorsque le code est saisi manuellement et qu'il fait 6 caractères
document.addEventListener('DOMContentLoaded', actionCode);
document.addEventListener('change', actionCode);