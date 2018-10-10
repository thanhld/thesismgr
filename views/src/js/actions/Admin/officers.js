import axios from 'axios'
import * as types from 'Constants'

export const loadOfficers = () => {
    return dispatch => {
        return dispatch({
            type: types.ADMIN_LOAD_OFFICERS,
            payload: axios.get(types.API_ADMIN_OFFICER)
        })
    }
}

export const createOfficer = (officer) => {
    return dispatch => {
        const { username, password, officerCode, fullname, vnuMail, role, degreeId, departmentId } = officer
        return dispatch({
            type: types.ADMIN_CREATE_OFFICER,
            payload: axios.post(types.API_ADMIN_IMPORT_OFFICER, [{
                username: username && username.trim(),
                password,
                officerCode: officerCode && officerCode.trim(),
                fullname: fullname && fullname.trim(),
                vnuMail: vnuMail && vnuMail.trim(),
                role: parseInt(role),
                degreeId: parseInt(degreeId),
                departmentId
            }])
        })
    }
}

export const importOfficer = (officers) => {
    return dispatch => {
        return dispatch({
            type: types.ADMIN_CREATE_OFFICER,
            payload: axios.post(types.API_ADMIN_IMPORT_OFFICER, officers)
        })
    }
}

export const updateOfficer = (officer) => {
    return dispatch => {
        const { id, username, officerCode, fullname, vnuMail, role, degreeId, departmentId } = officer
        return dispatch({
            type: types.ADMIN_UPDATE_OFFICER,
            payload: axios.put(`${types.API_ADMIN_OFFICER}/${id}`, {
                username: username && username.trim(),
                officerCode: officerCode && officerCode.trim(),
                fullname: fullname && fullname.trim(),
                vnuMail: vnuMail && vnuMail.trim(),
                role: parseInt(role),
                degreeId: parseInt(degreeId),
                departmentId
            })
        })
    }
}

export const deleteOfficer = (officer) => {
    return dispatch => {
        const { id } = officer
        return dispatch({
            type: types.ADMIN_DELETE_OFFICER,
            payload: axios.delete(`${types.API_ADMIN_OFFICER}/${id}`)
        })
    }
}
