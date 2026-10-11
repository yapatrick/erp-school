import {alertConstants} from '../_constants/alert.constants';
import {roleConstants} from '../_constants/role.constants';

export function role(state = {}, action) {
    switch (action.type) {
        case roleConstants.ROLECREATE_REQUEST:
            return {
                ...state,
                role: action.payload
            };

        case roleConstants.ROLECREATE_SUCCESS:
            return {
                ...state,
                role: action.payload,
                alert: alertConstants.SUCCESS
            };

        case roleConstants.ROLECREATE_FAILURE:
            return {alert: alertConstants.CLEAR};

        case roleConstants.ROLEDELETE_REQUEST:
            return {
                ...state,
                role: action.payload
            };

        case roleConstants.ROLEDELETE_SUCCESS:
            return {
                ...state,
                role: action.payload,
                alert: alertConstants.SUCCESS
            };

        case roleConstants.ROLEDELETE_FAILURE:
            return {alert: alertConstants.CLEAR};

        case roleConstants.ROLEUPDATE_REQUEST:
            return {
                ...state,
                role: action.payload
            };

        case roleConstants.ROLEUPDATE_SUCCESS:
            return {
                ...state,
                role: action.payload,
                alert: alertConstants.SUCCESS
            };

        case roleConstants.ROLEUPDATE_FAILURE:
            return {alert: alertConstants.CLEAR};

        case roleConstants.ROLEGETALL_REQUEST:
            return {
                ...state,
                role: action.payload
            };

        case roleConstants.ROLEGETALL_SUCCESS:
            return {
                ...state,
                role: action.payload,
                alert: alertConstants.SUCCESS
            };

        case roleConstants.ROLEGETALL_FAILURE:
            return {alert: alertConstants.CLEAR};
        
        case roleConstants.ROLEGETBYID_REQUEST:
              return {
                  ...state,
                  role: action.payload
              };
              case roleConstants.ROLEGETBYID_SUCCESS:
                return {
                    ...state,
                    role: action.payload,
                    alert: alertConstants.SUCCESS
                };
    
            case roleConstants.ROLEGETBYID_FAILURE:
                return {alert: alertConstants.CLEAR};

        default:
            return state
    }
}
