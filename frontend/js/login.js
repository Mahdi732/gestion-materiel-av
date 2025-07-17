let isLogin = JSON.parse(localStorage.getItem('user'))
if (isLogin) {
    switch (isLogin.role_id) {
        case 1:
            window.location.href = 'admin-dashboard.html'
            break;
        case 2:
            window.location.href = 'client.html'
            break;
        case 3:
            window.location.href = 'tech-dashboard.html'
            break;
        case 4:
            window.location.href = 'dashboard.html'
            break;
    }
}

document.getElementById('loginForm').addEventListener('submit', (e) => {
    e.preventDefault()
    const email = document.getElementById('email').value
    const password = document.getElementById('password').value
    login(email, password)
})

function login (email, password) {
    axios.post('http://localhost/gestion-materiel/backend/api/login', {
        email,
        password
    })
    .then(res => {
        const user = res.data
        localStorage.setItem('user', JSON.stringify(user))
        switch (user.role_id) {
        case 1:
            window.location.href = 'admin-dashboard.html'
            break;
        case 2:
            window.location.href = 'client.html'
            break;
        case 3:
            window.location.href = 'tech-dashboard.html'
            break;
        case 4:
            window.location.href = 'dashboard.html'
            break;
        }
    })

    .catch(err => {
        if (err.response) {
            console.error('error', err.response.data)
            alert('Erreur: ' + err.response.data.error)
        } else {
            alert('connection error')
        }
    })
}