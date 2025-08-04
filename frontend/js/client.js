document.addEventListener('DOMContentLoaded', () => {
    // Initialize the application
    initApp();
});

function initApp() {
    // Check authentication
    const user = JSON.parse(localStorage.getItem('user'));
    if (!user) {
        window.location.href = 'login.html';
        return;
    }

    // Redirect based on role
    if (user.role_id != 2) {
        const redirects = {
            1: "admin-dashboard.html",
            2: "client.html", 
            3: "tech-dashboard.html",
            4: "dashboard.html"
        };
        window.location.href = redirects[user.role_id] || 'login.html';
        return;
    }

    // Set user info
    setUserInfo(user);
    
    // Load orders
    loadUserOrders();

    // Setup event listeners
    setupEventListeners();
}

function setUserInfo(user) {
    const elements = {
        'userName': user.nom || 'Non spécifié',
        'userEmail': user.email || 'Non spécifié',
        'inscriptionDate': user.created_at ? formatDate(user.created_at) : 'Non spécifié'
    };

    Object.entries(elements).forEach(([id, value]) => {
        const el = document.getElementById(id);
        if (el) el.textContent = value;
    });

    // Set up logout button
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', () => {
            localStorage.removeItem('user');
            window.location.href = 'login.html';
        });
    }
}

async function loadUserOrders() {
    try {
        const user = JSON.parse(localStorage.getItem('user'));
        if (!user) return;

        showLoadingState();
        
        const response = await axios.post('http://localhost/gestion-materiel/backend/api/commande/user', {
            id: user.id
        }, {
            headers: {
                'Content-Type': 'application/json'
            }
        });

        renderOrders(response.data);
    } catch (error) {
        console.error('Error loading orders:', error);
        showEmptyStates();
    }
}

function renderOrders(orders) {
    const ordersTableBody = document.getElementById('orders-table');
    const invoicesTableBody = document.getElementById('invoices-table');
    
    if (!ordersTableBody || !invoicesTableBody) return;

    // Clear existing content
    ordersTableBody.innerHTML = '';
    invoicesTableBody.innerHTML = '';

    if (!orders || orders.length === 0) {
        showEmptyStates();
        return;
    }

    orders.forEach(order => {
        try {
            // Parse order details
            let details = [];
            if (order.details && order.details !== 'Array') {
                try {
                    details = JSON.parse(order.details);
                    if (!Array.isArray(details)) details = [details];
                } catch (e) {
                    console.error('Error parsing details:', e);
                    details = [];
                }
            }

            const orderDate = formatDate(order.date_creation);
            const statusClass = getStatusClass(order.statut);
            const isCancellable = order.statut?.toLowerCase().includes('pending');
            
            // Add to orders table
            ordersTableBody.innerHTML += `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#${order.commande_id}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${orderDate}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        <ul class="list-disc pl-5">
                            ${details.length > 0 ? 
                              details.map(item => `<li>${item.nom || 'Article'}</li>`).join('') : 
                              '<li>Aucun détail</li>'}
                        </ul>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ${getPaymentMethodIcon(order.paiement)} ${order.paiement}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                            ${order.statut || 'En attente'}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        ${isCancellable ? `
                        <button data-order-id="${order.commande_id}" class="cancel-order-btn text-red-600 hover:text-red-900 mr-3">
                            <i class="fas fa-times-circle mr-1"></i> Annuler
                        </button>
                        ` : ''}
                        <button data-order-id="${order.commande_id}" class="delete-order-btn text-red-600 hover:text-red-900 mr-3">
                            <i class="fas fa-trash-alt mr-1"></i> Supprimer
                        </button>
                        <button class="view-order-btn text-blue-600 hover:text-blue-900">
                            <i class="fas fa-eye mr-1"></i> Voir
                        </button>
                    </td>
                </tr>
            `;

            // Add to invoices table if paid
            if (order.statut?.toLowerCase().includes('payé')) {
                invoicesTableBody.innerHTML += `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#INV-${order.commande_id}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${orderDate}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${order.total || 'N/A'} €</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Payée
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="download-invoice-btn text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-download mr-1"></i> PDF
                            </button>
                            <button class="print-invoice-btn text-blue-600 hover:text-blue-900">
                                <i class="fas fa-print mr-1"></i> Imprimer
                            </button>
                        </td>
                    </tr>
                `;
            }
        } catch (e) {
            console.error('Error processing order:', order, e);
        }
    });

    // Setup action buttons
    setupActionButtons();
    // Hide empty states
    hideEmptyStates();
}

function setupEventListeners() {
    // Refresh orders button
    const refreshBtn = document.getElementById('refresh-orders');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', loadUserOrders);
    }

    // Modal controls
    const cancelModal = document.getElementById('cancel-modal');
    const closeModalBtn = document.getElementById('close-cancel-modal');
    const cancelActionBtn = document.getElementById('cancel-action');

    if (cancelModal && closeModalBtn && cancelActionBtn) {
        closeModalBtn.addEventListener('click', () => cancelModal.classList.add('hidden'));
        cancelActionBtn.addEventListener('click', () => cancelModal.classList.add('hidden'));
    }
}

