import axios from 'axios'
import * as types from 'Constants'

export const loadAuth = () => {
    return dispatch => {
        return dispatch({
            type: types.AUTH_LOAD,
            payload: axios.get(types.API_AUTH_LOAD)
        })
    }
}

export const auth = (username, password) => {
    return dispatch => {
        return dispatch({
            type: types.AUTH,
            payload: axios.post(types.API_AUTH, {
                username,
                password
            })
        })
    }
}

export const logout = () => {
    return dispatch => {
        return dispatch({
            type: types.LOGOUT,
            payload: axios.delete(types.API_LOGOUT)
        })
    }
}

export const forgotPassword = (vnuMail) => {
    return dispatch => {
        return dispatch({
            type: types.FORGOT_PASSWORD,
            payload: axios.post(types.API_FORGOT_PASSWORD, {
                vnuMail
            })
        })
    }
}

export const changePassword = (oldPassword, newPassword) => {
    return dispatch => {
        return dispatch({
            type: types.CHANGE_PASSWORD,
            payload: axios.post(types.API_CHANGE_PASSWORD, {
                oldPassword,
                newPassword
            })
        })
    }
}

export const setPassword = (user) => {
    const { uid, securityToken, password } = user;

    return dispatch => {
        return dispatch({
            type: types.SET_PASSWORD,
            payload: axios.post(types.API_SET_PASSWORD, {
                uid,
                securityToken,
                password
            })
        })
    }
}
