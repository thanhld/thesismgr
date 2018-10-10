import axios from 'axios'
import * as types from 'Constants'

export const importLearnerCodes = data => {
    return dispatch => {
        return dispatch({
            type: types.ADMIN_TOPIC_IMPORT_LEARNERCODES,
            payload: axios.post(types.API_ADMIN_TOPIC_IMPORT_LEARNERCODES, data)
        })
    }
}

export const createOutOfficers = data => {
    return dispatch => {
        return dispatch({
            type: types.ADMIN_CREATE_OUT_OFFICERS,
            payload: axios.post(types.API_ADMIN_OUT_OFFICER, data)
        })
    }
}

export const requestChange = data => {
    return dispatch => {
        return dispatch({
            type: types.ADMIN_REQUEST_TOPIC_CHANGE,
            payload: axios.put(types.API_ADMIN_REQUEST_CHANGE, data)
        })
    }
}

export const requestProtect = data => {
    return dispatch => {
        return dispatch({
            type: types.ADMIN_REQUEST_TOPIC_PROTECT,
            payload: axios.put(types.API_ADMIN_REQUEST_PROTECT, data)
        })
    }
}

export const deleteTopic = id => {
    return dispatch => {
        return dispatch({
            type: types.ADMIN_DELETE_TOPIC,
            payload: axios.delete(`${types.API_ADMIN_TOPIC}/${id}`)
        })
    }
}

export const flushTopics = () => {
    return {
        type: types.ADMIN_FLUSH_TOPICS
    }
}
