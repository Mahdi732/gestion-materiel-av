const isLogin = JSON.parse(localStorage.getItem('user'))

if (!isLogin) {
    window.location.href = 'login.html'
}

if (isLogin.role_id != 1) {
    switch (isLoged.role_id) {
            case 1:
        window.location.href = "admin-dashboard.html";
        break;
            case 2:
        window.location.href = "client.html";
        break;
            case 3:
        window.location.href = "tech-dashboard.html";
        break;
            case 4:
        window.location.href = "dashboard.html";
        break;
    }
}

// Base URL for your API (adjust as needed)
const API_BASE_URL = 'http://localhost/gestion-materiel/backend/api';

// DOM Elements
const equipmentBody = document.getElementById('equipment-body');
const rentalsBody = document.getElementById('rentals-body');
const commandsBody = document.getElementById('commands-body');
const totalSales = document.getElementById('total-sales');
const totalRentals = document.getElementById('total-rentals');
const equipmentCount = document.getElementById('equipment-count');
const availabilityRate = document.getElementById('availability-rate');

// Format date function
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    try {
        const date = new Date(dateString);
        return date.toLocaleDateString('fr-FR');
    } catch {
        return dateString.split(' ')[0];
    }
}

// Get status badge class
function getStatusBadge(status) {
    if (!status) return 'bg-gray-100 text-gray-800';
    
    const statusText = String(status).toLowerCase().replace('_', ' ');
    const statusMap = {
        'true': 'bg-green-100 text-green-800',
        'disponible': 'bg-green-100 text-green-800',
        'false': 'bg-red-100 text-red-800',
        'en attente': 'bg-yellow-100 text-yellow-800',
        'en_attente': 'bg-yellow-100 text-yellow-800',
        'validé': 'bg-green-100 text-green-800',
        'rejeté': 'bg-red-100 text-red-800',
        'payé': 'bg-blue-100 text-blue-800',
        'annulé': 'bg-red-100 text-red-800',
        'livré': 'bg-purple-100 text-purple-800',
        'default': 'bg-gray-100 text-gray-800'
    };
    
    return statusMap[statusText] || statusMap['default'];
}

// Get equipment type name
function getTypeName(typeId) {
    const types = {
        1: 'Caméra',
        2: 'Audio',
        3: 'Éclairage',
        4: 'Accessoire'
    };
    return types[typeId] || 'Autre';
}

// Get equipment state name
function getStateName(stateId) {
    const states = {
        1: 'Neuf',
        2: 'Bon',
        3: 'Moyen',
        4: 'Mauvais'
    };
    return states[stateId] || 'Inconnu';
}

// Fetch equipment data
async function fetchEquipment() {
    try {
        const response = await axios.get(`${API_BASE_URL}/materiels`);
        const equipment = response.data;
        
        equipmentCount.textContent = equipment.length;
        
        const availableCount = equipment.filter(item => item.disponible).length;
        const rate = Math.round((availableCount / equipment.length) * 100);
        availabilityRate.textContent = isNaN(rate) ? 0 : rate;
        
        equipmentBody.innerHTML = equipment.map(item => `
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">${item.id || 'N/A'}</td>
                <td class="px-6 py-4 whitespace-nowrap">${item.nom || item.marque || 'N/A'}</td>
                <td class="px-6 py-4 whitespace-nowrap">${getTypeName(item.type_id)}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusBadge(getStateName(item.etat_id))}">
                        ${getStateName(item.etat_id)}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusBadge(item.disponible)}">
                        ${item.disponible ? 'Disponible' : 'Indisponible'}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <button onclick="deleteMaterial(${item.id})" class="text-red-600 hover:text-red-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </td>
            </tr>
        `).join('');
        
        return equipment;
    } catch (error) {
        console.error('Error fetching equipment:', error);
        equipmentBody.innerHTML = `<tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Erreur de chargement des données</td></tr>`;
        return [];
    }
}

// Fetch rentals data
async function fetchRentals() {
    try {
        const response = await axios.get(`${API_BASE_URL}/location`);
        const rentals = response.data;
        
        totalRentals.textContent = rentals.length;
        
        rentalsBody.innerHTML = rentals.map(rental => `
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">${rental.id || 'N/A'}</td>
                <td class="px-6 py-4 whitespace-nowrap">Client ${rental.client_id || 'N/A'}</td>
                <td class="px-6 py-4 whitespace-nowrap">Matériel ${rental.materiel_id || 'N/A'}</td>
                <td class="px-6 py-4 whitespace-nowrap">${formatDate(rental.date_debut)}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusBadge(rental.statut)}">
                        ${rental.statut ? rental.statut.replace('_', ' ') : 'N/A'}
                    </span>
                </td>
            </tr>
        `).join('');
        
        return rentals;
    } catch (error) {
        console.error('Error fetching rentals:', error);
        rentalsBody.innerHTML = `<tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Erreur de chargement des données</td></tr>`;
        return [];
    }
}

