import { roleService }      from '../_services/role.service';
import { alertActions }     from '../_actions';
import { roleConstants }    from '../_constants/role.constants';


export const roleActions = {
    getAllRole,
    getRoleById,
    createRole,
    updateRole,
    deleteRole: _delete
};

function createRole(role){
    return dispatch => {
        dispatch(request(role));

        roleService.createRole(role)
            .then(
                user => { 
                    dispatch(success(user)); 
                   dispatch(alertActions.success('Role is create successfully'));
                },
                error => {
                    dispatch(failure(error.toString()));
                    dispatch(alertActions.error(error.toString()));
                }
            );
    };

    function request(role) { return { type: roleConstants.ROLECREATE_REQUEST, role } }
    function success(role) { return { type: roleConstants.ROLECREATE_SUCCESS, role } }
    function failure(error) { return { type: roleConstants.ROLECREATE_FAILURE, error } }
}

//Update an role 
function updateRole(role){
    return dispatch => {
        dispatch(request(role));

        roleService.updateRole(role)
            .then(
                response => { 
                    dispatch(success(response));
                    dispatch(alertActions.success('Role is updating successfully'));
                },
                error => {
                    dispatch(failure(error.toString()));
                    dispatch(alertActions.error(error.toString()));
                }
            );
        };

        function request(role) { return { type: roleConstants.ROLEUPDATE_REQUEST, role } }
        function success(role) { return { type: roleConstants.ROLEUPDATE_SUCCESS, role } }
        function failure(error) { return { type: roleConstants.ROLEUPDATE_FAILURE, error } }
}

//Retrieve all roles in store
function getAllRole(){
    return dispatch => {
        dispatch(request());

        roleService.getAllRole()
            .then(
                users => dispatch(success(users)),
                error => dispatch(failure(error.toString()))
            );
    };

    function request()      { return { type: roleConstants.ROLEGETALL_REQUEST } }
    function success(users) { return { type: roleConstants.ROLEGETALL_SUCCESS, users } }
    function failure(error) { return { type: roleConstants.ROLEGETALL_FAILURE, error } }
}

//Get Role By ID 
function getRoleById(id){
    return dispatch => {
        dispatch(request(id));

        roleService.getRoleByID(id)
        .then(
            id => dispatch(success(id)),
            error => dispatch(failure(id, error.toString()))
        );
    };
    function request(id) { return { type: roleConstants.ROLEGETBYID_REQUEST, id } }
    function success(id) { return { type: roleConstants.ROLEGETBYID_SUCCESS, id } }
    function failure(id, error) { return { type: roleConstants.ROLEGETBYID_FAILURE, id, error } }
}

// prefixed function name with underscore because delete is a reserved word in javascript
function _delete(id) {
    return dispatch => {
        dispatch(request(id));

        roleService.deleteRole(id)
            .then(
                id => dispatch(success(id)),
                error => dispatch(failure(id, error.toString()))
            );
    };

    function request(id) { return { type: roleConstants.ROLEDELETE_REQUEST, id } }
    function success(id) { return { type: roleConstants.ROLEDELETE_SUCCESS, id } }
    function failure(id, error) { return { type: roleConstants.ROLEDELETE_FAILURE, id, error } }
};
