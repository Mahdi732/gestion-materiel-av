const isLogin = JSON.parse(localStorage.getItem('user'))

if (!isLogin) {
    window.location.href = 'login.html'
}

if (isLogin.role_id != 3) {
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