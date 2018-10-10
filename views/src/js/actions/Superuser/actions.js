import axios from 'axios'
import * as types from 'Constants'

/*
    Superuser handle faculties
 */
export const loadFaculties = () => {
    return dispatch => {
        return dispatch({
            type: types.SUPERUSER_LOAD_FACULTIES,
            payload: axios.get(types.API_SUPERUSER_FACULTY)
        })
    }
}

export const createFaculty = (faculty) => {
    return dispatch => {
        const { username, password, vnuMail, name, shortName, phone, website, address } = faculty
        return dispatch({
            type: types.SUPERUSER_CREATE_FACULTY,
            payload: axios.post(types.API_SUPERUSER_FACULTY, {
                username: username && username.trim(),
                password,
                vnuMail: vnuMail && vnuMail.trim(),
                name: name && name.trim(),
                shortName: shortName && shortName.trim(),
                phone: phone && phone.trim(),
                website: website && website.trim(),
                address: address && address.trim()
            })
        })
    }
}

export const updateFaculty = (faculty) => {
    return dispatch => {
        const { id, username, vnuMail, name, shortName, phone, website, address } = faculty
        return dispatch({
            type: types.SUPERUSER_UPDATE_FACULTY,
            payload: axios.put(`${types.API_FACULTY}/${id}`, {
                username: username && username.trim(),
                vnuMail: vnuMail && vnuMail.trim(),
                name: name && name.trim(),
                shortName: shortName && shortName.trim(),
                phone: phone && phone.trim(),
                website: website && website.trim(),
                address: address && address.trim()
            })
        })
    }
}

export const deleteFaculty = (id) => {
    return dispatch => {
        return dispatch({
            type: types.SUPERUSER_DELETE_FACULTY,
            payload: axios.delete(`${types.API_SUPERUSER_FACULTY}/${id}`)
        })
    }
}

/*
    Superuser handle degrees
 */

export const createDegree = (degree) => {
    return dispatch => {
        const { name } = degree
        return dispatch({
            type: types.SUPERUSER_CREATE_DEGREE,
            payload: axios.post(types.API_SUPERUSER_DEGREE, {
                name: name && name.trim()
            })
        })
    }
}

export const updateDegree = (degree) => {
    return dispatch => {
        const { id, name } = degree
        return dispatch({
            type: types.SUPERUSER_UPDATE_DEGREE,
            payload: axios.put(`${types.API_SUPERUSER_DEGREE}/${id}`, {
                name: name && name.trim()
            })
        })
    }
}

export const deleteDegree = (id) => {
    return dispatch => {
        return dispatch({
            type: types.SUPERUSER_DELETE_DEGREE,
            payload: axios.delete(`${types.API_SUPERUSER_DEGREE}/${id}`)
        })
    }
}

/*
    Superuser handle training types
 */

export const createTrainingType = (type) => {
    return dispatch => {
        const { name } = type
        return dispatch({
            type: types.SUPERUSER_CREATE_TRAINING_TYPE,
            payload: axios.post(types.API_SUPERUSER_TRAINING_TYPE, {
                name: name && name.trim()
            })
        })
    }
}

export const updateTrainingType = (type) => {
    return dispatch => {
        const { id, name } = type
        return dispatch({
            type: types.SUPERUSER_UPDATE_TRAINING_TYPE,
            payload: axios.put(`${types.API_SUPERUSER_TRAINING_TYPE}/${id}`, {
                name: name && name.trim()
            })
        })
    }
}

export const deleteTrainingType = (id) => {
    return dispatch => {
        return dispatch({
            type: types.SUPERUSER_DELETE_TRAINING_TYPE,
            payload: axios.delete(`${types.API_SUPERUSER_TRAINING_TYPE}/${id}`)
        })
    }
}

/*
    Superuser handle training levels
 */

export const createTrainingLevel = (level) => {
    return dispatch => {
        const { name, levelType } = level
        return dispatch({
            type: types.SUPERUSER_CREATE_TRAINING_LEVEL,
            payload: axios.post(types.API_SUPERUSER_TRAINING_LEVEL, {
                name: name && name.trim(), levelType
            })
        })
    }
}

export const updateTrainingLevel = (level) => {
    return dispatch => {
        const { id, name, levelType } = level
        return dispatch({
            type: types.SUPERUSER_UPDATE_TRAINING_LEVEL,
            payload: axios.put(`${types.API_SUPERUSER_TRAINING_LEVEL}/${id}`, {
                name: name && name.trim(), levelType
            })
        })
    }
}

export const deleteTrainingLevel = (id) => {
    return dispatch => {
        return dispatch({
            type: types.SUPERUSER_DELETE_TRAINING_LEVEL,
            payload: axios.delete(`${types.API_SUPERUSER_TRAINING_LEVEL}/${id}`)
        })
    }
}

/*
    Superuser handles quotas
 */

export const loadQuotas = () => {
    return dispatch => {
        return dispatch({
            type: types.SUPERUSER_LOAD_QUOTAS,
            payload: axios.get(types.API_QUOTA)
        })
    }
}

export const createQuota = (data) => {
    return dispatch => {
        return dispatch({
            type: types.SUPERUSER_CREATE_QUOTA,
            payload: axios.post(types.API_SUPERUSER_QUOTA, data)
        })
    }
}

export const updateQuota = (version, data) => {
    return dispatch => {
        return dispatch({
            type: types.SUPERUSER_UPDATE_QUOTA,
            payload: axios.put(`${types.API_SUPERUSER_QUOTA}/${version}`, data)
        })
    }
}

export const changeActiveQuota = (version, isActive) => {
    return dispatch => {
        return dispatch({
            type: types.SUPERUSER_CHANGE_ACTIVE_QUOTA,
            payload: axios.patch(`${types.API_SUPERUSER_QUOTA}/${version}`, {
                isActive
            })
        })
    }
}

export const deleteQuota = version => {
    return dispatch => {
        return dispatch({
            type: types.SUPERUSER_DELETE_QUOTA,
            payload: axios.delete(`${types.API_SUPERUSER_QUOTA}/${version}`)
        })
    }
}
