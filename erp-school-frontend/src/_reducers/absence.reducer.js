import { alertConstants } from "../_constants";
import { absenceConstants } from '../_constants/absence.constants';

export function absence(state = {}, action) {
  switch (action.type) {
    case absenceConstants.ABSENCECREATE_REQUEST:
      return {
        ...state,
        absence: action.payload,
      };

    case absenceConstants.ABSENCECREATE_SUCCESS:
      return {
        ...state,
        absence: action.payload,
        alert: alertConstants.SUCCESS,
      };

    case absenceConstants.ABSENCECREATE_FAILURE:
      return {
        alert: alertConstants.CLEAR,
      };

    case absenceConstants.ABSENCEDELETE_REQUEST:
      return {
        ...state,
        absence: action.payload,
      };

    case absenceConstants.ABSENCEDELETE_SUCCESS:
      return {
        ...state,
        absence: action.payload,
        alert: alertConstants.SUCCESS,
      };

    case absenceConstants.ABSENCEDELETE_FAILURE:
      return {
        alert: alertConstants.CLEAR,
      };

    case absenceConstants.ABSENCEUPDATE_REQUEST:
      return {
        ...state,
        absence: action.payload,
      };

    case absenceConstants.ABSENCEUPDATE_SUCCESS:
      return {
        ...state,
        absence: action.payload,
        alert: alertConstants.SUCCESS,
      };

    case absenceConstants.ABSENCEUPDATE_FAILURE:
      return {
        alert: alertConstants.CLEAR,
      };

    case absenceConstants.ABSENCEGETALL_REQUEST:
      return {
        ...state,
        absence: action.payload,
      };

    case absenceConstants.ABSENCEGETALL_SUCCESS:
      return {
        ...state,
        absence: action.payload,
        alert: alertConstants.SUCCESS,
      };

    case absenceConstants.ABSENCEGETALL_FAILURE:
      return {
        alert: alertConstants.CLEAR,
      };

    case absenceConstants.ABSENCEGETBYID_REQUEST:
      return {
        ...state,
        absence: action.payload,
      };

    case absenceConstants.ABSENCEGETBYID_SUCCESS:
      return {
        ...state,
        absence: action.payload,
        alert: alertConstants.SUCCESS,
      };

    case absenceConstants.ABSENCEGETBYID_FAILURE:
      return {
        alert: alertConstants.CLEAR,
      };

    default:
      return state;
  }
}