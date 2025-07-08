// Attend que le contenu de la page soit entièrement chargé
document.addEventListener('DOMContentLoaded', function() {

    // Sélectionne le menu déroulant dans le HTML
    const planetSelect = document.getElementById('planete-select');

    // Effectue un appel à notre API pour récupérer les planètes
    fetch('api/get_planetes.php')
        .then(response => {
            // Vérifie si la requête a réussi
            if (!response.ok) {
                throw new Error('La réponse du réseau n\'était pas bonne.');
            }
            // Transforme la réponse en JSON
            return response.json();
        })
        .then(planetes => {
            // Boucle sur chaque planète reçue
            planetes.forEach(planete => {
                // Crée un nouvel élément <option>
                const option = document.createElement('option');
                // Définit la valeur (ex: "mars") et le texte (ex: "Mars")
                option.value = planete.nom.toLowerCase();
                option.textContent = planete.nom;
                // Ajoute l'option au menu déroulant
                planetSelect.appendChild(option);
            });
        })
        .catch(error => {
            // Affiche une erreur dans la console si l'appel échoue
            console.error('Erreur lors de la récupération des planètes:', error);
            // Optionnel : Affiche un message d'erreur à l'utilisateur
            planetSelect.innerHTML = '<option>Erreur de chargement</option>';
        });
});

// Sélectionner le formulaire et le conteneur de réponse
const signalForm = document.getElementById('signal-form');
const responseContainer = document.getElementById('response-container');

// Écouter l'événement de soumission du formulaire
signalForm.addEventListener('submit', function(event) {
    // Empêcher le rechargement de la page
    event.preventDefault();

    // Récupérer le nom de la planète sélectionnée
    const selectedPlanet = document.getElementById('planete-select').value;
    
    // Afficher un message de chargement
    responseContainer.textContent = 'Transmission du signal en cours...';

    // Envoyer les données à l'API via POST
    fetch('api/send_signal.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        // Convertir les données en chaîne JSON
        body: JSON.stringify({ planete: selectedPlanet })
    })
    .then(response => response.json())
    .then(data => {
        // Afficher la réponse de l'API dans le conteneur
        if (data.error) {
            responseContainer.textContent = 'Erreur de transmission : ' + data.error;
        } else {
            responseContainer.textContent = 'Réponse reçue : ' + data.message;
        }
    })
    .catch(error => {
        console.error('Erreur lors de l\'envoi du signal:', error);
        responseContainer.textContent = 'Connexion perdue avec le satellite. Veuillez réessayer.';
    });
});