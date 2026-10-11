import { authHeader } from '../_helpers';

const API_URL = `http://localhost:8005/api/`;

export const authService = {
    login,
    logout,
    register,
    getAll,
    getById,
    update,
    delete: _delete,
    getCurrentUser
};

async function login(email, password) {
    const requestOptions = {
        timeout: 1000,
        method: 'POST',
        headers: { 
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        json: true,
        body: JSON.stringify({ email, password })
    };

    const response = await fetch(API_URL + `login`, requestOptions);
    const user = await handleResponse(response);
    // store user details and jwt token in local storage to keep user logged in between page refreshes
    localStorage.setItem('user', JSON.stringify(user));
    return user;
}

function getCurrentUser() {
    return JSON.parse(localStorage.getItem("user"));
};

function logout() {
    // remove user from local storage to log user out
    localStorage.removeItem('user');
}

async function getAll() {
    const requestOptions = {
        method: 'GET',
        headers: authHeader()
    };

    const response = await fetch(API_URL + `agent/user`, requestOptions);
    return handleResponse(response);
}

async function getById(id) {
    const requestOptions = {
        method: 'GET',
        headers: authHeader()
    };

    const response = await fetch(API_URL + `v1/user/${id}`, requestOptions);
    return handleResponse(response);
}

async function register(user) {
    const requestOptions = {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(user)
    };

    const response = await fetch(API_URL + `v1/user`, requestOptions);
    return handleResponse(response);
}

 async function update(user) {
    const requestOptions = {
        method: 'PUT',
        headers: { ...authHeader(), 'Content-Type': 'application/json' },
        body: JSON.stringify(user)
    };

    const response = await fetch(API_URL + `v1/user/${user.id}`, requestOptions);
     return handleResponse(response);
} 

// prefixed function name with underscore because delete is a reserved word in javascript
 function _delete(id) {
    const requestOptions = {
        method: 'DELETE',
        headers: authHeader()
    };

    return fetch(API_URL + `v1/user/${id}`, requestOptions).then(handleResponse);
} 

function handleResponse(response) {
    return response.text().then(text => {
        const data = text && JSON.parse(text);
        if (!response.ok) {
            if (response.status === 401) {
                // auto logout if 401 response returned from api
                logout();
                window.location.reload(true);
            }
            const error = (data && data.message) || response.statusText;
            return Promise.reject(error);
        }

        return data;
    });
};