// Fetch commands data
async function fetchCommands() {
    try {
        const response = await axios.get(`${API_BASE_URL}/commande`);
        const commands = response.data;
        
        commandsBody.innerHTML = commands.map(command => `
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">${command.id || 'N/A'}</td>
                <td class="px-6 py-4 whitespace-nowrap">Client ${command.clientId || 'N/A'}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    ${command.details?.map(item => `
                        <div class="mb-1">
                            <span class="font-medium">${item.nom || 'N/A'}</span>
                            <span class="text-sm text-gray-500"> (${item.caracteristiques || 'N/A'})</span>
                        </div>
                    `).join('') || 'Aucun détail'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusBadge(command.statut)}">
                        ${command.statut ? command.statut.replace('_', ' ') : 'N/A'}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getPaymentBadge(command.paiement)}">
                        ${command.paiement || 'N/A'}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap flex space-x-2">
                    <button onclick="viewCommandDetails(${command.id})" class="text-secondary hover:text-blue-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                    ${command.statut === 'en attends' ? `
                    <button onclick="acceptCommand(${command.id})" class="text-green-600 hover:text-green-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </button>
                    <button onclick="rejectCommand(${command.id})" class="text-red-600 hover:text-red-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    ` : ''}
                </td>
            </tr>
        `).join('');
        
        return commands;
    } catch (error) {
        console.error('Error fetching commands:', error);
        commandsBody.innerHTML = `<tr><td colspan="6" class="px-6 py-4 text-center text-red-500">Erreur de chargement des commandes</td></tr>`;
        return [];
    }
}

// Get payment badge class
function getPaymentBadge(paymentMethod) {
    if (!paymentMethod) return 'bg-gray-100 text-gray-800';
    
    const paymentText = String(paymentMethod).toLowerCase();
    const paymentMap = {
        'paiementcb': 'bg-blue-100 text-blue-800',
        'espèces': 'bg-green-100 text-green-800',
        'virement': 'bg-purple-100 text-purple-800',
        'default': 'bg-gray-100 text-gray-800'
    };
    
    return paymentMap[paymentText] || paymentMap['default'];
}

// View command details
function viewCommandDetails(commandId) {
    console.log(`Viewing details for command ${commandId}`);
    // Implement actual details view
    alert(`Détails de la commande ${commandId} seront affichés ici`);
}

// Accept command
async function acceptCommand(commandId) {
    if (!confirm(`Voulez-vous vraiment accepter la commande ${commandId}?`)) return;
    
    try {
        const response = await axios.post(`${API_BASE_URL}/commande/statu`, {
            command_id: commandId,
            statut: 'validé'
        });
        
        if (response.data.message) {
            alert(response.data.message);
            fetchCommands(); // Refresh the commands list
        } else {
            throw new Error('Réponse inattendue du serveur');
        }
    } catch (error) {
        console.error('Error accepting command:', error);
        const errorMsg = error.response?.data?.error || error.message;
        alert(`Erreur lors de l'acceptation de la commande: ${errorMsg}`);
    }
}

// Reject command
async function rejectCommand(commandId) {
    const reason = prompt('Veuillez indiquer la raison du rejet:');
    if (!reason || !confirm(`Voulez-vous vraiment rejeter la commande ${commandId}?`)) return;
    
    try {
        const response = await axios.post(`${API_BASE_URL}/commande/statu`, {
            command_id: commandId,
            statut: 'rejeté',
            raison: reason
        });
        
        if (response.data.message) {
            alert(response.data.message);
            fetchCommands(); // Refresh the commands list
        } else {
            throw new Error('Réponse inattendue du serveur');
        }
    } catch (error) {
        console.error('Error rejecting command:', error);
        const errorMsg = error.response?.data?.error || error.message;
        alert(`Erreur lors du rejet de la commande: ${errorMsg}`);
    }
}

async function deleteMaterial(materialId) {
    if (!confirm(`Voulez-vous vraiment supprimer ce matériel (ID: ${materialId})? Cette action est irréversible.`)) {
        return;
    }

    const id = materialId

    try {
        const response = await axios.post(`${API_BASE_URL}/materiels/delete`, {
            id
        });

        if (response.data.message) {
            alert(response.data.message);
            fetchEquipment(); 
        } else {
            throw new Error('Réponse inattendue du serveur');
        }
    } catch (error) {
        console.error('Error deleting material:', error);
        const errorMsg = error.response?.data?.error || error.message;
        alert(`Échec de la suppression: ${errorMsg}`);
    }
}

// Initialize dashboard
async function initDashboard() {
    await Promise.all([
        fetchEquipment(),
        fetchRentals(),
        fetchCommands()
    ]);
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', initDashboard);