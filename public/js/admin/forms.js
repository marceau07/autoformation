document.addEventListener('DOMContentLoaded', () => {
    // Sélecteur pour le champ de sélection et le bouton
    const traineeSelector = document.querySelector('.js-trainee-selector');
    const pdfButton = document.querySelector('.js-pdf-button');
    const noPdfMessage = document.querySelector('.js-no-pdf-message');

    console.log(traineeSelector)
    console.log(pdfButton)
    console.log(noPdfMessage)
    if (traineeSelector && (pdfButton || noPdfMessage)) {
        const updatePdfLink = () => {
            const selectedOption = traineeSelector.options[traineeSelector.selectedIndex];
            const traineeName = selectedOption ? selectedOption.textContent.trim() : '';
            if (traineeName) {
                const pdfUrl = `/internships/tmp/fichier_${traineeName}.pdf`;

                // Vérification d'existence du bouton
                if (pdfButton) {
                    pdfButton.href = pdfUrl;
                    pdfButton.style.display = 'inline-block';
                }

                if (noPdfMessage) {
                    noPdfMessage.style.display = 'none';
                }
            } else {
                if (pdfButton) {
                    pdfButton.style.display = 'none';
                }

                if (noPdfMessage) {
                    noPdfMessage.style.display = 'inline';
                }
            }
        };

        // Mettre à jour l'URL au changement
        traineeSelector.addEventListener('change', updatePdfLink);

        // Appel initial au chargement
        updatePdfLink();
    }
});
