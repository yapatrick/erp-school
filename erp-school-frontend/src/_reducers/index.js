import { combineReducers } from 'redux';
import { authentication } from './authentication.reducer';
import { users }          from './users.reducer';
import { alert }          from './alert.reducer';
import { message }        from './message.reducer';
import { role }           from './role.reducer';
import { permission }     from './permission.reducer';

const rootReducer = combineReducers({
  authentication,
  users,
  alert,
  message,
  role,
  permission
});

export default rootReducer;