function setupActionButtons() {
    // Setup cancel buttons
    const cancelButtons = document.querySelectorAll('.cancel-order-btn');
    const cancelModal = document.getElementById('cancel-modal');
    const confirmCancelBtn = document.getElementById('confirm-cancel');

    if (cancelModal && confirmCancelBtn) {
        cancelButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const orderId = e.currentTarget.getAttribute('data-order-id');
                cancelModal.setAttribute('data-order-id', orderId);
                cancelModal.classList.remove('hidden');
            });
        });

        confirmCancelBtn.addEventListener('click', async () => {
            const orderId = cancelModal.getAttribute('data-order-id');
            if (!orderId) return;

            try {
                const response = await axios.post('http://localhost/gestion-materiel/backend/api/commande/cancel', {
                    id: orderId
                });

                if (response.data.success) {
                    alert('Commande annulée avec succès');
                    loadUserOrders();
                } else {
                    alert('Erreur lors de l\'annulation');
                }
            } catch (error) {
                console.error('Error cancelling order:', error);
                alert('Erreur lors de l\'annulation');
            } finally {
                cancelModal.classList.add('hidden');
            }
        });
    }

    // Setup delete buttons
    const deleteButtons = document.querySelectorAll('.delete-order-btn');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', async (e) => {
            const orderId = e.currentTarget.getAttribute('data-order-id');
            await deleteCommande(orderId);
        });
    });

    // Setup view buttons
    const viewButtons = document.querySelectorAll('.view-order-btn');
    viewButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            const orderId = e.currentTarget.getAttribute('data-order-id');
            // Implement view functionality here
            console.log('View order:', orderId);
        });
    });

    // Setup invoice buttons
    const downloadButtons = document.querySelectorAll('.download-invoice-btn');
    downloadButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            // Implement download functionality here
            console.log('Download invoice');
        });
    });

    const printButtons = document.querySelectorAll('.print-invoice-btn');
    printButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            // Implement print functionality here
            console.log('Print invoice');
        });
    });
}

async function deleteCommande(commandeId) {
    if (!confirm("Êtes-vous sûr de vouloir supprimer cette commande ? Cette action est irréversible.")) {
        return;
    }

    try {
        const response = await axios.post('http://localhost/gestion-materiel/backend/api/commande/delete', {
            id: commandeId
        }, {
            headers: {
                'Content-Type': 'application/json'
            }
        });

        alert("Commande supprimée avec succès: " + (response.data.message || ''));
        
    } catch (error) {
        console.error('Error deleting command:', error);
        alert("Erreur lors de la suppression. Veuillez réessayer.");
    }
}

// Helper functions
function showLoadingState() {
    const loadingHTML = `
        <tr>
            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                <i class="fas fa-spinner fa-spin mr-2"></i> Chargement...
            </td>
        </tr>
    `;
    const ordersTableBody = document.getElementById('orders-table');
    const invoicesTableBody = document.getElementById('invoices-table');
    
    if (ordersTableBody) ordersTableBody.innerHTML = loadingHTML;
    if (invoicesTableBody) invoicesTableBody.innerHTML = loadingHTML;
}

function showEmptyStates() {
    const ordersEmpty = document.getElementById('orders-empty-state');
    const invoicesEmpty = document.getElementById('invoices-empty-state');
    
    if (ordersEmpty) ordersEmpty.classList.remove('hidden');
    if (invoicesEmpty) invoicesEmpty.classList.remove('hidden');
}

function hideEmptyStates() {
    const ordersEmpty = document.getElementById('orders-empty-state');
    const invoicesEmpty = document.getElementById('invoices-empty-state');
    
    if (ordersEmpty) ordersEmpty.classList.add('hidden');
    if (invoicesEmpty) invoicesEmpty.classList.add('hidden');
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    try {
        const date = new Date(dateString);
        return date.toLocaleDateString('fr-FR', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch (e) {
        return dateString;
    }
}

function getStatusClass(status) {
    if (!status) return 'bg-gray-100 text-gray-800';
    status = status.toLowerCase();
    
    if (status.includes('payé') || status.includes('complete')) {
        return 'bg-green-100 text-green-800';
    } else if (status.includes('pending') || status.includes('en cours')) {
        return 'bg-yellow-100 text-yellow-800';
    } else if (status.includes('cancel') || status.includes('annulé')) {
        return 'bg-red-100 text-red-800';
    }
    return 'bg-gray-100 text-gray-800';
}

function getPaymentMethodIcon(method) {
    const icons = {
        'PaiementCB': '<i class="fas fa-credit-card"></i>',
        'PaiementVirement': '<i class="fas fa-university"></i>',
        'Espèces': '<i class="fas fa-money-bill-wave"></i>'
    };
    return icons[method] || '<i class="fas fa-money-check-alt"></i>';
}