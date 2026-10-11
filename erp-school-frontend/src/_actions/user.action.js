import { userConstants }    from '../_constants/user.constants';
import { authService }      from '../_services/auth.service';
import { alertActions }     from './alert.action';

export const userActions = {
    login,
    logout,
    register,
    update,
    getAllUsers,
    getUserById,
    delete: _delete,
};

//Function to loggin to system
function login(username, password) {
    return dispatch => {
        dispatch(request({ username }));

        authService.login(username, password)
            .then(
                (user) => { 
                    dispatch(success(user));
                  window.location.href = "/home";
                   
                },
                error => {
                    dispatch(failure(error.toString()));
                    dispatch(alertActions.error(error.toString()));
                }
            );
    };

    function request(user) { return { type: userConstants.LOGIN_REQUEST, user } }
    function success(user) { return { type: userConstants.LOGIN_SUCCESS, user } }
    function failure(error) { return { type: userConstants.LOGIN_FAILURE, error } }
}

//Function to loggout
function logout() {
    authService.logout();
    return { type: userConstants.LOGOUT };
}


//Function to Register a user
function register(user) {
    return dispatch => {
        dispatch(request(user));
        authService.register(user)
            .then(
                user => { 
                    dispatch(success()); 
                   dispatch(alertActions.success('User Registration successful'));
                },
                error => {
                    dispatch(failure(error.toString()));
                    dispatch(alertActions.error(error.toString()));
                }
            );
    };

    function request(user) { return { type: userConstants.USERCREATE_REQUEST, user } }
    function success(user) { return { type: userConstants.USERCREATE_SUCCESS, user } }
    function failure(error) { return { type: userConstants.USERCREATE_FAILURE, error } }
}

//Function to Update a user
function update(user) {
    return dispatch => {
        dispatch(request(user));

        authService.update(user)
            .then(
                users => { 
                    dispatch(success(users));
                   // window.location = '/listusers';
                    dispatch(alertActions.success('User is updating successfully'));
                },
                error => {
                    dispatch(failure(error.toString()));
                    dispatch(alertActions.error(error.toString()));
                }
            );
    };

    function request(user) { return { type: userConstants.USERUPDATE_REQUEST, user } }
    function success(user) { return { type: userConstants.USERUPDATE_SUCCESS, user } }
    function failure(error) { return { type: userConstants.USERUPDATE_FAILURE, error } }
}

//To retireve all users
function getAllUsers() {
    return dispatch => {
        dispatch(request());

        authService.getAll()
            .then(
                users => dispatch(success(users)),
                error => dispatch(failure(error.toString()))
            );
    };

    function request() { return { type: userConstants.USERGETALL_REQUEST } }
    function success(users) { return { type: userConstants.USERGETALL_SUCCESS, users } }
    function failure(error) { return { type: userConstants.USERGETALL_FAILURE, error } }
};

//To retrieve user by Id
function getUserById(id){
    return dispatch => {
        dispatch(request(id));

        authService.getById(id)
        .then(
            id => dispatch(success(id)),
            error => dispatch(failure(id, error.toString()))
        );
    };
    function request(id) { return { type: userConstants.USERGETBYID_REQUEST, id } }
    function success(id) { return { type: userConstants.USERGETBYID_SUCCESS, id } }
    function failure(id, error) { return { type: userConstants.USERGETBYID_FAILURE, id, error } }
}

// prefixed function name with underscore because delete is a reserved word in javascript
 function _delete(id) {
    return dispatch => {
        dispatch(request(id));

        authService.delete(id)
            .then(
                id => dispatch(success(id)),
                error => dispatch(failure(id, error.toString()))
            );
    };

    function request(id) { return { type: userConstants.USERDELETE_REQUEST, id } }
    function success(id) { return { type: userConstants.USERDELETE_SUCCESS, id } }
    function failure(id, error) { return { type: userConstants.USERDELETE_FAILURE, id, error } }
};
