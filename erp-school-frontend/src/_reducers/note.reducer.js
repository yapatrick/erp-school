import { alertActions } from "../_actions/alert.action";
import { noteConstants } from "../_constants/note.constants";

export function note(state = {}, action) {
    switch (action.type) {
        
        case noteConstants.noteCREATE_REQUEST:
            return {
                ...state,
                note: action.payload
            };  

        case noteConstants.noteCREATE_SUCCESS:
            return {
                ...state,
                note: action.payload,
                alert: alertActions.success('note créée avec succès')
            };

        case noteConstants.noteCREATE_FAILURE:
            return {
                alert: alertActions.error('Erreur lors de la création de la note')    
            };

        case noteConstants.noteDELETE_REQUEST:
            return {
                ...state,
                note: action.payload
            };

        case noteConstants.noteDELETE_SUCCESS:
            return {
                ...state,
                note: action.payload,
                alert: alertActions.success('note supprimée avec succès')
            };

        case noteConstants.noteDELETE_FAILURE:
            return {
                alert: alertActions.error('Erreur lors de la suppression de la note')
            };

        case noteConstants.noteUPDATE_REQUEST:
            return {
                ...state,
                note: action.payload
            };

        case noteConstants.noteUPDATE_SUCCESS:
            return {
                ...state,
                note: action.payload,
                alert: alertActions.success('note mise à jour avec succès')
            };

        case noteConstants.noteUPDATE_FAILURE:
            return {
                alert: alertActions.error('Erreur lors de la mise à jour de la note')
            };

        case noteConstants.noteGETALL_REQUEST:
            return {
                ...state,
                note: action.payload  
            };

        case noteConstants.noteGETALL_SUCCESS:
            return {
                ...state,
                note: action.payload,
                alert: alertActions.success('notes récupérées avec succès')
            };

        case noteConstants.noteGETALL_FAILURE:
            return {
                alert: alertActions.error('Erreur lors de la récupération des notes') 
            };

        case noteConstants.noteGETBYID_REQUEST:
            return {
                ...state,
                note: action.payload
            };

        case noteConstants.noteGETBYID_SUCCESS:
            return {
                ...state,   
                note: action.payload,
                alert: alertActions.success('note récupérée avec succès')
            }; 

        case noteConstants.noteGETBYID_FAILURE:
            return {
                alert: alertActions.error('Erreur lors de la récupération de la note')
            };

        default:
            return state;
    }
}