import { alertConstants } from '../_constants/alert.constants';
import { userConstants } from '../_constants/user.constants';
import { roleConstants } from '../_constants/role.constants';


function users(state = {}, action) {
  switch (action.type) {
    case userConstants.USERUPDATE_REQUEST:
      return {
        ...state,
        user: action.payload
      }

      case userConstants.USERUPDATE_SUCCESS:
        return {
            ...state,
            user: action.payload,
            alert: alertConstants.SUCCESS
        };
      
     case userConstants.USERUPDATE_FAILURE:
          return {
            alert: alertConstants.CLEAR
          };

    case userConstants.USERDELETE_REQUEST:
      return {
        ...state,
        user: action.payload
      };

      case userConstants.USERDELETE_SUCCESS:
        return {
            ...state,
            user: action.payload,
            alert: alertConstants.SUCCESS
        };
    
    case userConstants.USERDELETE_FAILURE:
      return {
        alert: alertConstants.CLEAR
      };  

    case userConstants.USERGETALL_REQUEST:
      return {
        ...state,
        user: action.payload
      };

    case userConstants.USERGETALL_SUCCESS:
      return {
        ...state,
        user: action.payload,
        alert: alertConstants.SUCCESS
      };
      
    case userConstants.USERGETALL_FAILURE:
      return {
        error: alertConstants.ERROR
      };

    case userConstants.USERCREATE_REQUEST:
      return {
        ...state,
        user: action.user,
    };

    case userConstants.USERCREATE_SUCCESS:
      return {
        ...state,
        user: action.payload,
        alert: alertConstants.SUCCESS
    };

    case userConstants.USERCREATE_FAILURE:
      return {
        alert: alertConstants.ERROR
      };

    case userConstants.USERGETBYID_REQUEST:
      return {
        ...state,
        user: action.payload
      };

      case userConstants.USERGETBYID_SUCCESS:
        return {
          ...state,
          user: action.payload,
          alert: alertConstants.SUCCESS
      };

      case userConstants.USERGETBYID_FAILURE:
        return {
          alert: alertConstants.ERROR
        };

    case userConstants.DELETE_REQUEST:
      // add 'deleting:true' property to user being deleted
      return {
        ...state,
        items: state.items.map(user =>
          user.id === action.id
            ? { ...user, deleting: true }
            : user
        )
      };

      case userConstants.LOGIN_SUCCESS:
     return {
    ...state,
    user: action.payload,
    isAuthenticated: true,
  };


    case userConstants.DELETE_SUCCESS:
      // remove deleted user from state
      return {
        items: state.items.filter(user => user.id !== action.id)
      };

    case userConstants.DELETE_FAILURE:
      // remove 'deleting:true' property and add 'deleteError:[error]' property to user 
      return {
        ...state,
        items: state.items.map(user => {
          if (user.id === action.id) {
            // make copy of user without 'deleting:true' property
            const { deleting, ...userCopy } = user;
            // return copy of user with 'deleteError:[error]' property
            return { ...userCopy, deleteError: action.error };
          }

          return user;
        })
      };
      
    default:
      return state
  }
}

export { users };