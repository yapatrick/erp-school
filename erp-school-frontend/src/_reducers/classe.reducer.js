import { alertConstants } from "../_constants";
import { classeConstants } from '../_constants/classe.constants';

export function classe(state = {}, action) {
  switch (action.type) {
    case classeConstants.CLASSECREATE_REQUEST:
      return {
        ...state,
        classe: action.payload,
      };

    case classeConstants.CLASSECREATE_SUCCESS:
      return {
        ...state,
        classe: action.payload,
        alert: alertConstants.SUCCESS,
      };

    case classeConstants.CLASSECREATE_FAILURE:
      return {
        alert: alertConstants.CLEAR,
      };

    case classeConstants.CLASSEDELETE_REQUEST:
      return {
        ...state,
        classe: action.payload,
      };

    case classeConstants.CLASSEDELETE_SUCCESS:
      return {
        ...state,
        classe: action.payload,
        alert: alertConstants.SUCCESS,
      };

    case classeConstants.CLASSEDELETE_FAILURE:
      return {
        alert: alertConstants.CLEAR,
      };

    case classeConstants.CLASSEUPDATE_REQUEST:
      return {
        ...state,
        classe: action.payload,
      };

    case classeConstants.CLASSEUPDATE_SUCCESS:
      return {
        ...state,
        classe: action.payload,
        alert: alertConstants.SUCCESS,
      };

    case classeConstants.CLASSEUPDATE_FAILURE:
      return {
        alert: alertConstants.CLEAR,
      };

    case classeConstants.CLASSEGETALL_REQUEST:
      return {
        ...state,
        classe: action.payload,
      };

    case classeConstants.CLASSEGETALL_SUCCESS:
      return {
        ...state,
        classe: action.payload,
        alert: alertConstants.SUCCESS,
      };

    case classeConstants.CLASSEGETALL_FAILURE:
      return {
        alert: alertConstants.CLEAR,
      };

    case classeConstants.CLASSEGETBYID_REQUEST:
      return {
        ...state,
        classe: action.payload,
      };

    case classeConstants.CLASSEGETBYID_SUCCESS:
      return {
        ...state,
        classe: action.payload,
        alert: alertConstants.SUCCESS,
      };

    case classeConstants.CLASSEGETBYID_FAILURE:
      return {
        alert: alertConstants.CLEAR,
      };

    default:
      return state;
  }
}