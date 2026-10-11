import { authHeader, handleResponse} from '../_helpers';
import { API_URL } from '../_api/ApiUrl';   

export const professeurService = {
    getAllProfesseurs,
    getProfesseurByID,
    createProfesseur,
    updateProfesseur,
    deleteProfesseur: _delete,
    logout
};

//for get all Professeurs
function getAllProfesseurs(){
    const requestOptions = {
        method: 'GET',
        headers: authHeader()
    };
    return fetch(API_URL + `professeurs`, requestOptions).then(handleResponse);
};

//for get Professeur by id
async function getProfesseurByID(id){
    const requestOptions = {
        method: 'GET',
        headers: {
            ...authHeader(),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    };
    const response = await fetch(API_URL + `professeurs/${id}`, requestOptions);
    return handleResponse(response);
};

//for create Professeur

async function createProfesseur(Professeur){
    const requestOptions = {
        method: 'POST',
        data: Professeur, // you are sending body instead
        headers: {
            ...authHeader(),    
        'Accept': 'application/json',
        "Content-Type": "application/json",
      },
        body: JSON.stringify(Professeur),
        json: true,
    };
    return fetch(API_URL + `professeurs`, requestOptions).then(handleResponse);
}   

//for update Professeur
async function updateProfesseur(Professeur){
    const requestOptions = {
        method: 'PUT',
        headers: { 
            ...authHeader(),
            'Accept': 'application/json',
            'Content-Type': 'application/json' 
        },
        body: JSON.stringify(Professeur)
    };
    return fetch(API_URL + `professeurs/${Professeur.id}`, requestOptions).then(handleResponse);
}   

// prefixed function name with underscore because delete is a reserved word in javascript
async function _delete(id) {
    const requestOptions = {
        method: 'DELETE',
        headers: authHeader()
    };
  
    const response = await fetch(API_URL + `Professeurs/${id}`, requestOptions);
    return handleResponse(response);
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
