import axios from 'axios'
import * as types from 'Constants'
import { ITEM_PER_PAGE } from 'Config'

export const loadLearners = (page = 1, limit = ITEM_PER_PAGE, filCourse) => {
    let filter = `page=${page}&limit=${limit}`
    if (filCourse) filter += `&filter=trainingCourseId=${filCourse}`
    filter += `&order=username&direction=ASC`
    return dispatch => {
        return dispatch({
            type: types.ADMIN_LOAD_LEARNERS,
            payload: axios.get(`${types.API_ADMIN_LEARNER}?${filter}`)
        })
    }
}

export const createLearner = (learner) => {
    return dispatch => {
        return dispatch({
            type: types.ADMIN_CREATE_LEARNER,
            payload: axios.post(types.API_ADMIN_CREATE_LEARNER, [learner])
        })
    }
}

export const importLearner = (learners) => {
    return dispatch => {
        return dispatch({
            type: types.ADMIN_CREATE_LEARNER,
            payload: axios.post(types.API_ADMIN_CREATE_LEARNER, learners)
        })
    }
}

export const updateLearner = (learner) => {
    return dispatch => {
        const { id, username, vnuMail, learnerCode, fullname, trainingCourseId } = learner
        return dispatch({
            type: types.ADMIN_UPDATE_LEARNER,
            payload: axios.put(`${types.API_ADMIN_LEARNER}/${id}`, {
                username: username && username.trim(),
                vnuMail: vnuMail && vnuMail.trim(),
                learnerCode: learnerCode && learnerCode.trim(),
                fullname: fullname && fullname.trim(),
                trainingCourseId
            })
        })
    }
}

export const deleteLearner = (id) => {
    return dispatch => {
        return dispatch({
            type: types.ADMIN_DELETE_LEARNER,
            payload: axios.delete(`${types.API_ADMIN_LEARNER}/${id}`)
        })
    }
}
