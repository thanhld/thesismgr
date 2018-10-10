import axios from 'axios'
import * as types from 'Constants'

export const loadDocuments = () => {
    return dispatch => {
        return dispatch({
            type: types.ADMIN_LOAD_DOCUMENTS,
            payload: axios.get(types.API_ADMIN_DOCUMENT)
        })
    }
}

export const createDocument = (documentCode, name, url, createdDate) => {
    let data = {
        documentCode, name, url, createdDate
    }
    if (!data['url']) delete data['url']
    return dispatch => {
        return dispatch({
            type: types.ADMIN_CREATE_DOCUMENT,
            payload: axios.post(types.API_DOCUMENT, data)
        })
    }
}

export const editDocument = (id, data) => {
    return dispatch => {
        return dispatch({
            type: types.ADMIN_EDIT_DOCUMENT,
            payload: axios.put(`${types.API_ADMIN_DOCUMENT}/${id}`, data)
        })
    }
}
