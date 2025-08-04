const container = document.getElementById('catalogue-section');
const noDataMessage = document.getElementById('no-data-message');
const isLogin = JSON.parse(localStorage.getItem('user'));

// Form submission handler
document.getElementById('form-catalogue').addEventListener('submit', (e) => {
    e.preventDefault();
    const nom = document.getElementById('nom').value;
    const marque = document.getElementById('marque').value;
    const modele = document.getElementById('modele').value;
    const type_id = document.getElementById('type_id').value;
    const etat_id = document.getElementById('etat_id').value;
    const disponible = document.getElementById('disponible').value;
    const caracteristiques = document.getElementById('caracteristiques').value;

    createCatalogue(nom, marque, modele, type_id, etat_id, disponible, caracteristiques);
});

function createCatalogue(nom, marque, modele, type_id, etat_id, disponible, caracteristiques) {
    axios.post('http://localhost/gestion-materiel/backend/api/materiels', {
        nom,
        marque,
        modele,
        type_id,
        etat_id,
        disponible,
        caracteristiques
    })
    .then(res => {
        console.log(res.data);
        document.getElementById('form-overlay').classList.add('hidden');
        loadCatalogue();
    })
    .catch(err => {
        console.error('error', err);
    });
}

// Load catalogue function
function loadCatalogue() {
    axios.get('http://localhost/gestion-materiel/backend/api/materiels')
    .then(res => {
        const data = res.data;
        container.innerHTML = "";

        if (data.length === 0) {
            noDataMessage.classList.remove('hidden');
            return;
        }

        noDataMessage.classList.add('hidden');
        data.forEach(element => {
            const card = document.createElement('div');
            card.innerHTML = `
                <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="bg-gray-100 h-48 flex items-center justify-center">
                        <span class="text-gray-400">Image non disponible</span>
                    </div>
                    <div class="p-4">
                        <div class="flex justify-between items-start">
                            <h3 class="text-lg font-semibold text-gray-800">${element.nom}</h3>
                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                                ${element.disponible ? 'disponible' : 'indisponible'}
                            </span>
                        </div>
                        <p class="text-gray-600 text-sm mt-1">Réf: ${element.marque}</p>
                        <p class="text-gray-700 mt-2">${element.caracteristiques}</p>
                        <div class="mt-4 flex justify-between items-center">
                            <span class="text-sm text-gray-500">Modele: ${element.modele}</span>
                            <button class="reserver-btn text-blue-600 hover:text-blue-800 text-sm font-medium" 
                                    data-nom="${element.nom}" 
                                    data-caracteristiques="${element.caracteristiques}"
                                    data-id="${element.id}">
                                Reserver
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(card);
        });
    })
    .catch(err => {
        console.error('error', err);
        noDataMessage.classList.remove('hidden');
    });
}

// Delete function
function deleteCatalogue(id) {
    if (!confirm("Êtes-vous sûr de vouloir supprimer ce matériel ?")) return;

    axios.post('http://localhost/gestion-materiel/backend/api/materiels/delete', {
        id: id
    })
    .then(res => {
        alert("Matériel supprimé avec succès !");
        loadCatalogue();
    })
    .catch(err => {
        console.error('Erreur lors de la suppression', err);
        alert("Erreur lors de la suppression.");
    });
}

// Reservation handling
let selectedNom = '';
let selectedCaracteristiques = '';
let selectedId = '';

document.addEventListener('click', function (e) {
    if (e.target.classList.contains('reserver-btn')) {
        e.preventDefault(); 
        e.stopPropagation();
        
        document.getElementById('form-overlay').classList.add('hidden');
        
        selectedNom = e.target.getAttribute('data-nom');
        selectedCaracteristiques = e.target.getAttribute('data-caracteristiques');
        selectedId = e.target.getAttribute('data-id');
        
        document.getElementById('payment-popup').classList.remove('hidden');
    }
});

document.getElementById('cancel-popup').addEventListener('click', () => {
    document.getElementById('payment-popup').classList.add('hidden');
});

document.getElementById('confirm-reservation').addEventListener('click', () => {
    const methode = document.getElementById('payment-method').value;
    sendReservationToAPI(selectedNom, selectedCaracteristiques, methode, selectedId);
    document.getElementById('payment-popup').classList.add('hidden');
});

function sendReservationToAPI(nom, caracteristiques, methodePaiement, materielId) {
    const user = JSON.parse(localStorage.getItem('user'));

    const commande = {
        clientId: user.id,
        materiel_id: materielId,
        paiement: methodePaiement,
        details: JSON.stringify([{ nom, caracteristiques }])
    };

    axios.post('http://localhost/gestion-materiel/backend/api/commande', commande)
        .then(res => {
            alert("Commande créée avec succès !");
            console.log(res.data);
            loadCatalogue(); 
        })
        .catch(err => {
            console.error("Erreur lors de la création de la commande", err);
            alert("Erreur lors de la commande.");
        });
}

// Form popup controls
document.getElementById('toggle-form-btn').addEventListener('click', function(e) {
    e.preventDefault();
    // Hide payment popup if open
    document.getElementById('payment-popup').classList.add('hidden');
    // Show form popup
    document.getElementById('form-overlay').classList.remove('hidden');
});

document.getElementById('close-form-btn').addEventListener('click', function() {
    document.getElementById('form-overlay').classList.add('hidden');
});

document.getElementById('cancel-form-btn').addEventListener('click', function() {
    document.getElementById('form-overlay').classList.add('hidden');
});

document.getElementById('form-overlay').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});

// Initial load
loadCatalogue();