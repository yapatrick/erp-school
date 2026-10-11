import { authHeader } from '../_helpers';
import { API_URL } from '../_api/ApiUrl';

export const roleService = {
    getAllRole,
    getRoleByID,
    createRole,
    updateRole,
    deleteRole: _delete,
    logout
};

 function getAllRole(){
    const requestOptions = {
        method: 'GET',
        headers: authHeader()
    };

    return fetch(API_URL + `roles`, requestOptions).then(handleResponse);
};

function getRoleByID(id){
    const requestOptions = {
        method: 'GET',
        headers: {
            ...authHeader(),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    };

    return fetch(API_URL + `roles/${id}`, requestOptions).then(handleResponse);
};

function createRole(role){
    const requestOptions = {
        method: 'POST',
        data: role, // you are sending body instead
        headers: {
            ...authHeader(),
            'Accept': 'application/json',
            "Content-Type": "application/json",
          },
        body: JSON.stringify(role),
        json: true,
    };

    return fetch(API_URL + `roles`, requestOptions).then(handleResponse);
};

function updateRole(role){
    const requestOptions = {
        method: 'PUT',
        headers: { 
            ...authHeader(), 
            'Accept': 'application/json',
            'Content-Type': 'application/json' 
        },
        body: JSON.stringify(role)
    };

    return fetch(API_URL + `roles/${role.id}`, requestOptions).then(handleResponse);
};

// prefixed function name with underscore because delete is a reserved word in javascript
function _delete(id) {
    const requestOptions = {
        method: 'DELETE',
        headers: authHeader()
    };

    return fetch(API_URL + `roles/${id}`, requestOptions).then(handleResponse);
} 

function logout() {
    // remove user from local storage to log user out
    localStorage.removeItem('user');
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