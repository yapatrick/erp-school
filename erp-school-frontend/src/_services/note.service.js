import { authHeader, handleResponse} from '../_helpers';
import { API_URL } from '../_api/ApiUrl';   

export const noteService = {
    getAllNotes,
    getNoteByID,
    createNote,
    updateNote,
    deleteNote: _delete,
    logout
};

//for get all notes
function getAllNotes(){
    const requestOptions = {
        method: 'GET',
        headers: authHeader()
    };
    return fetch(API_URL + `notes`, requestOptions).then(handleResponse);
};

//for get note by id
async function getNoteByID(id){
    const requestOptions = {
        method: 'GET',
        headers: {
            ...authHeader(),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    };
    const response = await fetch(API_URL + `notes/${id}`, requestOptions);
    return handleResponse(response);
};

//for create note

async function createNote(note){
    const requestOptions = {
        method: 'POST',
        data: note, // you are sending body instead
        headers: {
            ...authHeader(),    
        'Accept': 'application/json',
        "Content-Type": "application/json",
      },
        body: JSON.stringify(note),
        json: true,
    };
    return fetch(API_URL + `notes`, requestOptions).then(handleResponse);
}   

//for update note
async function updateNote(note){
    const requestOptions = {
        method: 'PUT',
        headers: { 
            ...authHeader(),
            'Accept': 'application/json',
            'Content-Type': 'application/json' 
        },
        body: JSON.stringify(note)
    };
    return fetch(API_URL + `notes/${note.id}`, requestOptions).then(handleResponse);
}   

// prefixed function name with underscore because delete is a reserved word in javascript
async function _delete(id) {
    const requestOptions = {
        method: 'DELETE',
        headers: authHeader()
    };
  
    const response = await fetch(API_URL + `notes/${id}`, requestOptions);
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
