import axios from 'axios'
import * as types from 'Constants'

export const reloadAreas = () => {
    return dispatch => {
        return dispatch({
            type: types.LOAD_AREAS,
            payload: axios.get(types.API_AREA)
        })
    }
}

export const createArea = (area) => {
    return dispatch => {
        return dispatch({
            type: types.ADMIN_CREATE_AREA,
            payload: axios.post(types.API_ADMIN_AREA, {
                        name: area.name && area.name.trim(),
                        parentId: area.parentId
                    })
        })
    }
}

export const updateArea = (area) => {
    return dispatch => {
        const { id, name, parentId } = area //have not handled parentId = null
        return dispatch({
            type: types.ADMIN_UPDATE_AREA,
            payload: axios.put(`${types.API_ADMIN_AREA}/${id}`, {
                name: name && name.trim(),
                parentId
            })
        })
    }
}

export const deleteArea = (id) => {
    return dispatch => {
        return dispatch({
            type: types.ADMIN_DELETE_AREA,
            payload: axios.delete(`${types.API_ADMIN_AREA}/${id}`)
        })
    }
}
