import axios from 'axios'
import * as types from 'Constants'

export const loadDegrees = () => {
    return dispatch => {
        return dispatch({
            type: types.LOAD_DEGREES,
            payload: axios.get(types.API_DEGREE)
        })
    }
}

export const loadTrainingTypes = () => {
    return dispatch => {
        return dispatch({
            type: types.LOAD_TRAINING_TYPES,
            payload: axios.get(types.API_TRAINING_TYPE)
        })
    }
}

export const loadTrainingLevels = () => {
    return dispatch => {
        return dispatch({
            type: types.LOAD_TRAINING_LEVELS,
            payload: axios.get(types.API_TRAINING_LEVEL)
        })
    }
}

export const loadDepartments = () => {
    return dispatch => {
        return dispatch({
            type: types.LOAD_DEPARTMENTS,
            payload: axios.get(types.API_DEPARTMENT)
        })
    }
}

export const loadAreas = () => {
    return dispatch => {
        return dispatch({
            type: types.LOAD_AREAS,
            payload: axios.get(types.API_AREA)
        })
    }
}

export const loadTrainingAreas = () => {
    return dispatch => {
        return dispatch({
            type: types.LOAD_TRAINING_AREAS,
            payload: axios.get(types.API_TRAINING_AREA)
        })
    }
}

export const loadTrainingPrograms = () => {
    return dispatch => {
        return dispatch({
            type: types.LOAD_TRAINING_PROGRAMS,
            payload: axios.get(types.API_TRAINING_PROGRAM)
        })
    }
}

export const loadLecturersOfDepartment = (departmentId) => {
    const filter = `departmentId=${departmentId}`
    return dispatch => {
        return dispatch({
            type: types.LOAD_LECTURERS_OF_DEPARTMENT,
            payload: axios.get(types.API_OFFICER, {
                params: { filter }
            })
        })
    }
}

export const loadLecturersHasArea = (areaId) => {
    const api = `${types.API_AREA}/${areaId}/officer`;
    return dispatch => {
        return dispatch({
            type: types.LOAD_LECTURERS_HAS_AREA,
            payload: axios.get(api)
        })
    }
}

export const loadLecturers = () => {
    return dispatch => {
        return dispatch({
            type: types.LOAD_LECTURERS,
            payload: axios.get(types.API_OFFICER)
        })
    }
}

export const loadPublicLecturer = id => {
    return dispatch => {
        return dispatch({
            type: types.LOAD_PUBLIC_LECTURER,
            payload: axios.get(`${types.API_OFFICER}/${id}`)
        })
    }
}

export const loadPublicLecturerAreas = (uid) => {
    const api = types.API_OFFICER + '/' + uid + '/knowledge-area';
    return dispatch => {
        return dispatch({
            type: types.LOAD_PUBLIC_LECTURER_AREAS,
            payload: axios.get(api)
        })
    }
}

export const loadTopics = (actionType, filter) => {
    const api = `${types.API_TOPIC}?filter=${filter}`
    return dispatch => {
        return dispatch({
            type: actionType,
            payload: axios.get(api)
        })
    }
}

export const createActivity = (actionType, data) => {
    return dispatch => {
        return dispatch({
            type: actionType,
            payload: axios.post(types.API_TOPIC_ACTIVITY, data)
        })
    }
}

export const uploadFile = data => {
    return dispatch => {
        return dispatch({
            type: types.UPLOAD_FILE,
            payload: axios.post(types.API_UPLOAD_FILE, data)
        })
    }
}

export const loadOutOfficers = () => {
    return dispatch => {
        return dispatch({
            type: types.LOAD_OUT_OFFICERS,
            payload: axios.get(types.API_OUT_OFFICER)
        })
    }
}

export const loadAnnouncements = () => {
    return dispatch => {
        return dispatch({
            type: types.LOAD_ANNOUNCEMENTS,
            payload: axios.get(types.API_ANNOUNCEMENT)
        })
    }
}

export const loadActiveQuota = () => {
    const filter = "isActive=1"
    return dispatch => {
        return dispatch({
            type: types.LOAD_ACTIVE_QUOTA,
            payload: axios.get(`${types.API_QUOTA}?filter=${filter}`)
        })
    }
}
