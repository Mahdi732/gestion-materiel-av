const container = document.getElementById('catalogue-section')
const noDataMessage = document.getElementById('no-data-message')
const isLogin = JSON.parse(localStorage.getItem('user'))


document.getElementById('form-catalogue').addEventListener('submit', (e) => {
       e.preventDefault();
       const nom = document.getElementById('nom').value 
       const marque = document.getElementById('marque').value
       const modele = document.getElementById('modele').value
       const type_id = document.getElementById('type_id').value
       const etat_id = document.getElementById('etat_id').value
       const disponible = document.getElementById('disponible').value
       const caracteristiques = document.getElementById('caracteristiques').value
       console.log('====================================');
       console.log([nom, marque, modele, type_id, etat_id, disponible, caracteristiques]);
       console.log('====================================');

       createCatalogue(nom, marque, modele, type_id, etat_id, disponible, caracteristiques)
})

function createCatalogue (nom, marque, modele, type_id, etat_id, disponible, caracteristiques) {
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
        console.log(res.data)
    })

    .catch(err => {
        console.error('error', err);
    })
}

axios.get('http://localhost/gestion-materiel/backend/api/materiels')

.then(res => {
    const data = res.data
    console.log('====================================');
    console.log(res.data);
    console.log('====================================');
    container.innerHTML = ""

    if (data.length === 0) {
        noDataMessage.classList.remove('hidden');
        return;
    }

    data.forEach(element => {
        const card = document.createElement('div')

        const deleteButton = (isLogin.role_id == 1) ? `
            <div class="mt-3 flex justify-end">
                <button 
                    class="text-red-600 hover:text-red-800 text-sm font-medium border border-red-600 px-3 py-1 rounded"
                    onclick="deleteCatalogue('${element.id}')"
                >
                    Supprimer
                </button>
            </div>
        ` : ''

        card.innerHTML = `
        <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
            <div class="bg-gray-100 h-48 flex items-center justify-center">
                <span class="text-gray-400">Image non disponible</span>
            </div>
            <div class="p-4">
                <div class="flex justify-between items-start">
                    <h3 class="text-lg font-semibold text-gray-800">${element.nom}</h3>
                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                        ${element.disponible ? 'disponible' : 'indesponible'}
                    </span>
                </div>
                <p class="text-gray-600 text-sm mt-1">Réf: ${element.marque}</p>
                <p class="text-gray-700 mt-2">${element.caracteristiques}</p>
                <div class="mt-4 flex justify-between items-center">
                    <span class="text-sm text-gray-500">Modele: ${element.modele}</span>
                    <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">Voir détails</button>
                </div>

                ${deleteButton}
            </div>
        </div>
        `
        container.appendChild(card)
    })
  
})

.catch(err => {
    console.error('error', err)
    
})

function deleteCatalogue(id) {
    if (!confirm("Êtes-vous sûr de vouloir supprimer ce matériel ?")) return;

    axios.post('http://localhost/gestion-materiel/backend/api/materiels/delete', {
        id: id
    })
    .then(res => {
        alert("Matériel supprimé avec succès !");
    })
    .catch(err => {
        console.error('Erreur lors de la suppression', err);
        alert("Erreur lors de la suppression.");
    });
}

