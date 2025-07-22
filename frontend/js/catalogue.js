const container = document.getElementById('catalogue-section')
const noDataMessage = document.getElementById('no-data-message')

axios.get('http://localhost/gestion-materiel/backend/api/materiels')

.then(res => {
    const data = res.data

    container.innerHTML = ""

    if (data.length === 0) {
        noDataMessage.classList.remove('hidden');
        return;
    }

    data.forEach(element => {
    const card = document.createElement('div')
    card.innerHTML = `
    <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
        <div class="bg-gray-100 h-48 flex items-center justify-center">
            <span class="text-gray-400">Image non disponible</span>
        </div>
        <div class="p-4">
            <div class="flex justify-between items-start">
                <h3 class="text-lg font-semibold text-gray-800">Ordinateur portable HP EliteBook</h3>
                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Disponible</span>
            </div>
            <p class="text-gray-600 text-sm mt-1">Réf: HP-ELB-2022</p>
            <p class="text-gray-700 mt-2">Ordinateur portable professionnel avec processeur i7 et 16Go RAM.</p>
            <div class="mt-4 flex justify-between items-center">
                <span class="text-sm text-gray-500">Catégorie: Informatique</span>
                <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">Voir détails</button>
            </div>
        </div>
    </div>
    `
        container.appendChild(card);
    });
    
})

.catch(err => {
    console.error('error', err)
    
})