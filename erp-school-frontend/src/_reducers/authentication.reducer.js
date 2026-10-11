import { userConstants } from '../_constants/user.constants';
import { alertConstants } from '../_constants/alert.constants';

let user = JSON.parse(localStorage.getItem('user'));

/* const initialState = user
  ? { isLoggedIn: true, user }
  : { isLoggedIn: false, user: null }; */

let storedUser = null;

try {
  storedUser = JSON.parse(localStorage.getItem('user'));
} catch (e) {
  storedUser = null;
}

const initialState = {
  isLoggedIn: !!storedUser,
  user: storedUser || null,
};

 function authentication(state = initialState, action) {
  switch (action.type) {
    case userConstants.LOGIN_REQUEST:
      return {
        isLoggedIn: true,
        user: action.payload
      };
      
    case userConstants.LOGIN_SUCCESS:
      return {
        ...state,
        isLoggedIn: true,
        user: action.payload,
        alert: alertConstants.SUCCESS
      };

    case userConstants.LOGIN_FAILURE:
      return {
        ...state,
        isLoggedIn: false,
        user: null
      };
    case userConstants.LOGOUT:
      return {    
         ...state,
        isLoggedIn: false,
        user: null
      };

    default:
      return state
  }
}

export { authentication };