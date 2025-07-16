
let isLoged = JSON.parse(localStorage.getItem('user'));

if (isLoged) {
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
            default:
        alert("Rôle inconnu !");
        break;
    }

}

document.getElementById('registerForm').addEventListener('submit', (e) => {
    e.preventDefault();
    const nom = document.getElementById('nom').value
    const email = document.getElementById('email').value
    const password = document.getElementById('password').value
    signup(nom, email, password, 2)
})

function signup (nom, email, password, role_id) {
    axios.post('http://localhost/gestion-materiel/backend/api/signup', {
        nom : nom, 
        email : email,
        password : password,
        role_id : role_id
    })
    .then(response => {
        const user = response.data
        console.log('good', user)
        localStorage.setItem('user', JSON.stringify(user))
        switch (user.role_id) {
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
            default:
        alert("Rôle inconnu !");
        break;
        }
    })
    .catch(error => {
        if (error.response) {
            console.error('error serveur:', error.response.data)
            alert('Erreur: ' + error.response.data.error)
        }else {
            alert('error in connection')
        }
    })
} 