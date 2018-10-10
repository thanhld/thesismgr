import * as types from 'Constants'
import { sortOfficers } from 'Helper'

const initialState = {
    lecturers: []
}

export default (state = initialState, action) => {
    switch (action.type) {
        case `${types.LOAD_LECTURERS_OF_DEPARTMENT}_FULFILLED`:
            return {
                ...state,
                lecturers: action.payload.data.data.sort(sortOfficers)
            }
        case `${types.LOAD_LECTURERS_HAS_AREA}_FULFILLED`:
            return {
                ...state,
                lecturers: action.payload.data.data.sort(sortOfficers)
            }
        default:
            return state
    }
}
