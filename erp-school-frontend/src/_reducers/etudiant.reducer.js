import { alertConstants } from "../_constants";
import { etudiantConstants } from '../_constants/etudiant.constants';

export function etudiant(state = {}, action) {
  switch (action.type) {
    case etudiantConstants.etudiantCREATE_REQUEST:
      return {
        ...state,
       etudiant: action.payload,
      };

    case etudiantConstants.etudiantCREATE_SUCCESS:
      return {
        ...state,
       etudiant: action.payload,
        alert: alertConstants.SUCCESS,
      };

    case etudiantConstants.etudiantCREATE_FAILURE:
      return {
        alert: alertConstants.CLEAR,
      };

    case etudiantConstants.etudiantDELETE_REQUEST:
      return {
        ...state,
       etudiant: action.payload,
      };

    case etudiantConstants.etudiantDELETE_SUCCESS:
      return {
        ...state,
       etudiant: action.payload,
        alert: alertConstants.SUCCESS,
      };

    case etudiantConstants.etudiantDELETE_FAILURE:
      return {
        alert: alertConstants.CLEAR,
      };

    case etudiantConstants.etudiantUPDATE_REQUEST:
      return {
        ...state,
       etudiant: action.payload,
      };

    case etudiantConstants.etudiantUPDATE_SUCCESS:
      return {
        ...state,
       etudiant: action.payload,
        alert: alertConstants.SUCCESS,
      };

    case etudiantConstants.etudiantUPDATE_FAILURE:
      return {
        alert: alertConstants.CLEAR,
      };

    case etudiantConstants.etudiantGETALL_REQUEST:
      return {
        ...state,
       etudiant: action.payload,
      };

    case etudiantConstants.etudiantGETALL_SUCCESS:
      return {
        ...state,
       etudiant: action.payload,
        alert: alertConstants.SUCCESS,
      };

    case etudiantConstants.etudiantGETALL_FAILURE:
      return {
        alert: alertConstants.CLEAR,
      };

    case etudiantConstants.etudiantGETBYID_REQUEST:
      return {
        ...state,
       etudiant: action.payload,
      };

    case etudiantConstants.etudiantGETBYID_SUCCESS:
      return {
        ...state,
       etudiant: action.payload,
        alert: alertConstants.SUCCESS,
      };

    case etudiantConstants.etudiantGETBYID_FAILURE:
      return {
        alert: alertConstants.CLEAR,
      };

    default:
      return state;
  }
}