import { permissionService }      from '../_services/permission.service';
import { alertActions }     from '../_actions';
import { permissionConstants }    from '../_constants/permission.constants';


export const permissionActions = {
    getAllPermission,
    getPermissionById,
    createPermission,
    updatePermission,
    deletePermission: _delete
};

function createPermission(permission){
    return dispatch => {
        dispatch(request(permission));

        permissionService.createPermission(permission)
            .then(
                user => { 
                    dispatch(success(user)); 
                   dispatch(alertActions.success('Permission is create successfully'));
                },
                error => {
                    dispatch(failure(error.toString()));
                    dispatch(alertActions.error(error.toString()));
                }
            );
    };

    function request(permission) { return { type: roleConstants.PERMISSIONCREATE_REQUEST, permission } }
    function success(permission) { return { type: roleConstants.PERMISSIONCREATE_SUCCESS, permission } }
    function failure(error) { return { type: roleConstants.PERMISSIONCREATE_FAILURE, error } }
}

//Update an permission 
function updatePermission(permission){
    return dispatch => {
        dispatch(request(permission));

        roleService.updatePermission(permission)
            .then(
                response => { 
                    dispatch(success(response));
                    dispatch(alertActions.success('Permission is updating successfully'));
                },
                error => {
                    dispatch(failure(error.toString()));
                    dispatch(alertActions.error(error.toString()));
                }
            );
        };

        function request(permission) { return { type: roleConstants.PERMISSIONUPDATE_REQUEST, permission } }
        function success(permission) { return { type: roleConstants.PERMISSIONUPDATE_SUCCESS, permission } }
        function failure(error) { return { type: roleConstants.PERMISSIONUPDATE_FAILURE, error } }
}

//Retrieve all roles in store
function getAllPermission(){
    return dispatch => {
        dispatch(request());

        roleService.getAllPermission()
            .then(
                users => dispatch(success(users)),
                error => dispatch(failure(error.toString()))
            );
    };

    function request()      { return { type: roleConstants.PERMISSIONGETALL_REQUEST } }
    function success(users) { return { type: roleConstants.PERMISSIONGETALL_SUCCESS, users } }
    function failure(error) { return { type: roleConstants.PERMISSIONGETALL_FAILURE, error } }
}

//Get Permission By ID 
function getPermissionById(id){
    return dispatch => {
        dispatch(request(id));

        roleService.getPermissionByID(id)
        .then(
            id => dispatch(success(id)),
            error => dispatch(failure(id, error.toString()))
        );
    };
    function request(id) { return { type: roleConstants.PERMISSIONGETBYID_REQUEST, id } }
    function success(id) { return { type: roleConstants.PERMISSIONGETBYID_SUCCESS, id } }
    function failure(id, error) { return { type: roleConstants.PERMISSIONGETBYID_FAILURE, id, error } }
}

// prefixed function name with underscore because delete is a reserved word in javascript
function _delete(id) {
    return dispatch => {
        dispatch(request(id));

        roleService.deletePermission(id)
            .then(
                id => dispatch(success(id)),
                error => dispatch(failure(id, error.toString()))
            );
    };

    function request(id) { return { type: roleConstants.PERMISSIONDELETE_REQUEST, id } }
    function success(id) { return { type: roleConstants.PERMISSIONDELETE_SUCCESS, id } }
    function failure(id, error) { return { type: roleConstants.PERMISSIONDELETE_FAILURE, id, error } }
};
