import { alertActions } from "../_actions/alert.action";
import { permissionConstants } from "../_constants/permission.constants";

export function permission(state = {}, action) {
    switch (action.type) {
        
        case permissionConstants.PERMISSIONCREATE_REQUEST:
            return {
                ...state,
                permission: action.payload
            };  

        case permissionConstants.PERMISSIONCREATE_SUCCESS:
            return {
                ...state,
                permission: action.payload,
                alert: alertActions.success('Permission créée avec succès')
            };

        case permissionConstants.PERMISSIONCREATE_FAILURE:
            return {
                alert: alertActions.error('Erreur lors de la création de la permission')    
            };

        case permissionConstants.PERMISSIONDELETE_REQUEST:
            return {
                ...state,
                permission: action.payload
            };

        case permissionConstants.PERMISSIONDELETE_SUCCESS:
            return {
                ...state,
                permission: action.payload,
                alert: alertActions.success('Permission supprimée avec succès')
            };

        case permissionConstants.PERMISSIONDELETE_FAILURE:
            return {
                alert: alertActions.error('Erreur lors de la suppression de la permission')
            };

        case permissionConstants.PERMISSIONUPDATE_REQUEST:
            return {
                ...state,
                permission: action.payload
            };

        case permissionConstants.PERMISSIONUPDATE_SUCCESS:
            return {
                ...state,
                permission: action.payload,
                alert: alertActions.success('Permission mise à jour avec succès')
            };

        case permissionConstants.PERMISSIONUPDATE_FAILURE:
            return {
                alert: alertActions.error('Erreur lors de la mise à jour de la permission')
            };

        case permissionConstants.PERMISSIONGETALL_REQUEST:
            return {
                ...state,
                permission: action.payload  
            };

        case permissionConstants.PERMISSIONGETALL_SUCCESS:
            return {
                ...state,
                permission: action.payload,
                alert: alertActions.success('Permissions récupérées avec succès')
            };

        case permissionConstants.PERMISSIONGETALL_FAILURE:
            return {
                alert: alertActions.error('Erreur lors de la récupération des permissions') 
            };

        case permissionConstants.PERMISSIONGETBYID_REQUEST:
            return {
                ...state,
                permission: action.payload
            };

        case permissionConstants.PERMISSIONGETBYID_SUCCESS:
            return {
                ...state,   
                permission: action.payload,
                alert: alertActions.success('Permission récupérée avec succès')
            }; 

        case permissionConstants.PERMISSIONGETBYID_FAILURE:
            return {
                alert: alertActions.error('Erreur lors de la récupération de la permission')
            };

        default:
            return state;
    }
}