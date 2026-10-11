import { alertConstants } from "../_constants";
import { professeurConstants } from '../_constants/professeur.constants';

export function professeur(state = {}, action) {
  switch (action.type) {
    case professeurConstants.professeurCREATE_REQUEST:
      return {
        ...state,
       professeur: action.payload,
      };

    case professeurConstants.professeurCREATE_SUCCESS:
      return {
        ...state,
       professeur: action.payload,
        alert: alertConstants.SUCCESS,
      };

    case professeurConstants.professeurCREATE_FAILURE:
      return {
        alert: alertConstants.CLEAR,
      };

    case professeurConstants.professeurDELETE_REQUEST:
      return {
        ...state,
       professeur: action.payload,
      };

    case professeurConstants.professeurDELETE_SUCCESS:
      return {
        ...state,
       professeur: action.payload,
        alert: alertConstants.SUCCESS,
      };

    case professeurConstants.professeurDELETE_FAILURE:
      return {
        alert: alertConstants.CLEAR,
      };

    case professeurConstants.professeurUPDATE_REQUEST:
      return {
        ...state,
       professeur: action.payload,
      };

    case professeurConstants.professeurUPDATE_SUCCESS:
      return {
        ...state,
       professeur: action.payload,
        alert: alertConstants.SUCCESS,
      };

    case professeurConstants.professeurUPDATE_FAILURE:
      return {
        alert: alertConstants.CLEAR,
      };

    case professeurConstants.professeurGETALL_REQUEST:
      return {
        ...state,
       professeur: action.payload,
      };

    case professeurConstants.professeurGETALL_SUCCESS:
      return {
        ...state,
       professeur: action.payload,
        alert: alertConstants.SUCCESS,
      };

    case professeurConstants.professeurGETALL_FAILURE:
      return {
        alert: alertConstants.CLEAR,
      };

    case professeurConstants.professeurGETBYID_REQUEST:
      return {
        ...state,
       professeur: action.payload,
      };

    case professeurConstants.professeurGETBYID_SUCCESS:
      return {
        ...state,
       professeur: action.payload,
        alert: alertConstants.SUCCESS,
      };

    case professeurConstants.professeurGETBYID_FAILURE:
      return {
        alert: alertConstants.CLEAR,
      };

    default:
      return state;
  }
}