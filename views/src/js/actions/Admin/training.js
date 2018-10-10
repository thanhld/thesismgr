import axios from 'axios'
import * as types from 'Constants'

/*
    Training Areas
 */

export const createTrainingArea = (area) => {
    return dispatch => {
        const { name, areaCode } = area
        return dispatch({
            type: types.ADMIN_CREATE_TRAINING_AREA,
            payload: axios.post(types.API_ADMIN_TRAINING_AREA, {
                name: name && name.trim(), areaCode
            })
        })
    }
}

export const updateTrainingArea = (area) => {
    return dispatch => {
        const { id, name, areaCode } = area
        return dispatch({
            type: types.ADMIN_UPDATE_TRAINING_AREA,
            payload: axios.put(`${types.API_ADMIN_TRAINING_AREA}/${id}`, {
                name: name && name.trim(), areaCode
            })
        })
    }
}

export const deleteTrainingArea = (id) => {
    return dispatch => {
        return dispatch({
            type: types.ADMIN_DELETE_TRAINING_AREA,
            payload: axios.delete(`${types.API_ADMIN_TRAINING_AREA}/${id}`)
        })
    }
}

/*
    Training Programs
 */

export const createTrainingProgram = (program) => {
    return dispatch => {
        const { name, programCode, vietnameseThesisTitle, englishThesisTitle, startTime, trainingDuration, departmentId, trainingAreasId, trainingLevelsId, trainingTypesId, isInUse, thesisNormalizedFactor } = program
        return dispatch({
            type: types.ADMIN_CREATE_TRAINING_PROGRAM,
            payload: axios.post(types.API_ADMIN_TRAINING_PROGRAM, {
                name, programCode, vietnameseThesisTitle, englishThesisTitle, startTime, trainingDuration, departmentId, trainingAreasId, trainingLevelsId, trainingTypesId, isInUse, thesisNormalizedFactor
            })
        })
    }
}

export const updateTrainingProgram = (program) => {
    return dispatch => {
        const { id, name, programCode, vietnameseThesisTitle, englishThesisTitle, startTime, trainingDuration, departmentId, trainingAreasId, trainingLevelsId, trainingTypesId, isInUse, thesisNormalizedFactor } = program
        return dispatch({
            type: types.ADMIN_UPDATE_TRAINING_PROGRAM,
            payload: axios.put(`${types.API_ADMIN_TRAINING_PROGRAM}/${id}`, {
                name, programCode, vietnameseThesisTitle, englishThesisTitle, startTime, trainingDuration, departmentId, trainingAreasId, trainingLevelsId, trainingTypesId, isInUse, thesisNormalizedFactor
            })
        })
    }
}

export const deleteTrainingProgram = (id) => {
    return dispatch => {
        return dispatch({
            type: types.ADMIN_DELETE_TRAINING_PROGRAM,
            payload: axios.delete(`${types.API_ADMIN_TRAINING_PROGRAM}/${id}`)
        })
    }
}

/*
    Training Course
 */
export const loadTrainingCourses = () => {
    return dispatch => {
        return dispatch({
            type: types.LOAD_TRAINING_COURSES,
            payload: axios.get(types.API_TRAINING_COURSE)
        })
    }
}

export const createTrainingCourse = (course) => {
    return dispatch => {
        const { courseCode, courseName, trainingProgramId, admissionYear, isCompleted } = course
        return dispatch({
            type: types.ADMIN_CREATE_TRAINING_COURSE,
            payload: axios.post(types.API_ADMIN_TRAINING_COURSE, {
                courseCode,
                courseName: courseName && courseName.trim(),
                trainingProgramId,
                admissionYear,
                isCompleted
            })
        })
    }
}

export const updateTrainingCourse = (course) => {
    return dispatch => {
        const { id, name, courseCode, courseName, trainingProgramId, admissionYear, isCompleted } = course
        return dispatch({
            type: types.ADMIN_UPDATE_TRAINING_COURSE,
            payload: axios.put(`${types.API_ADMIN_TRAINING_COURSE}/${id}`, {
                name: name && name.trim(),
                courseCode,
                courseName: courseName && courseName.trim(),
                trainingProgramId,
                admissionYear,
                isCompleted
            })
        })
    }
}

export const deleteTrainingCourse = (id) => {
    return dispatch => {
        return dispatch({
            type: types.ADMIN_DELETE_TRAINING_COURSE,
            payload: axios.delete(`${types.API_ADMIN_TRAINING_COURSE}/${id}`)
        })
    }
}
