import axios from 'axios'
import * as types from 'Constants'

export const loadLecturerInformation = (uid) => {
    return dispatch => {
        return dispatch({
            type: types.LECTURER_LOAD_INFOMATION,
            payload: axios.get(`${types.API_OFFICER}/${uid}`)
        })
    }
}

export const updateLecturerInformation = (lecturer) => {
    const { id, website, address, phone, otherEmail, description } = lecturer;

    return dispatch => {
        return dispatch({
            type: types.LECTURER_UPDATE_INFOMATION,
            payload: axios.put(`${types.API_OFFICER}/${id}`, {
                website: website && website.trim(),
                address: address && address.trim(),
                phone: phone && phone.trim(),
                otherEmail: otherEmail && otherEmail.trim(),
                description: description && description.trim()
            })
        })
    }
}

export const loadLecturerAreas = (uid) => {
    const api = types.API_OFFICER + '/' + uid + '/knowledge-area';

    return dispatch => {
        return dispatch({
            type: types.LECTURER_LOAD_AREAS,
            payload: axios.get(api)
        })
    }
}

export const addLecturerAreas = (areaIds, uid) => {
    const api = types.API_OFFICER_KNOWLEDGE_AREA;

    return dispatch => {
        return dispatch({
            type: types.LECTURER_ADD_AREAS,
            payload: axios.post(api, areaIds)
        })
    }
}

export const deleteLecturerArea = (uid, areaId) => {
    const api = `${types.API_OFFICER_KNOWLEDGE_AREA}/${areaId}`;

    return dispatch => {
        return dispatch({
            type: types.LECTURER_DELETE_AREA,
            payload: axios.delete(api)
        })
    }
}

export const loadTopics = id => {
    return dispatch => {
        return dispatch({
            type: types.LECTURER_LOAD_TOPICS,
            payload: axios.get(`${types.API_OFFICER}/${id}/topic`)
        })
    }
}

export const uploadAvatar = data => {
    return dispatch => {
        return dispatch({
            type: types.LECTURER_UPLOAD_AVATAR,
            payload: axios.post(types.API_LECTURER_UPLOAD_AVATAR, data)
        })
    }
}

export const removeAvatar = (url) => {
    return dispatch => {
        return dispatch({
            type: types.LECTURER_REMOVE_AVATAR,
            payload: axios.put(types.API_LECTURER_REMOVE_AVATAR, {avatarUrl : url})
        })
    }
}

export const updateReview = data => {
    return dispatch => {
        return dispatch({
            type: types.LECTURER_UPDATE_REVIEW,
            payload: axios.put(types.API_OFFICER_REVIEW, data)
        })
    }
}
