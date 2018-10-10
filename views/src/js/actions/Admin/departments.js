import axios from 'axios'
import * as types from 'Constants'

export const loadDepartmentOfFaculty = facultyId => {
    const filter = `facultyId=${facultyId}`
    return dispatch => {
        return dispatch({
            type: types.ADMIN_LOAD_DEPARTMENTS,
            payload: axios.get(types.API_DEPARTMENT, {
                params: { filter }
            })
        })
    }
}

export const createDepartment = (department) => {
    return dispatch => {
        const { name, type, address, phone, website } = department
        return dispatch({
            type: types.ADMIN_CREATE_DEPARTMENT,
            payload: axios.post(types.API_ADMIN_DEPARTMENT, {
                name: name && name.trim(),
                type: parseInt(type),
                address: address && address.trim(),
                phone: phone && phone.trim(),
                website: website && website.trim()
            })
        })
    }
}

export const updateDepartment = (department) => {
    return dispatch => {
        const { id, name, type, address, phone, website } = department
        return dispatch({
            type: types.ADMIN_UPDATE_DEPARTMENT,
            payload: axios.put(`${types.API_ADMIN_DEPARTMENT}/${id}`, {
                name: name && name.trim(),
                type: parseInt(type),
                address: address && address.trim(),
                phone: phone && phone.trim(),
                website: website && website.trim()
            })
        })
    }
}

export const deleteDepartment = (id) => {
    return dispatch => {
        return dispatch({
            type: types.ADMIN_DELETE_DEPARTMENT,
            payload: axios.delete(`${types.API_ADMIN_DEPARTMENT}/${id}`)
        })
    }
}
