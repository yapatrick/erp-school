import { alertConstants } from "../_constants";
import { enseignementConstants } from '../_constants/enseignement.constants';

export function enseignement(state = {}, action) {
  switch (action.type) {
    case enseignementConstants.enseignementCREATE_REQUEST:
      return {
        ...state,
       enseignement: action.payload,
      };

    case enseignementConstants.enseignementCREATE_SUCCESS:
      return {
        ...state,
       enseignement: action.payload,
        alert: alertConstants.SUCCESS,
      };

    case enseignementConstants.enseignementCREATE_FAILURE:
      return {
        alert: alertConstants.CLEAR,
      };

    case enseignementConstants.enseignementDELETE_REQUEST:
      return {
        ...state,
       enseignement: action.payload,
      };

    case enseignementConstants.enseignementDELETE_SUCCESS:
      return {
        ...state,
       enseignement: action.payload,
        alert: alertConstants.SUCCESS,
      };

    case enseignementConstants.enseignementDELETE_FAILURE:
      return {
        alert: alertConstants.CLEAR,
      };

    case enseignementConstants.enseignementUPDATE_REQUEST:
      return {
        ...state,
       enseignement: action.payload,
      };

    case enseignementConstants.enseignementUPDATE_SUCCESS:
      return {
        ...state,
       enseignement: action.payload,
        alert: alertConstants.SUCCESS,
      };

    case enseignementConstants.enseignementUPDATE_FAILURE:
      return {
        alert: alertConstants.CLEAR,
      };

    case enseignementConstants.enseignementGETALL_REQUEST:
      return {
        ...state,
       enseignement: action.payload,
      };

    case enseignementConstants.enseignementGETALL_SUCCESS:
      return {
        ...state,
       enseignement: action.payload,
        alert: alertConstants.SUCCESS,
      };

    case enseignementConstants.enseignementGETALL_FAILURE:
      return {
        alert: alertConstants.CLEAR,
      };

    case enseignementConstants.enseignementGETBYID_REQUEST:
      return {
        ...state,
       enseignement: action.payload,
      };

    case enseignementConstants.enseignementGETBYID_SUCCESS:
      return {
        ...state,
       enseignement: action.payload,
        alert: alertConstants.SUCCESS,
      };

    case enseignementConstants.enseignementGETBYID_FAILURE:
      return {
        alert: alertConstants.CLEAR,
      };

    default:
      return state;
  }
}