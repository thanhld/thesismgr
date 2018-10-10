import axios from 'axios'
import * as types from 'Constants'

export const adminLoadAnnouncements = () => {
    return dispatch => {
        return dispatch({
            type: types.ADMIN_LOAD_ANNOUNCEMENTS,
            payload: axios.get(types.API_ADMIN_ANNOUNCEMENT)
        })
    }
}

export const createAnnouncement = data => {
    return dispatch => {
        return dispatch({
            type: types.ADMIN_CREATE_ANNOUCEMENT,
            payload: axios.post(types.API_ADMIN_ANNOUNCEMENT, data)
        })
    }
}

export const updateAnnouncement = (data, id) => {
    return dispatch => {
        return dispatch({
            type: types.ADMIN_UPDATE_ANNOUCEMENT,
            payload: axios.put(`${types.API_ADMIN_ANNOUNCEMENT}/${id}`, data)
        })
    }
}

export const deleteAnnouncement = id => {
    return dispatch => {
        return dispatch({
            type: types.ADMIN_DELETE_ANNOUCEMENT,
            payload: axios.delete(`${types.API_ADMIN_ANNOUNCEMENT}`, {
                data: [id]
            })
        })
    }
}
