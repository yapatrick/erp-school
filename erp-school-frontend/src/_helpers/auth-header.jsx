function authHeader() {
    let user = JSON.parse(localStorage.getItem('user'));
    
    if (user && user.access_token) {
        return { 
            'Authorization': 'Bearer ' + user.access_token,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        };
    } else {
        return {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        };
    }
}

export { authHeader }