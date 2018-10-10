import axios from 'axios'
import * as types from 'Constants'

export const loadTopics = (learnerId) => {
    const api = `${types.API_LEARNER}/${learnerId}/topic`
    return dispatch => {
        return dispatch({
            type: types.LEARNER_LOAD_TOPICS,
            payload: axios.get(api)
        })
    }
}

export const uploadAttachment = data => {
    return dispatch => {
        return dispatch({
            type: types.LEARNER_UPLOAD_ATTACHMENT,
            payload: axios.post(types.API_LEARNER_ATTACHMENT, data)
        })
    }
}

export const loadProfile = (uid) => {
    return dispatch => {
        return dispatch({
            type: types.LEARNER_LOAD_PROFILE,
            payload: axios.get(`${types.API_LEARNER}/${uid}`)
        })
    }
}

export const updateProfile = (uid, data) => {
    return dispatch => {
        return dispatch({
            type: types.LEARNER_UPDATE_PROFILE,
            payload: axios.put(`${types.API_LEARNER}/${uid}`, data)
        })
    }
}

export const createOutOfficers = data => {
    return dispatch => {
        return dispatch({
            type: types.LEARNER_CREATE_OUTOFFICERS,
            payload: axios.post(`${types.API_OUT_OFFICER}`, data)
        })
    }
}

export const cancelChangeRequest = id => {
    return dispatch => {
        return dispatch({
            type: types.LEARNER_CANCEL_CHANGE_REQUEST,
            payload: axios.delete(`${types.API_TOPIC}/${id}/request-change`)
        })
    }
}
