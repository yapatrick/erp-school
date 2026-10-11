import axios        from 'axios';
import { authHeader }   from '../_helpers/auth-header';
import { API_URL }  from '../_api/ApiUrl';

export const getAdminBoard = () =>{
    return axios.get(API_URL + '/admin', { headers: authHeader() });
}

export const getManagerBoard = () =>{
    return axios.get(API_URL + '/manager', { headers: authHeader() });
}
export const userService = {
    getAdminBoard,
    getManagerBoard
}