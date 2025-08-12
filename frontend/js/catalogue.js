const container = document.getElementById('catalogue-section');
const noDataMessage = document.getElementById('no-data-message');
const isLogin = JSON.parse(localStorage.getItem('user'));

if (isLogin && isLogin.role_id === 1) {
document.getElementById('toggle-form-btn').classList.remove('hidden');
}


  const profileLink = document.getElementById('profileLink');

  if (isLogin && isLogin.id) {
    let page = '#'; // default fallback

    switch (isLogin.id) {
      case 1:
        page = 'admin.html';
        break;
      case 2:
        page = 'client.html';
        break;
      case 3:
        page = 'commercial.html';
        break;
      case 4:
        page = 'technicien.html';
        break;
      default:
        page = 'login.html'; // in case of unknown role
    }

    profileLink.setAttribute('href', page);
  }

// Reservation form elements
const reservationForm = document.getElementById('reservation-form');
const locationForm = document.getElementById('location-form');
const cancelReservationBtn = document.getElementById('cancel-reservation');

// Global variables for selected item
let selectedMaterielId = '';
let selectedMaterielNom = '';
let selectedMaterielPrixJour = 0;

// Form submission handler for creating new material (keep your existing code)
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

// Updated Location form submission handler
locationForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const dateDebut = document.getElementById('date_debut').value;
    const dateFin = document.getElementById('date_fin').value;
    const user = JSON.parse(localStorage.getItem('user'));
    
    if (!user || !user.id) {
        alert('Vous devez être connecté pour effectuer une réservation');
        return;
    }

    createLocation(selectedMaterielId, user.id, dateDebut, dateFin);
});

// Cancel reservation handler
cancelReservationBtn.addEventListener('click', () => {
    reservationForm.classList.add('hidden');
});

// Create material function (keep your existing code)
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
        document.getElementById('form-overlay').classList.add('hidden');
        loadCatalogue();
    })
    .catch(err => {
        console.error('Error creating material:', err);
    });
}

// Updated Create location function
function createLocation(materielId, clientId, dateDebut, dateFin) {
    axios.post('http://localhost/gestion-materiel/backend/api/location', {
        materiel_id: materielId,
        client_id: clientId,
        date_debut: dateDebut,
        date_fin: dateFin
    })
    .then(res => {
        const duration = calculateDurationDays(dateDebut, dateFin);
        const totalPrice = selectedMaterielPrixJour * duration;
        
        alert(`Location créée avec succès!\nDu ${formatDate(dateDebut)} au ${formatDate(dateFin)}\nDurée: ${duration} jours\nTarif total: ${totalPrice}€`);
        reservationForm.classList.add('hidden');
        loadCatalogue();
    })
    .catch(err => {
        console.error('Error creating location:', err.response?.data || err.message);
        alert(err.response?.data?.error || 'Erreur lors de la création de la location');
    });
}

// Helper function to calculate duration in days
function calculateDurationDays(startDate, endDate) {
    const start = new Date(startDate);
    const end = new Date(endDate);
    return Math.round((end - start) / (1000 * 60 * 60 * 24));
}

// Helper function to format date
function formatDate(dateString) {
    const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
    return new Date(dateString).toLocaleDateString('fr-FR', options);
}

// Updated Load catalogue function to include price per day
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
                        <p class="text-gray-700 mt-1">Prix/jour: ${element.prix_jour}€</p>
                        <div class="mt-4 flex justify-between items-center">
                            <span class="text-sm text-gray-500">Modele: ${element.modele}</span>
                            <button class="buy-btn text-blue-600 hover:text-blue-800 text-sm font-medium" 
                                    data-nom="${element.nom}" 
                                    data-caracteristiques="${element.caracteristiques}"
                                    data-id="${element.id}">
                                buy
                            </button>
                            <button class="reserver-btn text-blue-600 hover:text-blue-800 text-sm font-medium"
                                    data-id="${element.id}"
                                    data-prix="${element.prix_jour}">
                                reserver
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

// Delete function (keep your existing code)
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

// Updated Reservation handling
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('reserver-btn')) {
        e.preventDefault(); 
        e.stopPropagation();
        
        selectedMaterielId = e.target.getAttribute('data-id');
        selectedMaterielPrixJour = parseFloat(e.target.getAttribute('data-prix'));
        
        // Set default dates (today and tomorrow)
        const today = new Date();
        const tomorrow = new Date();
        tomorrow.setDate(today.getDate() + 1);
        
        document.getElementById('date_debut').value = today.toISOString().split('T')[0];
        document.getElementById('date_fin').value = tomorrow.toISOString().split('T')[0];
        
        reservationForm.classList.remove('hidden');
    }
    
    if (e.target.classList.contains('buy-btn')) {
        e.preventDefault(); 
        e.stopPropagation();
        
        document.getElementById('form-overlay').classList.add('hidden');
        
        selectedNom = e.target.getAttribute('data-nom');
        selectedCaracteristiques = e.target.getAttribute('data-caracteristiques');
        selectedId = e.target.getAttribute('data-id');
        
        document.getElementById('payment-popup').classList.remove('hidden');
    }
});

// Keep all your existing payment and form control handlers
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

// Form popup controls (keep your existing code)
document.getElementById('toggle-form-btn').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('payment-popup').classList.add('hidden');
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